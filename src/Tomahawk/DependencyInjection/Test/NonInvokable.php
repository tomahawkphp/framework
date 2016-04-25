<?php

namespace Tomahawk\DependencyInjection\Test;

class NonInvokable
{
    public function __call($a, $b)
    {
    }
}
