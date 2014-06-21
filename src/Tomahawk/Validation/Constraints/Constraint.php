<?php

namespace Tomahawk\Validation\Constraints;

use Tomahawk\Validation\Validator;

abstract class Constraint
{
    /**
     * @var
     */
    protected $message;

    /**
     * @var array
     */
    protected $data = array();

    public function __construct(array $config = array())
    {
        foreach ($config as $name => $value)
        {
            if (property_exists($this, $name))
            {
                $this->$name = $value;
            }
        }
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function getData()
    {
        return array();
    }
}