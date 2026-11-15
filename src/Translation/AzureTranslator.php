<?php

declare(strict_types=1);

namespace Neo\BasicBundle\Translation;

use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Azure Translator, Text Translation v3, for every extension that translates
 * texts. Texts go as HTML; Contao's insert tags ({{…}}) are protected: in
 * text with translate="no", inside tags (e.g. a link's href) as a
 * placeholder, and put back afterwards (HtmlText). Only the texts are sent,
 * together with the two language codes.
 */
final class AzureTranslator implements TranslatorInterface
{
    private const MAX_ITEMS = 100;

    private const MAX_CHARACTERS = 40000;

    public function __construct(
        private readonly AzureConnection $connection,
        private readonly HttpClientInterface $httpClient,
        private readonly LoggerInterface $logger,
    ) {
    }

    /**
     * @param list<string> $texts HTML (rich text) or plain text, as given by $html
     *
     * @return list<string> translations in the same order and form
     *
     * @throws TranslationException
     */
    public function translate(array $texts, string $from, string $to, bool $html = true): array
    {
        if (!$this->connection->isConfigured()) {
            throw TranslationException::notConfigured();
        }

        $results = [];

        foreach (HtmlText::batches($texts, self::MAX_ITEMS, self::MAX_CHARACTERS) as $batch) {
            $prepared = array_map(static fn (string $text): array => HtmlText::prepare($text, $html), $batch);
            $translated = $this->request(array_column($prepared, 0), self::languageCode($from), self::languageCode($to));

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
     * One short request for "Test connection".
     *
     * @return array{status: int, milliseconds: int, body: string}
     *
     * @throws TranslationException
     */
    public function probe(): array
    {
        if (!$this->connection->isConfigured()) {
            throw TranslationException::notConfigured();
        }

        $start = microtime(true);

        $response = $this->httpClient->request(
            'POST',
            $this->connection->baseUrl().'/translate',
            [
                'query' => ['api-version' => '3.0', 'to' => 'de', 'textType' => 'plain'],
                'headers' => $this->headers(),
                'json' => [['Text' => 'Hello']],
                'timeout' => 15,
            ],
        );

        return [
            'status' => $response->getStatusCode(),
            'milliseconds' => (int) ((microtime(true) - $start) * 1000),
            'body' => $response->getContent(false),
        ];
    }

    /**
     * Contao's language (de, en, de-AT, zh-Hans …) as Azure's code: the language part, except for scripts Azure distinguishes.
     */
    public static function languageCode(string $language): string
    {
        $language = str_replace('_', '-', trim($language));

        return match (strtolower($language)) {
            'zh', 'zh-cn', 'zh-sg', 'zh-hans' => 'zh-Hans',
            'zh-tw', 'zh-hk', 'zh-hant' => 'zh-Hant',
            'pt-pt' => 'pt-pt',
            'sr-latn' => 'sr-Latn',
            'sr', 'sr-cyrl' => 'sr-Cyrl',
            default => strtolower(explode('-', $language)[0]),
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
                $this->connection->baseUrl().'/translate',
                [
                    'query' => ['api-version' => '3.0', 'from' => $from, 'to' => $to, 'textType' => 'html'],
                    'headers' => $this->headers(),
                    'json' => array_map(static fn (string $text): array => ['Text' => $text], $texts),
                    'timeout' => 30,
                ],
            );

            $status = $response->getStatusCode();
            $data = $response->toArray(false);
        } catch (\Throwable $e) {
            $this->logger->warning('Azure Translator not reachable: '.$e->getMessage());

            throw TranslationException::unreachable();
        }

        if (200 !== $status || !array_is_list($data) || \count($data) !== \count($texts)) {
            $this->logger->warning(\sprintf('Azure Translator answered HTTP %d (code %s).', $status, (string) ($data['error']['code'] ?? '–')));

            throw TranslationException::rejected($status);
        }

        return array_map(static fn (mixed $item): string => (string) ($item['translations'][0]['text'] ?? ''), $data);
    }

    /**
     * @return array<string, string>
     */
    private function headers(): array
    {
        $headers = ['Ocp-Apim-Subscription-Key' => $this->connection->key(), 'Content-Type' => 'application/json; charset=UTF-8'];

        if ('' !== $this->connection->region()) {
            $headers['Ocp-Apim-Subscription-Region'] = $this->connection->region();
        }

        return $headers;
    }
}
