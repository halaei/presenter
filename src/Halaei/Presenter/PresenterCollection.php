<?php namespace Halaei\Presenter;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use ArrayAccess;
use Countable;
use IteratorAggregate;
use JsonSerializable;
use ArrayIterator;

class PresenterCollection implements ArrayAccess, Arrayable, Countable, IteratorAggregate, Jsonable, JsonSerializable
{
    /**
     * @var Collection
     */
    protected $collection;

    function __construct(Collection $collection)
    {
        $this->collection = $collection;
    }

    function toPresenterArray()
    {
        $array = [];
        foreach ($this->collection as $key => $model) {
            array_push($array, $model->present());
        }
        return $array;
    }

    function __call($name, $arguments)
    {
        $result = call_user_func_array([$this->collection, $name], $arguments);
        if($result === $this->collection)
            return $this;
        elseif($result instanceof Collection)
            return new static($result);
        elseif($result instanceof Model)
            return $result->present();
        elseif(is_array($result))
        {
            foreach ($result as &$item) {
                if($item instanceof Model)
                    $item = $item->present();
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

    //Method of Arrayable
    public function toArray()
    {
        return $this->collection->toArray();
    }

    //Method of Jsonable
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