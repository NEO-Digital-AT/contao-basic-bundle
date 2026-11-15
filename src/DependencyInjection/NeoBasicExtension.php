<?php

declare(strict_types=1);

namespace Neo\BasicBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

final class NeoBasicExtension extends Extension
{
    public function getAlias(): string
    {
        return 'neo_basic';
    }

    public function load(array $configs, ContainerBuilder $container): void
    {
        $config = $this->processConfiguration(new Configuration(), $configs);

        $container->setParameter('neo_basic.license_url', rtrim((string) $config['license_url'], '/'));
        $container->setParameter('neo_basic.deepl_url', rtrim((string) $config['deepl_url'], '/'));

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../../config'));
        $loader->load('services.yaml');
    }
}
