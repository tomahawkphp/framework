<?php

namespace Tomahawk\DI;

interface ContainerInterface extends \ArrayAccess
{
    /**
     * Registers a service provider.
     *
     * @param ServiceProviderInterface $provider A ServiceProviderInterface instance
     * @param array                    $values   An array of values that customizes the provider
     *
     * @return static
     */
    public function registerProvider(ServiceProviderInterface $provider, array $values = array());

    /**
     * @param $id
     * @return mixed
     */
    public function get($id);

    /**
     * @param $id
     * @return bool
     */
    public function has($id);

    /**
     * @param $id
     * @param $value
     * @return $this
     */
    public function set($id, $value);

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

    /**
     * @param $name
     * @param $id
     * @return $this
     */
    public function addAlias($name, $id);

    /**
     * @param $name
     * @return $this
     */
    public function removeAlias($name);

    /**
     * @param $name
     * @return $this
     */
    public function remove($name);
}