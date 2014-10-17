<?php

namespace Tomahawk\Forms\Tests;

use Tomahawk\Test\TestCase;
use Tomahawk\Forms\Form;
use Tomahawk\Forms\Element\Text;
use Tomahawk\Validation\Validator;
use Tomahawk\Validation\Constraints\Required;
use Tomahawk\Forms\Test\Model;
use Tomahawk\Forms\Test\Model2;

class FormModelBindingTest extends TestCase
{

    public function testValuesAreTakenFromModelWhenRendering()
    {
        $peep = new Model();

        $peep->setName('Tom Ellis');
        $peep->setAge(27);

        $form = new Form('/', 'POST', $peep);

        $form->add(new Text('name'));

        $html = $form->render('name');

        $this->assertEquals('<input type="text" name="name" value="Tom Ellis">', $html);

    }

    public function testBindNoGetSetMethods()
    {
        $peep = new Model2();

        $peep->name = 'Tom Ellis';
        $peep->age = 27;

        $form = new Form('/', 'POST', $peep);

        $form->add(new Text('name'));
        $form->add(new Text('age'));

        $html = $form->render('name');

        $this->assertEquals('<input type="text" name="name" value="Tom Ellis">', $html);

        $html = $form->render('age');

        $this->assertEquals('<input type="text" name="age" value="27">', $html);

    }

    public function testOldInputIsUsedWhenRendering()
    {
        $peep = new Model();

        $peep->setName('Tom Ellis');
        $peep->setAge(27);

        $form = new Form('/', 'POST', $peep, array(
            'name' => 'Tommy'
        ));

        $form->add(new Text('name'));
        $form->add(new Text('age'));

        $html = $form->render('name');
        $this->assertEquals('<input type="text" name="name" value="Tommy">', $html);
    }

    public function testBindWithFailedValidation()
    {
        $input = array();

        $validator = new Validator();
        $validator->add('name', array(
            new Required()
        ));

        $peep = new Model();

        $peep->setName('Tom Ellis');
        $peep->setAge(27);

        $form = new Form('/', 'POST', $peep);
        $form->setValidator($validator);
        $form->add(new Text('name'));
        $form->bind($input);
        $this->assertFalse($form->isValid());
    }

    public function testBindWithValidValidation()
    {
        $input = array(
            'name' => 'Tommy Ellis'
        );

        $validator = new Validator();
        $validator->add('name', array(
            new Required()
        ));

        $peep = new Model();

        $peep->setName('Tom Ellis');
        $peep->setAge(27);

        $form = new Form('/', 'POST', $peep);
        $form->setValidator($validator);
        $form->add(new Text('name'));
        $form->bind($input);
        $this->assertTrue($form->isValid());
        $this->assertEquals('Tommy Ellis', $peep->getName());
    }

    public function testLateBinding()
    {
        $input = array(
            'name' => 'Tommy Ellis'
        );

        $validator = new Validator();
        $validator->add('name', array(
            new Required()
        ));

        $peep = new Model();

        $peep->setName('Tom Ellis');
        $peep->setAge(27);

        $form = new Form('/', 'POST');
        $form->setModel($peep);
        $form->setValidator($validator);
        $form->add(new Text('name'));
        $form->bind($input);


        $this->assertInstanceOf('Tomahawk\Forms\Test\Model', $form->getModel());
        $this->assertTrue($form->isValid());
        $this->assertEquals('Tommy Ellis', $peep->getName());
    }

    public function testBindWithValidValidationNoSetter()
    {
        $input = array(
            'name' => 'Tommy Ellis'
        );

        $validator = new Validator();
        $validator->add('name', array(
            new Required()
        ));

        $peep = new Model2();

        $peep->name = 'Tom Ellis';
        $peep->age = 27;

        $form = new Form('/', 'POST', $peep);
        $form->setValidator($validator);
        $form->add(new Text('name'));
        $form->bind($input);
        $this->assertTrue($form->isValid());
        $this->assertEquals('Tommy Ellis', $peep->name);
    }

    public function testExceptionWhenCheckingIfFormIsValid()
    {
        $this->setExpectedException('Exception');

        $input = array(
            'name' => 'Tommy Ellis'
        );

        $form = new Form('/', 'POST');
        $form->bind($input);

        $form->isValid();
    }

}
