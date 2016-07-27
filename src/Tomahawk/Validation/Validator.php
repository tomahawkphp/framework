<?php

/*
 * This file is part of the TomahawkPHP package.
 *
 * (c) Tom Ellis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tomahawk\Validation;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Tomahawk\Validation\Constraints\ConstraintInterface;
use Symfony\Component\Translation\TranslatorInterface;

class Validator implements ValidatorInterface
{
    /**
     * @var ConstraintInterface array
     */
    protected $constraints = [];

    /**
     * @var
     */
    protected $input;

    /**
     * @var array
     */
    protected $messages = [];

    /**
     * @var TranslatorInterface $translator
     */
    protected $translator;

    /**
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator = null)
    {
        $this->translator = $translator;
    }

    /**
     * Add a constraint to the validator
     *
     * @param $name
     * @param ConstraintInterface|ConstraintInterface[] $constraints
     * @return $this
     */
    public function add($name, $constraints)
    {
        if (!isset($this->constraints[$name])) {
            $this->constraints[$name] = [];
        }

        if (!is_array($constraints)) {
            $constraints = array($constraints);
        }

        foreach ($constraints as $constraint) {
            $this->constraints[$name][] = $constraint;
        }

        return $this;
    }

    /**
     * Validate given input
     *
     * @param $input
     * @return bool
     */
    public function validate($input)
    {
        $this->messages = [];
        $this->input = $input;

        /**
         * @var ConstraintInterface $constraint
         */
        foreach ($this->constraints as $field => $constraints) {
            foreach ($constraints as $constraint) {

                // If value is empty and rule needs a value we can skip
                // checking the field
                if ($constraint->shouldSkipOnNoValue() && $this->isValueEmpty($this->getInput($field))) {
                    continue;
                }

                $constraint->validate($this, $field, $this->getInput($field));
            }

        }

        return count($this->messages) === 0;
    }

    /**
     * Add message
     *
     * @param $field
     * @param Message $message
     * @return $this
     */
    public function addMessage($field, Message $message)
    {
        if (!isset($this->messages[$field])) {
            $this->messages[$field] = [];
        }

        $this->messages[$field][] = $message;
        return $this;
    }

    /**
     * Get All Validation Messages
     *
     * @return array
     */
    public function getMessages()
    {
        return $this->messages;
    }

    /**
     * Get All Validation Messages for given field
     *
     * @param $field
     * @return array
     */
    public function getMessagesFor($field)
    {
        return isset($this->messages[$field]) ? $this->messages[$field] : array();
    }

    /**
     * @param $name
     * @return null
     */
    public function getInput($name)
    {
        return isset($this->input[$name]) ? $this->input[$name] : null;
    }

    /**
     * @param \Symfony\Component\Translation\TranslatorInterface $translator
     */
    public function setTranslator($translator)
    {
        $this->translator = $translator;
    }

    /**
     * @return \Symfony\Component\Translation\TranslatorInterface
     */
    public function getTranslator()
    {
        return $this->translator;
    }

    /**
     * @param $value
     * @return bool
     */
    public function isValueEmpty($value)
    {
        $empty = false;

        // $_FILES
        if ($value instanceof UploadedFile && !$value->isValid()) {
            $empty = true;
        }
        // Null
        else if (is_null($value)) {
            $empty = true;
        }
        // String
        else if ((is_string($value) && trim($value) === '')) {
            $empty = true;
        }
        // Array
        else if((is_array($value) && !$value)) {
            $empty = true;
        }

        return $empty;
    }
}
