<?php

namespace Tomahawk\Forms\Element;

abstract class Element
{
    /**
     * Name of element
     *
     * @var
     */
    protected $name;

    /**
     * Value of element
     *
     * @var
     */
    protected $value;

    public function __construct($name, $value = null)
    {
        $this->name = $name;
        $this->value = $value;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param $value
     * @return $this
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }

    /**
     * @param array $attributes
     * @return mixed
     */
    abstract public function render(array $attributes = array());

    protected function entities($value)
    {
        return htmlentities($value, ENT_QUOTES, 'UTF-8', false);
    }

    protected function attributes(array $attributes)
    {
        $html = array();

        // For numeric keys we will assume that the key and the value are the same
        // as this will convert HTML attributes such as "required" to a correct
        // form like required="required" instead of using incorrect numerics.
        foreach ($attributes as $key => $value) {
            $element = $this->attributeElement($key, $value);

            if ( ! is_null($element)) {
                $html[] = $element;
            }
        }

        return count($html) > 0 ? ' '.implode(' ', $html) : '';
    }

    /**
     * Build a single attribute element.
     *
     * @param  string  $key
     * @param  string  $value
     * @return string
     */
    protected function attributeElement($key, $value)
    {
        if (is_numeric($key))
        {
            $key = $value;
        }

        if ( ! is_null($value))
        {
            return $key.'="'.$this->entities($value).'"';
        }
    }
}