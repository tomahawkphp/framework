<?php

namespace Tomahawk\Hashing\Test;

use Tomahawk\Hashing\Hasher as BaseHasher;

class Hasher extends BaseHasher
{
    protected function doHash($value, $algo, array $options = array())
    {
        return false;
    }
}