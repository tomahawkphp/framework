<?php

/*
 * This file is part of the Doctrine Bundle
 *
 * The code was originally distributed inside the Symfony framework.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 * (c) Doctrine Project, Benjamin Eberlei <kontakt@beberlei.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tomahawk\Bundle\DoctrineBundle\Command;

use Doctrine\ORM\Tools\EntityGenerator;
use Symfony\Component\Console\Command\Command;
use Tomahawk\DependencyInjection\ContainerAwareInterface;
use Tomahawk\DependencyInjection\ContainerAwareTrait;

/**
 * Base class for Doctrine console commands to extend from.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
abstract class DoctrineCommand extends Command implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * get a doctrine entity generator
     *
     * @return EntityGenerator
     */
    protected function getEntityGenerator()
    {
        $entityGenerator = new EntityGenerator();
        $entityGenerator->setGenerateAnnotations(false);
        $entityGenerator->setGenerateStubMethods(true);
        $entityGenerator->setRegenerateEntityIfExists(false);
        $entityGenerator->setUpdateEntityIfExists(true);
        $entityGenerator->setNumSpaces(4);
        $entityGenerator->setAnnotationPrefix('ORM\\');

        return $entityGenerator;
    }

    /**
     * Get a doctrine entity manager by symfony name.
     *
     * @param string $name
     * @return \Doctrine\ORM\EntityManager
     */
    protected function getEntityManager($name)
    {
        return $this->container->get('doctrine')->getManager($name);
    }

    /**
     * Get a doctrine dbal connection by symfony name.
     *
     * @param string $name
     * @return \Doctrine\DBAL\Connection
     */
    protected function getDoctrineConnection($name)
    {
        return $this->container->get('doctrine')->getConnection($name);
    }
}
