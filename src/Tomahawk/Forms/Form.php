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
use Tomahawk\Forms\Exception\InvalidDataTransformerException;
use Tomahawk\Validation\ValidatorInterface;
use Tomahawk\Forms\Element\CheckableInterface;

class Form extends AttributeBuilder implements FormInterface
{
    /**
     * @var Element[]
     */
    protected $elements;

    /**
     * @var
     */
    protected $model;

    /**
     * @var array
     */
    protected $input = [];

    /**
     * @var string
     */
    protected $url;

    /**
     * @var string
     */
    protected $method;

    /**
     * @var array
     */
    protected $attributes = [];

    /**
     * @var ValidatorInterface
     */
    protected $validator;

    /**
     * @var DataTransformerInterface[]
     */
    protected $transformers = [];

    public function __construct($url, $method = 'POST', array $attributes = [])
    {
        $this->url = $url;
        $this->method = $method;
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
        $attributes = [
            'method' => $this->method,
            'action' => $this->url
        ];

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
    public function render($name, array $attributes = [])
    {
        $element = $this->elements[$name];

        $value = $this->getValue($name);

        $value = $this->applyTransform($name, $value);

        // Only set the value of an Element that isn't checkable
        if (($element instanceof CheckableInterface) && $value) {
            $element->setChecked($element->getValue() == $this->getValue($name));
        }
        else if ($value) {
            $element->setValue($value);
        }

        return $element->render($attributes);
    }

    /**
     * @param $input
     * @return $this
     */
    public function handleInput($input)
    {
        $this->setInput($input);

        if ($this->model) {

            foreach ($this->input as $name => $value) {

                $value = $this->applyReverseTransform($name, $value);

                $this->setValue($name, $value);
            }
        }

        return $this;
    }

    /**
     * @return bool
     */
    public function isValid()
    {
        if ( ! $this->getValidator()) {
            return true;
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
     * @param string $url
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
    public function getInput()
    {
        return $this->input;
    }

    /**
     * @param array $input
     * @return $this
     */
    public function setInput($input)
    {
        $this->input = $input;

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

    /**
     * Add a group of data transformers
     *
     * array (
     *  'date' => DataTransformerInterface
     * )
     *
     * @param array $dataTransformers
     * @return $this
     */
    public function addTransformers(array $dataTransformers)
    {
        foreach ($dataTransformers as $type => $dataTransformer) {

            if ( ! $dataTransformer instanceof DataTransformerInterface) {
                throw new InvalidDataTransformerException();
            }

            $this->addTransformer($type, $dataTransformer);
        }

        return $this;
    }

    /**
     * Add a data transformer
     *
     * @param $type
     * @param DataTransformerInterface $dataTransformer
     * @return $this
     */
    public function addTransformer($type, DataTransformerInterface $dataTransformer)
    {
        $this->transformers[$type] = $dataTransformer;

        return $this;
    }

    /**
     * Get all data tranformers
     *
     * @return DataTransformerInterface[]
     */
    public function getTransformers()
    {
        return $this->transformers;
    }

    protected function getValue($name)
    {
        if (isset($this->input[$name])) {
            return $this->input[$name];
        }
        else if ($this->model && method_exists($this->model, $method = $this->getElementGetMethod($name))) {
            return $this->model->$method();
        }
        else if ($this->model && property_exists($this->model, $name)) {
            return $this->model->$name;
        }

        return null;
    }

    protected function setValue($name, $value)
    {
        if ($this->model && method_exists($this->model, $method = $this->getElementSetMethod($name))) {
            $this->model->$method($value);
        }
        else if ($this->model && property_exists($this->model, $name)) {
            $this->model->$name = $value;
        }

        return $this;
    }

    protected function getElementGetMethod($element)
    {
        $method = ucwords(str_replace(['_', '-'], ' ', $element));

        return 'get' .str_replace(' ', '', $method);
    }

    protected function getElementSetMethod($element)
    {
        $method = ucwords(str_replace(['_', '-'], ' ', $element));

        return 'set' .str_replace(' ', '', $method);
    }

    protected function applyTransform($name, $value)
    {
        if ( ! isset($this->transformers[$name])) {
            return $value;
        }

        return $this->transformers[$name]->transform($value);
    }

    protected function applyReverseTransform($name, $value)
    {
        if ( ! isset($this->transformers[$name])) {
            return $value;
        }

        return $this->transformers[$name]->reverseTransform($value);
    }
}
