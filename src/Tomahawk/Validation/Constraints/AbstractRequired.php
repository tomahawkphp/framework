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
use Symfony\Component\HttpFoundation\File\UploadedFile;

abstract class AbstractRequired extends Constraint
{
    /**
     * Whether to skip validation when no value is passed
     *
     * @var bool
     */
    protected $skipOnNoValue = false;

    /**
     * @var string
     */
    protected $message = 'The field is required';

    /**
     * Check if posted value is valid
     *
     * @param $value
     * @return bool
     */
    protected function hasRequiredValue($value)
    {
        $valid = true;

        // $_FILES
        if ($value instanceof UploadedFile && !$value->isValid()) {
            $valid = false;
        }
        // Null
        else if (is_null($value)) {
            $valid = false;
        }
        // String
        else if ((is_string($value) && trim($value) === '')) {
            $valid = false;
        }
        // Array
        else if((is_array($value) && !$value)) {
            $valid = false;
        }

        return $valid;
    }
}
