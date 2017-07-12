<?php

namespace Tomahawk\Validation\Tests;

use PHPUnit_Framework_TestCase as TestCase;
use Tomahawk\Validation\Constraints\Alpha;
use Tomahawk\Validation\Constraints\AlphaDash;
use Tomahawk\Validation\Constraints\AlphaNumeric;
use Tomahawk\Validation\Constraints\DateFormat;
use Tomahawk\Validation\Constraints\DigitsBetween;
use Tomahawk\Validation\Constraints\Image;
use Tomahawk\Validation\Constraints\In;
use Tomahawk\Validation\Constraints\IPAddress;
use Tomahawk\Validation\Constraints\MimeTypes;
use Tomahawk\Validation\Constraints\NotIn;
use Tomahawk\Validation\Constraints\RequiredWithout;
use Tomahawk\Validation\Constraints\TimeZone;
use Tomahawk\Validation\Constraints\URL;
use Tomahawk\Validation\Validator;
use Tomahawk\Validation\Constraints\Email;
use Tomahawk\Validation\Constraints\Required;
use Tomahawk\Validation\Constraints\RequiredWith;
use Tomahawk\Validation\Constraints\Identical;
use Tomahawk\Validation\Constraints\StringLength;
use Tomahawk\Validation\Constraints\Numerical;
use Tomahawk\Validation\Constraints\Integer;
use Tomahawk\Validation\Constraints\MinLength;
use Tomahawk\Validation\Constraints\MaxLength;
use Tomahawk\Validation\Constraints\Regex;
use Tomahawk\Validation\Message;

use Symfony\Component\Translation\Translator;
use Symfony\Component\Translation\Loader\ArrayLoader;

class ValidationTest extends TestCase
{
    /**
     * @var Validator
     */
    protected $validator;

    /**
     * @var Translator
     */
    protected $translator;

    public function setUp()
    {
        $this->translator = new Translator('fr_FR');
        $this->translator->addLoader('array', new ArrayLoader());
        $this->translator->addResource('array', array(
            'The value is not a valid number' => 'La valeur n\'est pas un nombre valide',
            'The minimum length is %min_length%' => 'La longueur minimale est de %min_length%'
        ), 'fr_FR');
        $this->validator = new Validator();
        parent::setUp();
    }

    public function testSingle()
    {
        $this->validator->add('first_name', new Required());
        $input = array();
        $this->assertFalse($this->validator->validate($input));
    }

    public function testRequired()
    {
        $input = array();

        $this->validator->add('first_name', array(
            new Required()
        ));

        $this->assertFalse($this->validator->validate($input));
        /**
         * @var Message[] $errors
         */
        $errors = $this->validator->getMessagesFor('first_name');
        $this->assertEquals('The field is required', $errors[0]->getMessage());
        $this->assertCount(1, $this->validator->getMessages());

        $input = array(
            'first_name' => 'Tom'
        );

        $this->assertTrue($this->validator->validate($input));
        $this->assertCount(0, $this->validator->getMessages());
    }

    public function testRequiredWithArray()
    {
        $input = array(
            'things' => array()
        );

        $this->validator->add('things', array(
            new Required()
        ));


        $this->assertFalse($this->validator->validate($input));
        /**
         * @var Message[] $errors
         */
        $errors = $this->validator->getMessagesFor('things');
        $this->assertEquals('The field is required', $errors[0]->getMessage());
        $this->assertCount(1, $this->validator->getMessages());

        $input = array(
            'things' => array(1)
        );

        $this->assertTrue($this->validator->validate($input));
        $this->assertCount(0, $this->validator->getMessages());
    }

    public function testRequiredWithFiles()
    {
        $input = array(
            'file' => $this->getUploadedFile()
        );

        $this->validator->add('file', array(
            new Required()
        ));

        $this->assertTrue($this->validator->validate($input));

        $input2 = array(
            'file' => $this->getUploadedFile(false)
        );

        $this->assertFalse($this->validator->validate($input2));

        /**
         * @var Message[] $errors
         */
        $errors = $this->validator->getMessagesFor('file');
        $this->assertEquals('The field is required', $errors[0]->getMessage());
    }

