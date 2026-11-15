<?php

declare(strict_types=1);

namespace Neo\BasicBundle\Tests\Translation;

use Neo\BasicBundle\Tests\Support\MemoryLogger;
use Neo\BasicBundle\Tests\Support\RecordingClient;
use Neo\BasicBundle\Tests\Support\Settings;
use Neo\BasicBundle\Translation\DeepLConnection;
use Neo\BasicBundle\Translation\DeepLTranslator;
use Neo\BasicBundle\Translation\TranslationException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class DeepLTranslatorTest extends TestCase
{
    #[DataProvider('plans')]
    public function testTheKeyDecidesTheInterface(string $key, string $plan, string $baseUrl): void
    {
        $connection = new DeepLConnection(Settings::with(['deepl_key' => $key], ['deepl_key']));

        $this->assertSame($plan, $connection->plan());
        $this->assertSame($baseUrl, $connection->baseUrl());
        $this->assertTrue($connection->isConfigured());
    }

    /**
     * @return iterable<string, array{string, string, string}>
     */
    public static function plans(): iterable
    {
        yield 'free plan' => ['0a1b2c3d-0000-4000-8000-000000000000:fx', 'free', 'https://api-free.deepl.com'];
        yield 'pro plan' => ['0a1b2c3d-0000-4000-8000-000000000000', 'pro', 'https://api.deepl.com'];
    }

    public function testATestAddressReplacesDeepL(): void
    {
        $connection = new DeepLConnection(Settings::with(['deepl_key' => 'k:fx'], ['deepl_key']), ' http://127.0.0.1:8080/ ');

        $this->assertSame('http://127.0.0.1:8080', $connection->baseUrl());
    }

    public function testWithoutKeyThereIsNoAddress(): void
    {
        $connection = new DeepLConnection(Settings::with());

        $this->assertSame('', $connection->baseUrl());
        $this->assertFalse($connection->hasKey());
        $this->assertFalse($connection->isConfigured());
    }

    public function testTranslatesWithHtmlTagHandlingAndProtectedInsertTags(): void
    {
        $http = new RecordingClient(RecordingClient::json(['translations' => [
            ['detected_source_language' => 'DE', 'text' => '<p>Hello <span translate="no" class="notranslate" data-insert-tag="0">0</span></p>'],
        ]]));

        $result = $this->translator($http)->translate(['<p>Hallo {{user::firstname}}</p>'], 'de-AT', 'en');

        $this->assertSame(['<p>Hello {{user::firstname}}</p>'], $result);
        $this->assertSame('https://api-free.deepl.com/v2/translate', $http->requests[0]['url']);
        $this->assertSame('DeepL-Auth-Key deepl-key:fx', $http->requests[0]['headers']['authorization']);
        $this->assertSame('DE', $http->requests[0]['body']['source_lang']);
        $this->assertSame('EN-GB', $http->requests[0]['body']['target_lang']);
        $this->assertSame('html', $http->requests[0]['body']['tag_handling']);
        $this->assertStringNotContainsString('{{', (string) json_encode($http->requests[0]['body']['text']));
    }

    #[DataProvider('rejections')]
    public function testErrorAnswersAreRejected(int $status, array $body): void
    {
        $http = new RecordingClient(RecordingClient::json($body, $status));

        try {
            $this->translator($http)->translate(['Hallo', 'Welt'], 'de', 'en');
            $this->fail('No exception');
        } catch (TranslationException $e) {
            $this->assertSame(TranslationException::REJECTED, $e->reason);
            $this->assertSame($status, $e->status);
            $this->assertSame('DeepL', $e->service);
        }
    }

    /**
     * @return iterable<string, array{int, array<string, mixed>}>
     */
    public static function rejections(): iterable
    {
        yield 'key not accepted' => [403, ['message' => 'Wrong endpoint']];
        yield 'quota used up' => [456, ['message' => 'Quota exceeded']];
        yield 'one translation for two texts' => [200, ['translations' => [['text' => 'Hello']]]];
        yield 'no translations' => [200, ['other' => true]];
    }

    public function testAnUnreachableServiceIsReportedAsSuch(): void
    {
        $this->expectExceptionObject(TranslationException::unreachableFor('DeepL'));

        $this->translator(new RecordingClient(RecordingClient::unreachable()))->translate(['Hallo'], 'de', 'en');
    }

    public function testNotConfigured(): void
    {
        $this->expectExceptionObject(TranslationException::notConfiguredFor('DeepL'));

        (new DeepLTranslator(new DeepLConnection(Settings::with()), (new RecordingClient())->client, new MemoryLogger()))->translate(['Hallo'], 'de', 'en');
    }

    public function testUsageReadsCountAndLimit(): void
    {
        $http = new RecordingClient(RecordingClient::json(['character_count' => 1200, 'character_limit' => 500000]));

        $usage = $this->translator($http)->usage();

        $this->assertSame(200, $usage['status']);
        $this->assertSame(1200, $usage['count']);
        $this->assertSame(500000, $usage['limit']);
        $this->assertSame('GET', $http->requests[0]['method']);
        $this->assertSame('https://api-free.deepl.com/v2/usage', $http->requests[0]['url']);
    }

    public function testUsageWithoutNumbers(): void
    {
        $usage = $this->translator(new RecordingClient(RecordingClient::json(['message' => 'Forbidden'], 403)))->usage();

        $this->assertSame(403, $usage['status']);
        $this->assertNull($usage['count']);
        $this->assertNull($usage['limit']);
    }

    #[DataProvider('sourceLanguages')]
    public function testSourceCode(string $contao, string $deepl): void
    {
        $this->assertSame($deepl, DeepLTranslator::sourceCode($contao));
    }

    /**
     * @return iterable<string, array{string, string}>
     */
    public static function sourceLanguages(): iterable
    {
        yield 'German' => ['de', 'DE'];
        yield 'Austrian German' => ['de-AT', 'DE'];
        yield 'British English' => ['en_GB', 'EN'];
        yield 'Norwegian' => ['no', 'NB'];
    }

    #[DataProvider('targetLanguages')]
    public function testTargetCode(string $contao, string $deepl): void
    {
        $this->assertSame($deepl, DeepLTranslator::targetCode($contao));
    }

    /**
     * @return iterable<string, array{string, string}>
     */
    public static function targetLanguages(): iterable
    {
        yield 'English' => ['en', 'EN-GB'];
        yield 'American English' => ['en-US', 'EN-US'];
        yield 'Portuguese' => ['pt', 'PT-PT'];
        yield 'Brazilian Portuguese' => ['pt_BR', 'PT-BR'];
        yield 'Chinese' => ['zh', 'ZH-HANS'];
        yield 'Chinese, Taiwan' => ['zh-TW', 'ZH-HANT'];
        yield 'Norwegian' => ['no', 'NB'];
        yield 'Austrian German' => ['de-AT', 'DE'];
    }

    private function translator(RecordingClient $http): DeepLTranslator
    {
        return new DeepLTranslator(new DeepLConnection(Settings::with(['deepl_key' => 'deepl-key:fx'], ['deepl_key'])), $http->client, new MemoryLogger());
    }
}
