<?php

use Tomahawk\Forms\Form;

use Tomahawk\Forms\Element\Text;
use Tomahawk\Forms\Element\Password;
use Tomahawk\Forms\Element\Select;
use Tomahawk\Forms\Element\Hidden;

class FormTest extends PHPUnit_Framework_TestCase
{

    public function testThing()
    {
        $form = new Form();

        $form->add(new Text('first_name'));

        $html = $form->render('first_name');

        $this->assertEquals('<input type="text" name="first_name">', $html);

        $html = $form->render('first_name', array('class' => 'input-field'));

        $this->assertEquals('<input type="text" name="first_name" class="input-field">', $html);
    }

    public function testSelectNotSelected()
    {
        $form = new Form();

        $form->add(new Select('question1', array(
            'yes' => 'Yes',
            'no'  => 'No'
        )), 'yes');

        $html = $form->render('question1');

        $this->assertEquals('<select name="question1"><option value="yes">Yes</option><option value="no">No</option></select>', $html);
    }

    public function testSelectSelected()
    {
        $form = new Form();

        $form->add(new Select('question1', array(
            'yes' => 'Yes',
            'no'  => 'No'
        ), 'yes'));

        $html = $form->render('question1');

        $this->assertEquals('<select name="question1"><option value="yes" selected="selected">Yes</option><option value="no">No</option></select>', $html);
    }

    public function testSelectGroup()
    {
        $form = new Form();

        $form->add(new Select('question1', array(
            'group1' => array(
                '1' => 'One',
                '2' => 'Two'
            ),
            'group2' => array(
                '3' => 'Three',
                '4' => 'Four'
            )
        )));

        $html = $form->render('question1');

        $this->assertEquals('<select name="question1"><optgroup label="group1"><option value="1">One</option><option value="2">Two</option></optgroup><optgroup label="group2"><option value="3">Three</option><option value="4">Four</option></optgroup></select>', $html);
    }

    public function testSelectGroupSelected()
    {
        $form = new Form();

        $form->add(new Select('question1', array(
            'group1' => array(
                '1' => 'One',
                '2' => 'Two'
            ),
            'group2' => array(
                '3' => 'Three',
                '4' => 'Four'
            )
        ), '1'));

        $html = $form->render('question1');

        $this->assertEquals('<select name="question1"><optgroup label="group1"><option value="1" selected="selected">One</option><option value="2">Two</option></optgroup><optgroup label="group2"><option value="3">Three</option><option value="4">Four</option></optgroup></select>', $html);
    }
}