    public function testRequiredWithTranslation()
    {
        $this->validator->setTranslator($this->translator);

        $input = array();

        $this->validator->add('first_name', array(
            new Required()
        ));


        $this->assertFalse($this->validator->validate($input));
        /**
         * @var Message[] $errors
         */
        $errors = $this->validator->getMessagesFor('first_name');
        $this->assertEquals('The field is required', $errors[0]->getMessage());
        $this->assertCount(1, $this->validator->getMessages());

        $input = array(
            'first_name' => 'Tom'
        );

        $this->assertTrue($this->validator->validate($input));
        $this->assertCount(0, $this->validator->getMessages());
    }

    public function testRequiredWith()
    {
        $this->validator->add('last_name', array(
            new RequiredWith(array(
                    'with' => 'first_name',
                    'with_name' => 'First Name'
                ))
        ));

        $input = array(
            'first_name' => '',
            'last_name' => '',
        );


        $this->assertTrue($this->validator->validate($input));

        $input2 = array(
            'first_name' => 'Tom',
            'last_name' => '',
        );

        $this->assertFalse($this->validator->validate($input2));
        /**
         * @var Message[] $errors
         */
        $errors = $this->validator->getMessagesFor('last_name');
        $this->assertEquals('The field is required with First Name', $errors[0]->getMessage());
    }

    public function testRequiredWithout()
    {
        $this->validator->add('achoice', array(
            new RequiredWithout(array(
                'without' => 'bchoice',
            ))
        ));

        $input = array(
            'achoice' => 'foo',
        );


        $this->assertTrue($this->validator->validate($input));

        $input2 = array(
            'achoice' => 'foo',
            'bchoice' => '',
        );

        $this->assertTrue($this->validator->validate($input2));

        $input3 = array(
            'achoice' => '',
        );

        $this->assertFalse($this->validator->validate($input3));
        /**
         * @var Message[] $errors
         */
        $errors = $this->validator->getMessagesFor('achoice');
        $this->assertEquals('The field is required', $errors[0]->getMessage());
    }

    public function testRequiredWithWithTranslator()
    {
        $this->validator->setTranslator($this->translator);

        $this->validator->add('last_name', array(
            new RequiredWith(array(
                'with' => 'first_name',
                'with_name' => 'First Name'
            ))
        ));

        $input = array(
            'first_name' => '',
            'last_name' => '',
        );


        $this->assertTrue($this->validator->validate($input));

        $input2 = array(
            'first_name' => 'Tom',
            'last_name' => '',
        );

        $this->assertFalse($this->validator->validate($input2));
        /**
         * @var Message[] $errors
         */
        $errors = $this->validator->getMessagesFor('last_name');
        $this->assertEquals('The field is required with First Name', $errors[0]->getMessage());


    }

    public function testIdentical()
    {
        $input = array(
            'password' => 'password123',
            'password_confirmation' => 'password123'
        );

        $this->validator->add('password', array(
            new Identical(array(
                'with' => 'password_confirmation',
                'with_name' => 'Password Confirmation'
            ))
        ));

        $this->assertTrue($this->validator->validate($input));

        $input = array(
            'password' => 'password123',
            'password_confirmation' => 'password12'
        );

        $this->assertFalse($this->validator->validate($input));

        /**
         * @var Message[] $errors
         */
        $errors = $this->validator->getMessagesFor('password');
        $this->assertEquals('The field is doesn\'t match with Password Confirmation', $errors[0]->getMessage());
    }

