<?php

/*
 * This file is part of the TomahawkPHP package.
 *
 * (c) Tom Ellis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tomahawk\Forms\Element;

class Select extends Element
{
    /**
     * @var array
     */
    protected $list = array();

    public function __construct($name, array $list = array(), $selected = null)
    {
        parent::__construct($name, $selected);

        $this->list = $list;
    }

    /**
     * @param array $attributes
     * @return mixed
     */
    public function render(array $attributes = array())
    {
        $list = array();

        $multiple = array_key_exists('multiple', $attributes);

        foreach ($this->list as $value => $display) {
            $list[] = $this->getSelectOption($display, $value, $this->value, $multiple);
        }

        return sprintf('<select name="%s"%s>%s</select>', $this->name, $this->attributes($attributes), implode('', $list));
    }

    /**
     * Get the select option for the given value.
     *
     * @param  string $display
     * @param  string $value
     * @param  string $selected
     * @param bool $multiple
     * @return string
     */
    public function getSelectOption($display, $value, $selected, $multiple = false)
    {
        if (is_array($display)) {
            return $this->optionGroup($display, $value, $selected, $multiple);
        }

        return $this->option($display, $value, $selected, $multiple);
    }

    /**
     * Create an option group form element.
     *
     * @param  array $list
     * @param  string $label
     * @param  string $selected
     * @param bool $multiple
     * @return string
     */
    protected function optionGroup($list, $label, $selected, $multiple = false)
    {
        $html = array();

        foreach ($list as $value => $display) {
            $html[] = $this->option($display, $value, $selected, $multiple);
        }

        return sprintf('<optgroup label="%s">%s</optgroup>', $this->entities($label), implode('', $html));
    }

    /**
     * Create a select element option.
     *
     * @param  string $display
     * @param  string $value
     * @param  string $selected
     * @param bool $multiple
     * @return string
     */
    protected function option($display, $value, $selected, $multiple = false)
    {
        $selected = $this->getSelectedValue($value, $selected, $multiple);

        $options = array(
            'value'    => $this->entities($value),
            'selected' => $selected
        );

        return sprintf('<option%s>%s</option>', $this->attributes($options), $this->entities($display));
    }

    /**
     * Determine if the value is selected.
     *
     * @param  string $value
     * @param  string $selected
     * @param bool $multiple
     * @return string
     */
    protected function getSelectedValue($value, $selected, $multiple = false)
    {
        if (is_array($selected)) {
            return in_array($value, $selected) ? 'selected' : null;
        }

        return ((string) $value == (string) $selected) ? 'selected' : null;
    }
}
