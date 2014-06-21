<?php

namespace Tomahawk\Validation\Constraints;

use Tomahawk\Validation\Validator;

interface ConstraintInterface
{
    public function validate(Validator $validator, $attribute);

    public function getMessage();
}