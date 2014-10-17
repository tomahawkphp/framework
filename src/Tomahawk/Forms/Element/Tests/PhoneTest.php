<?php

namespace Tomahawk\Forms\Element\Tests;

use Tomahawk\Test\TestCase;
use Tomahawk\Forms\Form;
use Tomahawk\Forms\Element\Phone;

class PhoneTest extends TestCase
{
    public function testHidden()
    {
        $form = new Form('/');

        $form->add(new Phone('mobile_number'));

        $html = $form->render('mobile_number', array('class' => 'input-field'));

        $this->assertEquals('<input type="tel" name="mobile_number" class="input-field">', $html);
    }
}
