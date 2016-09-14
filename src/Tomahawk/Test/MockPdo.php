<?php

namespace Tomahawk\Test;

class MockPdo extends \PDO
{
    public function __construct ()
    {

    }

    public function quote($string, $parameter_type = \PDO::PARAM_STR) {
        return sprintf("%s", $string);
    }

}
