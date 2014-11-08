<?php

namespace Tomahawk\Bundle\WebProfilerBundle\Test;

class MockPdo extends \PDO
{
    public function __construct ()
    {

    }

    public function quote($var) {
        return sprintf("%s", $var);
    }

}
