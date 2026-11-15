<?php

declare(strict_types=1);

use Neo\BasicBundle\Dock\Dock;

/*
 * Buttons at the edge of the screen (dock): how the buttons of the extensions
 * are arranged when several sit in one corner. The field shows in the page
 * layout once an extension that uses the dock calls DockPalette::apply() in
 * its own contao/dca/tl_layout.php.
 */
$GLOBALS['TL_DCA']['tl_layout']['fields']['neo_dock'] = [
    'exclude' => true,
    'inputType' => 'select',
    'options' => Dock::ARRANGEMENTS,
    'reference' => &$GLOBALS['TL_LANG']['tl_layout']['neo_dock_options'],
    'eval' => ['tl_class' => 'w50'],
    'sql' => "varchar(16) NOT NULL default '".Dock::DEFAULT_ARRANGEMENT."'",
];
