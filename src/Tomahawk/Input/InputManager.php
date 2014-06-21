<?php

namespace Tomahawk\Input;

use Tomahawk\Session\SessionManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

class InputManager implements InputInterface
{
    /**
     * @var \Symfony\Component\HttpFoundation\Request
     */
    protected $request;

    /**
     * @var \Tomahawk\Session\SessionManager
     */
    protected $session;

    /**
     * @param Request $request
     * @param SessionManager $session
     */
    public function __construct(Request $request, SessionManager $session)
    {
        $this->request = $request;
        $this->session = $session;
    }

    /**
     * @param null $key
     * @param null $default
     * @return array|mixed|null
     */
    public function get( $key = null, $default = null )
    {
        if( is_null($key) )
        {
            return $this->request->query->all();
        }
        return $this->request->query->has( $key ) ? $this->request->query->get($key) : $default;
    }

    /**
     * @param null $key
     * @param null $default
     * @return array|mixed|null
     */
    public function post( $key = null, $default = null )
    {
        if (is_null($key)) {
            return $this->request->request->all();
        }
        return $this->request->request->has( $key ) ? $this->request->request->get($key) : $default;
    }

    /**
     * @param $values
     * @return array
     */
    public function getExcept( $values )
    {
        $all = $this->request->query->all();

        $get = array();

        foreach ($all as $key => $value) {
            if (!in_array($key, $values) ) {
                $get[$key] = $value;
            }
        }
        return $get;
    }

    /**
     * @param $values
     * @return array
     */
    public function getOnly( $values )
    {
        $all = $this->request->query->all();

        $get = array();

        foreach ($values as $key => $value) {
            $get[] = $this->get( $value );
        }
        return $get;
    }

    /**
     * @param $values
     * @return array
     */
    public function postExcept( $values )
    {
        $all = $this->request->request->all();

        $post = array();

        foreach ($all as $key => $value) {
            if( !in_array($key, $values) ) {
                $post[$key] = $value;
            }
        }
        return $post;
    }

    /**
     * @param $values
     * @return array
     */
    public function postOnly( $values )
    {
        $all = $this->request->request->all();

        $post = array();

        foreach ($values as $key => $value) {
            $post[] = $this->get( $value );
        }
        return $post;
    }

    public function old($name = null, $default = null)
    {
        if ($name === null) {
            return $this->session->getOldBag()->all();
        }
        return $this->session->getOldBag()->get($name, $default);
    }

    public function flash(array $data)
    {
        foreach ($data as $key => $item) {
            $this->session->setOld($key, $item);
        }
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function setRequest($request)
    {
        $this->request = $request;
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @param $session
     */
    public function setSession($session)
    {
        $this->session = $session;
    }

    /**
     * @return SessionManager
     */
    public function getSession()
    {
        return $this->session;
    }

}