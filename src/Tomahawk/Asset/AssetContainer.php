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

/**
 * Asset Container
 *
 * @author Tom Ellis
 */
class AssetContainer implements AssetContainerInterface
{
    /**
     * The asset container name.
     *
     * @var string
     */
    public $name;

    /**
     * All of the added assets.
     *
     * @var array
     */
    public $assets = array();

    /**
     * Create a new asset container instance.
     *
     * @param string $name
     * @return \Tomahawk\Asset\AssetContainer
     */
    public function __construct($name = 'default')
    {
        $this->name = $name;
    }

    /**
     * Set name of container
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Get name of container
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Add a CSS file to the registered assets.
     *
     * @param  string           $name
     * @param  string           $source
     * @param  array            $dependencies
     * @param  array            $attributes
     * @return AssetContainerInterface
     */
    public function addCss($name, $source, $dependencies = array(), $attributes = array())
    {
        if (!array_key_exists('media', $attributes)) {
            $attributes['media'] = 'all';
        }

        $this->add('css', $name, $source, $dependencies, $attributes);
        return $this;
    }

    /**
     * Add a JavaScript file to the registered assets.
     *
     * @param  string           $name
     * @param  string           $source
     * @param  array            $dependencies
     * @param  array            $attributes
     * @return AssetContainerInterface
     */
    public function addJs($name, $source, $dependencies = array(), $attributes = array())
    {
        $this->add('js', $name, $source, $dependencies, $attributes);
        return $this;
    }

    /**
     * Add an asset to the array of registered assets.
     *
     * @param  string  $type
     * @param  string  $name
     * @param  string  $source
     * @param  array   $dependencies
     * @param  array   $attributes
     * @return $this
     */
    protected function add($type, $name, $source, $dependencies, $attributes)
    {
        $dependencies = (array) $dependencies;

        $attributes = (array) $attributes;

        $this->assets[$type][$name] = compact('source', 'dependencies', 'attributes');
        return $this;
    }

}

