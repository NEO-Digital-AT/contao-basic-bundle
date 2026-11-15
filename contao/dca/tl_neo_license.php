<?php

declare(strict_types=1);

use Contao\DC_Table;

/*
 * tl_neo_license — storage of the licence of each paid extension (one row per
 * extension code): the encrypted key and the last signed certificate of the
 * licence service. No back-end module of its own; the key is edited in the
 * settings of the extension (Neo\BasicBundle\License\LicenseFields).
 */
$GLOBALS['TL_DCA']['tl_neo_license'] = [
    'config' => [
        'dataContainer' => DC_Table::class,
        'closed' => true,
        'notEditable' => true,
        'notCopyable' => true,
        'notDeletable' => true,
        'sql' => [
            'keys' => [
                'id' => 'primary',
                'module' => 'unique',
            ],
        ],
    ],
    'fields' => [
        'id' => ['sql' => 'int(10) unsigned NOT NULL auto_increment'],
        'tstamp' => ['sql' => 'int(10) unsigned NOT NULL default 0'],
        'module' => ['sql' => "varchar(32) NOT NULL default ''"],
        'package' => ['sql' => "varchar(128) NOT NULL default ''"],
        'license_key' => ['sql' => "varchar(255) NOT NULL default ''"],
        'payload' => ['sql' => 'text NULL'],
        'signature' => ['sql' => "varchar(255) NOT NULL default ''"],
        'key_id' => ['sql' => "varchar(64) NOT NULL default ''"],
        'domains' => ['sql' => 'text NULL'],
        'coverage' => ['sql' => 'text NULL'],
        // Installations of the licence (allowed, used) and domains per installation, as the licence service said.
        'allotment' => ['sql' => 'text NULL'],
        'checked_at' => ['sql' => 'int(10) unsigned NOT NULL default 0'],
        'next_check_at' => ['sql' => 'int(10) unsigned NOT NULL default 0'],
        'last_error' => ['sql' => "varchar(255) NOT NULL default ''"],
    ],
];
