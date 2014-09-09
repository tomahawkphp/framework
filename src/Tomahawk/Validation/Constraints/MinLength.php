<?php

namespace Tomahawk\Validation\Constraints;

use Tomahawk\Validation\Message;
use Tomahawk\Validation\Validator;

class MinLength extends Constraint
{
    protected $message = 'The minimum length is %min_length%';

    protected $min_length = 1;

    public function validate(Validator $validator, $attribute, $value)
    {
        if (strlen($value) < $this->min_length)
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
            '%min_length%' => $this->min_length
        );
    }
}
