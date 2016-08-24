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

class RequiredWith extends AbstractRequired
{
    /**
     * @var string
     */
    protected $message = 'The field is required with %with%';

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
        $withValue = $validator->getInput($this->with);

        if ($this->hasRequiredValue($withValue) && !$this->hasRequiredValue($value)) {
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
