<?php

namespace Tomahawk\Validation\Tests;

use Tomahawk\Test\TestCase;
use Tomahawk\Validation\Validator;
use Tomahawk\Validation\Constraints\Email;
use Tomahawk\Validation\Constraints\Required;
use Tomahawk\Validation\Constraints\RequiredWith;
use Tomahawk\Validation\Constraints\Identical;
use Tomahawk\Validation\Constraints\StringLength;
use Tomahawk\Validation\Constraints\Numeric;
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

    public function testNumeric()
    {
        $this->validator->add('number', array(
            new Numeric()
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
            new Numeric()
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
            new Numeric()
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
}
