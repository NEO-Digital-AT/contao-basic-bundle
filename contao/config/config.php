<?php

declare(strict_types=1);

use Neo\BasicBundle\Access\AccessPage;
use Neo\BasicBundle\Backend\AiTestOperation;
use Neo\BasicBundle\Backend\AzureTestOperation;
use Neo\BasicBundle\Backend\DeepLTestOperation;
use Neo\BasicBundle\License\LicenseFields;

/*
 * Back-end menu group of the extensions. Each extension registers its modules
 * under $GLOBALS['BE_MOD']['neo']; this bundle names the group
 * (languages/…/modules.php) and adds "Access keys and licences", which
 * BackendMenuListener moves to the end of the group or leaves out when no
 * installed extension adds a section to it. The operations run "Test
 * connection" and "Check now"; the script runs them in place (AccessActions).
 */
$GLOBALS['BE_MOD']['neo'][AccessPage::MODULE] = [
    'tables' => [AccessPage::TABLE],
    'javascript' => 'bundles/neobasic/access.js',
    'stylesheet' => 'bundles/neobasic/access.css',
    AiTestOperation::KEY => [AiTestOperation::class, '__invoke'],
    AzureTestOperation::KEY => [AzureTestOperation::class, '__invoke'],
    DeepLTestOperation::KEY => [DeepLTestOperation::class, '__invoke'],
    LicenseFields::OPERATION => [LicenseFields::class, 'refreshOperation'],
];
