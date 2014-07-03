<?php

namespace Tomahawk\Validation\Constraints;

use Tomahawk\Validation\Validator;

abstract class Constraint implements ConstraintInterface
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

    public function setMessage($message)
    {
        $this->message = $message;
        return $this;
    }

    public function getData()
    {
        return array();
    }

    /**
     * @return $this
     */
    public function mergeMessageData()
    {

        foreach ($this->getData() as $key => $value)
        {
            $this->message = str_replace($key, $value, $this->message);
        }

        //$this->setMessage($this->message);

        return $this;
    }
}