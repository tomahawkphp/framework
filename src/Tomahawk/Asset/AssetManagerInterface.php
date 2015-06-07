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

interface AssetManagerInterface
{
    /**
     * Get container, if it doesn't exist create it
     *
     * @param string $name
     * @return AssetContainer
     */
    public function container($name = 'default');

    /**
     * Add container to manager
     *
     * @param AssetContainer $container
     * @return $this
     */
    public function addContainer(AssetContainer $container);

    /**
     * Get all containers
     *
     * @return AssetContainer[]
     */
    public function getContainers();

    /**
     * Add a CSS file to the registered assets.
     *
     * @param  string           $name
     * @param  string           $source
     * @param  array            $dependencies
     * @param  array            $attributes
     * @return AssetContainer
     */
    public function addCss($name, $source, $dependencies = array(), $attributes = array());

    /**
     * Add a JavaScript file to the registered assets.
     *
     * @param  string           $name
     * @param  string           $source
     * @param  array            $dependencies
     * @param  array            $attributes
     * @return AssetContainer
     */
    public function addJs($name, $source, $dependencies = array(), $attributes = array());

    /**
     * Output a JS asset from the container
     *
     * @param string $container
     * @return string
     */
    public function outputJs($container = 'default');

    /**
     * Output a CSS asset from the container
     *
     * @param string $container
     * @return string
     */
    public function outputCss($container = 'default');
}
