<?php

use Tomahawk\Html\HtmlBuilder;

class HtmlBuilderTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var HtmlBuilder
     */
    protected $htmlBuilder;

    public function setup()
    {
        $this->htmlBuilder = new HtmlBuilder();
    }

    public function testThing()
    {
        $html = $this->htmlBuilder->link('http://google.com', 'A Link');
        $this->assertEquals('<a href="http://google.com">A Link</a>', $html);
    }
}