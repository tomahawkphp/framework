<?php
/**
 * Asset Container
 *
 * @package Tomahawk
 * @author Tom Ellis
 * @version 1.0
 * @since 1.0
 */
namespace Tomahawk\Asset;

interface AssetContainerInterface
{
    /**
     * @param string $name
     */
    public function setName($name);

    /**
     * @return string
     */
    public function getName();

    /**
     * Add a CSS file to the registered assets.
     *
     * @param  string           $name
     * @param  string           $source
     * @param  array            $dependencies
     * @param  array            $attributes
     * @return AssetContainerInterface
     */
    public function addCss($name, $source, $dependencies = array(), $attributes = array());

    /**
     * Add a JavaScript file to the registered assets.
     *
     * @param  string           $name
     * @param  string           $source
     * @param  array            $dependencies
     * @param  array            $attributes
     * @return AssetContainerInterface
     */
    public function addJs($name, $source, $dependencies = array(), $attributes = array());

}