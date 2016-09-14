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

class Required extends AbstractRequired
{
    /**
     * @var string
     */
    protected $message = 'The field is required';

    public function validate(Validator $validator, $attribute, $value)
    {
        if ( ! $valid = $this->hasRequiredValue($value)) {
            $this->fail($attribute, $validator);
        }

        return $valid;
    }

}
