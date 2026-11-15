<?php

declare(strict_types=1);

namespace Neo\BasicBundle\Tests\Backend;

use Contao\CoreBundle\Exception\ResponseException;
use Neo\BasicBundle\Ai\AiConnection;
use Neo\BasicBundle\Backend\AiTestOperation;
use Neo\BasicBundle\Backend\AzureTestOperation;
use Neo\BasicBundle\Backend\DeepLTestOperation;
use Neo\BasicBundle\Tests\Support\BackendFixtures;
use Neo\BasicBundle\Tests\Support\LanguageFiles;
use Neo\BasicBundle\Tests\Support\MemoryLogger;
use Neo\BasicBundle\Tests\Support\RecordingClient;
use Neo\BasicBundle\Tests\Support\Settings;
use Neo\BasicBundle\Translation\AzureConnection;
use Neo\BasicBundle\Translation\AzureTranslator;
use Neo\BasicBundle\Translation\DeepLConnection;
use Neo\BasicBundle\Translation\DeepLTranslator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * "Test connection" of the AI connection, Azure Translator and DeepL: the
 * result in place, the key never in clear.
 */
final class TestOperationsTest extends TestCase
{
    use BackendFixtures;

    private const KEY = 'sk-live-0123456789abcdef';

    protected function setUp(): void
    {
        LanguageFiles::load('tl_neo_access');
    }

    protected function tearDown(): void
    {
        LanguageFiles::reset();
    }

    public function testAiConnectionWorks(): void
    {
        $http = new RecordingClient(RecordingClient::json(['data' => [['id' => 'a'], ['id' => 'b'], ['id' => 'c']]]));

        $result = $this->answerOf(fn () => ($this->aiOperation($http))());

        $this->assertTrue($result['ok']);
        $this->assertSame('neo_access_ai', $result['target']);
        $this->assertMatchesRegularExpression('/Connection works: HTTP 200 in \d+ ms, 3 models available\./', $result['html']);
        $this->assertStringContainsString('Address queried: https://router.example.com/v1/models', $result['html']);
        $this->assertSame('Bearer '.self::KEY, $http->requests[0]['headers']['authorization']);
        $this->assertStringNotContainsString(self::KEY, $result['html']);
        $this->assertStringContainsString('24 · sk-l…ef', $result['html']);
    }

    public function testAiErrorAnswerIsShownWithTheKeyMasked(): void
    {
        $http = new RecordingClient(RecordingClient::json(['error' => 'Invalid key '.self::KEY], 401));

        $result = $this->answerOf(fn () => ($this->aiOperation($http))());

        $this->assertFalse($result['ok']);
        $this->assertStringContainsString('The route answers with HTTP 401', $result['html']);
        $this->assertStringContainsString('Invalid key ***', $result['html']);
        $this->assertStringNotContainsString(self::KEY, $result['html']);
    }

    public function testAiWithoutKeyNothingIsSent(): void
    {
        $http = new RecordingClient();
        $operation = new AiTestOperation(new AiConnection(Settings::with()), $http->client, $this->actions($this->post()));

        $result = $this->answerOf(fn () => $operation());

        $this->assertFalse($result['ok']);
        $this->assertStringContainsString('No key stored yet.', $result['html']);
        $this->assertSame([], $http->requests);
    }

    public function testAWrongRequestTokenStopsTheTestBeforeAnythingIsSent(): void
    {
        $http = new RecordingClient();
        $operation = new AiTestOperation($this->aiConnection(), $http->client, $this->actions($this->backendRequest('POST', [], ['REQUEST_TOKEN' => 'forged'], true)));

        $result = $this->answerOf(fn () => $operation());

        $this->assertFalse($result['ok']);
        $this->assertStringContainsString('The page is out of date.', $result['html']);
        $this->assertSame([], $http->requests);
    }

