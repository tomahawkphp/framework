<?php

namespace Tomahawk\DI;

interface ContainerAwareInterface
{
    /**
     * Sets the Container.
     *
     * @param ContainerInterface|null $container A ContainerInterface instance or null
     *
     * @api
     */
    public function setContainer(ContainerInterface $container = null);
}