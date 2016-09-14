<?php

/*
 * This file is part of the TomahawkPHP package.
 *
 * (c) Tom Ellis
 *
 * The code is based off the Doctrine Migrations Bundle by the Doctrine Project
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tomahawk\Bundle\DoctrineBundle\Command\Migrations;

use LogicException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Doctrine\DBAL\Sharding\PoolingShardConnection;
use Doctrine\DBAL\Migrations\Tools\Console\Command\DiffCommand;
use Tomahawk\Bundle\DoctrineBundle\Command\Proxy\CommandHelper;

/**
 * Command for executing single migrations up or down manually.
 *
 * @author Tom Ellis
 *
 * Based on the original by:
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Jonathan H. Wage <jonwage@gmail.com>
 *
 * @codeCoverageIgnore
 */

class DoctrineDiffCommand extends DiffCommand
{
    protected function configure()
    {
        parent::configure();
        $this
            ->setName('doctrine:migrations:diff')
            ->addOption('em', null, InputOption::VALUE_OPTIONAL, 'The entity manager to use for this command.')
            ->addOption('shard', null, InputOption::VALUE_REQUIRED, 'The shard connection to use for this command.');
    }
    public function execute(InputInterface $input, OutputInterface $output)
    {
        CommandHelper::setApplicationEntityManager($this->getApplication(), $input->getOption('em'));

        if ($input->getOption('shard')) {
            $connection = $this->getApplication()->getHelperSet()->get('db')->getConnection();
            if (!$connection instanceof PoolingShardConnection) {
                throw new LogicException(sprintf("Connection of EntityManager '%s' must implements shards configuration.", $input->getOption('em')));
            }
            $connection->connect($input->getOption('shard'));
        }

        $configuration = $this->getMigrationConfiguration($input, $output);

        DoctrineCommand::configureMigrations($this->getApplication()->getKernel()->getContainer(), $configuration);

        parent::execute($input, $output);
    }
}
