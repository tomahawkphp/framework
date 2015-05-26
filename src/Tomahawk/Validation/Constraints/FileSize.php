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
use Symfony\Component\HttpFoundation\File\File;

class FileSize extends Constraint
{
    /**
     * @var float
     */
    protected $size;

    /**
     * @var string
     */
    protected $message = 'The field is required';

    public function validate(Validator $validator, $attribute, $value)
    {
        if (!$value instanceof File) {
            $valid = false;
        }
        else {
            $size = $value->getSize() / 1024;
            $valid = $size == $this->size;
        }

        if (!$valid) {
            $this->fail($attribute, $validator);
        }

        return $valid;
    }

    public function getData()
    {
        return array(
            '%size%' => $this->size
        );
    }

}
