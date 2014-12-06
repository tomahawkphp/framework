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
