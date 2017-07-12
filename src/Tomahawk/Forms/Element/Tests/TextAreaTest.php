<?php

namespace Tomahawk\Forms\Element\Tests;

use PHPUnit_Framework_TestCase as TestCase;
use Tomahawk\Forms\Form;
use Tomahawk\Forms\Element\TextArea;

class TextAreaTest extends TestCase
{
    public function testTextAreaNoValue()
    {
        $form = new Form('/');

        $form->add(new TextArea('comments'));

        $html = $form->render('comments');

        $this->assertEquals('<textarea name="comments"></textarea>', $html);
    }
}