    public function testIdenticalWithTranslator()
    {
        $this->validator->setTranslator($this->translator);


        $input = array(
            'password' => 'password123',
            'password_confirmation' => 'password123'
        );

        $this->validator->add('password', array(
            new Identical(array(
                'with' => 'password_confirmation',
                'with_name' => 'Password Confirmation'
            ))
        ));

        $this->assertTrue($this->validator->validate($input));

        $input = array(
            'password' => 'password123',
            'password_confirmation' => 'password12'
        );

        $this->assertFalse($this->validator->validate($input));

        /**
         * @var Message[] $errors
         */
        $errors = $this->validator->getMessagesFor('password');
        $this->assertEquals('The field is doesn\'t match with Password Confirmation', $errors[0]->getMessage());
    }

    public function testStringLength()
    {
        $input = array(
            'username' => 'tom'
        );

        $this->validator->add('username', array(
            new StringLength(array(
                'min_length' => 5,
                'max_length' => 15
            ))
        ));

        $this->assertFalse($this->validator->validate($input));
        $this->assertCount(1, $this->validator->getMessages());

        /**
         * @var Message[] $errors
         */
        $errors = $this->validator->getMessagesFor('username');
        $this->assertEquals('The minimum length is 5', $errors[0]->getMessage());


        $input2 = array(
            'username' => 'thomasmichaeleellis'
        );

        $this->assertFalse($this->validator->validate($input2));
        $this->assertCount(1, $this->validator->getMessages());


        /**
         * @var Message[] $errors
         */
        $errors = $this->validator->getMessagesFor('username');

        $this->assertEquals('The maximum length is 15', $errors[0]->getMessage());


        $input = array(
            'username' => 'tomgrohl'
        );

        $this->assertTrue($this->validator->validate($input));
        $this->assertCount(0, $this->validator->getMessages());
    }

    public function testStringLengthWithTranslator()
    {
        $this->validator->setTranslator($this->translator);

        $input = array(
            'username' => 'tom'
        );

        $this->validator->add('username', array(
            new StringLength(array(
                'min_length' => 5,
                'max_length' => 15
            ))
        ));

        $this->assertFalse($this->validator->validate($input));
        $this->assertCount(1, $this->validator->getMessages());

        /**
         * @var Message[] $errors
         */
        $errors = $this->validator->getMessagesFor('username');
        $this->assertEquals('La longueur minimale est de 5', $errors[0]->getMessage());


        $input2 = array(
            'username' => 'thomasmichaeleellis'
        );

        $this->assertFalse($this->validator->validate($input2));
        $this->assertCount(1, $this->validator->getMessages());


        /**
         * @var Message[] $errors
         */
        $errors = $this->validator->getMessagesFor('username');

        $this->assertEquals('The maximum length is 15', $errors[0]->getMessage());


        $input = array(
            'username' => 'tomgrohl'
        );

        $this->assertTrue($this->validator->validate($input));
        $this->assertCount(0, $this->validator->getMessages());
    }

    public function testNumerical()
    {
        $this->validator->add('number', array(
            new Numerical()
        ));

        $input = array(
            'number' => '11'
        );

        $this->assertTrue($this->validator->validate($input));

        $input = array(
            'number' => 11
        );

        $this->assertTrue($this->validator->validate($input));

        $input = array(
            'number' => 1e2
        );

        $this->assertTrue($this->validator->validate($input));



        $input = array(
            'number' => 'not numeric'
        );

        $this->assertFalse($this->validator->validate($input));


        /**
         * @var Message[] $errors
         */
        $errors = $this->validator->getMessagesFor('number');

        $this->assertEquals('The value is not a valid number', $errors[0]->getMessage());
    }

    public function testNumericWithTranslator()
    {
        $this->validator->setTranslator($this->translator);


        $this->validator->add('number', array(
            new Numerical()
        ));

        $input = array(
            'number' => '11'
        );

        $this->assertTrue($this->validator->validate($input));

        $input = array(
            'number' => 11
        );

        $this->assertTrue($this->validator->validate($input));

        $input = array(
            'number' => 1e2
        );

        $this->assertTrue($this->validator->validate($input));



        $input = array(
            'number' => 'not numeric'
        );

        $this->assertFalse($this->validator->validate($input));


        /**
         * @var Message[] $errors
         */
        $errors = $this->validator->getMessagesFor('number');

        $this->assertEquals('La valeur n\'est pas un nombre valide', $errors[0]->getMessage());
    }

