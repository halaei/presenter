<?php namespace Halaei\Presenter;

use ArrayAccess;
use Halaei\Presenter\Exceptions\PresenterException;
use JsonSerializable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Contracts\ArrayableInterface;
use Illuminate\Support\Contracts\JsonableInterface;

class BasePresenter implements ArrayAccess, ArrayableInterface, JsonableInterface, JsonSerializable
{
    /**
     * @var Model
     */
    protected $model;

    /**
     * @var array
     * public methods of $model that can be called via presenter(using __call() magic method)
     */
    protected $callables = [];

    /**
     * @var array
     * methods in defauld_callables will always be accessable. Overwrite it if required.
     */
    protected static $default_callables = ['getAttribute', 'setAttribute', 'getOriginal'];

    /**
     * @var string
     * Friend class of this presenter, who can call getModel() method,
     * or access all the public methods of model via __call()
     */
    protected $friend;

    public function __construct($model, $callables = [], $friend = null)
    {
        $this->model = $model;
        $this->callables = array_flip(array_merge($callables, static::$default_callables));
        $this->friend = $friend;
    }

    public function __get($key)
    {
        return $this->model->getAttribute($key);
    }

    public function __set($key, $value)
    {
        return $this->model->setAttribute($key, $value);
    }

    public function __call($name, $arguments)
    {
        if (array_key_exists($name, $this->callables)) {
            return $this->wrapResult($name, $arguments);
        }
        $backtrace = debug_backtrace(0, 3)[2];
        if (isset($backtrace['class']) && $backtrace['class'] == $this->friend) {
            return $this->wrapResult($name, $arguments);
        }
        $msg = 'no access to ' . get_class($this->model) . ".$name() via" . 'class ' . get_class($this);
        throw new PresenterException($msg);
    }

    public function getModel()
    {
        $caller = debug_backtrace(0, 2)[1];
        if (isset($caller['class']) && !is_null($this->friend) && $caller['class'] == $this->friend)
            return $this->model;
        $msg = "No access to prenseter's getModel()";
        throw new PresenterException($msg);
    }

    //Methods of ArrayAccess:
    public function offsetExists($offset)
    {
        return $this->model->offsetExists($offset);
    }

    public function offsetGet($offset)
    {
        return $this->model->offsetGet($offset);
    }

    public function offsetSet($offset, $value)
    {
        return $this->model->offsetSet($offset, $value);
    }

    public function offsetUnset($offset)
    {
        return $this->model->offsetUnset($offset);
    }

    //Method of ArrayableInterface
    public function toArray()
    {
        return $this->model->toArray();
    }

    //Method of JsonableInterface
    public function toJson($options = 0)
    {
        return $this->model->toArray();
    }

    //Method of JsonSerializable
    function jsonSerialize()
    {
        return $this->model->jsonSerialize();
    }

    /**
     * @param $method_name
     * @param $arguments
     * @return $this|mixed
     */
    protected function wrapResult($method_name, $arguments)
    {
        $result = call_user_func([$this->model, $method_name], $arguments);
        if ($result === $this->model)
            return $this;
        elseif ($result instanceof Model)
            return $result->present();
        elseif ($result instanceof Collection)
            return new PresenterCollection($result);
        return $result;
    }
}