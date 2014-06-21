<?php

namespace Tomahawk\Forms;

interface FormsManagerInterface
{

    /**
     * @param $name
     * @param FormInterface $form
     * @return mixed
     */
    public function set($name, FormInterface $form);

    /**
     * @param $name
     * @return mixed
     */
    public function get($name);

    /**
     * @param $name
     * @return bool
     */
    public function has($name);

    /**
     * @return $this
     */
    public function clear();
}