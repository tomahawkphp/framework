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

interface AssetContainerInterface
{
    /**
     * Set name of container
     *
     * @param string $name
     * @return $this
     */
    public function setName($name);

    /**
     * Get name of container
     *
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
     * @return $this
     */
    public function addCss($name, $source, $dependencies = array(), $attributes = array());

    /**
     * Add a JavaScript file to the registered assets.
     *
     * @param  string           $name
     * @param  string           $source
     * @param  array            $dependencies
     * @param  array            $attributes
     * @return $this
     */
    public function addJs($name, $source, $dependencies = array(), $attributes = array());

}
