<?php

/*
 * This file is part of the TomahawkPHP package.
 *
 * (c) Tom Ellis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tomahawk\Cache\Command;

use Tomahawk\DependencyInjection\ContainerAwareTrait;
use Tomahawk\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CacheClearCommand
 *
 * @package Tomahawk\Cache\Command
 */
class CacheClearCommand extends Command implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    protected function configure()
    {
        $this
            ->setName('cache:clear')
            ->setDescription('Clear application cache.')
        ;
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $kernel = $this->container->get('kernel');

        /** @var Filesystem $filesystem */
        $filesystem = $this->container->get('filesystem');

        $realCacheDir = $kernel->getCacheDir();

        if ( ! $filesystem->exists($realCacheDir)) {
            throw new \RuntimeException('Cache directory does not exist.');
        }

        // the old cache dir name must not be longer than the real one to avoid exceeding
        // the maximum length of a directory or file path within it (esp. Windows MAX_PATH)
        $oldCacheDir = substr($realCacheDir, 0, -1).('~' === substr($realCacheDir, -1) ? '+' : '~');

        if ($filesystem->exists($oldCacheDir)) {
            $filesystem->remove($oldCacheDir);
        }

        $filesystem->rename($realCacheDir, $oldCacheDir);

        $filesystem->remove($oldCacheDir);

        $filesystem->mkdir($realCacheDir);

        $io->success(sprintf('Cache for the "%s" environment (debug=%s) was successfully cleared.', $kernel->getEnvironment(), var_export($kernel->isDebug(), true)));
    }

}
