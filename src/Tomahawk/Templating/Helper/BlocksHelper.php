<?php

/*
 * This file is part of the TomahawkPHP package.
 *
 * (c) Tom Ellis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tomahawk\Templating\Helper;

use Symfony\Component\Templating\Helper\Helper;

/**
 * BlocksHelper manages template blocks.
 *
 * @author Tom Elis <tellishtc@gmail.com>
 *
 * @api
 */
class BlocksHelper extends Helper
{
    protected $blocks = array();
    protected $openBlocks = array();
    protected $blockDefaults = array();

    /**
     * Starts a new block.
     *
     * This method starts an output buffer that will be
     * closed when the stop() method is called.
     *
     * @param string $name The block name
     *
     * @throws \InvalidArgumentException if a block with the same name is already started
     *
     * @api
     */
    public function start($name)
    {
        if (in_array($name, $this->openBlocks)) {
            throw new \InvalidArgumentException(sprintf('A block named "%s" is already started.', $name));
        }

        $this->openBlocks[] = $name;
        $this->blocks[$name] = '';

        ob_start();
        ob_implicit_flush(0);
    }

    public function startDefault($name)
    {
        if (in_array($name, $this->openBlocks)) {
            throw new \InvalidArgumentException(sprintf('A block named "%s" is already started.', $name));
        }

        $this->openBlocks[] = $name;
        $this->blockDefaults[$name] = '';

        ob_start();
        ob_implicit_flush(0);
    }

    /**
     * Stops a block.
     *
     * @throws \LogicException if no block has been started
     *
     * @api
     */
    public function stop()
    {
        if (!$this->openBlocks) {
            throw new \LogicException('No block started.');
        }

        $name = array_pop($this->openBlocks);

        $this->blocks[$name] = ob_get_clean();
    }

    public function stopDefault()
    {
        if (!$this->openBlocks) {
            throw new \LogicException('No block started.');
        }

        $name = array_pop($this->openBlocks);

        $this->blockDefaults[$name] = ob_get_clean();
    }
    /**
     * Returns true if the block exists.
     *
     * @param string $name The block name
     *
     * @return Boolean
     *
     * @api
     */
    public function hasDefault($name)
    {
        return isset($this->blockDefaults[$name]);
    }

    /**
     * Gets the block value.
     *
     * @param string         $name    The block name
     * @param Boolean|string $default The default block content
     *
     * @return string The block content
     *
     * @api
     */
    public function getDefault($name, $default = false)
    {
        return isset($this->blockDefaults[$name]) ? $this->blockDefaults[$name] : $default;
    }

    /**
     * Sets a block value.
     *
     * @param string $name    The block name
     * @param string $content The block content
     *
     * @api
     */
    public function setDefault($name, $content)
    {
        $this->blockDefaults[$name] = $content;
    }

    /**
     * Returns true if the block exists.
     *
     * @param string $name The block name
     *
     * @return Boolean
     *
     * @api
     */
    public function has($name)
    {
        return isset($this->blocks[$name]);
    }

    /**
     * Gets the block value.
     *
     * @param string         $name    The block name
     * @param Boolean|string $default The default block content
     *
     * @return string The block content
     *
     * @api
     */
    public function get($name, $default = false)
    {
        return isset($this->blocks[$name]) ? $this->blocks[$name] : $default;
    }

    /**
     * Sets a block value.
     *
     * @param string $name    The block name
     * @param string $content The block content
     *
     * @api
     */
    public function set($name, $content)
    {
        $this->blocks[$name] = $content;
    }

    /**
     * Outputs a block.
     *
     * @param string $name The block name
     *
     * @param bool $default
     * @return Boolean true if the block is defined or if a default content has been provided, false otherwise
     *
     * @api
     */
    public function output($name, $default = false)
    {
        if (isset($this->blocks[$name])) {

            echo $this->blocks[$name];

            return true;
        }

        if (isset($this->blockDefaults[$name])) {

            echo $this->blockDefaults[$name];

            return true;
        }

        if (false !== $default) {
            echo $default;

            return true;
        }

        return false;
    }

    /**
     * Returns the canonical name of this helper.
     *
     * @return string The canonical name
     *
     * @api
     */
    public function getName()
    {
        return 'blocks';
    }
}
