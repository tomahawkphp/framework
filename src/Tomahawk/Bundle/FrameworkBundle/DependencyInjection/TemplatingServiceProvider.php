<?php

/*
 * This file is part of the TomahawkPHP package.
 *
 * (c) Tom Ellis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tomahawk\Bundle\FrameworkBundle\DependencyInjection;

use Tomahawk\DependencyInjection\ContainerInterface;
use Tomahawk\DependencyInjection\ServiceProviderInterface;
use Tomahawk\Templating\GlobalVariables;
use Tomahawk\Templating\Twig\TwigEngine;
use Tomahawk\Templating\Helper\BlocksHelper;
use Tomahawk\Templating\Helper\InputHelper;
use Tomahawk\Templating\Helper\RequestHelper;
use Tomahawk\Templating\Helper\SessionHelper;
use Tomahawk\Templating\Helper\TranslatorHelper;
use Tomahawk\Templating\Helper\UrlHelper;
use Tomahawk\Templating\Loader\FilesystemLoader;
use Tomahawk\Templating\Twig\Extension\RequestExtension;
use Tomahawk\Templating\Twig\Extension\SessionExtension;
use Tomahawk\Templating\Twig\Extension\TranslatorExtension;
use Tomahawk\Templating\Twig\Extension\UrlExtension;
use Tomahawk\Templating\Twig\Loader\FilesystemLoader as TwigFilesystemLoader;
use Tomahawk\Templating\Loader\TemplateLocator;
use Tomahawk\Templating\TemplateNameParser;
use Tomahawk\HttpKernel\Config\FileLocator;
use Symfony\Component\Templating\DelegatingEngine;
use Symfony\Component\Templating\Helper\SlotsHelper;
use Symfony\Component\Templating\PhpEngine;

class TemplatingServiceProvider implements ServiceProviderInterface
{
    public function register(ContainerInterface $container)
    {
        $container->set('php.helper.slots', function(ContainerInterface $c) {
            return new SlotsHelper();
        });

        $container->set('php.helper.blocks', function(ContainerInterface $c) {
            return new BlocksHelper();
        });

        $container->set('php.helper.translator', function(ContainerInterface $c) {
            return new TranslatorHelper($c['translator']);
        });

        $container->set('php.helper.url_generator', function(ContainerInterface $c) {
            return new UrlHelper($c['url_generator']);
        });

        $container->tag('php.helper.slots', 'php.helper');
        $container->tag('php.helper.blocks', 'php.helper');
        $container->tag('php.helper.translator', 'php.helper');
        $container->tag('php.helper.url_generator', 'php.helper');

        $container->set('twig.extension.translator', function(ContainerInterface $c) {
            return new TranslatorExtension($c['translator']);
        });

        $container->set('twig.extension.url_generator', function(ContainerInterface $c) {
            return new UrlExtension($c['url_generator']);
        });

        $container->tag('twig.extension.translator', 'twig.extension');
        $container->tag('twig.extension.url_generator', 'twig.extension');

        $container->set('templating.engine.php', function(ContainerInterface $c) {
            $kernel = $c['kernel'];
            $config = $c['config'];

            $locator = new FileLocator($kernel, $kernel->getRootDir() . '/Resources/');
            $templateLocator = new TemplateLocator($locator);
            $loader = new FilesystemLoader($templateLocator);
            $parser = new TemplateNameParser($kernel);

            $helpers = [];
            $helperServiceIds = $c->findTaggedServiceIds('php.helper');

            foreach ($helperServiceIds as $helperServiceId) {
                $helpers[] = $c[$helperServiceId];
            }

            $phpEngine = new PhpEngine($parser, $loader, $helpers);
            $phpEngine->setCharset($config->get('templating.charset', 'UTF-8'));

            // Add global variables
            $phpEngine->addGlobal('app', new GlobalVariables($c));

            // Add globals from config
            $globals = $config->get('templating.globals', []);

            foreach ($globals as $name => $value) {
                $phpEngine->addGlobal($name, $value);
            }

            $globalServiceIds = $c->findTaggedServiceIds('php.global');

            foreach ($globalServiceIds as $globalServiceId) {
                $global = $c[$globalServiceId];
                $phpEngine->addGlobal($globalServiceId, $global);
            }

            return $phpEngine;
        });

        $container->set('templating.engine.twig', function(ContainerInterface $c) {
            $kernel = $c['kernel'];
            $config = $c['config'];

            $locator = new FileLocator($kernel, $kernel->getRootDir() . '/Resources/');
            $templateLocator = new TemplateLocator($locator);
            $parser = new TemplateNameParser($kernel);

            $loader = new TwigFilesystemLoader($templateLocator, $parser);

            $twig = new \Twig_Environment($loader, array(
                'debug'            => $config->get('templating.twig.debug', false),
                'auto_reload'      => $config->get('templating.twig.auto_reload', false),
                'charset'          => $config->get('templating.twig.charset', 'UTF-8'),
                'cache'            => $config->get('templating.twig.cache', ''),
                'strict_variables' => $config->get('templating.twig.strict_variables', false),
                'autoescape'       => $config->get('templating.twig.autoescape', 'html'),
                'optimizations'    => $config->get('templating.twig.optimizations', -1),
            ));

            // Add global variables
            $twig->addGlobal('app', new GlobalVariables($c));

            // Add globals from config
            $globals = $config->get('templating.globals', array());

            foreach ($globals as $name => $value) {
                $twig->addGlobal($name, $value);
            }

            $globalServiceIds = $c->findTaggedServiceIds('twig.global');

            foreach ($globalServiceIds as $globalServiceId) {
                $global = $c[$globalServiceId];
                $twig->addGlobal($globalServiceId, $global);
            }

            $extensionServiceIds = $c->findTaggedServiceIds('twig.extension');

            foreach ($extensionServiceIds as $extensionServiceId) {
                $extension = $c[$extensionServiceId];
                $twig->addExtension($extension);
            }

            $filterServiceIds = $c->findTaggedServiceIds('twig.filter');

            foreach ($filterServiceIds as $filterServiceId) {
                $filter = $c[$filterServiceId];
                $twig->addFilter($filter);
            }

            return new TwigEngine($twig, $parser, $locator);
        });

        $container->tag('templating.engine.php', 'templating.engine');
        $container->tag('templating.engine.twig', 'templating.engine');

        $container->set('Symfony\Component\Templating\EngineInterface', $container->factory(function(ContainerInterface $c) {

            $engineServiceIds = $c->findTaggedServiceIds('templating.engine');

            $engines = array();

            foreach ($engineServiceIds as $engineServiceId) {
                $engines[] = $c[$engineServiceId];
            }

            return new DelegatingEngine($engines);
        }));

        $container->addAlias('twig', 'templating.engine.twig');

        $container->addAlias('templating', 'Symfony\Component\Templating\EngineInterface');
    }
}
