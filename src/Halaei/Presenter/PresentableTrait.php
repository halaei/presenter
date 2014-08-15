<?php namespace Halaei\Presenter;

trait PresentableTrait {

    public function present()
    {
        $presenter_class = isset(static::$presenter_class) ? static::$presenter_class: 'Halaei\Presenter\BasePresenter';
        $presenter_callables = isset(static::$presenter_callables) ? static::$presenter_callables: [];
        $presenter_friend = isset(static::$presenter_friend) ? static::$presenter_friend: null;
        return new $presenter_class($this, $presenter_callables, $presenter_friend);
    }
} 