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

class MaxLength extends Constraint
{
    /**
     * @var string
     */
    protected $message = 'The maximum length is %max_length%';

    /**
     * @var int
     */
    protected $max_length = 100;

    public function validate(Validator $validator, $attribute, $value)
    {
        if (strlen($value) > $this->max_length) {
            $this->fail($attribute, $validator);
            return false;
        }

        return true;
    }

    public function getData()
    {
        return array(
            '%max_length%' => $this->max_length
        );
    }
}
