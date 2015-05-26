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

class Image extends MimeTypes
{
    /**
     * @var array
     */
    protected $types = array('image/jpeg', 'image/jpg', 'image/x-png', 'image/png', 'image/gif', 'image/bmp', 'image/svg');
}
