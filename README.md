presenter (A flexible presenter for Laravel)
=========
This is an alternative implementation of [laracasts/presenter](https://github.com/laracasts/Presenter) with some added features. Some parts of this document is copy-past!

# Easy View Presenters

So you have those scenarios where a bit of logic needs to be performed before some data (likely from your entity) is displayed from the view.

- Should that logic be hard-coded into the view? **No**.
- Should we instead store the logic in the model? **No again!**

Instead, leverage view presenters. That's what they're for! This package provides one such implementation.

**PS**: Using repository pattern, this package suggests that repository functions should return presenter instead of model and presenter collection instead of eloquent collection.

#Installation

```js
{
    "require": {
        "halaei/presenter": "0.*"
    }
}
```

## Usage

The first step is to store your presenters somewhere - anywhere. These will be simple objects that do nothing more than format data, as required.

Here's an example of a presenter.

```php
use Laracasts\Presenter\Presenter;

class UserPresenter extends Presenter {

    public function fullName()
    {
        return $this->first . ' ' . $this->last;
    }

    public function accountAge()
    {
        return $this->created_at->diffForHumans();
    }

}
```

Next, on your entity, pull in the `Halaei\Presenter\PresentableTrait` trait, which will automatically instantiate your presenter class.

Here's an example - maybe a Laravel `User` model.

```php
<?php

use Halaei\Presenter\PresentableTrait;

class User extends \Eloquent {

    use PresentableTrait;

    protected $presenter_class = 'UserPresenter';

}
```

That's it! You're done. Now, within your view, you can do:

```php
    <h1>Hello, {{ $user->present()->fullName }}</h1>
```

Notice how the call to the `present()` method (which will return your new or cached presenter object) also provides the benefit of making it perfectly clear where you must go, should you need to modify how a full name is displayed on the page.

Have fun!

# Added Features

## BasePresenter
If you want to return model from a repository function, but you don't want the client code do something like this:

```php
    $model->newQuery()->delete(); //delete all the records of table!
```
then you can use PresentableTrait in your model, even without setting $presenter_class. There is a default presenter class for this purpose: BasePresenter.

```php
    function someRepositoryFunction()
    {
        return $this->model->where('some condition')->firstOrFail()->present();
    }
```

## $presenter_callables
If you want to give some flexibilities of eloquent model to the client of your repository, but not too much flexibility, you can define $presenter_callables while using PresentableTrait:

```php
    use Halaei\Presenter\PresentableTrait;

        class User extends \Eloquent {

        use PresentableTrait;

        protected $presenter_class = 'UserPresenter';

        protected $presenter_callables = ['save']; //to make your presenter a real active record!
}
```

## $presenter_friend
If you want your repository being able to access the model that is presented, and you don't want that access nowhere else (i.e. YourRepositoryClass being a C++ friend of your presenter) do the following:

```php
        class User extends \Eloquent {

            use PresentableTrait;

            protected $presenter_class = 'UserPresenter';

            protected $presenter_friend = 'YourRepositoryClass';

        ...
        }

        class YourRepositoryClass()
        {
            function save(UserPresenter $user)
            {
                //you can do
                $user->save();
                //or equivalently
                $user->getModel()->save();
                //but that can be done only in this friend class! Outsiders will get an Exection!
            }
        }
```

## PresenterCollection
4- Instead of returning an eloquent collection, which is basically an array of eloquent models, simply wrap that collection inside a Halaei\Presenter\PresenterCollection:

```php
    use Halaei\Presenter\PresenterCollection;

    ...

    function anotherRepositoryFunction()
    {
        return new PresenterCollection($this->model->where('some condition')->get());
    }
```