<?php

namespace Tomahawk\DI\Test;

class NonInvokable
{
    public function __call($a, $b)
    {
    }
}