    public function testAzureUnreachableNamesTheErrorWithoutTheKey(): void
    {
        $settings = Settings::with(['azure_translator_key' => self::KEY], ['azure_translator_key']);
        $connection = new AzureConnection($settings);
        $http = new RecordingClient(RecordingClient::unreachable());
        $operation = new AzureTestOperation($connection, new AzureTranslator($connection, $http->client, new MemoryLogger()), $this->actions($this->post(), $settings));

        $result = $this->answerOf(fn () => $operation());

        $this->assertFalse($result['ok']);
        $this->assertStringContainsString('No connection:', $result['html']);
        $this->assertStringContainsString('Region: –', $result['html']);
        $this->assertStringNotContainsString(self::KEY, $result['html']);
    }

    public function testAzureWorks(): void
    {
        $settings = Settings::with(['azure_translator_key' => self::KEY, 'azure_translator_region' => 'westeurope'], ['azure_translator_key']);
        $connection = new AzureConnection($settings);
        $http = new RecordingClient(RecordingClient::json([['translations' => [['text' => 'Hallo']]]]));
        $operation = new AzureTestOperation($connection, new AzureTranslator($connection, $http->client, new MemoryLogger()), $this->actions($this->post(), $settings));

        $result = $this->answerOf(fn () => $operation());

        $this->assertTrue($result['ok']);
        $this->assertStringContainsString('Connection OK: HTTP 200', $result['html']);
        $this->assertStringContainsString('Region: westeurope', $result['html']);
    }

    public function testDeepLShowsTheUsageOfTheBillingPeriod(): void
    {
        $GLOBALS['TL_LANG']['MSC']['thousandsSeparator'] = '.';
        $result = $this->answerOf(fn () => ($this->deeplOperation(RecordingClient::json(['character_count' => 12345, 'character_limit' => 500000])))());

        $this->assertTrue($result['ok']);
        $this->assertStringContainsString('Used in this billing period: 12.345 of 500.000 characters.', $result['html']);
        $this->assertStringContainsString('Address queried: https://api-free.deepl.com/v2/usage', $result['html']);
    }

    public function testDeepLExplainsAKeyThatIsNotAccepted(): void
    {
        $result = $this->answerOf(fn () => ($this->deeplOperation(RecordingClient::json(['message' => 'Forbidden'], 403)))());

        $this->assertFalse($result['ok']);
        $this->assertStringContainsString('DeepL answers with HTTP 403', $result['html']);
        $this->assertStringContainsString('The key is not accepted.', $result['html']);
    }

    public function testDeepLExplainsAUsedUpQuota(): void
    {
        $result = $this->answerOf(fn () => ($this->deeplOperation(RecordingClient::json(['message' => 'Quota exceeded'], 456)))());

        $this->assertStringContainsString('The characters of this billing period are used up.', $result['html']);
    }

    private function aiConnection(): AiConnection
    {
        return new AiConnection(Settings::with(['ai_route' => 'custom', 'ai_base_url' => 'https://router.example.com/v1', 'ai_api_key' => self::KEY], ['ai_api_key']));
    }

    private function aiOperation(RecordingClient $http): AiTestOperation
    {
        return new AiTestOperation($this->aiConnection(), $http->client, $this->actions($this->post()));
    }

    private function deeplOperation(MockResponse $response): DeepLTestOperation
    {
        $settings = Settings::with(['deepl_key' => self::KEY.':fx'], ['deepl_key']);
        $connection = new DeepLConnection($settings);
        $http = new RecordingClient($response);

        return new DeepLTestOperation($connection, new DeepLTranslator($connection, $http->client, new MemoryLogger()), $this->actions($this->post(), $settings));
    }

    private function post(): RequestStack
    {
        return $this->backendRequest('POST', [], ['REQUEST_TOKEN' => 'valid-token'], true);
    }

    /**
     * @return array{ok: bool, target: string, html: string}
     */
    private function answerOf(callable $operation): array
    {
        try {
            $operation();
        } catch (ResponseException $e) {
            return json_decode((string) $e->getResponse()->getContent(), true);
        }

        $this->fail('No response');
    }
}
