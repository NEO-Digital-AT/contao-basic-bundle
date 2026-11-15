<?php

declare(strict_types=1);

use Contao\DC_Table;

/*
 * tl_neo_terms — confirmations of the terms of the extensions, one row per
 * passage and confirmation; all rows are kept as a record. No back-end module
 * of its own; confirmed on "Access keys and licences"
 * (Neo\BasicBundle\Terms\TermsFields).
 */
$GLOBALS['TL_DCA']['tl_neo_terms'] = [
    'config' => [
        'dataContainer' => DC_Table::class,
        'closed' => true,
        'notEditable' => true,
        'notCopyable' => true,
        'notDeletable' => true,
        'sql' => [
            'keys' => [
                'id' => 'primary',
                'module' => 'index',
            ],
        ],
    ],
    'fields' => [
        'id' => ['sql' => 'int(10) unsigned NOT NULL auto_increment'],
        'tstamp' => ['sql' => 'int(10) unsigned NOT NULL default 0'],
        'module' => ['sql' => "varchar(32) NOT NULL default ''"],
        'passage' => ['sql' => "varchar(32) NOT NULL default ''"],
        'version' => ['sql' => 'int(10) unsigned NOT NULL default 0'],
        'user_id' => ['sql' => 'int(10) unsigned NOT NULL default 0'],
        'username' => ['sql' => "varchar(64) NOT NULL default ''"],
        // Language the text was shown in; the German text is binding.
        'language' => ['sql' => "varchar(8) NOT NULL default ''"],
    ],
];
