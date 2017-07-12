<?php

namespace Tomahawk\Templating\Tests\Twig\Extension;

use PHPUnit_Framework_TestCase as TestCase;
use Tomahawk\Templating\Twig\Extension\TranslatorExtension;

class TranslatorExtensionTest extends TestCase
{
    public function testCorrectNumberOfFunctionsAreReturned()
    {
        $translatorExtension = new TranslatorExtension($this->getTranslatorMock());

        $this->assertCount(2, $translatorExtension->getFunctions());
    }

    public function testExtensionNameIsReturned()
    {
        $translatorExtension = new TranslatorExtension($this->getTranslatorMock());

        $this->assertEquals('translator', $translatorExtension->getName());
    }

    public function testTransFunction()
    {
        $translatorExtension = new TranslatorExtension($this->getTranslatorMock());

        $this->assertEquals('thing', $translatorExtension->trans('thing'));
    }

    public function testTransChoiceFunction()
    {
        $translatorExtension = new TranslatorExtension($this->getTranslatorMock());

        $this->assertEquals('things', $translatorExtension->transChoice('thing', 10));
    }

    protected function getTranslatorMock()
    {
        $translator = $this->getMock('Symfony\Component\Translation\TranslatorInterface');

        $translator->expects($this->any())
            ->method('trans')
            ->will($this->returnValue('thing'));

        $translator->expects($this->any())
            ->method('transChoice')
            ->will($this->returnValue('things'));

        return $translator;
    }

}
