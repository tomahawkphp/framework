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

use InvalidArgumentException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Tomahawk\Bundle\DoctrineBundle\Command\Proxy\CommandHelper;
use Doctrine\DBAL\Migrations\Tools\Console\Command\MigrateCommand;

/**
 * Command for executing a migration to a specified version or the latest available version.
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
class DoctrineMigrateCommand extends MigrateCommand
{
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('doctrine:migrations:migrate')
            ->addOption('db', null, InputOption::VALUE_REQUIRED, 'The database connection to use for this command.')
            ->addOption('em', null, InputOption::VALUE_REQUIRED, 'The entity manager to use for this command.')
            ->addOption('shard', null, InputOption::VALUE_REQUIRED, 'The shard connection to use for this command.');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        // EM and DB options cannot be set at same time
        if (null !== $input->getOption('em') && null !== $input->getOption('db')) {
            throw new InvalidArgumentException('Cannot set both "em" and "db" for command execution.');
        }

        Helper\CommandHelper::setApplicationHelper($this->getApplication(), $input);

        $configuration = $this->getMigrationConfiguration($input, $output);

        DoctrineCommand::configureMigrations($this->getApplication()->getKernel()->getContainer(), $configuration);

        parent::execute($input, $output);
    }
}