    public function testInteger()
    {
        $this->validator->add('number', array(
            new Integer()
        ));

        $input = array(
            'number' => '11'
        );

        $this->assertTrue($this->validator->validate($input));

        $input = array(
            'number' => 10
        );

        $this->assertTrue($this->validator->validate($input));

        $input = array(
            'number' => 'not a number'
        );

        $this->assertFalse($this->validator->validate($input));

        /**
         * @var Message[] $errors
         */
        $errors = $this->validator->getMessagesFor('number');
        $this->assertEquals('The value is not a valid integer number', $errors[0]->getMessage());

        $input = array(
            'number' => 2.8
        );

        $this->assertFalse($this->validator->validate($input));

        /**
         * @var Message[] $errors
         */
        $errors = $this->validator->getMessagesFor('number');
        $this->assertEquals('The value is not a valid integer number', $errors[0]->getMessage());
    }

    public function testIntegerWithTranslator()
    {
        $this->validator->setTranslator($this->translator);

        $this->validator->add('number', array(
            new Integer()
        ));

        $input = array(
            'number' => '11'
        );

        $this->assertTrue($this->validator->validate($input));

        $input = array(
            'number' => 10
        );

        $this->assertTrue($this->validator->validate($input));

        $input = array(
            'number' => 'not a number'
        );

        $this->assertFalse($this->validator->validate($input));

        /**
         * @var Message[] $errors
         */
        $errors = $this->validator->getMessagesFor('number');
        $this->assertEquals('The value is not a valid integer number', $errors[0]->getMessage());

        $input = array(
            'number' => 2.8
        );

        $this->assertFalse($this->validator->validate($input));

        /**
         * @var Message[] $errors
         */
        $errors = $this->validator->getMessagesFor('number');
        $this->assertEquals('The value is not a valid integer number', $errors[0]->getMessage());
    }

    public function testEmailWithTranslator()
    {
        $this->validator->setTranslator($this->translator);


        $this->validator->add('email', array(
            new Email()
        ));

        $input = array(
            'email' => 'tom@example.com'
        );

        $this->assertTrue($this->validator->validate($input));

        $input = array(
            'email' => 'tom.example.com'
        );

        $this->assertFalse($this->validator->validate($input));

        /**
         * @var Message[] $errors
         */
        $errors = $this->validator->getMessagesFor('email');
        $this->assertEquals('The value is not a valid email', $errors[0]->getMessage());
    }

    public function testEmail()
    {
        $this->validator->add('email', array(
            new Email()
        ));

        $input = array(
            'email' => 'tom@example.com'
        );

        $this->assertTrue($this->validator->validate($input));

        $input = array(
            'email' => 'tom.example.com'
        );

        $this->assertFalse($this->validator->validate($input));

        /**
         * @var Message[] $errors
         */
        $errors = $this->validator->getMessagesFor('email');
        $this->assertEquals('The value is not a valid email', $errors[0]->getMessage());
    }

    public function testMinLength()
    {
        $this->validator->add('username', array(
            new MinLength(array(
                'min_length' => 5
            ))
        ));

        $input = array(
            'username' => 'tomgrohl'
        );

        $this->assertTrue($this->validator->validate($input));

        $input = array(
            'username' => 'tom'
        );

        $this->assertFalse($this->validator->validate($input));

        /**
         * @var Message[] $errors
         */
        $errors = $this->validator->getMessagesFor('username');
        $this->assertEquals('The minimum length is 5', $errors[0]->getMessage());
    }

    public function testMinLengthWithTranslator()
    {
        $this->validator->setTranslator($this->translator);

        $this->validator->add('username', array(
            new MinLength(array(
                'min_length' => 5
            ))
        ));

        $input = array(
            'username' => 'tomgrohl'
        );

        $this->assertTrue($this->validator->validate($input));

        $input = array(
            'username' => 'tom'
        );

        $this->assertFalse($this->validator->validate($input));

        /**
         * @var Message[] $errors
         */
        $errors = $this->validator->getMessagesFor('username');
        $this->assertEquals('La longueur minimale est de 5', $errors[0]->getMessage());
    }

