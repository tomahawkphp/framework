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

abstract class CheckableElement extends Element implements CheckableInterface
{
    /**
     * @var bool
     */
    protected $checked;

    public function __construct($name, $value = null, $checked = false)
    {
        $this->name = $name;
        $this->value = $value;
        $this->checked = $checked;
    }

    /**
     * @param bool $checked
     * @return $this
     */
    public function setChecked($checked)
    {
        $this->checked = $checked;
        return $this;
    }

    /**
     * @return bool
     */
    public function getChecked()
    {
        return $this->checked;
    }

    /**
     * @return bool
     */
    public function isChecked()
    {
        return $this->getChecked();
    }

}
