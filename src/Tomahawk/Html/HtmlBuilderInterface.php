<?php

/*
 * This file is part of the TomahawkPHP package.
 *
 * (c) Tom Ellis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tomahawk\Html;

interface HtmlBuilderInterface
{
    /**
     * @param $value
     * @return mixed
     */
    function entities($value);

    public function script($url, array $attributes = array());

    public function style($url, array $attributes = array());

    /**
     * @param $url
     * @param $text
     * @param array $attributes
     * @return mixed
     */
    function link($url, $text, array $attributes = array());

    /**
     * @param array $attributes
     * @return mixed
     */
    function attributes(array $attributes);

    /**
     * @param $key
     * @param $value
     * @return mixed
     */
    function attributeElement($key, $value);
}
