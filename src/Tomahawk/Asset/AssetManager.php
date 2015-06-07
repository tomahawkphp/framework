<?php

/*
 * This file is part of the TomahawkPHP package.
 *
 * (c) Tom Ellis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tomahawk\Asset;

use Tomahawk\Html\HtmlBuilderInterface;
use Tomahawk\Url\UrlGeneratorInterface;
use Tomahawk\Asset\Exception\CircularDependencyException;
use Tomahawk\Asset\Exception\SelfDependencyException;

/**
 * Asset Manager
 *
 * @package Tomahawk
 * @author Tom Ellis
 * @version 1.0
 * @since 1.0
 */

class AssetManager implements AssetManagerInterface
{
    /**
     * All of the instantiated asset containers.
     *
     * @var AssetContainer[]
     */
    protected $containers = array();

    /**
     * @var \Tomahawk\Html\HtmlBuilderInterface
     */
    protected $html;

    /**
     * @var \Tomahawk\Url\UrlGeneratorInterface
     */
    protected $url;

    /**
     * @param HtmlBuilderInterface $html
     * @param UrlGeneratorInterface $url
     */
    public function __construct(HtmlBuilderInterface $html, UrlGeneratorInterface $url = null)
    {
        $this->html = $html;
        $this->url = $url;
    }

    /**
     * Get container, if it doesn't exist create it
     *
     * @param string $name
     * @return AssetContainer
     */
    public function container($name = 'default')
    {
        if (isset($this->containers[$name])) {
            return $this->containers[$name];
        }

        return $this->containers[$name] = new AssetContainer($name);
    }

    /**
     * Add container to manager
     *
     * @param AssetContainer $container
     * @return $this
     */
    public function addContainer(AssetContainer $container)
    {
        $this->containers[$container->name] = $container;
        return $this;
    }

    /**
     * Get all containers
     *
     * @return AssetContainer[]
     */
    public function getContainers()
    {
        return $this->containers;
    }

    /**
     * Add a CSS file to the registered assets.
     *
     * @param  string           $name
     * @param  string           $source
     * @param  array            $dependencies
     * @param  array            $attributes
     * @return AssetContainer
     */
    public function addCss($name, $source, $dependencies = array(), $attributes = array())
    {
        return $this->container()->addCss($name, $source, $dependencies, $attributes);
    }

    /**
     * Add a JavaScript file to the registered assets.
     *
     * @param  string           $name
     * @param  string           $source
     * @param  array            $dependencies
     * @param  array            $attributes
     * @return AssetContainer
     */
    public function addJs($name, $source, $dependencies = array(), $attributes = array())
    {
        return $this->container()->addJs($name, $source, $dependencies, $attributes);
    }

    /**
     * Output a JS asset from the container
     *
     * @param string $container
     * @return string
     */
    public function outputJs($container = 'default')
    {
        $assets = $this->getAssets($container, 'js');

        $html = array();

        foreach ($assets as $asset) {
            $source = $this->url ? $this->url->asset($asset['source']): $asset['source'];
            $html[] = $this->html->script($source, $asset['attributes']);
        }

        return trim(implode(PHP_EOL, $html));
    }

    /**
     * Output a CSS asset from the container
     *
     * @param string $container
     * @return string
     */
    public function outputCss($container = 'default')
    {
        $assets = $this->getAssets($container, 'css');

        $html = array();

        foreach ($assets as $asset)
        {
            $source = $this->url ? $this->url->asset($asset['source']): $asset['source'];
            $html[] = $this->html->style($source, $asset['attributes']);
        }

        return trim(implode(PHP_EOL, $html));
    }

    /**
     * Get all assets of a given type from a given container
     *
     * @param $container
     * @param $type
     * @return array
     */
    protected function getAssets($container, $type)
    {
        if ( ! isset($this->containers[$container])) {
            return array();
        }

        $container = $this->containers[$container];

        if ( ! isset($container->assets[$type]) || 0 === count($container->assets[$type])) {
            return array();
        }

        $assets = $container->assets[$type];

        $assets = $this->arrange($assets);

        return $assets;
    }

    /**
     * Sort and retrieve assets based on their dependencies
     *
     * @param   array  $assets
     * @return  array
     */
    protected function arrange(array $assets)
    {
        list($original, $sorted) = array($assets, array());

        while (count($assets) > 0) {
            foreach ($assets as $asset => $value) {
                $this->evaluateAsset($asset, $value, $original, $sorted, $assets);
            }
        }

        return $sorted;
    }

    /**
     * Evaluate an asset and its dependencies.
     *
     * @param  string  $asset
     * @param  string  $value
     * @param  array   $original
     * @param  array   $sorted
     * @param  array   $assets
     * @return void
     */
    protected function evaluateAsset($asset, $value, $original, &$sorted, &$assets)
    {
        // If the asset has no more dependencies, we can add it to the sorted list
        // and remove it from the array of assets. Otherwise, we will not verify
        // the asset's dependencies and determine if they've been sorted.
        if (count($assets[$asset]['dependencies']) == 0) {
            $sorted[$asset] = $value;

            unset($assets[$asset]);
        }
        else {
            foreach ($assets[$asset]['dependencies'] as $key => $dependency) {
                if ( ! $this->dependencyIsValid($asset, $dependency, $original, $assets)) {
                    unset($assets[$asset]['dependencies'][$key]);
                    continue;
                }

                // If the dependency has not yet been added to the sorted list, we can not
                // remove it from this asset's array of dependencies. We'll try again on
                // the next trip through the loop.
                if ( ! isset($sorted[$dependency])) {
                    continue;
                }

                unset($assets[$asset]['dependencies'][$key]);
            }
        }
    }

    /**
     * Verify that an asset's dependency is valid.
     *
     * A dependency is considered valid if it exists, is not a circular reference, and is
     * not a reference to the owning asset itself. If the dependency doesn't exist, no
     * error or warning will be given. For the other cases, an exception is thrown.
     *
     * @param  string $asset
     * @param  string $dependency
     * @param  array $original
     * @param  array $assets
     * @throws \Exception
     * @return bool
     */
    protected function dependencyIsValid($asset, $dependency, $original, $assets)
    {
        if ( ! isset($original[$dependency])) {
            return false;
        }
        else if ($dependency === $asset) {
            throw new SelfDependencyException("Asset [$asset] is dependent on itself.");
        }
        else if (isset($assets[$dependency]) && in_array($asset, $assets[$dependency]['dependencies'])) {
            throw new CircularDependencyException(sprintf('Assets %s and %s have a circular dependency.', $asset, $dependency));
        }

        return true;
    }
}
