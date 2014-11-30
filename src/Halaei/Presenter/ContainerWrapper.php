<?php namespace Halaei\Presenter;

use ArrayAccess;
use ArrayIterator;
use Countable;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use IteratorAggregate;
use JsonSerializable;

abstract class ContainerWrapper implements ArrayAccess, Arrayable, Countable, IteratorAggregate, Jsonable, JsonSerializable{

    /**
     * @var Model[] | Collection | Paginator
     */
    protected $container;

    //Methods of ArrayAccess
    public function offsetExists($offset)
    {
        return $this->container->offsetExists($offset);
    }

    public function offsetGet($offset)
    {
        return $this->container->offsetGet($offset)->present();
    }

    public function offsetSet($offset, $value)
    {
        return $this->container->offsetSet($offset, $value);
    }

    public function offsetUnset($offset)
    {
        return $this->container->offsetUnset($offset);
    }

    //Method of Countable
    public function count()
    {
        return $this->container->count();
    }

    //Method of IteratorAggregate
    public function getIterator()
    {
        return new ArrayIterator($this->toPresenterArray());
    }

    //Method of Arrayable
    public function toArray()
    {
        return $this->container->toArray();
    }

    //Method of Jsonable
    public function toJson($options = 0)
    {
        return $this->container->toJson();
    }

    //Method of JsonSerializable
    public function jsonSerialize()
    {
        return $this->container->jsonSerialize();
    }

    public function toPresenterArray()
    {
        return $this->getPresenterArray($this->container);
    }

    protected function getPresenterArray($items)
    {
        return array_map(
            function($item){
                return $item->present();
            },
            $items
        );
    }
}