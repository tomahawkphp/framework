<?php

/*
 * This file is part of the TomahawkPHP package.
 *
 * (c) Tom Ellis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tomahawk\Forms;

use Tomahawk\Forms\Element\Element;
use Tomahawk\Validation\ValidatorInterface;
use Tomahawk\Forms\Element\CheckableInterface;

class Form extends AttributeBuilder implements FormInterface
{
    /**
     * @var \Tomahawk\Forms\Element\Element[]
     */
    protected $elements;

    /**
     * @var
     */
    protected $model;

    /**
     * @var array
     */
    protected $input = array();

    protected $url;

    protected $method;

    protected $oldInput = array();

    protected $attributes = array();

    /**
     * @var ValidatorInterface
     */
    protected $validator;

    public function __construct($url, $method = 'POST', $model = null, array $oldInput = array(), array $attributes = array())
    {
        $this->url = $url;
        $this->method = $method;
        $this->model = $model;
        $this->oldInput = $oldInput;
        $this->attributes = $attributes;
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
     * Open the form
     *
     * @return string
     */
    public function open()
    {
        $attributes = array(
            'method' => $this->method,
            'action' => $this->url
        );

        $attributes = array_merge($attributes, $this->attributes);

        return sprintf('<form%s>', $this->attributes($attributes));
    }

    /**
     * Close the form
     *
     * @return string
     */
    public function close()
    {
        return '</form>';
    }

    /**
     * @param $name
     * @param array $attributes
     * @return mixed
     */
    public function render($name, array $attributes = array())
    {
        $element = $this->elements[$name];

        // Only set the value of an Element that isn't checkable
        if (!($element instanceof CheckableInterface) && ($value = $this->getValue($name))) {
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
        if (!$this->model) {
            return $this;
        }

        $this->input = $input;

        foreach ($this->input as $name => $value) {
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
        if (!$this->getValidator()) {
            throw new \Exception('Validator not present on Form');
        }

        return $this->getValidator()->validate($this->input);
    }

    /**
     * @return mixed
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param mixed $url
     * @return $this
     */
    public function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @param string $method
     * @return $this
     */
    public function setMethod($method)
    {
        $this->method = $method;
        return $this;
    }

    /**
     * @return array
     */
    public function getOldInput()
    {
        return $this->oldInput;
    }

    /**
     * @param array $oldInput
     * @return $this
     */
    public function setOldInput(array $oldInput)
    {
        $this->oldInput = $oldInput;
        return $this;
    }

    /**
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * @param array $attributes
     * @return $this
     */
    public function setAttributes(array $attributes)
    {
        $this->attributes = $attributes;
        return $this;
    }

    /**
     * @return Element[]
     */
    public function getElements()
    {
        return $this->elements;
    }

    protected function getValue($name)
    {
        $method = $this->getElementGetMethod($name);

        if (isset($this->oldInput[$name])) {
            return $this->oldInput[$name];
        }
        else if ($this->model && method_exists($this->model, $method)) {
            return $this->model->$method();
        }
        else if ($this->model && property_exists($this->model, $name)) {
            return $this->model->$name;
        }

        return null;
    }

    protected function setValue($name, $value)
    {
        $method = $this->getElementSetMethod($name);

        if ($this->model && method_exists($this->model, $method)) {
            $this->model->$method($value);
        }
        else if ($this->model && property_exists($this->model, $name)) {
            $this->model->$name = $value;
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
