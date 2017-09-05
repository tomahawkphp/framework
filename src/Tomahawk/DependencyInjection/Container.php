<?php

/*
 * This file is part of the TomahawkPHP package.
 *
 * (c) Tom Ellis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tomahawk\DependencyInjection;

use ReflectionClass;
use ReflectionParameter;
use Tomahawk\DependencyInjection\Exception\BindingResolutionException;
use Tomahawk\DependencyInjection\Exception\InstantiateException;

/**
 * Container main class.
 *
 * @author  Tom Ellis
 *
 * Base on Pimple
 *
 * @author  Fabien Potencier
 */
class Container implements \ArrayAccess, ContainerInterface
{
    /**
     * @var array
     */
    private $serviceTags = [];

    /**
     * @var array
     */
    private $values = [];

    /**
     * @var \SplObjectStorage
     */
    private $factories;

    /**
     * @var \SplObjectStorage
     */
    private $protected;

    /**
     * @var array
     */
    private $frozen = [];

    /**
     * @var array
     */
    private $raw = [];

    /**
     * @var array
     */
    private $keys = [];

    /**
     * @var array Aliases of something in the DIC
     */
    protected $aliases = [];

    /**
     * Instantiate the container.
     *
     * Objects and parameters can be passed as argument to the constructor.
     *
     * @param array $values The parameters or objects.
     */
    public function __construct(array $values = [])
    {
        $this->factories = new \SplObjectStorage();
        $this->protected = new \SplObjectStorage();

        foreach ($values as $key => $value) {
            $this->offsetSet($key, $value);
        }
    }

