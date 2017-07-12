<?php

namespace Tomahawk\Forms\Tests;

use Tomahawk\Forms\Test\Model;
use PHPUnit_Framework_TestCase as TestCase;
use Tomahawk\Forms\Form;
use Tomahawk\Forms\Element\Text;
use Tomahawk\Forms\DataTransformerInterface;

class FormDataTransformerTest extends TestCase
{
    /**
     * @expectedException \Tomahawk\Forms\Exception\InvalidDataTransformerException
     */
    public function testAddingInvalidTransformerThrowsException()
    {
        $form = new Form('/');
        $form->addTransformers(array(
            'foo' => 'invalidTranformer'
        ));
    }

    public function testAddingValidTransformers()
    {
        $transformer1 = $this->getTransformer();

        $transformer2 = $this->getTransformer();

        $transformers = array(
            'date' => $transformer1,
            'id'   => $transformer2,
        );

        $form = new Form('/');
        $form->addTransformers($transformers);

        $this->assertEquals($transformers, $form->getTransformers());
    }

    public function testFormRunsTransformers()
    {
        $transformer1 = $this->getTransformer();

        $transformer1->expects($this->exactly(1))
            ->method('transform');

        $transformer1->expects($this->never())
            ->method('reverseTransform');

        $transformer2 = $this->getTransformer();

        $transformer2->expects($this->exactly(1))
            ->method('transform');

        $transformer2->expects($this->never())
            ->method('reverseTransform');

        $form = new Form('/');
        $form->addTransformers(array(
            'date' => $transformer1,
            'id'   => $transformer2,
        ));

        $form->add(new Text('id'));
        $form->add(new Text('date'));

        $form->render('id');
        $form->render('date');

        $form->handleInput(array(
            'id' => 1,
            'date' => '2016-01-01',
        ));

    }

    public function testFormRunsTransformersWithModel()
    {
        $peep = new Model();

        $transformer1 = $this->getTransformer();

        $transformer1->expects($this->exactly(1))
            ->method('transform');

        $transformer1->expects($this->exactly(1))
            ->method('reverseTransform');

        $transformer2 = $this->getTransformer();

        $transformer2->expects($this->exactly(1))
            ->method('transform');

        $transformer2->expects($this->exactly(1))
            ->method('reverseTransform');

        $form = new Form('/');

        $form->setModel($peep);

        $form->addTransformers(array(
            'date' => $transformer1,
            'id'   => $transformer2,
        ));

        $form->add(new Text('id'));
        $form->add(new Text('date'));

        $form->render('id');
        $form->render('date');

        $form->handleInput(array(
            'id' => 1,
            'date' => '2016-01-01',
        ));

    }

    protected function getTransformer()
    {
        return $this->getMock(DataTransformerInterface::class);
    }
}
