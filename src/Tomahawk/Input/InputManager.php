<?php

/*
 * This file is part of the TomahawkPHP package.
 *
 * (c) Tom Ellis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tomahawk\Input;

use Tomahawk\Session\SessionInterface;
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
     * @param SessionInterface $session
     */
    public function __construct(Request $request, SessionInterface $session)
    {
        $this->request = $request;
        $this->session = $session;
    }

    /**
     * Get a value from the query string
     *
     * @param null $key
     * @param null $default
     * @return array|mixed|null
     */
    public function get($key = null, $default = null)
    {
        if (is_null($key)) {
            return $this->request->query->all();
        }
        return $this->request->query->get($key, $default);
    }

    /**
     * Check if a value exists from the query string
     *
     * @param $key
     * @return bool
     */
    public function getHas($key)
    {
        return $this->request->query->has($key);
    }

    /**
     * Get values from the query string except the one(s) passed
     *
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
     * Get certain values from the query string
     *
     * @param $values
     * @return array
     */
    public function getOnly($values)
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
     * Get a value from the request
     *
     * @param null $key
     * @param null $default
     * @return array|mixed|null
     */
    public function post($key = null, $default = null)
    {
        if (is_null($key)) {
            return $this->request->request->all();
        }
        return $this->request->request->get($key, $default);
    }

    /**
     * Check if a value exists on the request
     *
     * @param $key
     * @return bool
     */
    public function postHas($key)
    {
        return $this->request->request->has($key);
    }

    /**
     * Get all values from the request except the one(s) passed
     *
     * @param $values
     * @return array
     */
    public function postExcept($values)
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
     * Get certain values from the request
     *
     * @param $values
     * @return array
     */
    public function postOnly($values)
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

    /**
     * Get old input
     *
     * @param $name
     * @return mixed
     */
    public function hasOld($name)
    {
        return $this->session->getOldInputBag()->has($name);
    }

    /**
     * Get a value from the old input bag
     *
     * @param null $name
     * @param null $default
     * @return array|mixed
     */
    public function old($name = null, $default = null)
    {
        if ($name === null) {
            return $this->session->getOldInputBag()->all();
        }
        return $this->session->getOldInputBag()->get($name, $default);
    }

    /**
     * Flash input for use on next request
     *
     * @param array $data
     * @return $this
     */
    public function flashInput(array $data)
    {
        foreach ($data as $key => $item) {
            $this->session->setNewInput($key, $item);
        }

        return $this;
    }
}
