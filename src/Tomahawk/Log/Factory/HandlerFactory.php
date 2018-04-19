<?php

namespace Tomahawk\Log\Factory;
use Tomahawk\Config\ConfigInterface;

/**
 * Class HandlerFactory
 * @package Tomahawk\Log\Factory
 */
class HandlerFactory
{
    /**
     * @var ConfigInterface
     */
    protected $config;

    public function __construct(ConfigInterface $config)
    {
        $this->config = $config;
    }

    public function make(string $name)
    {

    }
}