    public function testMaxLength()
    {
        $this->validator->add('username', array(
            new MaxLength(array(
                'max_length' => 16
            ))
        ));

        $input = array(
            'username' => 'tomgrohl'
        );

        $this->assertTrue($this->validator->validate($input));

        $input = array(
            'username' => 'thomasmichaeleellis'
        );

        $this->assertFalse($this->validator->validate($input));

        /**
         * @var Message[] $errors
         */
        $errors = $this->validator->getMessagesFor('username');
        $this->assertEquals('The maximum length is 16', $errors[0]->getMessage());
    }

    public function testMaxLengthWithTranslator()
    {
        $this->validator->setTranslator($this->translator);

        $this->validator->add('username', array(
            new MaxLength(array(
                'max_length' => 16
            ))
        ));

        $input = array(
            'username' => 'tomgrohl'
        );

        $this->assertTrue($this->validator->validate($input));

        $input = array(
            'username' => 'thomasmichaeleellis'
        );

        $this->assertFalse($this->validator->validate($input));

        /**
         * @var Message[] $errors
         */
        $errors = $this->validator->getMessagesFor('username');
        $this->assertEquals('The maximum length is 16', $errors[0]->getMessage());
    }

    public function testRegexAlpha()
    {
        $this->validator->add('username', array(
            new Regex(array(
                'expression' => '/^[\pL\pM]+$/u'
            ))
        ));

        $input = array(
            'username' => 'tomgrohl'
        );

        $this->assertTrue($this->validator->validate($input));

        $input = array(
            'username' => 'tomgrohl09'
        );

        /**
         * @var Message[] $errors
         */
        $this->assertFalse($this->validator->validate($input));
        $errors = $this->validator->getMessagesFor('username');
        $this->assertEquals('The field is not in the correct format', $errors[0]->getMessage());
    }

    public function testRegexAlphaWithTranslator()
    {
        $this->validator->setTranslator($this->translator);


        $this->validator->add('username', array(
            new Regex(array(
                'expression' => '/^[\pL\pM]+$/u'
            ))
        ));

        $input = array(
            'username' => 'tomgrohl'
        );

        $this->assertTrue($this->validator->validate($input));

        $input = array(
            'username' => 'tomgrohl09'
        );

        /**
         * @var Message[] $errors
         */
        $this->assertFalse($this->validator->validate($input));
        $errors = $this->validator->getMessagesFor('username');
        $this->assertEquals('The field is not in the correct format', $errors[0]->getMessage());
    }

    public function testRegexAlphaNumeric()
    {
        $this->validator->add('username', array(
            new Regex(array(
                'expression' => '/^[\pL\pM\pN]+$/u'
            ))
        ));

        $input = array(
            'username' => 'tomgrohl0988'
        );

        $this->assertTrue($this->validator->validate($input));

        $input = array(
            'username' => 'tomgrohl-_'
        );

        /**
         * @var Message[] $errors
         */
        $this->assertFalse($this->validator->validate($input));
        $errors = $this->validator->getMessagesFor('username');
        $this->assertEquals('The field is not in the correct format', $errors[0]->getMessage());
    }

    public function testRegexAlphaNumericWithTranslator()
    {
        $this->validator->setTranslator($this->translator);

        $this->validator->add('username', array(
            new Regex(array(
                'expression' => '/^[\pL\pM\pN]+$/u'
            ))
        ));

        $input = array(
            'username' => 'tomgrohl0988'
        );

        $this->assertTrue($this->validator->validate($input));

        $input = array(
            'username' => 'tomgrohl-_'
        );

        /**
         * @var Message[] $errors
         */
        $this->assertFalse($this->validator->validate($input));
        $errors = $this->validator->getMessagesFor('username');
        $this->assertEquals('The field is not in the correct format', $errors[0]->getMessage());
    }

