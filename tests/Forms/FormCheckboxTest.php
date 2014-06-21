<?php

use Tomahawk\Forms\Form;

use Tomahawk\Forms\Element\Checkbox;

class FormCheckboxTest extends PHPUnit_Framework_TestCase
{

    public function testCheckbox()
    {
        $form = new Form();

        $form->add(new Checkbox('enabled', 1));

        $html = $form->render('enabled', array('class' => 'input-field'));

        $this->assertEquals('<input type="checkbox" name="enabled" value="1" class="input-field">', $html);
    }

    public function testCheckboxChecked()
    {
        $form = new Form();

        $form->add(new Checkbox('enabled', 1, true));

        $html = $form->render('enabled', array('class' => 'input-field'));

        $this->assertEquals('<input type="checkbox" name="enabled" value="1" class="input-field" checked="checked">', $html);
    }

    public function testCheckboxCheckedAlternative()
    {
        $form = new Form();

        $form->add(new Checkbox('enabled', 1));

        $html = $form->render('enabled', array('class' => 'input-field', 'checked' => 'checked'));

        $this->assertEquals('<input type="checkbox" name="enabled" value="1" class="input-field" checked="checked">', $html);
    }
}