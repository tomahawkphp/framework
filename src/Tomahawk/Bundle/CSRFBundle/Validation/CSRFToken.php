<?php

namespace Tomahawk\Bundle\CSRFBundle\Validation;

use Tomahawk\Bundle\CSRFBundle\Exception\InvalidTokenException;
use Tomahawk\Bundle\CSRFBundle\Exception\TokenNotFoundException;
use Tomahawk\Bundle\CSRFBundle\Token\TokenManagerInterface;
use Tomahawk\Validation\Constraints\Constraint;
use Tomahawk\Validation\Message;
use Tomahawk\Validation\Validator;

class CSRFToken extends Constraint
{
    /**
     * @var TokenManagerInterface
     */
    protected $tokenManager;

    /**
     * @var bool
     */
    protected $throw = false;

    protected $message = 'Security token has expired';

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
            if ($this->throw) {
                throw new TokenNotFoundException();
            }
        }
        // Check if token is valid
        else if ($value !== $actualToken) {

            if ($this->throw) {
                throw new InvalidTokenException();
            }
        }

        if ( ! $valid) {

            if ($trans = $validator->getTranslator()) {
                $this->setMessage($trans->trans($this->getMessage(), $this->getData()));
            }
            else {
                $this->mergeMessageData();
            }

            $validator->addMessage($attribute, new Message($this->getMessage(), array()));

            return false;
        }

        return true;
    }
}
