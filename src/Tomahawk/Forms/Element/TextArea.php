<?php

namespace Tomahawk\Forms\Element;

class TextArea extends Element
{
    public function __construct($name, $value = null)
    {
        $this->name = $name;
    }

    /**
     * @param array $attributes
     * @return mixed
     */
    public function render(array $attributes = array())
    {
        $current_attributes = array(
            'name'  => $this->getName(),
        );

        $attributes = array_merge($current_attributes, $attributes);

        return sprintf('<textarea%s>%s</textarea', $this->attributes($attributes), $this->entities($this->getValue()));
    }
}