<?php

declare(strict_types=1);

namespace Neo\BasicBundle\Tests\Translation;

use Neo\BasicBundle\Tests\Support\MemoryLogger;
use Neo\BasicBundle\Tests\Support\RecordingClient;
use Neo\BasicBundle\Tests\Support\Settings;
use Neo\BasicBundle\Translation\AzureConnection;
use Neo\BasicBundle\Translation\AzureTranslator;
use Neo\BasicBundle\Translation\TranslationException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class AzureTranslatorTest extends TestCase
{
    public function testConnectionDefaults(): void
    {
        $connection = new AzureConnection(Settings::with());

        $this->assertSame('eur', $connection->location());
        $this->assertSame('https://api-eur.cognitive.microsofttranslator.com', $connection->baseUrl());
        $this->assertFalse($connection->hasKey());
        $this->assertFalse($connection->isConfigured());
    }

    public function testConnectionWithOwnAddressAndRegion(): void
    {
        $connection = new AzureConnection(Settings::with([
            'azure_translator_key' => 'azure-key-123',
            'azure_translator_location' => 'custom',
            'azure_translator_url' => 'https://translator.example.com/',
            'azure_translator_region' => ' westeurope ',
        ], ['azure_translator_key']));

        $this->assertSame('azure-key-123', $connection->key());
        $this->assertSame('https://translator.example.com', $connection->baseUrl());
        $this->assertSame('westeurope', $connection->region());
        $this->assertTrue($connection->isConfigured());
    }

    public function testAnUnknownLocationFallsBackToEurope(): void
    {
        $connection = new AzureConnection(Settings::with(['azure_translator_location' => 'mars']));

        $this->assertSame('eur', $connection->location());
    }

    public function testTheOwnAddressMissingMeansNotConfigured(): void
    {
        $connection = new AzureConnection(Settings::with(['azure_translator_key' => 'k', 'azure_translator_location' => 'custom'], ['azure_translator_key']));

        $this->assertSame('', $connection->baseUrl());
        $this->assertFalse($connection->isConfigured());
    }

    public function testTranslatesHtmlWithProtectedInsertTags(): void
    {
        $http = new RecordingClient(RecordingClient::json([
            ['translations' => [['text' => '<p>Hallo <span translate="no" class="notranslate" data-insert-tag="0">0</span></p>', 'to' => 'de']]],
            ['translations' => [['text' => '<a href="contao-insert-tag-0">Mehr</a>', 'to' => 'de']]],
        ]));

        $result = $this->translator($http)->translate(['<p>Hello {{user::firstname}}</p>', '<a href="{{link_url::3}}">More</a>'], 'en', 'de-AT');

        $this->assertSame(['<p>Hallo {{user::firstname}}</p>', '<a href="{{link_url::3}}">Mehr</a>'], $result);

        $request = $http->requests[0];
        $this->assertSame('POST', $request['method']);
        $this->assertStringStartsWith('https://api-eur.cognitive.microsofttranslator.com/translate?', $request['url']);
        parse_str((string) parse_url($request['url'], PHP_URL_QUERY), $query);
        $this->assertSame(['api-version' => '3.0', 'from' => 'en', 'to' => 'de', 'textType' => 'html'], $query);
        $this->assertSame('azure-key-123', $request['headers']['ocp-apim-subscription-key']);
        $this->assertSame('westeurope', $request['headers']['ocp-apim-subscription-region']);
        $this->assertStringNotContainsString('{{', (string) json_encode($request['body']));
    }

    public function testPlainTextComesBackAsPlainText(): void
    {
        $http = new RecordingClient(RecordingClient::json([['translations' => [['text' => 'Fisch &amp; Chips']]]]));

        $this->assertSame(['Fisch & Chips'], $this->translator($http)->translate(['Fish & chips'], 'en', 'de', false));
        $this->assertSame([['Text' => 'Fish &amp; chips']], $http->requests[0]['body']);
    }

    public function testWithoutRegionNoRegionHeaderIsSent(): void
    {
        $http = new RecordingClient(RecordingClient::json([['translations' => [['text' => 'Hallo']]]]));
        $connection = new AzureConnection(Settings::with(['azure_translator_key' => 'azure-key-123'], ['azure_translator_key']));

        (new AzureTranslator($connection, $http->client, new MemoryLogger()))->translate(['Hello'], 'en', 'de');

        $this->assertArrayNotHasKey('ocp-apim-subscription-region', $http->requests[0]['headers']);
    }

    public function testManyTextsAreSentInSeveralRequests(): void
    {
        $answer = static fn (int $count): array => array_fill(0, $count, ['translations' => [['text' => 'x']]]);
        $http = new RecordingClient(RecordingClient::json($answer(100)), RecordingClient::json($answer(5)));

        $result = $this->translator($http)->translate(array_fill(0, 105, 'a'), 'en', 'de');

        $this->assertCount(105, $result);
        $this->assertCount(2, $http->requests);
        $this->assertCount(100, $http->requests[0]['body']);
    }

    public function testNotConfigured(): void
    {
        $translator = new AzureTranslator(new AzureConnection(Settings::with()), (new RecordingClient())->client, new MemoryLogger());

        $this->assertFalse($translator->isConfigured());
        $this->expectExceptionObject(TranslationException::notConfigured());

        $translator->translate(['Hello'], 'en', 'de');
    }

    public function testAnErrorAnswerIsRejectedWithItsStatus(): void
    {
        $logger = new MemoryLogger();
        $http = new RecordingClient(RecordingClient::json(['error' => ['code' => 401000, 'message' => 'Invalid key']], 401));

        try {
            $this->translator($http, $logger)->translate(['Hello'], 'en', 'de');
            $this->fail('No exception');
        } catch (TranslationException $e) {
            $this->assertSame(TranslationException::REJECTED, $e->reason);
            $this->assertSame(401, $e->status);
            $this->assertSame('Azure Translator', $e->service);
        }

        $this->assertSame(['warning: Azure Translator answered HTTP 401 (code 401000).'], $logger->records);
    }

    public function testAnAnswerWithTooFewTranslationsIsRejected(): void
    {
        $http = new RecordingClient(RecordingClient::json([['translations' => [['text' => 'Hallo']]]]));

        $this->expectException(TranslationException::class);
        $this->expectExceptionMessage('Azure Translator answered HTTP 200 without a usable translation.');

        $this->translator($http)->translate(['Hello', 'World'], 'en', 'de');
    }

    public function testAnUnreachableServiceIsReportedAsSuch(): void
    {
        $http = new RecordingClient(RecordingClient::unreachable());

        try {
            $this->translator($http)->translate(['Hello'], 'en', 'de');
            $this->fail('No exception');
        } catch (TranslationException $e) {
            $this->assertSame(TranslationException::UNREACHABLE, $e->reason);
        }
    }

    public function testProbeTranslatesOneWord(): void
    {
        $http = new RecordingClient(RecordingClient::json([['translations' => [['text' => 'Hallo']]]]));

        $result = $this->translator($http)->probe();

        $this->assertSame(200, $result['status']);
        $this->assertSame([['Text' => 'Hello']], $http->requests[0]['body']);
        $this->assertStringContainsString('textType=plain', $http->requests[0]['url']);
    }

    #[DataProvider('languages')]
    public function testLanguageCode(string $contao, string $azure): void
    {
        $this->assertSame($azure, AzureTranslator::languageCode($contao));
    }

    /**
     * @return iterable<string, array{string, string}>
     */
    public static function languages(): iterable
    {
        yield 'German' => ['de', 'de'];
        yield 'Austrian German' => ['de-AT', 'de'];
        yield 'underscore' => ['en_GB', 'en'];
        yield 'Chinese' => ['zh', 'zh-Hans'];
        yield 'Chinese, Taiwan' => ['zh-TW', 'zh-Hant'];
        yield 'Portuguese, Portugal' => ['pt-PT', 'pt-pt'];
        yield 'Portuguese, Brazil' => ['pt-BR', 'pt'];
        yield 'Serbian' => ['sr', 'sr-Cyrl'];
        yield 'Serbian, Latin' => ['sr-Latn', 'sr-Latn'];
    }

    private function translator(RecordingClient $http, MemoryLogger|null $logger = null): AzureTranslator
    {
        $connection = new AzureConnection(Settings::with([
            'azure_translator_key' => 'azure-key-123',
            'azure_translator_region' => 'westeurope',
        ], ['azure_translator_key']));

        return new AzureTranslator($connection, $http->client, $logger ?? new MemoryLogger());
    }
}
