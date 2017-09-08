<?php

use Tomahawk\Html\HtmlBuilder;

class HtmlBuilderTest extends PHPUnit\Framework\TestCase
{
    /**
     * @var HtmlBuilder
     */
    protected $htmlBuilder;

    public function setup()
    {
        $this->htmlBuilder = new HtmlBuilder();
    }

    public function testBasic()
    {
        $html = $this->htmlBuilder->link('http://google.com', 'A Link');
        $this->assertEquals('<a href="http://google.com">A Link</a>', $html);
    }

    public function testScript()
    {
        $html = $this->htmlBuilder->script('http://example.com/script.js');
        $this->assertEquals('<script src="http://example.com/script.js"></script>', $html);
    }

    public function testStyle()
    {
        $html = $this->htmlBuilder->style('http://example.com/style.css');
        $this->assertEquals('<link rel="stylesheet" type="text/css" href="http://example.com/style.css">', $html);
    }

    public function testNumericAttribute()
    {
        $html = $this->htmlBuilder->link('http://google.com', 'A Link', array('disabled'));
        $this->assertEquals('<a href="http://google.com" disabled="disabled">A Link</a>', $html);
    }

    public function testNullValueAttribute()
    {
        $html = $this->htmlBuilder->link('http://google.com', 'A Link', array('class' => null));
        $this->assertEquals('<a href="http://google.com">A Link</a>', $html);
    }
}
