<?php

namespace Tomahawk\Routing\Tests;

use Tomahawk\Test\TestCase;
use Tomahawk\Templating\Helper\InputHelper;

class InputHelperTest extends TestCase
{
    public function testHelperReturnsCorrectName()
    {
        $helper = new InputHelper($this->getInputManager());
        $this->assertEquals('input', $helper->getName());
    }

    public function testHelperCallsGetMethod()
    {
        $inputManager = $this->getInputManager();

        $inputManager->expects($this->once())
            ->method('get');

        $helper = new InputHelper($inputManager);
        $helper->get('name');
    }

    /**
     * @expectedException \BadMethodCallException
     */
    public function testInvalidMethodThrowsException()
    {
        $inputManager = $this->getInputManager();

        $helper = new InputHelper($inputManager);
        $helper->foo();
    }

    protected function getInputManager()
    {
        return $this->getMock('Tomahawk\Input\InputInterface');
    }
}
