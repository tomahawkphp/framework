<?php

/*
 * This file is part of the TomahawkPHP package.
 *
 * (c) Tom Ellis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tomahawk\Bundle\FrameworkBundle\DI;

use Tomahawk\DI\ContainerInterface;
use Tomahawk\DI\ServiceProviderInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Translation\Loader\ArrayLoader;
use Symfony\Component\Translation\Loader\PhpFileLoader as TransPhpFileLoader;
use Symfony\Component\Translation\MessageSelector;
use Symfony\Component\Translation\Translator;

class TranslatorProvider implements ServiceProviderInterface
{
    public function register(ContainerInterface $container)
    {
        $container->set('Symfony\Component\Translation\TranslatorInterface', function(ContainerInterface $c) {

            $config = $c['config'];
            $locale = $config->get('translation.locale');
            $fallbackLocale = $config->get('translation.fallback_locale');
            $translationDirs = $config->get('translation.translation_dirs');
            $cacheDir = $config->get('translation.cache_dir');

            $translator = new Translator($locale, new MessageSelector(), $cacheDir);
            $translator->setFallbackLocales(array($fallbackLocale));
            $translator->addLoader('php', new TransPhpFileLoader());
            $translator->addLoader('array', new ArrayLoader());

            foreach ($translationDirs as $translationDir) {

                $finder = new Finder();

                $finder->in($translationDir)->depth(0)->directories();

                foreach ($finder as $directory)  {

                    $dFinder = new Finder();
                    $dFinder->in($directory->getPathname())->files()->name('*.php');

                    foreach ($dFinder as $file) {
                        $translator->addResource('php', $file->getPathname(), $directory->getFileName());
                    }
                }
            }

            return $translator;
        });
    }
}
