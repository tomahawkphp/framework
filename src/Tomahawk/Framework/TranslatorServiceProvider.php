<?php

/*
 * This file is part of the TomahawkPHP package.
 *
 * (c) Tom Ellis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tomahawk\Framework;

use Symfony\Component\Translation\Formatter\MessageFormatter;
use Symfony\Component\Translation\TranslatorInterface;
use Tomahawk\DependencyInjection\ContainerInterface;
use Tomahawk\DependencyInjection\ServiceProviderInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Translation\Loader\ArrayLoader;
use Symfony\Component\Translation\Loader\PhpFileLoader as TransPhpFileLoader;
use Symfony\Component\Translation\Translator;

/**
 * Class TranslatorServiceProvider
 *
 * @package Tomahawk\Framework
 */
class TranslatorServiceProvider implements ServiceProviderInterface
{
    public function register(ContainerInterface $container)
    {
        $container->set(TranslatorInterface::class, function(ContainerInterface $c) {

            $config = $c->get('config');
            $locale = $config->get('translation.locale');
            $fallbackLocale = $config->get('translation.fallback_locale');
            $translationDirs = $config->get('translation.translation_dirs');
            $cacheDir = $config->get('translation.cache');

            if (false === $cacheDir) {
                $cacheDir = null;
            }

            $translator = new Translator($locale, new MessageFormatter(), $cacheDir);
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

                        $domain = basename($file->getPathname(), '.php');
                        $translator->addResource('php', $file->getPathname(), $directory->getFileName(), $domain);
                    }
                }
            }

            return $translator;
        });

        $container->addAlias('translator', TranslatorInterface::class);
    }
}
