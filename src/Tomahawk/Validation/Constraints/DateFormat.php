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

class DateFormat extends Constraint
{
    /**
     * @var string
     */
    protected $format = 'Y-m-d';

    /**
     * @var string
     */
    protected $message = 'The date format of the field is incorrect. Must be in format: %date_format%';

    public function getData()
    {
        return array(
            '%date_format%' => $this->format
        );
    }

    public function validate(Validator $validator, $attribute, $value)
    {
        $date = \DateTime::createFromFormat($this->format, $value);

        if (false === $date || $date->format($this->format) !== $value) {
            $this->fail($attribute, $validator);
            return false;
        }

        return true;
    }
}
