<?php namespace Halaei\Presenter;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class PresenterCollection extends ContainerWrapper
{
    /**
     * @var Collection
     */
    protected $container;

    function __construct(Collection $collection)
    {
        $this->container = $collection;
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

}