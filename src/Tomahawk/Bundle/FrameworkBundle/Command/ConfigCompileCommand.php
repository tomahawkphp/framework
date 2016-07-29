<?php

namespace Tomahawk\Bundle\FrameworkBundle\Command;

use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tomahawk\DependencyInjection\ContainerAwareInterface;
use Tomahawk\DependencyInjection\ContainerAwareTrait;

class ConfigCompileCommand extends Command implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    protected function configure()
    {
        $this
            ->setName('config:compile')
            ->setDescription('Compile environment config into one file.');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $kernel = $this->container->get('kernel');

        $rootPath = $kernel->getRootDir();

        $env = $kernel->getEnvironment();

        $configFile = sprintf('%s/config/config_%s.php', $rootPath, $env);

        $config = $this->container->get('config');

        // Force a reload of configs
        $config->load(true);

        $configs = $config->get();

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

        $io->success(sprintf('Config has been compiled for environment %s.', $kernel->getEnvironment()));
    }
}
