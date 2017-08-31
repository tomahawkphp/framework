<?php

use Tomahawk\Templating\TemplateFilenameParser;
use Tomahawk\Templating\TemplateReference;

class TemplateFilenameParserTest extends PHPUnit\Framework\TestCase
{
    /**
     * @var TemplateFilenameParser
     */
    protected $parser;

    protected function setUp()
    {
        $this->parser = new TemplateFilenameParser();
    }

    protected function tearDown()
    {
        $this->parser = null;
    }

    /**
     * @dataProvider getFilenameToTemplateProvider
     */
    public function testParseFromFilename($file, $ref)
    {
        $template = $this->parser->parse($file);

        if ($ref === false) {
            $this->assertFalse($template);
        } else {
            $this->assertEquals($template->getLogicalName(), $ref->getLogicalName());
        }
    }

    public function testParseReference()
    {
        $ref = new TemplateReference('FooBundle', 'Post', 'index', 'php');
        $template = $this->parser->parse($ref);

        $this->assertEquals($ref, $template);
    }

    public function getFilenameToTemplateProvider()
    {
        return array(
            array('/path/to/section/name.engine', new TemplateReference('', '/path/to/section', 'name', 'engine')),
            array('\\path\\to\\section\\name.engine', new TemplateReference('', '/path/to/section', 'name', 'engine')),
            array('name.engine', new TemplateReference('', '', 'name', 'engine')),
            array('name', false),
        );
    }
}
