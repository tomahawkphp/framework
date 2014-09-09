<?php

namespace Tomahawk\Forms\Element;

class Select extends Element
{
    protected $list = array();

    public function __construct($name, array $list = array(), $selected = null)
    {
        $this->name = $name;
        $this->list = $list;
        $this->value = $selected;
    }

    /**
     * @param array $attributes
     * @return mixed
     */
    public function render(array $attributes = array())
    {
        $list = array();

        foreach ($this->list as $value => $display)
        {
            $list[] = $this->getSelectOption($display, $value, $this->value);
        }

        return sprintf('<select name="%s"%s>%s</select>', $this->name, $this->attributes($attributes), implode('', $list));
    }

    /**
     * Get the select option for the given value.
     *
     * @param  string  $display
     * @param  string  $value
     * @param  string  $selected
     * @return string
     */
    public function getSelectOption($display, $value, $selected)
    {
        if (is_array($display))
        {
            return $this->optionGroup($display, $value, $selected);
        }

        return $this->option($display, $value, $selected);
    }

    /**
     * Create an option group form element.
     *
     * @param  array   $list
     * @param  string  $label
     * @param  string  $selected
     * @return string
     */
    protected function optionGroup($list, $label, $selected)
    {
        $html = array();

        foreach ($list as $value => $display)
        {
            $html[] = $this->option($display, $value, $selected);
        }

        return sprintf('<optgroup label="%s">%s</optgroup>', $this->entities($label), implode('', $html));
    }

    /**
     * Create a select element option.
     *
     * @param  string  $display
     * @param  string  $value
     * @param  string  $selected
     * @return string
     */
    protected function option($display, $value, $selected)
    {
        $selected = $this->getSelectedValue($value, $selected);

        $options = array(
            'value'    => $this->entities($value),
            'selected' => $selected
        );

        return sprintf('<option%s>%s</option>', $this->attributes($options), $this->entities($display));
    }

    /**
     * Determine if the value is selected.
     *
     * @param  string  $value
     * @param  string  $selected
     * @return string
     */
    protected function getSelectedValue($value, $selected)
    {
        if (is_array($selected))
        {
            return in_array($value, $selected) ? 'selected' : null;
        }

        return ((string) $value == (string) $selected) ? 'selected' : null;
    }
}
