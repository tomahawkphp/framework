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

class Identical extends Constraint
{
    /**
     * @var string
     */
    protected $message = 'The field is doesn\'t match with %with%';

    /**
     * @var
     */
    protected $with;

    /**
     * @var null
     */
    protected $with_name = null;

    public function validate(Validator $validator, $attribute, $value)
    {
        if ($value !== $validator->getInput($this->with)) {
            $this->fail($attribute, $validator);
            return false;
        }

        return true;
    }

    public function getData()
    {
        return array(
            '%with%' => $this->with_name ?: $this->with
        );
    }

}
