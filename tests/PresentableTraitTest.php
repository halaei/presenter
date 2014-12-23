<?php namespace Halaei\Presenter;

use Illuminate\Database\Eloquent\Model;

class PresentableTraitTest extends \PHPUnit_Framework_TestCase{

    /**
     * @var FooModel
     */
    public $foo_model;
    /**
     * @var StFooModel
     */
    public $st_foo_model;

    protected function setUp()
    {
        parent::setUp();
        $this->foo_model = new FooModel();
        $this->st_foo_model = new StFooModel();
    }

    public function testBasePresenterIsConstructed()
    {
        $presenter = $this->foo_model->present();
        $this->assertInstanceOf('Halaei\Presenter\BasePresenter', $presenter);

        $presenter = $this->foo_model->present();
        $this->assertInstanceOf('Halaei\Presenter\BasePresenter', $presenter);
    }

    public function testFooPresenterIsConstructed()
    {
        $this->foo_model->setPresenterVars('Halaei\Presenter\FooPresenter', null, null);
        $this->st_foo_model->setPresenterVars('Halaei\Presenter\FooPresenter', null, null);

        $presenter = $this->foo_model->present();
        $this->assertInstanceOf('Halaei\Presenter\FooPresenter', $presenter);

        $presenter = $this->foo_model->present();
        $this->assertInstanceOf('Halaei\Presenter\FooPresenter', $presenter);
    }

    public function testAccessToBar()
    {
        $this->foo_model->setPresenterVars('Halaei\Presenter\FooPresenter', ['bar'], null);
        $this->st_foo_model->setPresenterVars('Halaei\Presenter\FooPresenter', ['bar', 'baz'], null);

        $presenter = $this->foo_model->present();
        $this->assertEquals(1, $presenter->bar());

        $presenter = $this->st_foo_model->present();
        $this->assertEquals(1, $presenter->bar());
        $this->assertEquals(2, $presenter->baz());
    }

    public function testNoAccessToBaz()
    {
        $this->foo_model->setPresenterVars('Halaei\Presenter\FooPresenter', ['bar'], null);
        $presenter = $this->foo_model->present();

        $this->setExpectedException('Halaei\Presenter\Exceptions\PresenterException');

        $presenter->baz();
    }

    public function testFriend()
    {
        $this->foo_model->setPresenterVars('Halaei\Presenter\FooPresenter', ['bar'], 'Halaei\Presenter\PresentableTraitTest');
        $this->st_foo_model->setPresenterVars('Halaei\Presenter\FooPresenter', ['baz'], 'Halaei\Presenter\PresentableTraitTest');

        $presenter = $this->foo_model->present();
        $this->assertEquals(1, $presenter->bar());
        $this->assertEquals(2, $presenter->baz());

        $presenter = $this->st_foo_model->present();
        $this->assertEquals(1, $presenter->bar());
        $this->assertEquals(2, $presenter->baz());
    }

    public function testWrap()
    {
        $this->st_foo_model->setPresenterVars('Halaei\Presenter\FooPresenter', ['qux'], null);

        $presenter = $this->st_foo_model->present();

        $this->assertInstanceOf('Halaei\Presenter\FooPresenter', $presenter->qux());
    }
}

class BaseModel extends Model
{
    use PresentableTrait;


    public function bar()
    {
        return 1;
    }

    public function baz()
    {
        return 2;
    }

    public function qux()
    {
        return new static();
    }
}

class FooModel extends BaseModel
{
    protected $presenter_class;
    protected $presenter_callables;
    protected $presenter_friend;

    public function setPresenterVars($class, $callables, $friend)
    {
        $this->presenter_class = $class;
        $this->presenter_callables = $callables;
        $this->presenter_friend = $friend;
    }
}

class StFooModel extends BaseModel
{
    use PresentableTrait;

    protected static $presenter_class;
    protected static $presenter_callables;
    protected static $presenter_friend;

    public static function setPresenterVars($class, $callables, $friend)
    {
        static::$presenter_class = $class;
        static::$presenter_callables = $callables;
        static::$presenter_friend = $friend;
    }
}

class FooPresenter extends BasePresenter
{

}