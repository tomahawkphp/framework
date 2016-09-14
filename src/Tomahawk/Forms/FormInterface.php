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
     * Open the form
     *
     * @return string
     */
    public function open();

    /**
     * Close the form
     *
     * @return string
     */
    public function close();

    /**
     * @param $name
     * @param array $attributes
     * @return mixed
     */
    public function render($name, array $attributes = []);

    /**
     * @param $input
     * @return $this
     */
    public function handleInput($input);

    /**
     * @return bool
     */
    public function isValid();

    /**
     * @return mixed
     */
    public function getUrl();

    /**
     * @param string $url
     * @return $this
     */
    public function setUrl($url);

    /**
     * @return string
     */
    public function getMethod();

    /**
     * @param string $method
     * @return $this
     */
    public function setMethod($method);

    /**
     * @return array
     */
    public function getInput();

    /**
     * @param array $input
     * @return $this
     */
    public function setInput($input);

    /**
     * @return array
     */
    public function getAttributes();

    /**
     * @param array $attributes
     * @return $this
     */
    public function setAttributes(array $attributes);

    /**
     * @return Element[]
     */
    public function getElements();

    /**
     * Add a group of datra transformers
     *
     * array (
     *  'date' => DataTransformerInterface
     * )
     *
     * @param array $dataTransformers
     * @return $this
     */
    public function addTransformers(array $dataTransformers);

    /**
     * Add a data transformer
     *
     * @param $type
     * @param DataTransformerInterface $dataTransformer
     * @return $this
     */
    public function addTransformer($type, DataTransformerInterface $dataTransformer);

    /**
     * Get all data tranformers
     *
     * @return DataTransformerInterface[]
     */
    public function getTransformers();
}