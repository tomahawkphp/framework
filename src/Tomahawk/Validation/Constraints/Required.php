<?php

namespace Tomahawk\Validation\Constraints;

use Tomahawk\Validation\Validator;
use Tomahawk\Validation\Message;

class Required extends Constraint
{
    protected $message = 'The field is required';

    public function validate(Validator $validator, $attribute, $value)
    {
        if (is_null($value) || (is_string($value) and trim($value) === '') || (is_array($validator) && !$value))
        {
            if ($trans = $validator->getTranslator())
            {
                $this->setMessage($trans->trans($this->getMessage(), $this->getData()));
            }
            else
            {
                $this->mergeMessageData();
            }

            $validator->addMessage($attribute, new Message($this->getMessage(), array()));

            return false;
        }

        return true;
    }

}