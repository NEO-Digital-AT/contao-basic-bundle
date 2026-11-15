<?php

declare(strict_types=1);

namespace Neo\BasicBundle\Backend;

use Contao\System;
use Neo\BasicBundle\Access\AccessPage;
use Neo\BasicBundle\Translation\DeepLConnection;
use Neo\BasicBundle\Translation\DeepLTranslator;

/**
 * "Test connection" of DeepL on "Access keys and licences" (key=neo_deepl_test):
 * asks DeepL from this server for the usage of the current billing period
 * (GET /v2/usage) and shows status, duration and the characters used of the
 * limit in the section, in place (see AccessActions). Nothing is translated.
 * The key appears only as length, start and end.
 */
final class DeepLTestOperation
{
    public const KEY = 'neo_deepl_test';

    public const TARGET = 'neo_access_deepl';

    public function __construct(
        private readonly DeepLConnection $connection,
        private readonly DeepLTranslator $translator,
        private readonly AccessActions $actions,
    ) {
    }

    public function __invoke(): never
    {
        System::loadLanguageFile(AccessPage::TABLE);

        $lang = $GLOBALS['TL_LANG'][AccessPage::TABLE];

        $this->actions->requireToken(self::TARGET, $lang['action_invalid_token'] ?? '');

        [$ok, $html] = $this->result($lang);

        $this->actions->respond(self::TARGET, $html, $ok);
    }

    /**
     * @param array<string, mixed> $lang
     *
     * @return array{0: bool, 1: string}
     */
    private function result(array $lang): array
    {
        $key = $this->connection->key();

        if ('' === $key) {
            return [false, TestResult::line('tl_error', $lang['deepl_no_key'] ?? '')];
        }

        $details = TestResult::details([
            $lang['deepl_test_address'] ?? 'Address' => $this->connection->baseUrl().'/v2/usage',
            $lang['deepl_key'][0] ?? 'Key' => TestResult::fingerprint($key),
        ]);

        try {
            $usage = $this->translator->usage();
        } catch (\Throwable $e) {
            return [false, TestResult::line('tl_error', \sprintf($lang['deepl_test_failed'] ?? '%s', TestResult::mask($e->getMessage(), $key))).$details];
        }

        if (200 === $usage['status']) {
            $text = null !== $usage['count'] && null !== $usage['limit']
                ? \sprintf($lang['deepl_test_ok'] ?? '%d %d %s %s', $usage['status'], $usage['milliseconds'], $this->number($usage['count']), $this->number($usage['limit']))
                : \sprintf($lang['deepl_test_ok_short'] ?? '%d %d', $usage['status'], $usage['milliseconds']);

            return [true, TestResult::line('tl_confirm', $text).$details];
        }

        $hint = match ($usage['status']) {
            403 => $lang['deepl_test_403'] ?? '',
            456 => $lang['deepl_test_456'] ?? '',
            default => '',
        };

        return [false, TestResult::line('tl_error', trim(\sprintf($lang['deepl_test_http'] ?? '%d %d', $usage['status'], $usage['milliseconds']).' '.$hint)).TestResult::answer($usage['body'], $key).$details];
    }

    private function number(int $value): string
    {
        return number_format($value, 0, '', $GLOBALS['TL_LANG']['MSC']['thousandsSeparator'] ?? ',');
    }
}
