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

class In extends Constraint
{
    protected $message = 'Please choose from the following: %choices%';
    protected $choices = array();

    public function getData()
    {
        return array(
            '%choices%' => implode(', ', $this->choices)
        );
    }

    public function validate(Validator $validator, $attribute, $value)
    {
        if ( ! in_array($value, $this->choices)) {

            $this->fail($attribute, $validator);

            return false;
        }

        return true;
    }

}
