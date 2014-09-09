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

    /**
     * @param $message
     * @param array $data
     */
    public function __construct($message, array $data = array())
    {
        $this->message = $message;
        $this->data = $data;
    }


    /**
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }
}
