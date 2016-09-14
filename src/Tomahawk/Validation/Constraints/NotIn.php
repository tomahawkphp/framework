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

class NotIn extends Constraint
{
    /**
     * @var string
     */
    protected $message = 'Please choose a value that is not from the following: %choices%';

    /**
     * @var array
     */
    protected $choices = array();

    public function getData()
    {
        return array(
            '%choices%' => implode(', ', $this->choices)
        );
    }

    public function validate(Validator $validator, $attribute, $value)
    {
        if (in_array($value, $this->choices)) {

            $this->fail($attribute, $validator);

            return false;
        }

        return true;
    }

}
