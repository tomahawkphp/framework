<?php

namespace Tomahawk\Forms\Element;

class Checkbox extends Element
{
    /**
     * @var bool
     */
    protected $checked;

    public function __construct($name, $value = null, $checked = false)
    {
        $this->name = $name;
        $this->value = $value;
        $this->checked = $checked;
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

        if ($this->checked)
        {
            $attributes['checked'] = 'checked';
        }

        return sprintf('<input type="checkbox"%s>', $this->attributes($attributes));
    }
}
