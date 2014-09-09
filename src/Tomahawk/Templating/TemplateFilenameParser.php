<?php

namespace Tomahawk\Templating;

use Symfony\Component\Templating\TemplateNameParserInterface;
use Symfony\Component\Templating\TemplateReferenceInterface;

class TemplateFilenameParser implements TemplateNameParserInterface
{
    /**
     * {@inheritdoc}
     */
    public function parse($name)
    {
        if ($name instanceof TemplateReferenceInterface) {
            return $name;
        }

        $parts = explode('/', strtr($name, '\\', '/'));

        $elements = explode('.', array_pop($parts));
        if (2 > count($elements)) {
            return false;
        }
        $engine = array_pop($elements);

        return new TemplateReference('', implode('/', $parts), implode('.', $elements), $engine);
    }
}
