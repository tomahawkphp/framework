<?php

namespace Tomahawk\Forms;

use Tomahawk\Forms\Element\Element;
use Tomahawk\Validation\ValidatorInterface;

class Form implements FormInterface
{
    /**
     * @var \Tomahawk\Forms\Element\Element[]
     */
    protected $elements;

    /**
     * @var
     */
    protected $model;

    protected $input = array();

    /**
     * @var ValidatorInterface
     */
    protected $validator;

    public function __construct($model = null)
    {
        $this->model = $model;
    }

    /**
     * @param ValidatorInterface $validator
     */
    public function setValidator(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * @return ValidatorInterface
     */
    public function getValidator()
    {
        return $this->validator;
    }

    /**
     * @param mixed $model
     * @return $this
     */
    public function setModel($model)
    {
        $this->model = $model;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @param Element $element
     * @return $this
     */
    public function add(Element $element)
    {
        $this->elements[$element->getName()] = $element;
        return $this;
    }

    /**
     * @param $name
     * @param array $attributes
     * @return mixed
     */
    public function render($name, array $attributes = array())
    {
        $element = $this->elements[$name];
        if ($value = $this->getValue($name))
        {
            $element->setValue($value);
        }
        return $element->render($attributes);
    }

    /**
     * @param $input
     * @return $this
     */
    public function bind($input)
    {
        $this->input = $input;

        if (!$this->model)
        {
            return $this;
        }

        foreach ($this->input as $name => $value)
        {
            $this->setValue($name, $value);
        }

        return $this;
    }

    /**
     * @return bool
     * @throws \Exception
     */
    public function isValid()
    {
        if (!$this->getValidator())
        {
            throw new \Exception('Validator not present on Form');
        }

        return $this->getValidator()->validate($this->input);
    }

    /**
     * @return Element[]
     */
    public function getElements()
    {
        return $this->elements;
    }

    protected function getValue($element)
    {
        $method = $this->getElementGetMethod($element);

        if ($this->model && method_exists($this->model, $method))
        {
            return $this->model->$method();
        }
        else if ($this->model && property_exists($this->model, $element))
        {
            return $this->model->$element;
        }

        return null;
    }

    protected function setValue($element, $value)
    {
        $method = $this->getElementSetMethod($element);

        if ($this->model && method_exists($this->model, $method))
        {
            $this->model->$method($value);
        }
        else if ($this->model && property_exists($this->model, $element))
        {
            $this->model->$element = $value;
        }

        return $this;
    }

    protected function getElementGetMethod($element)
    {
        $method = ucwords(str_replace(array('_', '-'), ' ', $element));

        return 'get' .str_replace(' ', '', $method);
    }

    protected function getElementSetMethod($element)
    {
        $method = ucwords(str_replace(array('_', '-'), ' ', $element));

        return 'set' .str_replace(' ', '', $method);
    }
}