    public function testRegexAlphaDash()
    {
        $this->validator->add('username', array(
            new Regex(array(
                'expression' => '/^[\pL\pM\pN-_]+$/u'
            ))
        ));

        $input = array(
            'username' => 'tomgrohl-_'
        );

        $this->assertTrue($this->validator->validate($input));

        $input = array(
            'username' => 'tomgrohl%$'
        );

        /**
         * @var Message[] $errors
         */
        $this->assertFalse($this->validator->validate($input));
        $errors = $this->validator->getMessagesFor('username');
        $this->assertEquals('The field is not in the correct format', $errors[0]->getMessage());
    }

    public function testRegexAlphaDashWithTranslator()
    {
        $this->validator->setTranslator($this->translator);

        $this->validator->add('username', array(
            new Regex(array(
                'expression' => '/^[\pL\pM\pN-_]+$/u'
            ))
        ));

        $input = array(
            'username' => 'tomgrohl-_'
        );

        $this->assertTrue($this->validator->validate($input));

        $input = array(
            'username' => 'tomgrohl%$'
        );

        /**
         * @var Message[] $errors
         */
        $this->assertFalse($this->validator->validate($input));
        $errors = $this->validator->getMessagesFor('username');
        $this->assertEquals('The field is not in the correct format', $errors[0]->getMessage());
    }

    public function testTranslated()
    {
        $this->validator->setTranslator($this->translator);

        $this->validator->add('number', array(
            new Numerical()
        ));

        $input = array(
            'number' => 'not numeric'
        );

        $this->assertFalse($this->validator->validate($input));
        /**
         * @var Message[] $errors
         */
        $errors = $this->validator->getMessagesFor('number');

        $this->assertEquals('La valeur n\'est pas un nombre valide', $errors[0]->getMessage());


    }

    public function testTranslatedWithData()
    {
        $this->validator->setTranslator($this->translator);

        $this->validator->add('username', array(
            new MinLength(array(
                'min_length' => 5
            ))
        ));

        $input = array(
            'username' => 'tom'
        );

        $this->assertFalse($this->validator->validate($input));
        /**
         * @var Message[] $errors
         */
        $errors = $this->validator->getMessagesFor('username');

        $this->assertEquals('La longueur minimale est de 5', $errors[0]->getMessage());
    }

    public function testTranslatedWithData2()
    {
        $this->validator->setTranslator($this->translator);

        $this->validator->getTranslator()->setLocale('en_GB');

        $this->validator->add('username', array(
            new MinLength(array(
                'min_length' => 5
            ))
        ));

        $input = array(
            'username' => 'tom'
        );

        $this->assertFalse($this->validator->validate($input));
        /**
         * @var Message[] $errors
         */
        $errors = $this->validator->getMessagesFor('username');

        $this->assertEquals('The minimum length is 5', $errors[0]->getMessage());
        $this->assertCount(1, $errors[0]->getData());
    }

    public function testInConstraintPasses()
    {
        $this->validator->setTranslator($this->translator);

        $this->validator->getTranslator()->setLocale('en_GB');

        $this->validator->add('prefix', array(
            new In(array(
                'choices' => array(
                    'Mr',
                    'Mrs'
                )
            ))
        ));

        $input = array(
            'prefix' => 'Mr'
        );

        $this->assertTrue($this->validator->validate($input));
    }

    public function testInConstraintFails()
    {
        $this->validator->setTranslator($this->translator);

        $this->validator->getTranslator()->setLocale('en_GB');

        $this->validator->add('prefix', array(
            new In(array(
                'choices' => array(
                    'Mr',
                    'Mrs'
                )
            ))
        ));

        $input = array(
            'prefix' => 'Dr'
        );

        $this->assertFalse($this->validator->validate($input));
        /**
         * @var Message[] $errors
         */
        $errors = $this->validator->getMessagesFor('prefix');

        $this->assertEquals('Please choose from the following: Mr, Mrs', $errors[0]->getMessage());
        $this->assertCount(1, $errors[0]->getData());
    }

