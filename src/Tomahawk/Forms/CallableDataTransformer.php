<?php

/*
 * This file is part of the TomahawkPHP package.
 *
 * (c) Tom Ellis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tomahawk\Forms;

/**
 * Class CallableDataTransformer
 *
 * Inspired by Symfonys Form Data Transformers.
 *
 * Added to make using Doctrine entities with Forms easier
 *
 * @package Tomahawk\Forms
 */
class CallableDataTransformer implements DataTransformerInterface
{
    private $transform;

    private $reverseTransform;

    /**
     * CallableDataTransformer constructor.
     * @param callable $transform
     * @param callable $reverseTransform
     */
    public function __construct(callable $transform, callable $reverseTransform)
    {
        $this->transform = $transform;
        $this->reverseTransform = $reverseTransform;
    }

    /**
     * Transform a value
     *
     * Transforms a value to go in a form element
     *
     * e.g get the id from an entity
     *
     * @param $value
     * @return mixed
     */
    public function transform($value)
    {
        return call_user_func($this->transform, $value);
    }

    /**
     * Reverse a transform
     *
     * Transforms a value from form input to go in a model
     *
     * e.g lookup an entity using id from form input
     *
     * @param $value
     * @return mixed
     */
    public function reverseTransform($value)
    {
        return call_user_func($this->reverseTransform, $value);
    }
}
