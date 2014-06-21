<?php

namespace Tomahawk\Input;

use Tomahawk\Session\SessionManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

interface InputInterface
{

    /**
     * @param null $key
     * @param null $default
     * @return array|mixed|null
     */
    public function get($key = null, $default = null);
    /**
     * @param null $key
     * @param null $default
     * @return array|mixed|null
     */
    public function post($key = null, $default = null);

    /**
     * @param $values
     * @return array
     */
    public function getExcept( $values );

    /**
     * @param $values
     * @return array
     */
    public function getOnly( $values );
    /**
     * @param $values
     * @return array
     */
    public function postExcept( $values );

    /**
     * @param $values
     * @return array
     */
    public function postOnly( $values );

    public function old($name = null, $default = null);

    public function flash(array $data);

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function setRequest($request);

    /**
     * @return \Symfony\Component\HttpFoundation\Request
     */
    public function getRequest();

    /**
     * @param $session
     */
    public function setSession($session);

    /**
     * @return SessionManager
     */
    public function getSession();

}