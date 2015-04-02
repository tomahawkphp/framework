<?php

/*
 * This file is part of the TomahawkPHP package.
 *
 * (c) Tom Ellis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tomahawk\Templating\Twig\Extension;

use Tomahawk\Url\UrlGenerator;
use Tomahawk\Url\UrlGeneratorInterface;

class UrlExtension extends \Twig_Extension
{
    /**
     * @var UrlGeneratorInterface
     */
    protected $urlGenerator;

    /**
     * Construct
     *
     * @param UrlGeneratorInterface $urlGenerator
     */
    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }


    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return array An array of functions
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('base_url', array($this, 'getBaseUrl')),
            new \Twig_SimpleFunction('current_url', array($this, 'getCurrentUrl')),
            new \Twig_SimpleFunction('asset_url', array($this, 'asset')),
            new \Twig_SimpleFunction('url_to', array($this, 'to')),
            new \Twig_SimpleFunction('secure_url_to', array($this, 'secureTo')),
            new \Twig_SimpleFunction('route', array($this, 'route')),
        );
    }

    /**
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->urlGenerator->getBaseUrl();
    }

    /**
     * @return mixed|string
     */
    public function getCurrentUrl()
    {
        return $this->urlGenerator->getCurrentUrl();
    }

    /**
     * URL to an asset
     *
     * @param $path
     * @param array $extra
     * @param bool $secure
     * @return string
     */
    public function asset($path, array $extra = array(), $secure = false)
    {
        return $this->urlGenerator->asset($path, $extra, $secure, true);
    }

    /**
     * @param string $path
     * @param array $extra
     * @param bool $secure
     * @param bool $asset
     * @return mixed|string
     */
    public function to($path = '', array $extra = array(), $secure = false, $asset = false)
    {
        return $this->urlGenerator->to($path, $extra, $secure, $asset);
    }

    /**
     * @param string $url
     * @param array $extra
     * @param bool $secure
     * @param bool $asset
     * @return string
     */
    public function secureTo($url = '', array $extra = array(), $secure = true, $asset = false)
    {
        return $this->urlGenerator->secureTo($url, $extra, $secure, $asset);
    }

    /**
     * @param $name
     * @param array $data
     * @return mixed|string
     */
    public function route($name, $data = array())
    {
        return $this->urlGenerator->route($name, $data, UrlGenerator::ABSOLUTE_URL);
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'url';
    }
}
