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
