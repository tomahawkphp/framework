<?php

namespace Tomahawk\Forms\Test;

use Tomahawk\Forms\Form;
use Tomahawk\Forms\Element\Text;

class TestForm extends Form {


    public function __construct()
    {

        $this->add(new Text('username'));
    }


}