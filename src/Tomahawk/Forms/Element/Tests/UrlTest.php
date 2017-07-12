<?php

namespace Tomahawk\Forms\Element\Tests;

use PHPUnit_Framework_TestCase as TestCase;
use Tomahawk\Forms\Form;
use Tomahawk\Forms\Element\Url;

class UrlTest extends TestCase
{
    public function testTextAreaNoValue()
    {
        $form = new Form('/');

        $form->add(new Url('website'));

        $html = $form->render('website');

        $this->assertEquals('<input type="url" name="website">', $html);
    }
}
