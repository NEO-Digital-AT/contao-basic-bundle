<?php

declare(strict_types=1);

namespace Neo\BasicBundle\Backend;

use Contao\System;
use Neo\BasicBundle\Access\AccessPage;
use Neo\BasicBundle\Translation\AzureConnection;
use Neo\BasicBundle\Translation\AzureTranslator;

/**
 * "Test connection" of Azure Translator on "Access keys and licences"
 * (key=neo_azure_translator_test): translates one word from this server and
 * shows status and duration in the section, in place (see AccessActions). The
 * key appears only as length, start and end, and is masked in every message.
 */
final class AzureTestOperation
{
    public const KEY = 'neo_azure_translator_test';

    public const TARGET = 'neo_access_azure_translator';

    public function __construct(
        private readonly AzureConnection $connection,
        private readonly AzureTranslator $translator,
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
        $baseUrl = $this->connection->baseUrl();

        if ('' === $key || '' === $baseUrl) {
            return [false, TestResult::line('tl_error', '' === $key ? ($lang['azure_translator_no_key'] ?? '') : ($lang['azure_translator_no_url'] ?? ''))];
        }

        $details = TestResult::details([
            $lang['azure_translator_test_address'] ?? 'Address' => $baseUrl.'/translate',
            $lang[AzureConnection::REGION][0] ?? 'Region' => '' !== $this->connection->region() ? $this->connection->region() : '–',
            $lang[AzureConnection::KEY][0] ?? 'Key' => TestResult::fingerprint($key),
        ]);

        try {
            $result = $this->translator->probe();
        } catch (\Throwable $e) {
            return [false, TestResult::line('tl_error', \sprintf($lang['azure_translator_test_failed'] ?? '%s', TestResult::mask($e->getMessage(), $key))).$details];
        }

        if ($result['status'] >= 200 && $result['status'] < 300) {
            return [true, TestResult::line('tl_confirm', \sprintf($lang['azure_translator_test_ok'] ?? '%d %d', $result['status'], $result['milliseconds'])).$details];
        }

        return [false, TestResult::line('tl_error', \sprintf($lang['azure_translator_test_http'] ?? '%d %d', $result['status'], $result['milliseconds'])).TestResult::answer($result['body'], $key).$details];
    }
}