    public function testNotInConstraintPasses()
    {
        $this->validator->setTranslator($this->translator);

        $this->validator->getTranslator()->setLocale('en_GB');

        $this->validator->add('prefix', array(
            new NotIn(array(
                'choices' => array(
                    'Mr',
                    'Mrs'
                )
            ))
        ));

        $input = array(
            'prefix' => 'Dr'
        );

        $this->assertTrue($this->validator->validate($input));
    }

    public function testNotInConstraintFails()
    {
        $this->validator->setTranslator($this->translator);

        $this->validator->getTranslator()->setLocale('en_GB');

        $this->validator->add('prefix', array(
            new NotIn(array(
                'choices' => array(
                    'Mr',
                    'Mrs'
                )
            ))
        ));

        $input = array(
            'prefix' => 'Mr'
        );

        $this->assertFalse($this->validator->validate($input));
        /**
         * @var Message[] $errors
         */
        $errors = $this->validator->getMessagesFor('prefix');

        $this->assertEquals('Please choose a value that is not from the following: Mr, Mrs', $errors[0]->getMessage());
        $this->assertCount(1, $errors[0]->getData());
    }

    public function testValidateAlpha()
    {
        $this->validator->add('prefix', array(
            new Alpha()
        ));

        $input = array(
            'prefix' => 'abcdefg'
        );

        $this->assertTrue($this->validator->validate($input));

        $input2 = array(
            'prefix' => 'abcdefg-$'
        );

        $this->assertFalse($this->validator->validate($input2));

        $errors = $this->validator->getMessagesFor('prefix');

        $this->assertEquals('The field is must only container alphabetic characters', $errors[0]->getMessage());

    }

    public function testValidateAlphaNumeric()
    {
        $this->validator->add('prefix', array(
            new AlphaNumeric()
        ));

        $input = array(
            'prefix' => 'abcdefg1234'
        );

        $this->assertTrue($this->validator->validate($input));

        $input2 = array(
            'prefix' => 'abcdefg$f'
        );

        $this->assertFalse($this->validator->validate($input2));

        $errors = $this->validator->getMessagesFor('prefix');

        $this->assertEquals('The field is must only container alphanumeric characters', $errors[0]->getMessage());
    }

    public function testValidateAlphaDash()
    {
        $this->validator->add('prefix', array(
            new AlphaDash()
        ));

        $input = array(
            'prefix' => 'abcdefg-_'
        );

        $this->assertTrue($this->validator->validate($input));

        $input2 = array(
            'prefix' => 'abcdefg$'
        );

        $this->assertFalse($this->validator->validate($input2));

        $errors = $this->validator->getMessagesFor('prefix');

        $this->assertEquals('The field is must only container alphanumeric characters, dashes and underscores', $errors[0]->getMessage());
    }

    public function testValidateDateFormat()
    {
        $this->validator->add('date', array(
            new DateFormat(array(
                'format' => 'Y-m-d H:i:s'
            ))
        ));

        $input = array(
            'date' => '2015-04-11 13:00:00'
        );

        $this->assertTrue($this->validator->validate($input));

        $input2 = array(
            'date' => '2015-04-11'
        );

        $this->assertFalse($this->validator->validate($input2));

        $errors = $this->validator->getMessagesFor('date');

        $this->assertEquals('The date format of the field is incorrect. Must be in format: Y-m-d H:i:s', $errors[0]->getMessage());

    }

    public function testValidateIPAddress()
    {
        $this->validator->add('ip_address', array(
            new IPAddress()
        ));

        $input = array(
            'ip_address' => '128.0.0.1'
        );

        $this->assertTrue($this->validator->validate($input));

        $input2 = array(
            'ip_address' => '128.256.0.1'
        );

        $this->assertFalse($this->validator->validate($input2));

        $errors = $this->validator->getMessagesFor('ip_address');

        $this->assertEquals('The IP Address is invalid', $errors[0]->getMessage());

    }

