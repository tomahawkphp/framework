<?php
/*
 * This file is part of the TomahawkPHP package.
 *
 * The is based on code originally distributed inside the Symfony/Doctrine framework.
 *
 * (c) Tom Ellis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tomahawk\Bundle\DoctrineBundle;

use Doctrine\Common\Persistence\AbstractManagerRegistry;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMException;
use Tomahawk\DependencyInjection\ContainerInterface;

class Registry extends AbstractManagerRegistry implements RegistryInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * Construct.
     *
     * @param ContainerInterface $container
     * @param array              $connections
     * @param array              $entityManagers
     * @param string             $defaultConnection
     * @param string             $defaultEntityManager
     */
    public function __construct(ContainerInterface $container, array $connections, array $entityManagers, $defaultConnection, $defaultEntityManager)
    {
        $this->setContainer($container);

        parent::__construct('ORM', $connections, $entityManagers, $defaultConnection, $defaultEntityManager, 'Doctrine\ORM\Proxy\Proxy');
    }

    /**
     * {@inheritdoc}
     */
    protected function getService($name)
    {
        return $this->container->get($name);
    }

    /**
     * {@inheritdoc}
     */
    protected function resetService($name)
    {
        $this->container->set($name, null);
    }

    /**
     * {@inheritdoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * Gets the default entity manager name.
     *
     * @return string The default entity manager name
     *
     * @codeCoverageIgnore
     *
     * @deprecated
     */
    public function getDefaultEntityManagerName()
    {
        return $this->getDefaultManagerName();
    }

    /**
     * Gets a named entity manager.
     *
     * @param string $name The entity manager name (null for the default one)
     *
     * @return EntityManager
     *
     * @codeCoverageIgnore
     *
     * @deprecated
     */
    public function getEntityManager($name = null)
    {
        return $this->getManager($name);
    }

    /**
     * Gets an array of all registered entity managers
     *
     * @return EntityManager[] an array of all EntityManager instances
     *
     * @codeCoverageIgnore
     *
     * @deprecated
     */
    public function getEntityManagers()
    {
        return $this->getManagers();
    }

    /**
     * Resets a named entity manager.
     *
     * This method is useful when an entity manager has been closed
     * because of a rollbacked transaction AND when you think that
     * it makes sense to get a new one to replace the closed one.
     *
     * Be warned that you will get a brand new entity manager as
     * the existing one is not useable anymore. This means that any
     * other object with a dependency on this entity manager will
     * hold an obsolete reference. You can inject the registry instead
     * to avoid this problem.
     *
     * @param string $name The entity manager name (null for the default one)
     *
     * @return EntityManager
     *
     * @codeCoverageIgnore
     *
     * @deprecated
     */
    public function resetEntityManager($name = null)
    {
        trigger_error('resetEntityManager is deprecated since Symfony 2.1. Use resetManager instead', E_USER_DEPRECATED);

        $this->resetManager($name);
    }

    /**
     * Resolves a registered namespace alias to the full namespace.
     *
     * This method looks for the alias in all registered entity managers.
     *
     * @param string $alias The alias
     *
     * @return string The full namespace
     *
     * @codeCoverageIgnore
     *
     * @deprecated
     */
    public function getEntityNamespace($alias)
    {
        return $this->getAliasNamespace($alias);
    }

    /**
     * Resolves a registered namespace alias to the full namespace.
     *
     * This method looks for the alias in all registered entity managers.
     *
     * @param string $alias The alias
     *
     * @return string The full namespace
     *
     * @see Configuration::getEntityNamespace
     */
    public function getAliasNamespace($alias)
    {
        foreach (array_keys($this->getManagers()) as $name) {
            try {
                return $this->getManager($name)->getConfiguration()->getEntityNamespace($alias);
            } catch (ORMException $e) {
            }
        }

        throw ORMException::unknownEntityNamespace($alias);
    }

    /**
     * Gets all connection names.
     *
     * @return array An array of connection names
     *
     * @codeCoverageIgnore
     *
     * @deprecated
     */
    public function getEntityManagerNames()
    {
        return $this->getManagerNames();
    }

    /**
     * Gets the entity manager associated with a given class.
     *
     * @param string $class A Doctrine Entity class name
     *
     * @return EntityManager|null
     *
     * @codeCoverageIgnore
     *
     * @deprecated
     */
    public function getEntityManagerForClass($class)
    {
        return $this->getManagerForClass($class);
    }
}
