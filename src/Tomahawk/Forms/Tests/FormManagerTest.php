<?php

namespace Tomahawk\Forms\Tests;

use Tomahawk\Test\TestCase;
use Tomahawk\Forms\Form;
use Tomahawk\Forms\FormsManager;
use Tomahawk\Forms\Test\TestForm;

class FormManagerTest extends TestCase
{
    /**
     * @var \Tomahawk\Forms\FormsManager
     */
    protected $formManager;

    public function setUp()
    {
        $this->formManager = new FormsManager();
    }

    public function testFormAdding()
    {
        $this->formManager->set('test_form', new TestForm());

        $this->assertTrue($this->formManager->has('test_form'));

        $this->assertInstanceOf('Tomahawk\Forms\Test\TestForm', $this->formManager->get('test_form'));

        $this->assertCount(1, $this->formManager->getAll());

        $this->formManager->clear();

        $this->assertCount(0, $this->formManager->getAll());
    }
}
