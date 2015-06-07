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

/**
 * Class HtmlBuilder
 *
 * @package Tomahawk\Html
 */
class HtmlBuilder implements HtmlBuilderInterface
{

    /**
     * @param $value
     * @return string
     */
    public function entities($value)
    {
        return htmlentities($value, ENT_QUOTES, 'UTF-8', false);
    }

    /**
     * @param $url
     * @param array $attributes
     * @return string
     */
    public function script($url, array $attributes = array())
    {
        return sprintf('<script src="%s"%s></script>', $url, $this->attributes($attributes));
    }

    /**
     * @param $url
     * @param array $attributes
     * @return string
     */
    public function style($url, array $attributes = array())
    {
        return sprintf('<link rel="stylesheet" type="text/css" href="%s"%s>', $url, $this->attributes($attributes));
    }

    /**
     * @param $url
     * @param $text
     * @param array $attributes
     * @return string
     */
    public function link($url, $text, array $attributes = array())
    {
        return sprintf('<a href="%s"%s>%s</a>', $url, $this->attributes($attributes), $text);
    }

    /**
     * @param array $attributes
     * @return string
     */
    public function attributes(array $attributes)
    {
        $html = array();

        // For numeric keys we will assume that the key and the value are the same
        // as this will convert HTML attributes such as "required" to a correct
        // form like required="required" instead of using incorrect numerics.
        foreach ($attributes as $key => $value) {
            $element = $this->attributeElement($key, $value);

            if ( ! is_null($element)) {
                $html[] = $element;
            }
        }

        return count($html) > 0 ? ' '.implode(' ', $html) : '';
    }

    /**
     * Build a single attribute element.
     *
     * @param  string  $key
     * @param  string  $value
     * @return string
     */
    public function attributeElement($key, $value)
    {
        if (is_numeric($key)) {
            $key = $value;
        }

        if (!is_null($value)) {
            return $key.'="'.$this->entities($value).'"';
        }
    }
}
