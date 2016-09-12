<?php

/*
 * This file is part of the TomahawkPHP package.
 *
 * (c) Tom Ellis
 *
 * The is based on code originally distributed inside the Symfony/Doctrine framework.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 * (c) Doctrine Project, Benjamin Eberlei <kontakt@beberlei.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tomahawk\Bundle\DoctrineBundle\Command\Migrations\Helper;

use Tomahawk\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Doctrine\DBAL\Sharding\PoolingShardConnection;
use Tomahawk\Bundle\DoctrineBundle\Command\Proxy\CommandHelper as BaseCommandHelper;

/**
 * Provides some helper and convenience methods to configure doctrine commands in the context of bundles
 * and multiple connections/entity managers.
 *
 * Based on the DoctrineCommandHelper from the DoctrineMigrationsBundle
 *
 * @author Guilherme Blanco <guilhermeblanco@hotmail.com>
 */
abstract class CommandHelper extends BaseCommandHelper
{
    public static function setApplicationHelper(Application $application, InputInterface $input)
    {
        $container = $application->getKernel()->getContainer();

        $doctrine  = $container->get('doctrine');

        $managerNames = $doctrine->getManagerNames();

        if ($input->getOption('db') || empty($managerNames)) {
            self::setApplicationConnection($application, $input->getOption('db'));
        }
        else {
            self::setApplicationEntityManager($application, $input->getOption('em'));
        }

        if ($input->getOption('shard')) {
            $connection = $application->getHelperSet()->get('db')->getConnection();

            if ( ! $connection instanceof PoolingShardConnection) {

                if (empty($managerNames)) {
                    throw new \LogicException(sprintf("Connection '%s' must implement shards configuration.", $input->getOption('db')));
                }
                else {
                    throw new \LogicException(sprintf("Connection of EntityManager '%s' must implement shards configuration.", $input->getOption('em')));
                }
            }

            $connection->connect($input->getOption('shard'));
        }
    }
}
