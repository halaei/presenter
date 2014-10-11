<?php namespace Halaei\Presenter;

trait PresentableTrait {

    public function present()
    {
        $presenter_class = isset($this->presenter_class) ? $this->presenter_class: 'Halaei\Presenter\BasePresenter';
        $presenter_callables = isset($this->presenter_callables) ? $this->presenter_callables: [];
        $presenter_friend = isset($this->presenter_friend) ? $this->presenter_friend: null;
        return new $presenter_class($this, $presenter_callables, $presenter_friend);
    }
} 