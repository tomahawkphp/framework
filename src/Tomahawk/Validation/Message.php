<?php

namespace Tomahawk\Validation;


class Message
{
    /**
     * @var
     */
    protected $message;

    /**
     * @var array
     */
    protected $data;

    public function __construct($message, array $data = array())
    {
        $this->message = $message;
        $this->data = $data;
    }


    public function getMessage()
    {
        $message = $this->message;

        foreach ($this->data as $key => $value)
        {
            $message = str_replace($key, $value, $message);
        }

        return $message;
    }
}