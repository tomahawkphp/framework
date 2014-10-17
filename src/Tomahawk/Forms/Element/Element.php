<?php

namespace Tomahawk\Forms\Element;

use Tomahawk\Forms\AttributeBuilder;

abstract class Element extends AttributeBuilder
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

}
