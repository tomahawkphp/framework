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

class MinLength extends Constraint
{
    protected $message = 'The minimum length is %min_length%';

    protected $min_length = 1;

    public function validate(Validator $validator, $attribute, $value)
    {
        if (strlen($value) < $this->min_length) {

            $this->fail($attribute, $validator);

            return false;
        }

        return true;
    }

    public function getData()
    {
        return array(
            '%min_length%' => $this->min_length
        );
    }
}
