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

class URL extends Constraint
{
    protected $message = 'The URL is invalid';

    public function validate(Validator $validator, $attribute, $value)
    {
        if (false === filter_var($value, FILTER_VALIDATE_URL)) {
            $this->fail($attribute, $validator);
            return false;
        }

        return true;
    }
}