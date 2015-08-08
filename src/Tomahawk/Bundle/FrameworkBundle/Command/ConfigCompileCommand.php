<?php

namespace Tomahawk\Bundle\FrameworkBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tomahawk\DI\ContainerAwareInterface;
use Tomahawk\DI\ContainerInterface;

class ConfigCompileCommand extends Command implements ContainerAwareInterface
{
    protected $container;

    protected function configure()
    {
        $this
            ->setName('config:compile')
            ->setDescription('Compile environment config into one file.');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $kernel = $this->container->get('kernel');

        $rootPath = $kernel->getRootDir();

        $env = $kernel->getEnvironment();

        $configFile = sprintf('%s/config/config_%s.php', $rootPath, $env);

        $configs = $this->container->get('config')->get();

        $compliledConfigs = <<<EOF
<?php

return array(\n
EOF;

        // Go over every config setting
        foreach ($configs as $name => $values) {

            $compliledConfigs .= sprintf("'%s' => %s,\n", $name, var_export($values, true));

        }

        $compliledConfigs .= ');';

        file_put_contents($configFile, $compliledConfigs);

        $output->writeln(sprintf('<info>Config has been compiled for environment %s.</info>', $kernel->getEnvironment()));
    }

    /**
     * Sets the Container.
     *
     * @param ContainerInterface|null $container A ContainerInterface instance or null
     *
     * @api
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
}
