<?php

namespace Tomahawk\Forms\Element\Tests;

use Tomahawk\Test\TestCase;
use Tomahawk\Forms\Form;
use Tomahawk\Forms\Element\Email;

class EmailTest extends TestCase
{
    public function testDate()
    {
        $form = new Form();

        $form->add(new Email('email_address'));

        $html = $form->render('email_address', array('class' => 'input-field'));

        $this->assertEquals('<input type="email" name="email_address" class="input-field">', $html);
    }
}
