<?php

/*
 * This file is part of the TomahawkPHP package.
 *
 * (c) Tom Ellis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tomahawk\Security\Csrf\Validation;

use Tomahawk\Security\Csrf\Token\TokenManagerInterface;
use Tomahawk\Validation\Constraints\Constraint;
use Tomahawk\Validation\Validator;

/**
 * Class CsrfToken
 *
 * @package Tomahawk\Security\Csrf\Validation
 */
class CsrfToken extends Constraint
{
    /**
     * @var TokenManagerInterface
     */
    protected $tokenManager;

    /**
     * @var string
     */
    protected $message = 'Invalid security token';

    /**
     * Whether to skip validation when no value is passed
     *
     * @var bool
     */
    protected $skipOnNoValue = false;

    public function __construct(TokenManagerInterface $tokenManager, array $config = array())
    {
        $this->tokenManager = $tokenManager;

        parent::__construct($config);
    }

    /**
     * @param Validator $validator
     * @param $attribute
     * @param $value
     * @return bool
     */
    public function validate(Validator $validator, $attribute, $value)
    {
        $actualToken = $this->tokenManager->getToken();
        $valid = true;

        // Check if token is set
        if ( ! $value) {
            $valid = false;
        }
        // Check if token is valid
        elseif ($value !== $actualToken) {
            $valid = false;
        }

        if ( ! $valid) {
            $this->fail($attribute, $validator);
            return false;
        }

        return true;
    }
}
