<?php

/*
 * This file is part of the TomahawkPHP package.
 *
 * (c) Tom Ellis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tomahawk\Forms\Element;

class TextArea extends Element
{
    public function __construct($name, $value = null)
    {
        $this->name = $name;
    }

    /**
     * @param array $attributes
     * @return mixed
     */
    public function render(array $attributes = array())
    {
        $current_attributes = array(
            'name'  => $this->getName(),
        );

        $attributes = array_merge($current_attributes, $attributes);

        return sprintf('<textarea%s>%s</textarea>', $this->attributes($attributes), $this->entities($this->getValue()));
    }
}
