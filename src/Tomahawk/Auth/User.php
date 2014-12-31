<?php
/*
 * This file is part of the TomahawkPHP package.
 *
 * (c) Tom Ellis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tomahawk\Auth;

/**
 * Class User
 *
 * A simple user class for use with the DatabaseAuthHandler
 *
 * @package Tomahawk\Auth
 */
class User implements UserInterface
{
    protected $primaryKey;
    protected $passwordField;
    protected $properties = array();

    public function __construct(array $properties = array())
    {
        foreach ($properties as $key => $value) {
            $this->setAttribute($key, $value);
        }
    }

    /**
     * @return mixed
     */
    public function getPrimaryKey()
    {
        return $this->primaryKey;
    }

    /**
     * @param mixed $primaryKey
     */
    public function setPrimaryKey($primaryKey)
    {
        $this->primaryKey = $primaryKey;
    }

    /**
     * @return mixed
     */
    public function getPasswordField()
    {
        return $this->passwordField;
    }

    /**
     * @param mixed $passwordField
     */
    public function setPasswordField($passwordField)
    {
        $this->passwordField = $passwordField;
    }

    /**
     * @return mixed
     */
    public function getAuthIdentifier()
    {
        return $this->getAttribute($this->primaryKey);
    }

    /**
     * @return mixed
     */
    public function getAuthPassword()
    {
        return $this->getAttribute($this->passwordField);
    }

    /**
     * Get a given attribute from the model.
     *
     * @param string $key
     * @return null
     */
    public function getAttribute($key)
    {
        return isset($this->properties[$key]) ? $this->properties[$key] : null;
    }

    /**
     * Set an attribute's value on the model.
     *
     * @param  string  $key
     * @param  mixed   $value
     * @return $this
     */
    public function setAttribute($key, $value)
    {
        $this->properties[$key] = $value;
        return $this;
    }

    public function __get($key)
    {
        return $this->getAttribute($key);
    }

    /**
     * Handle the dynamic setting of attributes.
     *
     * @param string $key
     * @param mixed $value
     */
    public function __set($key, $value)
    {
        $this->setAttribute($key, $value);
    }

    /**
     * Determine if an attribute exists on the model.
     *
     * @param string $key
     * @return bool
     */
    public function __isset($key)
    {
        return array_key_exists($key, $this->properties);
    }

    /**
     * Remove an attribute from the model.
     *
     * @param  string  $key
     * @return void
     */
    public function __unset($key)
    {
        unset($this->properties[$key]);
    }
}