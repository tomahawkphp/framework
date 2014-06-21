<?php

namespace Tomahawk\Html;

use Tomahawk\Html\HtmlBuilder;

class FormBuilder
{
    /**
     * @var \Tomahawk\Html\HtmlBuilder
     */
    protected $html;

    public function __construct(HtmlBuilderInterface $html)
    {
        $this->html = $html;
    }

    /**
     * Open A Form
     *
     * @param $url
     * @param array $attributes
     * @return string
     */
    function open($url, array $attributes = array())
    {
        $attributes['action'] = $url;

        if (!array_key_exists('method', $attributes))
        {
            $attributes['method'] = 'POST';
        }
        
        return sprintf('<form%s>', $this->html->attributes($attributes)) . PHP_EOL;
    }

    /**
     * Open a Form for Uploading Files
     *
     * @param $url
     * @param array $attributes
     * @return string
     */
    function openWithFiles($url, array $attributes = array())
    {
        $attributes['enctype'] = 'multipart/form-data';

        return $this->open($url, $attributes);
    }

    /**
     * Close a Form
     *
     * @return string
     */
    function close()
    {
        return '</form>' . PHP_EOL;
    }
}