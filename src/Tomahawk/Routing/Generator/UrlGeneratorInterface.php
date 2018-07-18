<?php

/*
 * This file is part of the TomahawkPHP package.
 *
 * (c) Tom Ellis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tomahawk\Routing\Generator;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface as SymfonyUrlGeneratorInterface;

interface UrlGeneratorInterface extends SymfonyUrlGeneratorInterface
{
    /**
     * @return string
     */
    public function getBaseUrl();

    /**
     * @return mixed|string
     */
    public function getCurrentUrl();

    /**
     * URL to an asset
     *
     * @param $path
     * @param array $extra
     * @param bool $secure
     * @return string
     */
    public function asset($path, array $extra = array(), $secure = false);

    /**
     * @param string $path
     * @param array $extra
     * @param bool $secure
     * @param bool $asset
     * @return mixed|string
     */
    public function to($path = '', array $extra = array(), $secure = false, $asset = false);

    /**
     * @param string $url
     * @param array $extra
     * @param bool $secure
     * @param bool $asset
     * @return string
     */
    public function secureTo($url = '', array $extra = array(), $secure = true, $asset = false);

    /**
     * Determine if the given path is a valid URL.
     *
     * @param $url
     * @return bool
     */
    public function validateUrl($url);

    /**
     * @param $name
     * @param array $data
     * @return mixed|string
     */
    public function route($name, $data = array());
}
