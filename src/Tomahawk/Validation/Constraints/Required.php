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
use Tomahawk\Validation\Message;

class Required extends Constraint
{
    protected $message = 'The field is required';

    public function validate(Validator $validator, $attribute, $value)
    {
        if (is_null($value) || (is_string($value) && trim($value) === '') || (is_array($value) && !$value)) {
            if ($trans = $validator->getTranslator()) {
                $this->setMessage($trans->trans($this->getMessage(), $this->getData()));
            }
            else {
                $this->mergeMessageData();
            }

            $validator->addMessage($attribute, new Message($this->getMessage(), array()));

            return false;
        }

        return true;
    }

}
