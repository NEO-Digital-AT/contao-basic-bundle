<?php

declare(strict_types=1);

namespace Neo\BasicBundle\Tests\Translation;

use Neo\BasicBundle\Access\AccessPage;
use Neo\BasicBundle\Access\AccessSettings;
use Neo\BasicBundle\Ai\AiConnection;
use Neo\BasicBundle\Tests\Support\MemoryLogger;
use Neo\BasicBundle\Tests\Support\RecordingClient;
use Neo\BasicBundle\Tests\Support\Settings;
use Neo\BasicBundle\Translation\AiTranslator;
use Neo\BasicBundle\Translation\AzureConnection;
use Neo\BasicBundle\Translation\AzureTranslator;
use Neo\BasicBundle\Translation\DeepLConnection;
use Neo\BasicBundle\Translation\DeepLTranslator;
use Neo\BasicBundle\Translation\Translators;
use PHPUnit\Framework\TestCase;

final class TranslatorsTest extends TestCase
{
    public function testGetReturnsTheTranslatorOfTheServiceAndTheLanguageModelWithTheModel(): void
    {
        $translators = $this->translators(Settings::with());

        $this->assertInstanceOf(AzureTranslator::class, $translators->get(Translators::AZURE));
        $this->assertInstanceOf(DeepLTranslator::class, $translators->get(Translators::DEEPL));

        $ai = $translators->get(Translators::AI, 'openai/gpt-test');
        $this->assertInstanceOf(AiTranslator::class, $ai);
        $this->assertSame('openai/gpt-test', $ai->model());
    }

    public function testAnUnknownServiceIsAnError(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->translators(Settings::with())->get('babelfish');
    }

    public function testOnlyServicesWithAccessAreOffered(): void
    {
        $this->assertSame([], $this->translators(Settings::with())->withAccess());

        $settings = Settings::with(['deepl_key' => 'k:fx', 'ai_api_key' => 'sk-test'], ['deepl_key', 'ai_api_key']);
        $translators = $this->translators($settings);

        $this->assertSame([Translators::DEEPL, Translators::AI], $translators->withAccess());
        $this->assertFalse($translators->hasAccess(Translators::AZURE));
        $this->assertFalse($translators->hasAccess('babelfish'));
    }

    public function testLegend(): void
    {
        $this->assertSame(AccessPage::AZURE_TRANSLATOR_LEGEND, Translators::legend(Translators::AZURE));
        $this->assertSame(AccessPage::DEEPL_LEGEND, Translators::legend(Translators::DEEPL));
        $this->assertSame(AccessPage::AI_LEGEND, Translators::legend(Translators::AI));
    }

    private function translators(AccessSettings $settings): Translators
    {
        $http = (new RecordingClient())->client;
        $logger = new MemoryLogger();
        $ai = new AiConnection($settings);

        return new Translators(
            new AzureTranslator(new AzureConnection($settings), $http, $logger),
            new DeepLTranslator(new DeepLConnection($settings), $http, $logger),
            new AiTranslator($ai, $http, $logger),
            $ai,
        );
    }
}
