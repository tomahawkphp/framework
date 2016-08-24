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

class TimeZone extends Constraint
{
    /**
     * @var string
     */
    protected $message = 'The timezone is incorrect';

    public function validate(Validator $validator, $attribute, $value)
    {
        try {
            new \DateTimeZone($value);
        }
        catch(\Exception $e) {
            $this->fail($attribute, $validator);
            return false;
        }

        return true;
    }
}
