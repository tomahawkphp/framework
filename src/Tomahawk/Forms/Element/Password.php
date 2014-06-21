<?php

namespace Tomahawk\Forms\Element;

class Password extends Element
{
    public function __construct($name)
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

        return sprintf('<input type="password"%s>', $this->attributes($attributes));
    }
}