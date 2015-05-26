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
use Tomahawk\Validation\Message;

class Integer extends Constraint
{
    protected $message = 'The value is not a valid integer number';

    public function validate(Validator $validator, $attribute, $value)
    {
        if (!filter_var($value, FILTER_VALIDATE_INT)) {
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
