<?php

declare(strict_types=1);

namespace Neo\BasicBundle\Translation;

use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * DeepL (API v2) for every extension that translates texts. Texts go as HTML
 * (tag_handling "html"); Contao's insert tags are protected exactly as for
 * Azure Translator (HtmlText) and put back afterwards. The formality is left
 * to DeepL. Only the texts are sent, together with the two language codes.
 */
final class DeepLTranslator implements TranslatorInterface
{
    public const SERVICE = 'DeepL';

    /**
     * A request may carry at most 128 KiB; 30,000 characters stay below it even in scripts with four bytes a character.
     */
    private const MAX_ITEMS = 50;

    private const MAX_CHARACTERS = 30000;

    public function __construct(
        private readonly DeepLConnection $connection,
        private readonly HttpClientInterface $httpClient,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function translate(array $texts, string $from, string $to, bool $html = true): array
    {
        if (!$this->connection->isConfigured()) {
            throw TranslationException::notConfiguredFor(self::SERVICE);
        }

        $results = [];

        foreach (HtmlText::batches($texts, self::MAX_ITEMS, self::MAX_CHARACTERS) as $batch) {
            $prepared = array_map(static fn (string $text): array => HtmlText::prepare($text, $html), $batch);
            $translated = $this->request(array_column($prepared, 0), self::sourceCode($from), self::targetCode($to));

            foreach ($translated as $index => $text) {
                $results[] = HtmlText::finish($text, $prepared[$index][1], $html);
            }
        }

        return $results;
    }

    public function isConfigured(): bool
    {
        return $this->connection->isConfigured();
    }

    /**
     * "Test connection": the usage of the current billing period (GET /v2/usage).
     *
     * @return array{status: int, milliseconds: int, count: int|null, limit: int|null, body: string}
     *
     * @throws TranslationException when no key is stored
     * @throws \Throwable           when the service cannot be reached
     */
    public function usage(): array
    {
        if (!$this->connection->isConfigured()) {
            throw TranslationException::notConfiguredFor(self::SERVICE);
        }

        $start = microtime(true);

        $response = $this->httpClient->request(
            'GET',
            $this->connection->baseUrl().'/v2/usage',
            [
                'headers' => $this->headers(),
                'timeout' => 15,
            ],
        );

        $status = $response->getStatusCode();
        $body = $response->getContent(false);
        $data = json_decode($body, true);

        return [
            'status' => $status,
            'milliseconds' => (int) ((microtime(true) - $start) * 1000),
            'count' => isset($data['character_count']) ? (int) $data['character_count'] : null,
            'limit' => isset($data['character_limit']) ? (int) $data['character_limit'] : null,
            'body' => $body,
        ];
    }

    /**
     * Contao's language (de, en, de-AT, pt-BR …) as DeepL's source language: the language part in capitals.
     */
    public static function sourceCode(string $language): string
    {
        $primary = strtolower(explode('-', str_replace('_', '-', trim($language)))[0]);

        return match ($primary) {
            'no' => 'NB',
            default => strtoupper($primary),
        };
    }

    /**
     * Contao's language as DeepL's target language. English, Portuguese and
     * Chinese need their variant: EN-GB unless American English is asked for,
     * PT-PT unless Brazilian, ZH-HANS unless traditional characters.
     */
    public static function targetCode(string $language): string
    {
        $language = strtolower(str_replace('_', '-', trim($language)));
        $primary = explode('-', $language)[0];

        return match ($primary) {
            'en' => 'en-us' === $language ? 'EN-US' : 'EN-GB',
            'pt' => 'pt-br' === $language ? 'PT-BR' : 'PT-PT',
            'zh' => \in_array($language, ['zh-tw', 'zh-hk', 'zh-mo', 'zh-hant'], true) ? 'ZH-HANT' : 'ZH-HANS',
            'no' => 'NB',
            default => strtoupper($primary),
        };
    }

    /**
     * @param list<string> $texts
     *
     * @return list<string>
     *
     * @throws TranslationException
     */
    private function request(array $texts, string $from, string $to): array
    {
        try {
            $response = $this->httpClient->request(
                'POST',
                $this->connection->baseUrl().'/v2/translate',
                [
                    'headers' => $this->headers(),
                    'json' => [
                        'text' => $texts,
                        'source_lang' => $from,
                        'target_lang' => $to,
                        'tag_handling' => 'html',
                    ],
                    'timeout' => 30,
                ],
            );

            $status = $response->getStatusCode();
            $data = $response->toArray(false);
        } catch (\Throwable $e) {
            $this->logger->warning('DeepL not reachable: '.$e->getMessage());

            throw TranslationException::unreachableFor(self::SERVICE);
        }

        $translations = $data['translations'] ?? null;

        if (200 !== $status || !\is_array($translations) || !array_is_list($translations) || \count($translations) !== \count($texts)) {
            // 403: key not accepted, 456: quota of the billing period used up, 400: e.g. a language DeepL does not offer.
            $this->logger->warning(\sprintf('DeepL answered HTTP %d: %s', $status, mb_substr((string) ($data['message'] ?? ''), 0, 200)));

            throw TranslationException::rejectedBy(self::SERVICE, $status);
        }

        return array_map(static fn (mixed $item): string => (string) ($item['text'] ?? ''), $translations);
    }

    /**
     * @return array<string, string>
     */
    private function headers(): array
    {
        return ['Authorization' => 'DeepL-Auth-Key '.$this->connection->key(), 'Accept' => 'application/json'];
    }
}
