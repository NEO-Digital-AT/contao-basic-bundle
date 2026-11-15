<?php

declare(strict_types=1);

namespace Neo\BasicBundle\Dock;

use Contao\CoreBundle\DataContainer\PaletteManipulator;

/**
 * Shows the setting of the dock (field neo_dock, defined by this bundle) in
 * the page layout. Called from contao/dca/tl_layout.php of each extension
 * that puts a button into the dock, so the setting appears only where an
 * installed extension uses it; calling it again changes nothing.
 */
final class DockPalette
{
    public static function apply(): void
    {
        $palettes = $GLOBALS['TL_DCA']['tl_layout']['palettes'] ?? [];

        foreach ($palettes as $name => $palette) {
            if ('__selector__' === $name || !\is_string($palette) || preg_match('/[,;{]neo_dock(?:[,;]|$)/', $palette)) {
                continue;
            }

            $manipulator = PaletteManipulator::create()
                ->addLegend('neo_dock_legend', 'modules_legend', PaletteManipulator::POSITION_AFTER)
                ->addField('neo_dock', 'neo_dock_legend', PaletteManipulator::POSITION_APPEND)
            ;

            $manipulator->applyToPalette($name, 'tl_layout');
        }
    }
}
