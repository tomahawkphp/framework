<?php

/*
 * This file is part of the TomahawkPHP package.
 *
 * (c) Tom Ellis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tomahawk\Validation\Constraints;

use Tomahawk\Validation\Validator;
use Tomahawk\Validation\Message;

class StringLength extends Constraint
{
    /**
     * @var string
     */
    protected $min_message = 'The minimum length is %min_length%';

    /**
     * @var string
     */
    protected $max_message = 'The maximum length is %max_length%';

    /**
     * @var int
     */
    protected $min_length = 100;

    /**
     * @var int
     */
    protected $max_length = 100;

    public function validate(Validator $validator, $attribute, $value)
    {
        $passed = true;

        if (strlen($value) > $this->max_length) {
            if ($trans = $validator->getTranslator()) {
                $this->setMaxMessage($trans->trans($this->getMaxMessage(), $this->getData()));
            }
            else {
                $this->mergeMaxMessageData();
            }
            $validator->addMessage($attribute, new Message($this->getMaxMessage(), $this->getData()));
            $passed = false;
        }

        if (strlen($value) < $this->min_length) {
            if ($trans = $validator->getTranslator()) {
                $this->setMinMessage($trans->trans($this->getMinMessage(), $this->getData()));
            }
            else {
                $this->mergeMinMessageData();
            }
            $validator->addMessage($attribute, new Message($this->getMinMessage(), $this->getData()));
            $passed = false;
        }

        return $passed;
    }

    public function getData()
    {
        return array(
            '%min_length%' => $this->min_length,
            '%max_length%' => $this->max_length
        );
    }

    public function getMinMessage()
    {
        return $this->min_message;
    }

    public function setMinMessage($message)
    {
        $this->min_message = $message;
        return $this;
    }

    public function getMaxMessage()
    {
        return $this->max_message;
    }

    public function setMaxMessage($message)
    {
        $this->max_message = $message;
        return $this;
    }

    protected function mergeMinMessageData()
    {

        foreach ($this->getData() as $key => $value) {
            $this->min_message = str_replace($key, $value, $this->min_message);
        }

        return $this;
    }

    protected function mergeMaxMessageData()
    {

        foreach ($this->getData() as $key => $value) {
            $this->max_message = str_replace($key, $value, $this->max_message);
        }

        return $this;
    }
}
