<?php namespace Halaei\Presenter;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Contracts\ArrayableInterface;
use Illuminate\Support\Contracts\JsonableInterface;
use ArrayAccess;
use Countable;
use IteratorAggregate;
use JsonSerializable;
use ArrayIterator;
class PresenterCollection implements ArrayAccess, ArrayableInterface, Countable, IteratorAggregate, JsonableInterface, JsonSerializable
{
    /**
     * @var Collection
     */
    protected $collection;
    protected $callables;
    protected $friend;


    function __construct(Collection $collection, $callables = [], $friend = null)
    {
        $this->collection = $collection;
        $this->callables = $callables;
        $this->friend = $friend;
    }

    function toPresenterArray()
    {
        $array = [];
        foreach ($this->collection as $key => $model) {
            array_push($array, $model->present($this->callables, $this->friend));
        }
        return $array;
    }

    function __call($name, $arguments)
    {
        $result = call_user_func_array([$this->collection, $name], $arguments);
        if($result === $this->collection)
            return $this;
        elseif($result instanceof Collection)
            return new static($result, $this->callables, $this->friend);
        elseif($result instanceof Model)
            return $result->present($this->callables, $this->friend);
        elseif(is_array($result))
        {
            foreach ($result as &$item) {
                if($item instanceof Model)
                    $item = $item->present($this->callables, $this->friend);
            }
            unset($item);
            return $result;
        }
        return $result;
    }

    //Methods of ArrayAccess
    public function offsetExists($offset)
    {
        return $this->collection->offsetExists($offset);
    }

    public function offsetGet($offset)
    {
        return $this->collection->offsetGet($offset)->present();
    }

    public function offsetSet($offset, $value)
    {
        return $this->collection->offsetSet($offset, $value);
    }

    public function offsetUnset($offset)
    {
        return $this->collection->offsetUnset($offset);
    }

    //Method of Countable
    public function count()
    {
        return $this->collection->count();
    }

    //Method of IteratorAggregate
    public function getIterator()
    {
        return new ArrayIterator($this->toPresenterArray());
    }

    //Method of ArrayableInterface
    public function toArray()
    {
        return $this->toArray();
    }

    //Method of JsonableInterface
    public function toJson($options = 0)
    {
        return $this->collection->toJson();
    }

    //Method of JsonSerializable
    function jsonSerialize()
    {
        return $this->collection->jsonSerialize();
    }
}