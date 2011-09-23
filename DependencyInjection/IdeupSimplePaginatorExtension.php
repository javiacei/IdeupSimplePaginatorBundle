<?php

namespace Ideup\SimplePaginatorBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\Config\FileLocator;

/**
 * IdeupSimplePaginator Dependency Injection Extension
 *
 * Class that defines the Dependency Injection Extension to expose the bundle's semantic configuration
 * @package IdeupSimplePaginatorBundle
 * @subpackage DependencyInjection
 * @author Francisco Javier Aceituno <javier.aceituno@ideup.com>
 * @author Moises Maciá <moises.macia@ideup.com>
 * @author Gustavo Piltcher
 */
class IdeupSimplePaginatorExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        // registering services
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('paginator.xml');
        $loader->load('twig.xml');
        $loader->load('templating.xml');
    }
}
