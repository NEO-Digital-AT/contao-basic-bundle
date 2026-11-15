<?php

declare(strict_types=1);

namespace Neo\BasicBundle\Tests\Dock;

use Neo\BasicBundle\Dock\DockPalette;
use PHPUnit\Framework\TestCase;

final class DockPaletteTest extends TestCase
{
    protected function tearDown(): void
    {
        unset($GLOBALS['TL_DCA']);
    }

    public function testTheSettingFollowsTheModulesInEveryLayoutTypeOnce(): void
    {
        $GLOBALS['TL_DCA']['tl_layout']['palettes'] = [
            '__selector__' => ['type', 'rows'],
            'default' => '{title_legend},name,type;{style_legend},framework;{modules_legend},modules;{script_legend},scripts',
            'modern' => '{title_legend},name,type;{modules_legend},template,modules',
        ];

        DockPalette::apply();
        DockPalette::apply();

        $this->assertSame('{title_legend},name,type;{style_legend},framework;{modules_legend},modules;{neo_dock_legend},neo_dock;{script_legend},scripts', $GLOBALS['TL_DCA']['tl_layout']['palettes']['default']);
        $this->assertSame('{title_legend},name,type;{modules_legend},template,modules;{neo_dock_legend},neo_dock', $GLOBALS['TL_DCA']['tl_layout']['palettes']['modern']);
        $this->assertSame(['type', 'rows'], $GLOBALS['TL_DCA']['tl_layout']['palettes']['__selector__']);
    }

    public function testWithoutLayoutTableNothingHappens(): void
    {
        DockPalette::apply();

        $this->assertArrayNotHasKey('tl_layout', $GLOBALS['TL_DCA'] ?? []);
    }
}
