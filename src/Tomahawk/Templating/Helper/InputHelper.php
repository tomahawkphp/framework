<?php

/*
 * This file is part of the TomahawkPHP package.
 *
 * (c) Tom Ellis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tomahawk\Templating\Helper;

use Tomahawk\Input\InputInterface;
use Symfony\Component\Templating\Helper\Helper;

/**
 * InputHelper gives access to the InputManager
 *
 * @author Tom Elis <tellishtc@gmail.com>
 *
 * @api
 */
class InputHelper extends Helper
{

    /**
     * @var InputInterface
     */
    protected $input;

    /**
     * Construct
     *
     * @param InputInterface $input
     */
    public function __construct(InputInterface $input)
    {
        $this->input = $input;
    }

    /**
     * Pass all method calls off to the UrlGenerator
     *
     * @param $method
     * @param array $arguments
     * @return mixed
     * @throws \BadMethodCallException
     */
    public function __call($method, $arguments = array())
    {
        if (method_exists($this->input, $method)) {
            return call_user_func_array(array($this->input, $method), $arguments);
        }

        throw new \BadMethodCallException(sprintf('Method "%s" does not exist on the Input', $method));
    }

    /**
     * Returns the canonical name of this helper.
     *
     * @return string The canonical name
     *
     * @api
     */
    public function getName()
    {
        return 'input';
    }
}
