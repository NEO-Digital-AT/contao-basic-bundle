<?php

declare(strict_types=1);

namespace Neo\BasicBundle\Backend;

use Contao\System;
use Neo\BasicBundle\Access\AccessPage;
use Neo\BasicBundle\Ai\AiConnection;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * "Test connection" of the AI connection on "Access keys and licences"
 * (key=neo_ai_test): calls `GET {base}/models` of the route from this server
 * and shows status, duration and the answer in the section, in place (see
 * AccessActions). The key is never shown: only length, start and end, and it
 * is masked in every message.
 */
final class AiTestOperation
{
    public const KEY = 'neo_ai_test';

    public const TARGET = 'neo_access_ai';

    public function __construct(
        private readonly AiConnection $connection,
        private readonly HttpClientInterface $httpClient,
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
        $key = $this->connection->apiKey();
        $baseUrl = $this->connection->baseUrl();

        if ('' === $key || '' === $baseUrl) {
            return [false, TestResult::line('tl_error', '' === $key ? ($lang['ai_no_key'] ?? '') : ($lang['ai_no_url'] ?? ''))];
        }

        $details = TestResult::details([
            $lang['ai_test_address'] ?? 'Address' => $baseUrl.'/models',
            $lang['ai_api_key'][0] ?? 'Key' => TestResult::fingerprint($key),
        ]);

        $start = microtime(true);

        try {
            $response = $this->httpClient->request(
                'GET',
                $baseUrl.'/models',
                ['headers' => ['Authorization' => 'Bearer '.$key, 'Accept' => 'application/json']],
            );

            $status = $response->getStatusCode();
            $body = $response->getContent(false);
        } catch (\Throwable $e) {
            return [false, TestResult::line('tl_error', \sprintf($lang['ai_test_failed'] ?? '%s', TestResult::mask($e->getMessage(), $key))).$details];
        }

        $ms = (int) ((microtime(true) - $start) * 1000);

        if ($status >= 200 && $status < 300) {
            $data = json_decode($body, true);
            $count = \is_array($data) ? \count((array) ($data['data'] ?? [])) : 0;

            return [true, TestResult::line('tl_confirm', \sprintf($lang['ai_test_ok'] ?? '%d %d %d', $status, $ms, $count)).$details];
        }

        return [false, TestResult::line('tl_error', \sprintf($lang['ai_test_http'] ?? '%d %d', $status, $ms)).TestResult::answer($body, $key).$details];
    }
}
