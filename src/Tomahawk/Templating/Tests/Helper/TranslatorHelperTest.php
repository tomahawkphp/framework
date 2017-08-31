<?php

namespace Tomahawk\Routing\Tests;

use PHPUnit\Framework\TestCase;
use Tomahawk\Templating\Helper\TranslatorHelper;
use Symfony\Component\Translation\TranslatorInterface;

class TranslatorHelperTest extends TestCase
{
    public function testHelperCallTransMethod()
    {
        $translator = $this->getTranslator();

        $translator->expects($this->once())
            ->method('trans');

        $helper = new TranslatorHelper($translator);
        $helper->trans('name');
    }

    public function testHelperCallTransChoiceMethod()
    {
        $translator = $this->getTranslator();

        $translator->expects($this->once())
            ->method('transChoice');

        $helper = new TranslatorHelper($translator);
        $helper->transChoice('name', 2);
    }

    public function testGetName()
    {
        $helper = new TranslatorHelper($this->getTranslator());
        $this->assertEquals('translator', $helper->getName());
    }

    protected function getTranslator()
    {
        return $this->createMock('Symfony\Component\Translation\TranslatorInterface');
    }
}
