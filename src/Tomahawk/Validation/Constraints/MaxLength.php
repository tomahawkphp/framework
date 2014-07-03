<?php

namespace Tomahawk\Validation\Constraints;

use Tomahawk\Validation\Validator;
use Tomahawk\Validation\Message;

class MaxLength extends Constraint
{
    protected $message = 'The maximum length is %max_length%';

    protected $max_length = 100;

    public function validate(Validator $validator, $attribute, $value)
    {
        if (strlen($value) > $this->max_length)
        {
            if ($trans = $validator->getTranslator())
            {
                $this->setMessage($trans->trans($this->getMessage(), $this->getData()));
            }
            else
            {
                $this->mergeMessageData();
            }

            $validator->addMessage($attribute, new Message($this->getMessage(), $this->getData()));
            return false;
        }

        return true;
    }

    public function getData()
    {
        return array(
            '%max_length%' => $this->max_length
        );
    }
}