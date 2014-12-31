<?php

use Tomahawk\Auth\Handlers\DatabaseAuthHandler;
use Tomahawk\Auth\User;

class DatabaseAuthHandlerTest extends PHPUnit_Framework_TestCase
{
    public function testRetrieveByIDReturnsNull()
    {
        $queryBuilder = $this->getQueryBuilder();

        $connection = $this->getConnection();

        $connection->expects($this->any())
            ->method('table')
            ->will($this->returnValue($queryBuilder));

        $queryBuilder->expects($this->any())
            ->method('where')
            ->will($this->returnSelf());

        $queryBuilder->expects($this->any())
            ->method('first')
            ->will($this->returnValue(null));

        $handler = new DatabaseAuthHandler($this->getHasher(), $connection, 'users', 'id', 'password');

        $this->assertEquals(null, $handler->retrieveById(1));
    }

    public function testRetrieveByIDReturnsUser()
    {
        $queryBuilder = $this->getQueryBuilder();

        $connection = $this->getConnection();

        $connection->expects($this->any())
            ->method('table')
            ->will($this->returnValue($queryBuilder));

        $queryBuilder->expects($this->any())
            ->method('where')
            ->will($this->returnSelf());

        $queryBuilder->expects($this->any())
            ->method('first')
            ->will($this->returnValue(array(
                'username' => 'tomgrohl'
            )));

        $handler = new DatabaseAuthHandler($this->getHasher(), $connection, 'users', 'id', 'password');

        $this->assertInstanceOf('Tomahawk\Auth\UserInterface', $handler->retrieveById(1));
    }

    public function testRetrieveByCredentialsReturnsNull()
    {
        $queryBuilder = $this->getQueryBuilder();

        $connection = $this->getConnection();

        $connection->expects($this->any())
            ->method('table')
            ->will($this->returnValue($queryBuilder));

        $queryBuilder->expects($this->any())
            ->method('where')
            ->will($this->returnSelf());

        $queryBuilder->expects($this->any())
            ->method('first')
            ->will($this->returnValue(null));

        $handler = new DatabaseAuthHandler($this->getHasher(), $connection, 'users', 'id', 'password');

        $this->assertEquals(null, $handler->retrieveByCredentials(array(
            'username' => 'tomgrohl',
            'password' => 'password'
        )));
    }

    public function testRetrieveByCredentialsReturnsUser()
    {
        $queryBuilder = $this->getQueryBuilder();

        $connection = $this->getConnection();

        $connection->expects($this->any())
            ->method('table')
            ->will($this->returnValue($queryBuilder));

        $queryBuilder->expects($this->any())
            ->method('where')
            ->will($this->returnSelf());

        $queryBuilder->expects($this->any())
            ->method('first')
            ->will($this->returnValue(array(
                'username' => 'tomgrohl',
                'password' => 'sdonfosdf'
            )));

        $handler = new DatabaseAuthHandler($this->getHasher(), $connection, 'users', 'id', 'password');

        $this->assertInstanceOf('Tomahawk\Auth\UserInterface', $handler->retrieveByCredentials(array(
            'username' => 'tomgrohl',
            'password' => 'password'
        )));
    }

    public function testValidateCredentialsReturnsTrue()
    {
        $user = $this->getUser();

        $user->expects($this->once())
            ->method('getAuthPassword')
            ->will($this->returnValue('sdbfsgssd'));

        $connection = $this->getConnection();

        $hasher = $this->getHasher();

        $hasher->expects($this->once())
            ->method('check')
            ->will($this->returnValue(true));

        $handler = new DatabaseAuthHandler($hasher, $connection, 'users', 'id', 'password');

        $this->assertTrue($handler->validateCredentials($user, array(
            'username' => 'tomgrohl',
            'password' => 'password'
        )));
    }

    public function testValidateCredentialsReturnsFalse()
    {
        $user = $this->getUser();

        $user->expects($this->once())
            ->method('getAuthPassword')
            ->will($this->returnValue('sdbfsgssd'));

        $connection = $this->getConnection();

        $hasher = $this->getHasher();

        $hasher->expects($this->once())
            ->method('check')
            ->will($this->returnValue(false));

        $handler = new DatabaseAuthHandler($hasher, $connection, 'users', 'id', 'password');

        $this->assertFalse($handler->validateCredentials($user, array(
            'username' => 'tomgrohl',
            'password' => 'password'
        )));
    }

    public function testAuthUser()
    {
        $user = new User();

        $user->setPrimaryKey('id');

        $user->id = 13;
        $user->setPasswordField('password_column');
        $user->password_column = 'mypassword';

        $this->assertEquals('id', $user->getPrimaryKey());
        $this->assertEquals(13, $user->getAuthIdentifier());

        $this->assertEquals('password_column', $user->getPasswordField());
        $this->assertEquals('mypassword', $user->getAuthPassword());
        $this->assertEquals('mypassword', $user->password_column);
        $this->assertTrue(isset($user->password_column));

        unset($user->password_column);

        $this->assertFalse(isset($user->password_column));
    }

    protected function getConnection()
    {
        return $this->getMockBuilder('Illuminate\Database\Connection')
            ->disableOriginalConstructor()
            ->setMethods( array(
                'table'
            ))
            ->getMock();
    }

    protected function getQueryBuilder()
    {
        return $this->getMockBuilder('Illuminate\Database\Query\Builder')
            ->disableOriginalConstructor()
            ->setMethods( array(
                'insert',
                'orderBy',
                'where',
                'delete',
                'get',
                'lists',
                'max',
                'first'
            ))
            ->getMock();
    }

    protected function getUser()
    {
        return $this->getMock('Tomahawk\Auth\UserInterface');
    }

    protected function getHasher()
    {
        return $this->getMock('Tomahawk\Hashing\HasherInterface');
    }

}
