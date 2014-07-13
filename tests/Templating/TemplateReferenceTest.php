<?php

use Tomahawk\Templating\TemplateReference;

class TemplateReferenceTest extends PHPUnit_Framework_TestCase
{
    public function testGetPathWorksWithNamespacedControllers()
    {
        $reference = new TemplateReference('ThingBlogBundle', 'Admin\Post', 'index', 'twig');

        $this->assertSame(
            '@ThingBlogBundle/Resources/views/Admin/Post/index.twig',
            $reference->getPath()
        );
    }
}