<?php

namespace Tomahawk\DI;

interface DIContainerInterface extends \ArrayAccess
{
    /**
     * @param $id
     * @return mixed
     */
    public function get($id);

    /**
     * @param $id
     * @return bool
     */
    public function registered($id);

    /**
     * @param $id
     * @param $value
     * @return $this
     */
    public function register($id, $value);

    /**
     * @param $id
     * @return mixed
     */
    public function raw($id);

    /**
     * @param $id
     * @param $callback
     * @return mixed
     */
    public function extend($id, $callback);

    /**
     * @param $callable
     * @return mixed
     */
    public function factory($callable);

    /**
     * @param $callable
     * @return mixed
     */
    public function protect($callable);
}