<?php

namespace Tomahawk\Validation\Constraints;

use Tomahawk\Validation\Validator;
use Tomahawk\Validation\Message;

class RequiredWith extends Constraint
{
    protected $message = 'The field is required with %with%';

    protected $with;

    protected $with_name = null;

    public function validate(Validator $validator, $attribute, $value)
    {
        $withValue = $validator->getInput($this->with);

        if ((strlen(trim($withValue)) > 0) && !trim($value))
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
