<?php

/*
 * This file is part of the TomahawkPHP package.
 *
 * (c) Tom Ellis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tomahawk\Validation\Constraints;

use Tomahawk\Validation\Validator;

class Numeric extends Constraint
{
    protected $message = 'The value is not a valid number';

    public function validate(Validator $validator, $attribute, $value)
    {
        if (!is_numeric($value)) {
            $this->fail($attribute, $validator);
            return false;
        }

        return true;
    }

    public function getData()
    {
        return array();
    }
}
