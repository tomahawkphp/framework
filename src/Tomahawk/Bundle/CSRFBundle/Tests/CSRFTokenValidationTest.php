<?php

namespace Tomahawk\Bundle\CSRFBundle\Test;

use Tomahawk\Bundle\CSRFBundle\Token\TokenManager;
use Tomahawk\Bundle\CSRFBundle\Validation\CSRFToken;
use PHPUnit\Framework\TestCase;
use Tomahawk\Validation\Validator;

class CSRFTokenValidationTest extends TestCase
{
    public function testConstraint()
    {
        $validator = new Validator();
        $validator->add('token', array(
            new CSRFToken($this->getTokenManager())
        ));

        $input = array(
            'token' =>  'atoken'
        );

        $this->assertTrue($validator->validate($input));

        $input = array(
            'token' =>  'btoken'
        );

        $this->assertFalse($validator->validate($input));
        $errors = $validator->getMessagesFor('token');

        $this->assertEquals('Invalid security token', $errors[0]->getMessage());

        $input = array();

        $this->assertFalse($validator->validate($input));
        $errors = $validator->getMessagesFor('token');

        $this->assertEquals('Invalid security token', $errors[0]->getMessage());
    }

    protected function getTokenManager()
    {
        $tokenManager = $this->getMockBuilder('Tomahawk\Bundle\CSRFBundle\Token\TokenManagerInterface')->getMock();

        $tokenManager->expects($this->any())
            ->method('getToken')
            ->will($this->returnValue('atoken'));

        return $tokenManager;
    }
}
