<?php

/*
 * This file is part of the TomahawkPHP package.
 *
 * (c) Tom Ellis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tomahawk\HttpKernel;

use Tomahawk\DependencyInjection\Container;
use Tomahawk\DependencyInjection\ContainerAwareInterface;
use Tomahawk\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\TerminableInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

abstract class Kernel implements KernelInterface, TerminableInterface
{
    /**
     * @var BundleInterface[]
     */
    protected $bundles = array();

    /**
     * @var BundleInterface[]
     */
    protected $bundleMap;

    /**
     * @var Container
     */
    protected $container;

    /**
     * @var mixed|string
     */
    protected $rootDir;

    /**
     * @var string
     */
    protected $environment;

    /**
     * @var bool
     */
    protected $debug;

    /**
     * @var bool
     */
    protected $booted = false;

    /**
     * @var mixed|string
     */
    protected $name;

    /**
     * @var mixed
     */
    protected $startTime;

    /**
     * @var array
     */
    protected $paths = array();

    /**
     * @var array
     */
    protected $routePaths = array();

    const VERSION         = '2.0.0';
    const VERSION_ID      = '20000';
    const MAJOR_VERSION   = '2';
    const MINOR_VERSION   = '0';
    const RELEASE_VERSION = '0';
    const EXTRA_VERSION   = 'b1';

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
     * Returns an array of bundles to register.
     *
     * @return BundleInterface[] An array of bundle instances.
     *
     * @api
     */
    abstract public function registerBundles();

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

        foreach ($this->getBundles() as $bundle) {
            if ($bundle instanceof ContainerAwareInterface) {
                $bundle->setContainer($this->container);
            }

            $bundle->boot();
        }

        $this->booted = true;

        // Load routes from bundles
        $this->loadRoutes();

        // Register events from Bundles
        $this->registerEvents();
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
     * @param \Tomahawk\DependencyInjection\ContainerInterface $container
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
        return $this->rootDir.'/storage/cache';
    }

    /**
     * {@inheritdoc}
     *
     * @api
     */
    public function getLogDir()
    {
        return $this->rootDir.'/storage/logs';
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
        $this->container = new Container();
        $this->container['kernel'] = $this;

        $this->getKernelParameters();
    }

    /**
     * Get Bundle Route Directories
     *
     * @return array
     */
    public function getRoutePaths()
    {
        return $this->routePaths;
    }

    /**
     * Set Bundle Route Directories
     *
     * @param array $routePaths
     * @return $this
     */
    public function setRoutePaths($routePaths)
    {
        $this->routePaths = $routePaths;
        return $this;
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
        return isset($this->paths[$path]) ?  $this->paths[$path] : null;
    }

    public function getBundles()
    {
        return $this->bundles;
    }

    /**
     * @param string $name
     * @param bool $first
     * @return BundleInterface|Bundle\BundleInterface[]
     * @throws \InvalidArgumentException
     */
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

    /**
     * Returns the file path for a given resource.
     *
     * A Resource can be a file or a directory.
     *
     * The resource name must follow the following pattern:
     *
     *     @<BundleName>/path/to/a/file.something
     *
     * where BundleName is the name of the bundle
     * and the remaining part is the relative path in the bundle.
     *
     * If $dir is passed, and the first segment of the path is "Resources",
     * this method will look for a file named:
     *
     *     $dir/<BundleName>/path/without/Resources
     *
     * before looking in the bundle resource folder.
     *
     * @param string  $name  A resource name to locate
     * @param string  $dir   A directory where to look for the resource first
     * @param bool    $first Whether to return the first path or paths for all matching bundles
     *
     * @return string|array The absolute path of the resource or an array if $first is false
     *
     * @throws \InvalidArgumentException if the file cannot be found or the name is not valid
     * @throws \RuntimeException         if the name contains invalid/unsafe
     * @throws \RuntimeException         if a custom resource is hidden by a resource in a derived bundle
     *
     * @api
     */
    public function locateResource($name, $dir = null, $first = true)
    {
        if ('@' !== $name[0]) {
            throw new \InvalidArgumentException(sprintf('A resource name must start with @ ("%s" given).', $name));
        }

        if (false !== strpos($name, '..')) {
            throw new \RuntimeException(sprintf('File name "%s" contains invalid characters (..).', $name));
        }

        $bundleName = substr($name, 1);
        $path = '';
        if (false !== strpos($bundleName, '/')) {
            list($bundleName, $path) = explode('/', $bundleName, 2);
        }

        $isResource = 0 === strpos($path, 'Resources') && null !== $dir;
        $overridePath = substr($path, 9);
        $resourceBundle = null;
        $bundles = $this->getBundle($bundleName, false);
        $files = array();

        foreach ($bundles as $bundle) {
            if ($isResource && file_exists($file = $dir.'/'.$bundle->getName().$overridePath)) {
                if (null !== $resourceBundle) {
                    throw new \RuntimeException(sprintf('"%s" resource is hidden by a resource from the "%s" derived bundle. Create a "%s" file to override the bundle resource.',
                        $file,
                        $resourceBundle,
                        $dir.'/'.$bundles[0]->getName().$overridePath
                    ));
                }

                if ($first) {
                    return $file;
                }
                $files[] = $file;
            }

            if (file_exists($file = $bundle->getPath().'/'.$path)) {
                if ($first && !$isResource) {
                    return $file;
                }
                $files[] = $file;
                $resourceBundle = $bundle->getName();
            }
        }

        if (count($files) > 0) {
            return $first && $isResource ? $files[0] : $files;
        }

        throw new \InvalidArgumentException(sprintf('Unable to find file "%s".', $name));
    }

    /**
     * @return EventDispatcherInterface
     */
    protected function getEventDispatcher()
    {
        if (!$this->container) {
            return null;
        }
        return $this->container->get('event_dispatcher');
    }

    protected function loadRoutes()
    {
        foreach ($this->getBundles() as $bundle) {

            // Add Bundle Routes
            if ($path = $bundle->getRoutePath()) {
                $this->routePaths[] = $path;
            }
        }
    }

    protected function registerEvents()
    {
        $eventDispatcher = $this->getEventDispatcher();

        if (!$eventDispatcher) {
            return;
        }

        // Add Routes and Register Event Listeners
        foreach ($this->getBundles() as $bundle) {

            // Register Events
            $bundle->registerEvents($eventDispatcher);
        }
    }

}
