<?php

namespace Tomahawk\Console\Tests;

use Tomahawk\Test\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Tomahawk\DI\Container;
use Tomahawk\Routing\Router;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Tomahawk\Routing\Controller\ControllerResolver;
use Symfony\Component\HttpFoundation\Response;
use Tomahawk\HttpKernel\HttpKernel;
use Symfony\Component\Console\Tester\ApplicationTester;
use Tomahawk\Console\Application;
use Tomahawk\Console\Output;

class OutputTest extends TestCase
{
    public function testWrapperMethodInfo()
    {
        $output = new TestOutput();
        $output->info('some information');
        $this->assertEquals("some information\n", $output->output);
    }

    public function testWrapperMethodSuccess()
    {
        $output = new TestOutput();
        $output->success('some success message');
        $this->assertEquals("some success message\n", $output->output);
    }
    public function testWrapperMethodError()
    {
        $output = new TestOutput();
        $output->error('some error message');
        $this->assertEquals("some error message\n", $output->output);
    }

    public function testWrapperMethodQuestion()
    {
        $output = new TestOutput();
        $output->question('question?');
        $this->assertEquals("question?\n", $output->output);
    }
}

class TestOutput extends Output
{
    public $output = '';

    public function clear()
    {
        $this->output = '';
    }

    protected function doWrite($message, $newline)
    {
        $this->output .= $message.($newline ? "\n" : '');
    }
}