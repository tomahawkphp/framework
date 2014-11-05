<?php

namespace Tomahawk\Bundle\GeneratorBundle\Tests;

use Tomahawk\Test\TestCase;
use Tomahawk\Bundle\GeneratorBundle\Command\Validators;

class ValidatorsTest extends TestCase
{
    public function testValidateBundleNamespaceReturnsNamespace()
    {
        $namespace = 'Foo\\FooBundle';
        $this->assertEquals($namespace, Validators::validateBundleNamespace($namespace));
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage The namespace must end with Bundle.
     */
    public function testValidateBundleNamespaceThrowsExceptionWithEnding()
    {
        Validators::validateBundleNamespace('Foo');
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage The namespace contains invalid characters.
     */
    public function testValidateBundleNamespaceThrowsExceptionWithInvalidCharacters()
    {
        Validators::validateBundleNamespace('Foo_?Bundle');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testValidateBundleNamespaceThrowsExceptionWithoutEndingSlash()
    {
        Validators::validateBundleNamespace('FooBundle');
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage The namespace cannot contain PHP reserved words ("Abstract").
     */
    public function testValidateBundleNamespaceThrowsExceptionWithReservedWord()
    {
        Validators::validateBundleNamespace('Foo\\Abstract\\FooBundle');
    }

    public function testValidateBundleNameReturnsName()
    {
        $name = 'FooBundle';
        $this->assertEquals($name, Validators::validateBundleName($name));
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage The bundle name must end with Bundle.
     */
    public function testValidateBundleNameThrowsExceptionWithEnding()
    {
        Validators::validateBundleName('Foo');
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage The bundle name contains invalid characters.
     */
    public function testValidateBundleNameThrowsExceptionWithInvalidCharacters()
    {
        Validators::validateBundleName('Foo_?');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testValidateControllerThrowsException()
    {
        $name = 'UserController';
        Validators::validateControllerName($name);
    }

    public function testValidateControllerReturnsName()
    {
        $name = 'UserBundle:UserController';
        $this->assertEquals($name, Validators::validateControllerName($name));
    }

    public function testValidateTargetDirReturnWithSlash()
    {
        $this->assertEquals('dir/', Validators::validateTargetDir('dir', null, null));
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testValidateFormatThrowsException()
    {
        Validators::validateFormat('foo');
    }

    public function testValidateFormatReturnsFormat()
    {
        $format = 'yml';
        $this->assertEquals($format, Validators::validateFormat($format));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testValidateEntityNameThrowsException()
    {
        Validators::validateEntityName('User');
    }

    public function testValidateEntityNameReturnsName()
    {
        $name = 'UserBundle:User';
        $this->assertEquals($name, Validators::validateEntityName($name));
    }
    
    public function testGetReservedWordsReturnsArray()
    {
        $this->assertTrue(is_array(Validators::getReservedWords()));
    }
}
