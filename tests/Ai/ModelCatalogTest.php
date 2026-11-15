<?php

declare(strict_types=1);

namespace Neo\BasicBundle\Tests\Ai;

use Neo\BasicBundle\Ai\AiConnection;
use Neo\BasicBundle\Ai\ModelCatalog;
use Neo\BasicBundle\Tests\Support\MemoryLogger;
use Neo\BasicBundle\Tests\Support\RecordingClient;
use Neo\BasicBundle\Tests\Support\Settings;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Cache\Adapter\ArrayAdapter;

final class ModelCatalogTest extends TestCase
{
    private MemoryLogger $logger;

    private ArrayAdapter $cache;

    protected function setUp(): void
    {
        $this->logger = new MemoryLogger();
        $this->cache = new ArrayAdapter();
    }

    public function testTheModelsOfTheRouteSortedAndCached(): void
    {
        $http = new RecordingClient(RecordingClient::json(['data' => [['id' => 'openai/gpt-b'], ['id' => ' acme/model-a '], ['id' => ''], ['object' => 'model']]]));
        $catalog = $this->catalog($http);

        $this->assertSame(['acme/model-a' => 'acme/model-a', 'openai/gpt-b' => 'openai/gpt-b'], $catalog->models());
        $this->assertSame(['acme/model-a' => 'acme/model-a', 'openai/gpt-b' => 'openai/gpt-b'], $catalog->models());
        $this->assertCount(1, $http->requests, 'second call from the cache');
        $this->assertSame('https://router.example.com/v1/models', $http->requests[0]['url']);
        $this->assertSame('Bearer sk-test', $http->requests[0]['headers']['authorization']);
    }

    public function testWithoutKeyTheRouteIsNotAsked(): void
    {
        $http = new RecordingClient();
        $catalog = new ModelCatalog($http->client, $this->cache, new AiConnection(Settings::with()), $this->logger);

        $this->assertSame([], $catalog->models());
        $this->assertSame([], $catalog->transcriptionModels());
        $this->assertSame([], $catalog->optionsWith('openai/gpt-b'));
        $this->assertSame([], $http->requests);
    }

    public function testAnUnreachableRouteGivesNoModelsAndIsAskedAgainNextTime(): void
    {
        $http = new RecordingClient(RecordingClient::unreachable(), RecordingClient::json(['data' => [['id' => 'm']]]));
        $catalog = $this->catalog($http);

        $this->assertSame([], $catalog->models());
        $this->assertStringContainsString('warning: Basic: could not load the model list', implode("\n", $this->logger->records));
        $this->assertSame(['m' => 'm'], $catalog->models());
    }

    public function testAnEmptyListIsNotCached(): void
    {
        $http = new RecordingClient(RecordingClient::json(['data' => []]), RecordingClient::json(['data' => [['id' => 'm']]]));
        $catalog = $this->catalog($http);

        $this->assertSame([], $catalog->models());
        $this->assertSame(['m' => 'm'], $catalog->models());
    }

    public function testTheCacheDependsOnAddressAndKey(): void
    {
        $first = new RecordingClient(RecordingClient::json(['data' => [['id' => 'one']]]));
        $second = new RecordingClient(RecordingClient::json(['data' => [['id' => 'two']]]));

        $this->assertSame(['one' => 'one'], $this->catalog($first, 'sk-one')->models());
        $this->assertSame(['two' => 'two'], $this->catalog($second, 'sk-two')->models());
    }

    public function testTheStoredModelStaysInTheOptions(): void
    {
        $catalog = $this->catalog(new RecordingClient(RecordingClient::json(['data' => [['id' => 'b'], ['id' => 'a']]])));

        $this->assertSame(['retired' => 'retired', 'a' => 'a', 'b' => 'b'], $catalog->optionsWith('retired'));
        $this->assertSame(['a' => 'a', 'b' => 'b'], $catalog->optionsWith('a'));
        $this->assertSame(['a' => 'a', 'b' => 'b'], $catalog->optionsWith(''));
    }

    public function testCapabilitiesAsTheRouteStatesThem(): void
    {
        $catalog = $this->catalog(new RecordingClient(RecordingClient::json(['data' => [
            ['id' => 'reasoner', 'supports_reasoning' => true, 'supports_tool_calling' => false, 'max_output_tokens' => 64000, 'input_price' => 1],
            ['id' => 'plain', 'max_output_tokens' => 0],
        ]])));

        $this->assertSame(['reasoning' => true, 'tools' => false, 'maxOutput' => 64000], $catalog->capabilities('reasoner'));
        $this->assertSame(['reasoning' => null, 'tools' => null, 'maxOutput' => null], $catalog->capabilities('plain'));
        $this->assertSame(['reasoning' => null, 'tools' => null, 'maxOutput' => null], $catalog->capabilities('unknown'));
    }

    public function testTranscriptionModelsFromTheirOwnList(): void
    {
        $http = new RecordingClient(RecordingClient::json(['data' => [['id' => 'whisper-b'], ['id' => 'whisper-a'], 'not an entry']]));

        $this->assertSame(['whisper-a' => 'whisper-a', 'whisper-b' => 'whisper-b'], $this->catalog($http)->transcriptionModels());
        $this->assertSame('https://router.example.com/v1/models/transcription', $http->requests[0]['url']);
    }

    public function testARouteWithoutTranscriptionListGivesNone(): void
    {
        $catalog = $this->catalog(new RecordingClient(RecordingClient::unreachable()));

        $this->assertSame([], $catalog->transcriptionModels());
        $this->assertStringContainsString('info: Basic: the route lists no transcription models', implode("\n", $this->logger->records));
    }

    private function catalog(RecordingClient $http, string $key = 'sk-test'): ModelCatalog
    {
        $connection = new AiConnection(Settings::with(['ai_route' => 'custom', 'ai_base_url' => 'https://router.example.com/v1/', 'ai_api_key' => $key], ['ai_api_key']));

        return new ModelCatalog($http->client, $this->cache, $connection, $this->logger);
    }
}
