<?php

declare(strict_types=1);

namespace Neo\BasicBundle\ContaoManager;

use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use Neo\BasicBundle\NeoBasicBundle;

/**
 * Contao Manager plugin: registers the bundle. The extensions load after it;
 * it names their shared menu group "neo" (back end, front-end modules, content
 * elements), none of them names the group itself.
 */
class Plugin implements BundlePluginInterface
{
    public function getBundles(ParserInterface $parser): array
    {
        return [
            BundleConfig::create(NeoBasicBundle::class)
                ->setLoadAfter([ContaoCoreBundle::class]),
        ];
    }
}
