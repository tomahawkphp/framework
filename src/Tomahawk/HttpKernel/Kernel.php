<?php

namespace Tomahawk\HttpKernel;

use Tomahawk\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\TerminableInterface;
use Symfony\Component\HttpKernel\HttpKernel;
use Tomahawk\DI\Container;
use Tomahawk\DI\ContainerAwareInterface;

abstract class Kernel implements KernelInterface, TerminableInterface
{
    /**
     * @var BundleInterface[]
     */
    protected $bundles = array();

    protected $bundleMap;
    /**
     * @var Container
     */
    protected $container;
    protected $rootDir;
    protected $environment;
    protected $debug;
    protected $booted = false;
    protected $name;
    protected $startTime;
    protected $paths = array();

    const VERSION         = '0.2.1';
    const VERSION_ID      = '00201';
    const MAJOR_VERSION   = '0';
    const MINOR_VERSION   = '2';
    const RELEASE_VERSION = '1';
    const EXTRA_VERSION   = '';

    /**
     * Constructor.
     *
     * @param string  $environment The environment
     * @param bool    $debug       Whether to enable debugging or not
     *
     * @api
     */
    public function __construct($environment, $debug)
    {
        $this->environment = $environment;
        $this->debug = (bool) $debug;
        $this->rootDir = $this->getRootDir();
        $this->name = $this->getName();

        if ($this->debug) {
            $this->startTime = microtime(true);
        }
    }

    public function __clone()
    {
        if ($this->debug) {
            $this->startTime = microtime(true);
        }

        $this->booted = false;
        $this->container = null;
    }

    /**
     * Boots the current kernel.
     *
     * @api
     */
    public function boot()
    {
        if (true === $this->booted) {
            return;
        }

        // init container
        $this->initializeContainer();


        // init bundles
        $this->initializeBundles();

        /*if ($this->loadClassCache) {
            $this->doLoadClassCache($this->loadClassCache[0], $this->loadClassCache[1]);
        }
        */


        // init container
        //$this->initializeContainer();

        foreach ($this->getBundles() as $bundle) {
            if ($bundle instanceof ContainerAwareInterface)
            {
                $bundle->setContainer($this->container);
            }

            $bundle->boot();
        }

        $this->booted = true;
    }

    /**
     * {@inheritdoc}
     *
     * @api
     */
    public function terminate(Request $request, Response $response)
    {
        if (false === $this->booted) {
            return;
        }

        if ($this->getHttpKernel() instanceof TerminableInterface) {
            $this->getHttpKernel()->terminate($request, $response);
        }
    }

    /**
     * {@inheritdoc}
     *
     * @api
     */
    public function shutdown()
    {
        if (false === $this->booted) {
            return;
        }

        $this->booted = false;

        foreach ($this->getBundles() as $bundle) {
            $bundle->shutdown();
            $bundle->setContainer(null);
        }

        $this->container = null;
    }

    /**
     * {@inheritdoc}
     *
     * @api
     */
    public function handle(Request $request, $type = HttpKernelInterface::MASTER_REQUEST, $catch = true)
    {
        if (false === $this->booted) {
            $this->boot();
        }

        return $this->getHttpKernel()->handle($request, $type, $catch);
    }

    /**
     * Gets a HTTP kernel from the container
     *
     * @return HttpKernel
     */
    protected function getHttpKernel()
    {
        return $this->container->get('http_kernel');
    }

    /**
     * {@inheritdoc}
     *
     * @api
     */
    public function getName()
    {
        if (null === $this->name) {
            $this->name = preg_replace('/[^a-zA-Z0-9_]+/', '', basename($this->rootDir));
        }

        return $this->name;
    }

    /**
     * {@inheritdoc}
     *
     * @api
     */
    public function getEnvironment()
    {
        return $this->environment;
    }

    /**
     * {@inheritdoc}
     *
     * @api
     */
    public function isDebug()
    {
        return $this->debug;
    }

    /**
     * {@inheritdoc}
     *
     * @api
     */
    public function getRootDir()
    {
        if (null === $this->rootDir) {
            $r = new \ReflectionObject($this);
            $this->rootDir = str_replace('\\', '/', dirname($r->getFileName()));
        }

        return $this->rootDir;
    }

    /**
     * {@inheritdoc}
     *
     * @api
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * @param \Tomahawk\DI\ContainerInterface $container
     */
    public function setContainer($container)
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     *
     * @api
     */
    public function getStartTime()
    {
        return $this->debug ? $this->startTime : -INF;
    }

    /**
     * {@inheritdoc}
     *
     * @api
     */
    public function getCacheDir()
    {
        return $this->rootDir.'/cache/'.$this->environment;
    }

    /**
     * {@inheritdoc}
     *
     * @api
     */
    public function getLogDir()
    {
        return $this->rootDir.'/logs';
    }

