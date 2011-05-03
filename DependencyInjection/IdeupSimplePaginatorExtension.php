<?php
/**
 * GNFCustomerManagerExtension class definition
 * @copyright 2011 ideup!
 */

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
 */
class IdeupSimplePaginatorExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        // registering services
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');

        // registering twig extension
        $definition = new Definition('Ideaup\SimplePaginatorBundle\TwigExtension\MyTwigExtension');
        $definition->addTag('twig.extension');
        $container->setDefinition('my_twig_extension', $definition);
    }

    // sounds like this is not needed anymore since Symfony2 calls 'load' method automatically now
    public function getAlias()
    {
        return 'ideup_simple_paginator';
    }
}
