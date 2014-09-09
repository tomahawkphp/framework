<?php

namespace Tomahawk\Forms;

use Tomahawk\Forms\Element\Element;
use Tomahawk\Validation\ValidatorInterface;

interface FormInterface
{
    /**
     * @param ValidatorInterface $validator
     */
    public function setValidator(ValidatorInterface $validator);

    /**
     * @return ValidatorInterface
     */
    public function getValidator();
    /**
     * @param mixed $model
     * @return $this
     */
    public function setModel($model);

    /**
     * @return mixed
     */
    public function getModel();
    /**
     * @param Element $element
     * @return $this
     */
    public function add(Element $element);

    /**
     * @param $name
     * @param array $attributes
     * @return mixed
     */
    public function render($name, array $attributes = array());

    /**
     * @return boolean
     */
    public function isValid();

    /**
     * @return Element[]
     */
    public function getElements();
}
