<?php

namespace Tomahawk\Forms\Element;

class Number extends Element
{
    /**
     * @param array $attributes
     * @return mixed
     */
    public function render(array $attributes = array())
    {
        $current_attributes = array(
            'name'  => $this->getName(),
            'value' => $this->getValue(),
            'pattern' => '[0-9]*'
        );

        $attributes = array_merge($current_attributes, $attributes);

        return sprintf('<input type="text"%s>', $this->attributes($attributes));
    }
}