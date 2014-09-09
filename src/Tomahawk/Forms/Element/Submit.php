<?php

namespace Tomahawk\Forms\Element;

class Submit extends Element
{
    public function __construct($name, $value = 'Submit')
    {
        $this->name = $name;
        $this->value = $value;
    }

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

        return sprintf('<input type="submit"%s>', $this->attributes($attributes));
    }
}
