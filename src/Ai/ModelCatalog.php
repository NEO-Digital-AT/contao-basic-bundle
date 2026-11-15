<?php

declare(strict_types=1);

namespace Neo\BasicBundle\Ai;

use Psr\Cache\CacheItemPoolInterface;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * The models the route offers (OpenAI-compatible `GET {base}/models`), so a
 * model field in the back end can be a searchable selection instead of a field
 * to type an identifier into. Empty without a key or when the route does not
 * answer; the field then stays a text field, so nobody locks themselves out.
 * Cached for an hour.
 */
final class ModelCatalog
{
    private const TTL = 3600;

    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly CacheItemPoolInterface $cache,
        private readonly AiConnection $connection,
        private readonly LoggerInterface $logger,
    ) {
    }

    /**
     * @return array<string, string> model id => model id, sorted
     */
    public function models(): array
    {
        $key = $this->connection->apiKey();
        $baseUrl = $this->connection->baseUrl();

        if ('' === $key || '' === $baseUrl) {
            return [];
        }

        $item = $this->cache->getItem('neo_basic_models_'.hash('sha256', $baseUrl.'|'.$key));

        if ($item->isHit()) {
            return (array) $item->get();
        }

        try {
            $data = $this->httpClient->request(
                'GET',
                $baseUrl.'/models',
                ['headers' => ['Authorization' => 'Bearer '.$key, 'Accept' => 'application/json']],
            )->toArray(false);
        } catch (\Throwable $e) {
            $this->logger->warning('Basic: could not load the model list: '.$e->getMessage());

            return [];
        }

        $models = [];

        // { "data": [ { "id": "…" }, … ] } — the id is also the label.
        foreach ((array) ($data['data'] ?? []) as $entry) {
            $id = trim((string) ($entry['id'] ?? ''));

            if ('' !== $id) {
                $models[$id] = $id;
            }
        }

        if ([] === $models) {
            return [];
        }

        ksort($models);

        $item->set($models)->expiresAfter(self::TTL);
        $this->cache->save($item);

        return $models;
    }

    /**
     * What the route says about a model: whether it can reason ("thinking"), call
     * tools, and how many tokens it may write at most. Null where the route does
     * not say (OpenAI-compatible routes other than Requesty list ids only).
     *
     * @return array{reasoning: bool|null, tools: bool|null, maxOutput: int|null}
     */
    public function capabilities(string $model): array
    {
        $entry = $this->entries()[$model] ?? [];

        return [
            'reasoning' => isset($entry['supports_reasoning']) ? (bool) $entry['supports_reasoning'] : null,
            'tools' => isset($entry['supports_tool_calling']) ? (bool) $entry['supports_tool_calling'] : null,
            'maxOutput' => isset($entry['max_output_tokens']) && (int) $entry['max_output_tokens'] > 0 ? (int) $entry['max_output_tokens'] : null,
        ];
    }

    /**
     * The transcription models approved at the route (`GET {base}/models/transcription`
     * at Requesty; `/models` lists only chat models), sorted; empty when the route
     * does not list them, so the field stays a text field. Cached for an hour.
     *
     * @return array<string, string> model id => model id
     */
    public function transcriptionModels(): array
    {
        $key = $this->connection->apiKey();
        $baseUrl = $this->connection->baseUrl();

        if ('' === $key || '' === $baseUrl) {
            return [];
        }

        $item = $this->cache->getItem('neo_basic_transcription_models_'.hash('sha256', $baseUrl.'|'.$key));

        if ($item->isHit()) {
            return (array) $item->get();
        }

        try {
            $data = $this->httpClient->request(
                'GET',
                $baseUrl.'/models/transcription',
                ['headers' => ['Authorization' => 'Bearer '.$key, 'Accept' => 'application/json']],
            )->toArray(false);
        } catch (\Throwable $e) {
            $this->logger->info('Basic: the route lists no transcription models: '.$e->getMessage());

            return [];
        }

        $models = [];

        foreach ((array) ($data['data'] ?? []) as $entry) {
            $id = \is_array($entry) ? trim((string) ($entry['id'] ?? '')) : '';

            if ('' !== $id) {
                $models[$id] = $id;
            }
        }

        ksort($models);

        $item->set($models)->expiresAfter(self::TTL);
        $this->cache->save($item);

        return $models;
    }

    /**
     * The raw entries of the model list (id => entry), cached like models().
     *
     * @return array<string, array<string, mixed>>
     */
    private function entries(): array
    {
        $key = $this->connection->apiKey();
        $baseUrl = $this->connection->baseUrl();

        if ('' === $key || '' === $baseUrl) {
            return [];
        }

        $item = $this->cache->getItem('neo_basic_model_entries_'.hash('sha256', $baseUrl.'|'.$key));

        if ($item->isHit()) {
            return (array) $item->get();
        }

        try {
            $data = $this->httpClient->request(
                'GET',
                $baseUrl.'/models',
                ['headers' => ['Authorization' => 'Bearer '.$key, 'Accept' => 'application/json']],
            )->toArray(false);
        } catch (\Throwable $e) {
            $this->logger->warning('Basic: could not load the model list: '.$e->getMessage());

            return [];
        }

        $entries = [];

        foreach ((array) ($data['data'] ?? []) as $entry) {
            $id = \is_array($entry) ? trim((string) ($entry['id'] ?? '')) : '';

            if ('' !== $id) {
                $entries[$id] = array_intersect_key($entry, array_flip(['supports_reasoning', 'supports_tool_calling', 'max_output_tokens']));
            }
        }

        if ([] !== $entries) {
            $item->set($entries)->expiresAfter(self::TTL);
            $this->cache->save($item);
        }

        return $entries;
    }

    /**
     * Options for a model field: the route's models with the stored value always
     * in the list (even when the route does not list it), so saving never loses
     * it. Empty when the route offers nothing; keep a text field then.
     *
     * @return array<string, string>
     */
    public function optionsWith(string $current): array
    {
        $models = $this->models();

        if ([] === $models) {
            return [];
        }

        if ('' !== $current && !isset($models[$current])) {
            $models = [$current => $current] + $models;
        }

        return $models;
    }
}
