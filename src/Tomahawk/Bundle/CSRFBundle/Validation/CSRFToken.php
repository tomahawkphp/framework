<?php

namespace Tomahawk\Bundle\CSRFBundle\Validation;

use Tomahawk\Bundle\CSRFBundle\Token\TokenManagerInterface;
use Tomahawk\Validation\Constraints\Constraint;
use Tomahawk\Validation\Validator;

class CSRFToken extends Constraint
{
    /**
     * @var TokenManagerInterface
     */
    protected $tokenManager;

    protected $message = 'Invalid security token';

    public function __construct(TokenManagerInterface $tokenManager, array $config = array())
    {
        $this->tokenManager = $tokenManager;

        parent::__construct($config);
    }

    public function validate(Validator $validator, $attribute, $value)
    {
        $actualToken = $this->tokenManager->getToken();
        $valid = true;

        // Check if token is set
        if (!$value) {
            $valid = false;
        }
        // Check if token is valid
        else if ($value !== $actualToken) {
            $valid = false;
        }

        if (!$valid) {
            $this->fail($attribute, $validator);
            return false;
        }

        return true;
    }
}
