<?php

namespace Tomahawk\Validation\Constraints;

use Tomahawk\Validation\Validator;

class MaxLength extends Constraint
{
    protected $message = 'The max length is {{ max_length }}';

    protected $max_length = 100;

    public function validate(Validator $validator, $attribute)
    {
        if (strlen($attribute) >= $this->max_length)
        {
            return false;
        }

        return true;
    }

    public function getData()
    {
        return array(
            '{{ max_length }}' => $this->max_length
        );
    }
}