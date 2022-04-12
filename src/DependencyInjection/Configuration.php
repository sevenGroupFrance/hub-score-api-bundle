<?php

// src/Acme/SocialBundle/DependencyInjection/Configuration.php
namespace SevenGroupFrance\HubScoreApiBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('hub_score_api');

        $treeBuilder->getRootNode()
            ->children()
                ->arrayNode('login')
                    ->children()
                        ->scalarNode('id')->end()
                        ->scalarNode('pswd')->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
