<?php

use Tomahawk\HttpKernel\Kernel;

class TestApp extends Kernel
{
    public function registerBundles()
    {
        return array();
    }
}

class TestController extends \Tomahawk\Routing\Controller
{
    public function get_index()
    {
        return $this->response->content('Test');
    }

    public function get_thing()
    {
        return $this->response->content('Test2');
    }
}


class TestController2
{
    function action($foo)
    {

    }
}

class UncallableController
{
    private function action($foo)
    {

    }
}



class TestInvokeableClass
{
    public function __invoke($x)
    {
        return $x;
    }
}

function some_controller_function($foo, $foobar)
{
}

function controller() {

}