    public function testValidateURL()
    {
        $this->validator->add('url', array(
            new URL()
        ));

        $input = array(
            'url' => 'http://example.com'
        );

        $this->assertTrue($this->validator->validate($input));

        $input2 = array(
            'url' => 'example.com'
        );

        $this->assertFalse($this->validator->validate($input2));

        $errors = $this->validator->getMessagesFor('url');

        $this->assertEquals('The URL is invalid', $errors[0]->getMessage());

    }

    public function testValidateTimeZone()
    {
        $this->validator->add('timezone', array(
            new TimeZone()
        ));

        $input = array(
            'timezone' => 'Europe/London'
        );

        $this->assertTrue($this->validator->validate($input));

        $input2 = array(
            'timezone' => 'Foo'
        );

        $this->assertFalse($this->validator->validate($input2));

        $errors = $this->validator->getMessagesFor('timezone');

        $this->assertEquals('The timezone is incorrect', $errors[0]->getMessage());
    }

    public function testValidateConstraintIsSkipped()
    {
        $this->validator->add('prefix', array(
            new Alpha()
        ));

        $input = array(
            'prefix' => '',
        );

        $this->assertTrue($this->validator->validate($input));

        $input2 = array(
            'prefix' => null,
        );

        $this->assertTrue($this->validator->validate($input2));

        $input3 = array(
            'prefix' => array(),
        );

        $this->assertTrue($this->validator->validate($input3));
    }

    public function testValidateDigitsBetween()
    {
        $this->validator->add('number', array(
            new DigitsBetween(array(
                'start' => 10,
                'end'   => 20
            ))
        ));

        $input = array(
            'number' =>  15
        );

        $this->assertTrue($this->validator->validate($input));

        $input2 = array(
            'number' => 5
        );

        $this->assertFalse($this->validator->validate($input2));

        $errors = $this->validator->getMessagesFor('number');

        $this->assertEquals('The field must be between 10 and 20', $errors[0]->getMessage());

    }

    public function testValidateMimeTypes()
    {
        $this->validator->add('file', array(
            new MimeTypes(array(
                'types' => array(
                    'image/png'
                )
            ))
        ));

        $input = array(
            'file' =>  'not a file'
        );

        $this->assertFalse($this->validator->validate($input));

        $input2 = array(
            'file' =>  $this->getUploadedFile(true, 'image/png')
        );

        $this->assertTrue($this->validator->validate($input2));

        $input3 = array(
            'file' => $this->getUploadedFile(true, 'text/plain')
        );

        $this->assertFalse($this->validator->validate($input3));

        $errors = $this->validator->getMessagesFor('file');

        $this->assertEquals('The field has an invalid mime type', $errors[0]->getMessage());
    }

    public function testValidateImage()
    {
        $this->validator->add('file', array(
            new Image()
        ));

        $input = array(
            'file' =>  $this->getUploadedFile(false)
        );

        $this->assertTrue($this->validator->validate($input));

        $input2 = array(
            'file' =>  $this->getUploadedFile(true, 'image/png')
        );

        $this->assertTrue($this->validator->validate($input2));

        $input2 = array(
            'file' => $this->getUploadedFile(true, 'text/plain')
        );

        $this->assertFalse($this->validator->validate($input2));

        $errors = $this->validator->getMessagesFor('file');

        $this->assertEquals('The field has an invalid mime type', $errors[0]->getMessage());
    }

    protected function getUploadedFile($valid = true, $mimeType = null)
    {
        $mock = $this->getMockBuilder('Symfony\Component\HttpFoundation\File\UploadedFile')
            ->enableOriginalConstructor()
            ->setConstructorArgs(array(tempnam(sys_get_temp_dir(), ''), 'dummy'))
            ->setMethods(array(
                'isValid',
                'getMimeType'
            ))
            ->getMock();

        $mock->expects($this->any())
            ->method('isValid')
            ->will($this->returnValue($valid));

        $mock->expects($this->any())
            ->method('getMimeType')
            ->will($this->returnValue($mimeType));

        return $mock;
    }
}
