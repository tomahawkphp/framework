<?php

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