    /**
     * {@inheritdoc}
     *
     * @api
     */
    public function getCharset()
    {
        return 'UTF-8';
    }

    public function serialize()
    {
        return serialize(array($this->environment, $this->debug));
    }

    public function unserialize($data)
    {
        list($environment, $debug) = unserialize($data);

        $this->__construct($environment, $debug);
    }

    /**
     * Initializes the IOC container.
     *
     */
    protected function initializeContainer()
    {
        //$this->container = new Container();

        //$this->getKernelParameters();
    }

    public function setPaths($paths)
    {
        $this->paths = $paths;
        return $this;
    }

    public function getPaths()
    {
        return $this->paths;
    }

    public function getPath($path)
    {
        return array_get($this->paths, $path);
    }

    public function getBundles()
    {
        return $this->bundles;
    }

    public function getBundle($name, $first = true)
    {
        if (!isset($this->bundleMap[$name])) {
            throw new \InvalidArgumentException(sprintf('Bundle "%s" does not exist or it is not enabled. Maybe you forgot to add it in the registerBundles() method of your %s.php file?', $name, get_class($this)));
        }

        if (true === $first) {
            return $this->bundleMap[$name][0];
        }

        return $this->bundleMap[$name];
    }

    /**
     * Initializes the data structures related to the bundle management.
     *
     *  - the bundles property maps a bundle name to the bundle instance,
     *  - the bundleMap property maps a bundle name to the bundle inheritance hierarchy (most derived bundle first).
     *
     * @throws \LogicException if two bundles share a common name
     * @throws \LogicException if a bundle tries to extend a non-registered bundle
     * @throws \LogicException if a bundle tries to extend itself
     * @throws \LogicException if two bundles extend the same ancestor
     */
    protected function initializeBundles()
    {
        // init bundles
        $this->bundles = array();
        $topMostBundles = array();
        $directChildren = array();

        foreach ($this->registerBundles() as $bundle) {
            $name = $bundle->getName();
            if (isset($this->bundles[$name])) {
                throw new \LogicException(sprintf('Trying to register two bundles with the same name "%s"', $name));
            }
            $this->bundles[$name] = $bundle;

            if ($parentName = $bundle->getParent()) {
                if (isset($directChildren[$parentName])) {
                    throw new \LogicException(sprintf('Bundle "%s" is directly extended by two bundles "%s" and "%s".', $parentName, $name, $directChildren[$parentName]));
                }
                if ($parentName == $name) {
                    throw new \LogicException(sprintf('Bundle "%s" can not extend itself.', $name));
                }
                $directChildren[$parentName] = $name;
            } else {
                $topMostBundles[$name] = $bundle;
            }
        }

        // look for orphans
        if (!empty($directChildren) && count($diff = array_diff_key($directChildren, $this->bundles))) {
            $diff = array_keys($diff);

            throw new \LogicException(sprintf('Bundle "%s" extends bundle "%s", which is not registered.', $directChildren[$diff[0]], $diff[0]));
        }

        // inheritance
        $this->bundleMap = array();
        foreach ($topMostBundles as $name => $bundle) {
            $bundleMap = array($bundle);
            $hierarchy = array($name);

            while (isset($directChildren[$name])) {
                $name = $directChildren[$name];
                array_unshift($bundleMap, $this->bundles[$name]);
                $hierarchy[] = $name;
            }

            foreach ($hierarchy as $bundle) {
                $this->bundleMap[$bundle] = $bundleMap;
                array_pop($bundleMap);
            }
        }

    }

    /**
     * Returns the kernel parameters.
     *
     * @return array An array of kernel parameters
     */
    protected function getKernelParameters()
    {
        $bundles = array();
        foreach ($this->bundles as $name => $bundle) {
            $bundles[$name] = get_class($bundle);
        }

        return array_merge(
            array(
                'kernel.root_dir'        => $this->rootDir,
                'kernel.environment'     => $this->environment,
                'kernel.debug'           => $this->debug,
                'kernel.name'            => $this->name,
                'kernel.cache_dir'       => $this->getCacheDir(),
                'kernel.logs_dir'        => $this->getLogDir(),
                'kernel.bundles'         => $bundles,
                'kernel.charset'         => $this->getCharset()
            ),
            $this->getEnvParameters()
        );
    }

    /**
     * Gets the environment parameters.
     *
     * Only the parameters starting with "SYMFONY__" are considered.
     *
     * @return array An array of parameters
     */
    protected function getEnvParameters()
    {
        $parameters = array();
        foreach ($_SERVER as $key => $value) {
            if (0 === strpos($key, 'TOMAHAWK_')) {
                $parameters[strtolower(str_replace('__', '.', substr($key, 9)))] = $value;
            }
        }

        return $parameters;
    }

}