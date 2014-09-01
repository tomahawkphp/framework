<?php

namespace Tomahawk\DI;

use Pimple\Container as BaseContainer;
use ReflectionClass;
use ReflectionParameter;
use Tomahawk\DI\Exception\BindingResolutionException;
use Tomahawk\DI\Exception\InstantiateException;

class Container extends BaseContainer implements ContainerInterface
{
    /**
     * @var array Aliases of something in the DIC
     */
    protected $aliases = array();

    /**
     * Registers a service provider.
     *
     * @param ServiceProviderInterface $provider A ServiceProviderInterface instance
     * @param array                    $values   An array of values that customizes the provider
     *
     * @return static
     */
    public function registerProvider(ServiceProviderInterface $provider, array $values = array())
    {
        $provider->register($this);

        foreach ($values as $key => $value) {
            $this[$key] = $value;
        }

        return $this;
    }

    /**
     * @param $id
     * @return mixed
     */
    public function get($id)
    {
        if ($this->hasAlias($id))
        {
            $id = $this->getAlias($id);
        }

        if (!$this->has($id) && class_exists($id))
        {
            return $this->build($id);
        }

        return $this[$id];
    }

    /**
     * Gets a parameter or an object.
     *
     * @param string $id The unique identifier for the parameter or object
     *
     * @return mixed The value of the parameter or an object
     *
     * @throws \InvalidArgumentException if the identifier is not defined
     */
    public function offsetGet($id)
    {
        if ($this->hasAlias($id))
        {
            $id = $this->getAlias($id);
        }

        return parent::offsetGet($id);
    }

    public function build($class, $parameters = array())
    {

        $reflector = new ReflectionClass($class);

        // If the class is not instantiable, error
        if (!$reflector->isInstantiable()) {
            throw new InstantiateException(sprintf('Class %s is not instantiable.', $class));
        }

        if (!$constructor = $reflector->getConstructor()) {
            return new $class;
        }

        $classes = $constructor->getParameters();

        // Once we have all the constructor's parameters we can create each of the
        // dependency instances and then use the reflection instances to make a
        // new instance of this class, injecting the created dependencies in.
        $deps = array_merge($parameters, $this->getDependencies(
            array_diff_key($classes, $parameters)
        ));

        return $reflector->newInstanceArgs($deps);
    }

    /**
     * Resolve all of the dependencies from the ReflectionParameters.
     *
     * @param  array  $parameters
     * @return array
     */
    protected function getDependencies($parameters)
    {
        $dependencies = array();

        foreach ($parameters as $parameter)
        {
            // If the class is null, it means the dependency is a string or some other
            // primitive type which we can not resolve since it is not a class and
            // we'll just bomb out with an error since we have no-where to go.
            if (!$parameter->getClass())
            {
                $dependencies[] = $this->resolveNonClass($parameter);
            }
            else
            {
                $dependencies[] = $this->resolveClass($parameter);
            }
        }

        return (array)$dependencies;
    }

    /**
     * Resolve a non-class hinted dependency.
     *
     * @param  ReflectionParameter  $parameter
     * @return mixed
     *
     * @throws BindingResolutionException
     */
    protected function resolveNonClass(ReflectionParameter $parameter)
    {
        if ($parameter->isDefaultValueAvailable())
        {
            return $parameter->getDefaultValue();
        }
        else
        {
            $message = "Unresolvable dependency resolving [$parameter].";

            throw new BindingResolutionException($message);
        }
    }

    /**
     * Resolve a class based dependency from the container.
     *
     * @param  \ReflectionParameter  $parameter
     * @return mixed
     *
     * @throws BindingResolutionException
     */
    protected function resolveClass(ReflectionParameter $parameter)
    {
        try
        {
            return $this->get($parameter->getClass()->name);
        }

            // If we can not resolve the class instance, we will check to see if the value
            // is optional, and if it is we will return the optional parameter value as
            // the value of the dependency, similarly to how we do this with scalars.
        catch (BindingResolutionException $e)
        {
            if ($parameter->isOptional())
            {
                return $parameter->getDefaultValue();
            }
            else
            {
                throw $e;
            }
        }
    }

    /**
     * @param $id
     * @return bool
     */
    public function has($id)
    {
        return isset($this[$id]);
    }

    /**
     * @param $id
     * @param $value
     * @return $this
     */
    public function set($id, $value)
    {
        $this[$id] = $value;

        return $this;
    }

    /**
     * @param $name
     * @param $id
     * @return $this
     */
    public function addAlias($name, $id)
    {
        $this->aliases[$name] = $id;
        return $this;
    }

    /**
     * @param $name
     * @return $this
     */
    public function getAlias($name)
    {
        return $this->aliases[$name];
    }

    public function hasAlias($name)
    {
        return isset($this->aliases[$name]);
    }

    /**
     * @param $name
     * @return $this
     */
    public function removeAlias($name)
    {
        unset($this->aliases[$name]);
        return $this;
    }

    /**
     * @param $name
     * @return $this
     */
    public function remove($name)
    {
        unset($this[$name]);
        return $this;
    }

}