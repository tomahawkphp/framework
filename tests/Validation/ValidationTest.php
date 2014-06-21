<?php

use Tomahawk\Validation\Validator;
use Tomahawk\Validation\Constraints\Required;
use Tomahawk\Validation\Constraints\RequiredWith;
use Tomahawk\Validation\Constraints\MinLength;
use Tomahawk\Validation\Constraints\MaxLength;

class ValidationTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Validator
     */
    protected $validator;

    public function setUp()
    {
        $this->validator = new Validator();
        parent::setUp();
    }

    public function testRequiredConstraint()
    {
        $input = array();

        $this->validator->add('first_name', array(
            new Required()
        ));

        $this->assertFalse($this->validator->validate($input));
    }

}