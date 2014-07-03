<?php

use Tomahawk\Forms\Form;

use Tomahawk\Forms\Element\Text;
use Tomahawk\Forms\Element\Password;
use Tomahawk\Forms\Element\Select;
use Tomahawk\Forms\Element\Hidden;

class FormTest extends PHPUnit_Framework_TestCase
{

    public function testAddingElements()
    {
        $form = new Form();

        $form->add(new Text('first_name'));

        $html = $form->render('first_name');

        $this->assertEquals('<input type="text" name="first_name">', $html);

        $html = $form->render('first_name', array('class' => 'input-field', 'disabled'));

        $this->assertEquals('<input type="text" name="first_name" class="input-field" disabled="disabled">', $html);

        $this->assertCount(1, $form->getElements());
    }

    public function testChangingElementName()
    {
        $text = new Text('first_name');

        $this->assertEquals('first_name', $text->getName());

        $text->setName('full_name');

        $this->assertEquals('full_name', $text->getName());
    }
}