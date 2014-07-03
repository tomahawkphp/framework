<?php

use Tomahawk\Forms\Form;
use Tomahawk\Forms\FormsManager;

class FormManagerTest extends PHPUnit_Framework_TestCase
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
        $this->formManager->set('personForm', new PersonFormStub());

        $this->assertTrue($this->formManager->has('personForm'));

        $this->assertInstanceOf('PersonFormStub', $this->formManager->get('personForm'));

        $this->assertCount(1, $this->formManager->getAll());

        $this->formManager->clear();

        $this->assertCount(0, $this->formManager->getAll());
    }
}

class PersonFormStub extends Form {


    public function __construct()
    {

        $this->add( new \Tomahawk\Forms\Element\Text('username'));
    }


}