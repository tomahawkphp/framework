<?php

namespace Tomahawk\Forms\Element\Tests;

use PHPUnit_Framework_TestCase as TestCase;
use Tomahawk\Forms\Form;
use Tomahawk\Forms\Element\Submit;

class SubmitTest extends TestCase
{
    public function testSubmit()
    {
        $form = new Form('/');

        $form->add(new Submit('submitForm', 'Save'));

        $html = $form->render('submitForm');

        $this->assertEquals('<input type="submit" name="submitForm" value="Save">', $html);
    }
}
