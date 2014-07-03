<?php

namespace Tomahawk\Validation\Constraints;

use Tomahawk\Validation\Validator;
use Tomahawk\Validation\Message;

class Identical extends Constraint
{
    protected $message = 'The field is doesn\'t match with %with%';

    protected $with;

    protected $with_name = null;

    public function validate(Validator $validator, $attribute, $value)
    {
        if ($validator->getInput($this->with) !== $value)
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
            '%with%' => $this->with_name ?: $this->with
        );
    }

}