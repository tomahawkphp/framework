<?php

namespace Tomahawk\Forms;

class FormsManager implements FormsManagerInterface
{
    /**
     * @var Form array
     */
    protected $forms = array();

    /**
     * @param $name
     * @param FormInterface $form
     * @return $this
     */
    public function set($name, FormInterface $form)
    {
        $this->forms[$name] = $form;

        return $this;
    }

    /**
     * @param $name
     * @return mixed
     */
    public function get($name)
    {
        return $this->forms[$name];
    }

    /**
     * @param $name
     * @return bool
     */
    public function has($name)
    {
        return isset($this->forms[$name]);
    }

    /**
     * @return $this
     */
    public function clear()
    {
        $this->forms = array();

        return $this;
    }

    /**
     * @return array
     */
    public function getAll()
    {
        return $this->forms;
    }

}
