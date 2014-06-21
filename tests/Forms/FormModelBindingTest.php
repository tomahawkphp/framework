<?php

use Tomahawk\Forms\Form;

use Tomahawk\Forms\Element\Text;
use Tomahawk\Forms\Element\Password;
use Tomahawk\Forms\Element\Select;
use Tomahawk\Forms\Element\Hidden;
use Tomahawk\Validation\Validator;
use Tomahawk\Validation\Constraints\Required;

class FormModelBindingTest extends PHPUnit_Framework_TestCase
{

    public function testBind()
    {
        $peep = new PeepStub();

        $peep->setName('Tom Ellis');
        $peep->setAge(27);

        $form = new Form($peep);

        $form->add(new Text('name'));

        $html = $form->render('name');

        $this->assertEquals('<input type="text" name="name" value="Tom Ellis">', $html);

    }

    public function testBindWithFailedValidation()
    {
        $input = array();

        $validator = new Validator();
        $validator->add('name', array(
            new Required()
        ));

        $peep = new PeepStub();

        $peep->setName('Tom Ellis');
        $peep->setAge(27);

        $form = new Form($peep);
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

        $peep = new PeepStub();

        $peep->setName('Tom Ellis');
        $peep->setAge(27);

        $form = new Form($peep);
        $form->setValidator($validator);
        $form->add(new Text('name'));
        $form->bind($input);
        $this->assertTrue($form->isValid());
        $this->assertEquals('Tommy Ellis', $peep->getName());
    }

}

class PeepStub
{
    public $name;

    public $age;

    /**
     * @param mixed $age
     */
    public function setAge($age)
    {
        $this->age = $age;
    }

    /**
     * @return mixed
     */
    public function getAge()
    {
        return $this->age;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

}