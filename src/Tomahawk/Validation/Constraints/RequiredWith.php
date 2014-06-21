<?php

namespace Tomahawk\Validation\Constraints;

use Tomahawk\Validation\Validator;

class RequiredWith extends Constraint
{
    protected $message = 'The field is required with {{ with }}';

    protected $with;

    protected $with_name = null;

    public function validate(Validator $validator, $attribute)
    {
        if (is_null($validator->getInput($this->with)))
        {
            return true;
        }

        if (is_null($attribute) or $attribute === '')
        {
            return false;
        }

        return true;
    }

    public function getData()
    {
        return array(
            '{{ with }}' => $this->with_name ?: $this->with
        );
    }

}