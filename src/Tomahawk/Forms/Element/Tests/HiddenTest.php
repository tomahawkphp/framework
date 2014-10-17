<?php

namespace Tomahawk\Forms\Element\Tests;

use Tomahawk\Test\TestCase;
use Tomahawk\Forms\Form;
use Tomahawk\Forms\Element\Hidden;

class HiddenTest extends TestCase
{
    public function testHidden()
    {
        $form = new Form('/');

        $form->add(new Hidden('user_id'));

        $html = $form->render('user_id', array('class' => 'input-field'));

        $this->assertEquals('<input type="hidden" name="user_id" class="input-field">', $html);
    }
}
