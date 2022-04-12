<?php

namespace SevenGroupFrance\HubScoreApiBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;

class HubScoreApiExtension extends ConfigurableExtension
{
    public function loadInternal(array $configs, ContainerBuilder $container)
    {
        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.xml');

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        
        $definition = $container->getDefinition('twitter');
        $definition->replaceArgument(0, $config['client_id']);
        $definition->replaceArgument(1, $config['client_secret']);
    }
}
