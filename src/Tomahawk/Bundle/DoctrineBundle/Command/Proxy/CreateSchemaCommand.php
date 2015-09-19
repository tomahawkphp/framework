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

namespace Tomahawk\Bundle\DoctrineBundle\Command\Proxy;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\Output;
use Doctrine\ORM\Tools\Console\Command\SchemaTool\CreateCommand;

/**
 * Command to execute the SQL needed to generate the database schema for
 * a given entity manager.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Jonathan H. Wage <jonwage@gmail.com>
 */
class CreateSchemaCommand extends CreateCommand
{
    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('doctrine:schema:create')
            ->setDescription('Executes (or dumps) the SQL needed to generate the database schema')
            ->addOption('em', null, InputOption::VALUE_OPTIONAL, 'The entity manager to use for this command')
            ->setHelp(<<<EOT
The <info>doctrine:schema:create</info> command executes the SQL needed to
generate the database schema for the default entity manager:

<info>php app/hatchet doctrine:schema:create</info>

You can also generate the database schema for a specific entity manager:

<info>php app/hatchet doctrine:schema:create --em=default</info>

Finally, instead of executing the SQL, you can output the SQL:

<info>php app/hatchet doctrine:schema:create --dump-sql</info>
EOT
        );
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        CommandHelper::setApplicationEntityManager($this->getApplication(), $input->getOption('em'));

        parent::execute($input, $output);
    }
}
