<?php

declare(strict_types=1);

namespace Neo\BasicBundle\DependencyInjection;

use Neo\BasicBundle\License\LicenseService;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Technical settings only. The AI connection lives in the Contao settings, not
 * here. The licence service address can be changed for a test installation;
 * certificates are still checked against the public keys in the code. The
 * address of DeepL can be changed for a test installation too; empty, it
 * follows from the key (DeepL API Free or Pro).
 */
final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('neo_basic');
        $treeBuilder->getRootNode()
            ->children()
            ->scalarNode('license_url')
            ->defaultValue(LicenseService::DEFAULT_URL)
            ->cannotBeEmpty()
            ->end()
            ->scalarNode('deepl_url')
            ->defaultValue('')
            ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
