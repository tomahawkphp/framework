<?php

use Tomahawk\Forms\Form;

use Tomahawk\Forms\Element\Date;

class DateTest extends PHPUnit_Framework_TestCase
{

    public function testDate()
    {
        $form = new Form();

        $form->add(new Date('date'));

        $html = $form->render('date', array('class' => 'input-field'));

        $this->assertEquals('<input type="date" name="date" class="input-field">', $html);
    }
}