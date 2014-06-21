<?php

namespace Tomahawk\Validation;

use Tomahawk\Validation\Constraints\ConstraintInterface;

class Validator implements ValidatorInterface
{
    /**
     * @var ConstraintInterface array
     */
    protected $constraints = array();

    protected $input;

    protected $messages = array();

    /**
     * Add a constraint to the validator
     *
     * @param $name
     * @param ConstraintInterface|ConstraintInterface[] $constraints
     * @return $this
     */
    public function add($name, $constraints)
    {
        if (!isset($this->constraints[$name]))
        {
            $this->constraints[$name] = array();
        }

        if (!is_array($constraints))
        {
            $constraints = array($constraints);
        }

        foreach ($constraints as $constraint)
        {
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
        $this->input = $input;

        /**
         * @var ConstraintInterface $constraint
         */
        foreach ($this->constraints as $field => $constraints)
        {
            foreach ($constraints as $constraint)
            {
                if (!$constraint->validate($this, $this->getInput($field)))
                {
                    $this->addMessage($field, new Message($constraint->getMessage(), $constraint->getData()));
                }
            }

        }

        return count($this->messages) === 0;
    }

    /**
     * Add message
     *
     * @param $field
     * @param $message
     * @return $this
     */
    public function addMessage($field, $message)
    {
        if (!isset($this->messages[$field]))
        {
            $this->messages[$field] = array();
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

}