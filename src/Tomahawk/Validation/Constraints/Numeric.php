<?php

namespace Tomahawk\Validation\Constraints;

use Tomahawk\Validation\Validator;
use Tomahawk\Validation\Message;

class Numeric extends Constraint
{
    protected $message = 'The value is not a valid number';

    public function validate(Validator $validator, $attribute, $value)
    {
        if (!is_numeric($value))
        {
            if ($trans = $validator->getTranslator())
            {
                $this->setMessage($trans->trans($this->getMessage(), $this->getData()));
            }
            else
            {
                $this->mergeMessageData();
            }

            $validator->addMessage($attribute, new Message($this->getMessage()));
            return false;
        }

        return true;
    }

    public function getData()
    {
        return array();
    }
}