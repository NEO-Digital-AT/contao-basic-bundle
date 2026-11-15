<?php

declare(strict_types=1);

namespace Neo\BasicBundle\EventListener;

use Contao\CoreBundle\DependencyInjection\Attribute\AsHook;

/**
 * The shared script and stylesheet of the back-end modules of the extensions (public/actions.js and
 * actions.css): a spinner on the button or link that was used until the page answers, and for submit buttons
 * marked with data-neo-action the place on the screen and their messages right at them after the page loaded
 * again. Added to every module of the menu group "neo", after the module's own files.
 */
#[AsHook('initializeSystem')]
final class BackendActionsListener
{
    public const SCRIPT = 'bundles/neobasic/actions.js';

    public const STYLESHEET = 'bundles/neobasic/actions.css';

    public function __invoke(): void
    {
        foreach (array_keys($GLOBALS['BE_MOD']['neo'] ?? []) as $name) {
            $module = &$GLOBALS['BE_MOD']['neo'][$name];
            $module['javascript'] = [...array_diff((array) ($module['javascript'] ?? []), [self::SCRIPT]), self::SCRIPT];
            $module['stylesheet'] = [...array_diff((array) ($module['stylesheet'] ?? []), [self::STYLESHEET]), self::STYLESHEET];
            unset($module);
        }
    }
}
