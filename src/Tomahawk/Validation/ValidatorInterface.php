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

use Tomahawk\Validation\Constraints\ConstraintInterface;

interface ValidatorInterface
{

    /**
     * Add a constraint to the validator
     *
     * @param $name
     * @param ConstraintInterface|ConstraintInterface[] $constraints
     * @return $this
     */
    public function add($name, $constraints);

    /**
     * @param $input
     * @return boolean
     */
    public function validate($input);

    /**
     * Add message
     *
     * @param $field
     * @param Message $message
     * @return $this
     */
    public function addMessage($field, Message $message);

    /**
     * Get All Validation Messages
     *
     * @return array
     */
    public function getMessages();

    /**
     * Get All Validation Messages for given field
     *
     * @param $field
     * @return array
     */
    public function getMessagesFor($field);
    /**
     * @param $name
     * @return null
     */
    public function getInput($name);

    /**
     * @param \Symfony\Component\Translation\TranslatorInterface $translator
     */
    public function setTranslator($translator);

    /**
     * @return \Symfony\Component\Translation\TranslatorInterface
     */
    public function getTranslator();
}
