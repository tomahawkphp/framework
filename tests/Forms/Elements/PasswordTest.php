<?php

use Tomahawk\Forms\Form;

use Tomahawk\Forms\Element\Password;

class PasswordTest extends PHPUnit_Framework_TestCase
{
    public function testHidden()
    {
        $form = new Form();

        $form->add(new Password('password'));

        $html = $form->render('password', array('class' => 'input-field'));

        $this->assertEquals('<input type="password" name="password" class="input-field">', $html);
    }
}