<?php

declare(strict_types=1);

namespace Neo\BasicBundle\Tests\Support;

use Contao\System;

/**
 * Loads the English language files of this bundle into $GLOBALS['TL_LANG'] and
 * marks them as loaded, so System::loadLanguageFile() returns without the
 * Contao framework.
 */
abstract class LanguageFiles extends System
{
    public static function load(string ...$names): void
    {
        $GLOBALS['TL_LANGUAGE'] = 'en';

        foreach ($names as $name) {
            include \dirname(__DIR__, 2).'/contao/languages/en/'.$name.'.php';

            static::$arrLanguageFiles[$name]['en'] = true;
        }
    }

    public static function reset(): void
    {
        static::$arrLanguageFiles = [];
        unset($GLOBALS['TL_LANG'], $GLOBALS['TL_LANGUAGE']);
    }
}
