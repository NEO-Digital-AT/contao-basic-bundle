<?php

declare(strict_types=1);

namespace Neo\BasicBundle\Backend;

use Contao\StringUtil;

/**
 * The pieces of a result of "Test connection", in Contao's back-end classes.
 * Keys appear only as length, start and end, and are masked in answers.
 *
 * @internal
 */
final class TestResult
{
    /**
     * A status line (tl_confirm, tl_info, tl_error).
     */
    public static function line(string $class, string $text): string
    {
        return \sprintf('<div class="tl_message"><p class="%s">%s</p></div>', $class, StringUtil::specialchars($text));
    }

    /**
     * What was tested, in one quiet line: "Address queried: … · Key: …".
     *
     * @param array<string, string> $rows label => value
     */
    public static function details(array $rows): string
    {
        $parts = [];

        foreach ($rows as $label => $value) {
            $parts[] = StringUtil::specialchars($label.': '.$value);
        }

        return '<p class="tl_help">'.implode(' · ', $parts).'</p>';
    }

    /**
     * The beginning of an error answer of the service, the key masked.
     */
    public static function answer(string $body, string $key): string
    {
        $body = trim(self::mask(mb_substr($body, 0, 400), $key));

        return '' !== $body ? '<pre class="tl_help" style="white-space:pre-wrap">'.StringUtil::specialchars($body).'</pre>' : '';
    }

    public static function mask(string $text, string $key): string
    {
        return \strlen($key) >= 6 ? str_replace([$key, rawurlencode($key)], '***', $text) : $text;
    }

    /**
     * Enough to compare with the original (length, start, end), never the key itself.
     */
    public static function fingerprint(string $key): string
    {
        return \sprintf('%d · %s…%s', mb_strlen($key), mb_substr($key, 0, 4), mb_substr($key, -2));
    }
}
