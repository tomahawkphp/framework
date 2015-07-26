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

class TemplatingProvider implements ServiceProviderInterface
{
    public function register(ContainerInterface $container)
    {
        $container->set('templating.php.helpers', function(ContainerInterface $c) {

            return array(
                new SlotsHelper(),
                new BlocksHelper(),
                new TranslatorHelper($c['translator']),
                new UrlHelper($c['url_generator']),
                new RequestHelper($c['request_stack']),
                new SessionHelper($c['session']),
                new InputHelper($c['input']),
            );
        });

        $container->set('templating.twig.extensions', function(ContainerInterface $c) {

            return array(
                new TranslatorExtension($c['translator']),
                new UrlExtension($c['url_generator']),
                new RequestExtension($c['request_stack']),
                new SessionExtension($c['session']),
            );
        });

        $container->set('templating.engine.php', function(ContainerInterface $c) {
            $kernel = $c['kernel'];
            $locator = new FileLocator($kernel, $kernel->getRootDir() . '/Resources/');
            $templateLocator = new TemplateLocator($locator);
            $loader = new FilesystemLoader($templateLocator);
            $parser = new TemplateNameParser($kernel);
            return new PhpEngine($parser, $loader, $c['templating.php.helpers']);
        });

        $container->set('templating.engine.twig', function(ContainerInterface $c) {
            $kernel = $c['kernel'];
            $locator = new FileLocator($kernel, $kernel->getRootDir() . '/Resources/');
            $templateLocator = new TemplateLocator($locator);
            $parser = new TemplateNameParser($kernel);

            $loader = new TwigFilesystemLoader($templateLocator, $parser);

            $twig = new \Twig_Environment($loader);

            $extensions = $c['templating.twig.extensions'];

            foreach ($extensions as $extension) {
                $twig->addExtension($extension);
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

        $container->addAlias('templating', 'Symfony\Component\Templating\EngineInterface');
    }
}
