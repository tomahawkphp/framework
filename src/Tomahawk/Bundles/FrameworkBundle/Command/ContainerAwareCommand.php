<?php

namespace Tomahawk\Bundles\FrameworkBundle\Command;

use Symfony\Component\Console\Command\Command;
use Tomahawk\DI\ContainerInterface;
use Tomahawk\DI\ContainerAwareInterface;

/**
 * Command.
 *
 * @author Tom Ellis
 */
abstract class ContainerAwareCommand extends Command implements ContainerAwareInterface
{
    /**
     * @var ContainerInterface|null
     */
    private $container;

    /**
     * @return ContainerInterface
     * @throws \LogicException
     */
    protected function getContainer()
    {
        if (null === $this->container) {
            $application = $this->getApplication();
            if (null === $application) {
                throw new \LogicException('The container cannot be retrieved as the application instance is not yet set.');
            }

            $this->container = $application->getKernel()->getContainer();
        }

        return $this->container;
    }

    /**
     * {@inheritdoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
}