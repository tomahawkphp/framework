<?php

namespace Tomahawk\Validation\Constraints;

use Tomahawk\Validation\Validator;

interface ConstraintInterface
{
    /**
     * @param Validator $validator
     * @param $attribute
     * @param $value
     * @return bool
     */
    public function validate(Validator $validator, $attribute, $value);

    /**
     * @return string
     */
    public function getMessage();

    /**
     * @return array
     */
    public function getData();

    /**
     * @return bool
     */
    public function shouldSkipOnNoValue();
}
