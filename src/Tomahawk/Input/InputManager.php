<?php

namespace Tomahawk\Input;

use Tomahawk\Session\Session;
use Symfony\Component\HttpFoundation\Request;

class InputManager implements InputInterface
{
    /**
     * @var \Symfony\Component\HttpFoundation\Request
     */
    protected $request;

    /**
     * @var \Tomahawk\Session\Session
     */
    protected $session;

    /**
     * @param Request $request
     * @param Session $session
     */
    public function __construct(Request $request, Session $session)
    {
        $this->request = $request;
        $this->session = $session;
    }

    /**
     * @param null $key
     * @param null $default
     * @return array|mixed|null
     */
    public function get($key = null, $default = null)
    {
        if (is_null($key))
        {
            return $this->request->query->all();
        }
        return $this->request->query->get($key, $default);
    }

    public function getHas($key)
    {
        return $this->request->query->has($key);
    }

    /**
     * @param $values
     * @return array
     */
    public function getExcept($values)
    {
        if (!is_array($values)) {
            $values = array($values);
        }

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
        if (!is_array($values)) {
            $values = array($values);
        }

        $get = array();

        foreach ($values as $value) {
            $get[] = $this->get($value);
        }
        return $get;
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
        return $this->request->request->get($key, $default);
    }

    public function postHas($key)
    {
        return $this->request->request->has($key);
    }

    /**
     * @param $values
     * @return array
     */
    public function postExcept( $values )
    {
        if (!is_array($values)) {
            $values = array($values);
        }

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
        if (!is_array($values)) {
            $values = array($values);
        }

        $post = array();

        foreach ($values as $value) {
            $post[] = $this->get( $value );
        }
        return $post;
    }

    public function hasOld($name)
    {
        return $this->session->getOldBag()->has($name);
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

}
