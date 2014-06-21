<?php

namespace Tomahawk\Html;

class HtmlBuilder implements HtmlBuilderInterface
{

    public function entities($value)
    {
        return htmlentities($value, ENT_QUOTES, 'UTF-8', false);
    }

    public function script($url, array $attributes = array())
    {
        return sprintf('<script url="%s"%s></script>', $url, $this->attributes($attributes));
    }

    public function style($url, array $attributes = array())
    {
        return sprintf('<link rel="stylesheet" type="text/css" href="%s"%s>', $url, $this->attributes($attributes));
    }

    public function link($url, $text, array $attributes = array())
    {
        return sprintf('<a href="%s"%s>%s</a>', $url, $this->attributes($attributes), $text);
    }

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
        if (is_numeric($key))
        {
            $key = $value;
        }

        if ( ! is_null($value))
        {
            return $key.'="'.$this->entities($value).'"';
        }
    }
}