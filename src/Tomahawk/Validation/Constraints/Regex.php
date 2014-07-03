<?php

namespace Tomahawk\Validation\Constraints;

use Tomahawk\Validation\Validator;
use Tomahawk\Validation\Message;

class Regex extends Constraint
{
    protected $message = 'The field is not in the correct format';

    protected $expression;

    public function validate(Validator $validator, $attribute, $value)
    {
        if (!preg_match($this->expression, $value))
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

}