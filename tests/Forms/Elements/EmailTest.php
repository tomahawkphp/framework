<?php

use Tomahawk\Forms\Form;
use Tomahawk\Forms\Element\Email;

class EmailTest extends PHPUnit_Framework_TestCase
{
    public function testDate()
    {
        $form = new Form();

        $form->add(new Email('email_address'));

        $html = $form->render('email_address', array('class' => 'input-field'));

        $this->assertEquals('<input type="email" name="email_address" class="input-field">', $html);
    }
}