<?php

use Tomahawk\Forms\Form;

use Tomahawk\Forms\Element\Radio;

class RadioTest extends PHPUnit_Framework_TestCase
{
    public function testHidden()
    {
        $form = new Form();

        $form->add(new Radio('status'));

        $html = $form->render('status', array('class' => 'input-field'));

        $this->assertEquals('<input type="radio" name="status" class="input-field">', $html);
    }
}