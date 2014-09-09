<?php

namespace Tomahawk\Url;

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
     * @param string $path
     * @param array $extra
     * @param bool $secure
     * @return mixed|string
     */
    public function to($path = '', array $extra = array(), $secure = false);

    /**
     * @param string $url
     * @param array $extra
     * @param bool $secure
     * @return string
     */
    public function secureTo($url = '', array $extra = array(), $secure = true);

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
