<?php

namespace Tomahawk\Html;

interface FormBuilderInterface
{
    /**
     * Open A Form
     *
     * @param $url
     * @param array $attributes
     * @return string
     */
    function open($url, array $attributes = array());

    /**
     * Open a Form for Uploading Files
     *
     * @param $url
     * @param array $attributes
     * @return string
     */
    function openWithFiles($url, array $attributes = array());

    /**
     * Close a Form
     *
     * @return string
     */
    function close();
}