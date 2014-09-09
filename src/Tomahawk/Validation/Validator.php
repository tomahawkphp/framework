<?php

namespace Tomahawk\Validation;

use Tomahawk\Validation\Constraints\ConstraintInterface;
use Symfony\Component\Translation\TranslatorInterface;

class Validator implements ValidatorInterface
{
    /**
     * @var ConstraintInterface array
     */
    protected $constraints = array();

    /**
     * @var
     */
    protected $input;

    /**
     * @var array
     */
    protected $messages = array();

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
        $this->messages = array();
        $this->input = $input;

        /**
         * @var ConstraintInterface $constraint
         */
        foreach ($this->constraints as $field => $constraints)
        {
            foreach ($constraints as $constraint)
            {
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
}
