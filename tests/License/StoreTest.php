<?php

declare(strict_types=1);

namespace Neo\BasicBundle\Tests\License;

use Neo\BasicBundle\License\Store;
use PHPUnit\Framework\TestCase;

final class StoreTest extends TestCase
{
    public function testAnExtensionLinksToItsPageInTheStore(): void
    {
        $this->assertSame('https://store.neo-digital.at/extensions/superagent', Store::url('neo-digital/contao-superagent-bundle'));
        $this->assertSame('https://store.neo-digital.at/extensions/frontend-editor', Store::url('neo-digital/contao-frontendeditor-bundle'));
        $this->assertSame('https://store.neo-digital.at/extensions/website-check', Store::url('neo-digital/contao-websitecheck-bundle'));
        $this->assertSame('https://store.neo-digital.at/extensions/accessibility', Store::url('neo-digital/contao-accessibility-bundle'));
    }

    public function testAPackageWithoutPageLinksToTheStartPage(): void
    {
        $this->assertSame('https://store.neo-digital.at', Store::url('acme/not-installed'));
    }
}
