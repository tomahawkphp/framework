<?php

namespace Tomahawk\Validation\Constraints;

use Tomahawk\Validation\Validator;

class MinLength extends Constraint
{
    protected $message = 'The min length is {{ min_length }}';

    protected $min_length = 1;

    public function validate(Validator $validator, $attribute)
    {
        if (strlen($attribute) <= $this->min_length)
        {
            return false;
        }

        return true;
    }

    public function getData()
    {
        return array(
            '{{ min_length }}' => $this->min_length
        );
    }
}