<?php
/**
 * Asset Manager
 *
 * @package Tomahawk
 * @author Tom Ellis
 * @version 1.0
 * @since 1.0
 */

namespace Tomahawk\Asset;

interface AssetManagerInterface
{

    /**
     * @param string $name
     * @return AssetContainer
     */
    public function container($name = 'default');

    public function addContainer(AssetContainer $container);
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

    public function outputJs($container = 'default');

    public function outputCss($container = 'default');

}

