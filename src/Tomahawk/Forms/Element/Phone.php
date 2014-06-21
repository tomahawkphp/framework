<?php

namespace Tomahawk\Forms\Element;

class Phone extends Element
{
    /**
     * @param array $attributes
     * @return mixed
     */
    public function render(array $attributes = array())
    {
        $current_attributes = array(
            'name'  => $this->getName(),
            'value' => $this->getValue()
        );

        $attributes = array_merge($current_attributes, $attributes);

        return sprintf('<input type="tel"%s>', $this->attributes($attributes));
    }
}