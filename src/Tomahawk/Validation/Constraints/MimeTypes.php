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

class MimeTypes extends Constraint
{
    /**
     * @var array
     */
    protected $types = array();

    /**
     * @var string
     */
    protected $message = 'The field has an invalid mime type';

    public function validate(Validator $validator, $attribute, $value)
    {
        if ( ! $value instanceof File) {
            $valid = false;
        }
        else {
            $valid = in_array($value->getMimeType(), $this->types);
        }

        if ( ! $valid) {
            $this->fail($attribute, $validator);
        }

        return $valid;
    }

    public function getData()
    {
        return array(
            '%types%' => implode(', ', $this->types)
        );
    }

}
