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

class Regex extends Constraint
{
    protected $message = 'The field is not in the correct format';
    protected $expression;

    public function validate(Validator $validator, $attribute, $value)
    {
        if (!preg_match($this->expression, $value)) {
            $this->fail($attribute, $validator);
            return false;
        }

        return true;
    }

}
