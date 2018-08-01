<?php

namespace Tomahawk\Security\Csrf\Test;

use Tomahawk\Security\Csrf\Token\TokenManagerInterface;
use Tomahawk\Security\Csrf\Validation\CsrfToken;
use PHPUnit\Framework\TestCase;
use Tomahawk\Validation\Validator;

class CsrfTokenValidationTest extends TestCase
{
    public function testConstraint()
    {
        $validator = new Validator();
        $validator->add('token', array(
            new CsrfToken($this->getTokenManager())
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
        $tokenManager = $this->getMockBuilder(TokenManagerInterface::class)->getMock();

        $tokenManager->expects($this->any())
            ->method('getToken')
            ->will($this->returnValue('atoken'));

        return $tokenManager;
    }
}
