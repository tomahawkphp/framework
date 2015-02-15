<?php

namespace Tomahawk\Forms\Tests;

use Tomahawk\Forms\Element\Checkbox;
use Tomahawk\Forms\Element\Radio;
use Tomahawk\Test\TestCase;
use Tomahawk\Forms\Form;
use Tomahawk\Forms\Element\Text;

class FormTest extends TestCase
{

    public function testAddingElements()
    {
        $form = new Form('/');

        $form->add(new Text('first_name'));
        $form->add(new Checkbox('enabled', 1, true));
        $form->add(new Radio('status', 'active'));

        $html = $form->render('first_name');

        $this->assertEquals('<input type="text" name="first_name">', $html);

        $html = $form->render('enabled');

        $this->assertEquals('<input type="checkbox" name="enabled" value="1" checked="checked">', $html);

        $html = $form->render('status');

        $this->assertEquals('<input type="radio" name="status" value="active">', $html);

        $html = $form->render('first_name', array('class' => 'input-field', 'disabled'));

        $this->assertEquals('<input type="text" name="first_name" class="input-field" disabled="disabled">', $html);

        $this->assertCount(3, $form->getElements());
    }

    public function testOldInputWithElements()
    {
        // Checkable elements shouldn't have there value set
        // and if a value exists in the post data it should be checked

        $input = array(
            'first_name' => 'Tommy Ellis',
            'enabled'    => 110,
            'status'     => 11,
        );

        $form = new Form('/');
        $form->setOldInput($input);

        $form->add(new Text('first_name'));
        $form->add(new Checkbox('enabled', 1));
        $form->add(new Radio('status', 'active'));

        $html = $form->render('first_name');

        $this->assertEquals('<input type="text" name="first_name">', $html);

        $html = $form->render('enabled');

        $this->assertEquals('<input type="checkbox" name="enabled" value="1" checked="checked">', $html);

        $html = $form->render('status');

        $this->assertEquals('<input type="radio" name="status" value="active">', $html);

        $html = $form->render('first_name', array('class' => 'input-field', 'disabled'));

        $this->assertEquals('<input type="text" name="first_name" class="input-field" disabled="disabled" value="Tommy Ellis">', $html);

        $this->assertCount(3, $form->getElements());
    }

    public function testChangingElementName()
    {
        $text = new Text('first_name');

        $this->assertEquals('first_name', $text->getName());

        $text->setName('full_name');

        $this->assertEquals('full_name', $text->getName());
    }

    public function testOpenReturnsCorrectHtml()
    {
        $form = new Form('/', 'GET');

        $html = $form->open();

        $this->assertEquals('<form method="GET" action="/">', $html);
    }

    public function testCloseReturnsCorrectHtml()
    {
        $form = new Form('/', 'GET');

        $html = $form->close();

        $this->assertEquals('</form>', $html);
    }
}
