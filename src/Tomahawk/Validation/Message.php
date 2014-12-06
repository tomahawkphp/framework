<?php

/*
 * This file is part of the TomahawkPHP package.
 *
 * (c) Tom Ellis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
