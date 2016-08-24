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

class DigitsBetween extends Constraint
{
    /**
     * @var int
     */
    protected $start = 0;

    /**
     * @var int
     */
    protected $end = 10;

    /**
     * @var string
     */
    protected $message = 'The field must be between %start% and %end%';

    public function getData()
    {
        return array(
            '%start%' => $this->start,
            '%end%' => $this->end,
        );
    }

    public function validate(Validator $validator, $attribute, $value)
    {
        $value = (int)$value;

        if ($value < $this->start || $value > $this->end) {
            $this->fail($attribute, $validator);
            return false;
        }

        return true;
    }

}