    public function build($class, $parameters = [])
    {
        $reflector = new ReflectionClass($class);

        // If the class is not instantiable, error
        if ( ! $reflector->isInstantiable()) {
            throw new InstantiateException(sprintf('Class %s is not instantiable.', $class));
        }

        if ( ! $constructor = $reflector->getConstructor()) {
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
        $dependencies = [];

        foreach ($parameters as $parameter) {
            // If the class is null, it means the dependency is a string or some other
            // primitive type which we can not resolve since it is not a class and
            // we'll just bomb out with an error since we have no-where to go.
            if ( ! $parameter->getClass()) {
                $dependencies[] = $this->resolveNonClass($parameter);
            }
            else {
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
        try {
            return $this->get($parameter->getClass()->name);
        }

            // If we can not resolve the class instance, we will check to see if the value
            // is optional, and if it is we will return the optional parameter value as
            // the value of the dependency, similarly to how we do this with scalars.
        catch (BindingResolutionException $e) {

            if ($parameter->isOptional()) {
                return $parameter->getDefaultValue();
            }
            else {
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
     * @return mixed
     */
    public function get($id)
    {
        return $this[$id];
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

    /**
     * Sets a parameter or an object.
     *
     * Objects must be defined as Closures.
     *
     * Allowing any PHP callable leads to difficult to debug problems
     * as function names (strings) are callable (creating a function with
     * the same name as an existing parameter would break your container).
     *
     * @param  string            $id    The unique identifier for the parameter or object
     * @param  mixed             $value The value of the parameter or a closure to define an object
     * @throws \RuntimeException Prevent override of a frozen service
     */
    public function offsetSet($id, $value)
    {
        if (isset($this->frozen[$id])) {
            throw new \RuntimeException(sprintf('Cannot override frozen service "%s".', $id));
        }

        $this->values[$id] = $value;
        $this->keys[$id] = true;
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
        if ($this->hasAlias($id)) {
            $id = $this->getAlias($id);
        }

        if ( ! $this->has($id) && class_exists($id)) {
            return $this->build($id);
        }

        if ( ! isset($this->keys[$id])) {
            throw new \InvalidArgumentException(sprintf('Identifier "%s" is not defined.', $id));
        }

        if (isset($this->raw[$id])
            || !is_object($this->values[$id])
            || isset($this->protected[$this->values[$id]])
            || !method_exists($this->values[$id], '__invoke')
        ) {
            return $this->values[$id];
        }

        if (isset($this->factories[$this->values[$id]])) {
            return $this->values[$id]($this);
        }

        $raw = $this->values[$id];
        $val = $this->values[$id] = $raw($this);
        $this->raw[$id] = $raw;

        $this->frozen[$id] = true;

        return $val;
    }

    /**
     * Checks if a parameter or an object is set.
     *
     * @param string $id The unique identifier for the parameter or object
     *
     * @return bool
     */
    public function offsetExists($id)
    {
        if ($this->hasAlias($id)) {
            $id = $this->getAlias($id);
        }

        return isset($this->keys[$id]);
    }

    /**
     * Unsets a parameter or an object.
     *
     * @param string $id The unique identifier for the parameter or object
     */
    public function offsetUnset($id)
    {
        if ($this->hasAlias($id)) {
            $alias = $id;
            $id = $this->getAlias($id);
            $this->removeAlias($alias);
        }

        if (isset($this->keys[$id])) {
            if (is_object($this->values[$id])) {
                unset($this->factories[$this->values[$id]], $this->protected[$this->values[$id]]);
            }

            unset($this->values[$id], $this->frozen[$id], $this->raw[$id], $this->keys[$id]);
        }
    }

    /**
     * Marks a callable as being a factory service.
     *
     * @param callable $callable A service definition to be used as a factory
     *
     * @return callable The passed callable
     *
     * @throws \InvalidArgumentException Service definition has to be a closure of an invokable object
     */
    public function factory($callable)
    {
        if ( ! is_object($callable) || !method_exists($callable, '__invoke')) {
            throw new \InvalidArgumentException('Service definition is not a Closure or invokable object.');
        }

        $this->factories->attach($callable);

        return $callable;
    }

    /**
     * Protects a callable from being interpreted as a service.
     *
     * This is useful when you want to store a callable as a parameter.
     *
     * @param callable $callable A callable to protect from being evaluated
     *
     * @return callable The passed callable
     *
     * @throws \InvalidArgumentException Service definition has to be a closure of an invokable object
     */
    public function protect($callable)
    {
        if ( ! is_object($callable) || ! method_exists($callable, '__invoke')) {
            throw new \InvalidArgumentException('Callable is not a Closure or invokable object.');
        }

        $this->protected->attach($callable);

        return $callable;
    }

    /**
     * Gets a parameter or the closure defining an object.
     *
     * @param string $id The unique identifier for the parameter or object
     *
     * @return mixed The value of the parameter or the closure defining an object
     *
     * @throws \InvalidArgumentException if the identifier is not defined
     */
    public function raw($id)
    {
        if ( ! isset($this->keys[$id])) {
            throw new \InvalidArgumentException(sprintf('Identifier "%s" is not defined.', $id));
        }

        if (isset($this->raw[$id])) {
            return $this->raw[$id];
        }

        return $this->values[$id];
    }

    /**
     * Extends an object definition.
     *
     * Useful when you want to extend an existing object definition,
     * without necessarily loading that object.
     *
     * @param string   $id       The unique identifier for the object
     * @param callable $callable A service definition to extend the original
     *
     * @return callable The wrapped callable
     *
     * @throws \InvalidArgumentException if the identifier is not defined or not a service definition
     */
    public function extend($id, $callable)
    {
        if ( ! isset($this->keys[$id])) {
            throw new \InvalidArgumentException(sprintf('Identifier "%s" is not defined.', $id));
        }

        if ( ! is_object($this->values[$id]) || ! method_exists($this->values[$id], '__invoke')) {
            throw new \InvalidArgumentException(sprintf('Identifier "%s" does not contain an object definition.', $id));
        }

        if ( ! is_object($callable) || ! method_exists($callable, '__invoke')) {
            throw new \InvalidArgumentException('Extension service definition is not a Closure or invokable object.');
        }

        $factory = $this->values[$id];

        $extended = function ($c) use ($callable, $factory) {
            /** @var \Closure|string $callable */
            return $callable($factory($c), $c);
        };

        if (isset($this->factories[$factory])) {
            $this->factories->detach($factory);
            $this->factories->attach($extended);
        }

        return $this[$id] = $extended;
    }

    /**
     * Returns all defined value names.
     *
     * @return array An array of value names
     */
    public function keys()
    {
        return array_keys($this->values);
    }

    /**
     * Registers a service provider.
     *
     * @param ServiceProviderInterface $provider A ServiceProviderInterface instance
     * @param array                    $values   An array of values that customizes the provider
     *
     * @return static
     */
    public function register(ServiceProviderInterface $provider, array $values = [])
    {
        $provider->register($this);

        foreach ($values as $key => $value) {
            $this[$key] = $value;
        }

        return $this;
    }

    /**
     * Tag a service
     *
     * @param string $id
     * @param string $tag
     * @return $this
     */
    public function tag($id, $tag)
    {
        if ( ! isset($this->serviceTags[$id])) {
            $this->serviceTags[$id] = [];
        }

        $this->serviceTags[$id][] = $tag;
        return $this;
    }

    /**
     * Find all service ids with a given tag
     *
     * @param string $tag
     * @return array
     */
    public function findTaggedServiceIds($tag)
    {
        $ids = [];

        foreach ($this->serviceTags as $id => $tags) {
            if (in_array($tag, $tags)) {
                $ids[] = $id;
            }
        }

        return $ids;
    }

    /**
     * Get all service tags
     *
     * @return array
     */
    public function getServiceTags()
    {
        return $this->serviceTags;
    }
}
