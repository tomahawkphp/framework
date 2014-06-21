<?php

namespace Tomahawk\Validation\Constraints;

use Tomahawk\Validation\Validator;

class Required extends Constraint
{
    protected $message = 'The field is required';

    public function validate(Validator $validator, $attribute)
    {
        if (is_null($attribute) or $attribute === '')
        {
            return false;
        }

        return true;
    }

}