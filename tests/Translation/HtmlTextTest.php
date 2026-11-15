<?php

declare(strict_types=1);

namespace Neo\BasicBundle\Tests\Translation;

use Neo\BasicBundle\Translation\HtmlText;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class HtmlTextTest extends TestCase
{
    public function testInsertTagsInTextBecomeElementsTheServicesLeaveAlone(): void
    {
        [$prepared, $tags] = HtmlText::protect('<p>Call {{email::info@example.com}} or {{link::12}}.</p>');

        $this->assertSame(['{{email::info@example.com}}', '{{link::12}}'], $tags);
        $this->assertStringNotContainsString('{{', $prepared);
        $this->assertStringContainsString('<span translate="no" class="notranslate" data-insert-tag="0">0</span>', $prepared);
        $this->assertStringContainsString('<span translate="no" class="notranslate" data-insert-tag="1">1</span>', $prepared);
    }

    public function testInsertTagsInsideATagBecomePlaceholders(): void
    {
        [$prepared, $tags] = HtmlText::protect('<a href="{{link_url::12}}" title="{{page::title}}">More</a>');

        $this->assertSame('<a href="contao-insert-tag-0" title="contao-insert-tag-1">More</a>', $prepared);
        $this->assertSame(['{{link_url::12}}', '{{page::title}}'], $tags);
    }

    public function testRestorePutsTheInsertTagsBackEvenWhenTheServiceChangedTheElement(): void
    {
        $tags = ['{{link_url::12}}', '{{email::info@example.com}}'];
        $translated = '<a href="contao-insert-tag-0">Mehr</a> Schreiben Sie an <span class="notranslate" data-insert-tag="1" translate="no">eins</span>.';

        $this->assertSame('<a href="{{link_url::12}}">Mehr</a> Schreiben Sie an {{email::info@example.com}}.', HtmlText::restore($translated, $tags));
    }

    public function testAPlaceholderWithoutTagIsRemovedNotLeftInTheText(): void
    {
        $this->assertSame('<a href="">x</a>', HtmlText::restore('<a href="contao-insert-tag-7">x</a>', ['{{link_url::12}}']));
    }

    public function testHtmlRoundTripKeepsTheMarkup(): void
    {
        $html = '<p class="lead">Text with <strong>{{insert_article::4}}</strong> and <a href="{{link_url::9}}">link</a>.</p>';

        [$prepared, $tags] = HtmlText::prepare($html, true);

        $this->assertSame($html, HtmlText::finish($prepared, $tags, true));
    }

    public function testPlainTextGoesAsEscapedHtmlAndComesBackAsPlainText(): void
    {
        [$prepared, $tags] = HtmlText::prepare('5 < 7 & "quotes" {{date::Y}}', false);

        $this->assertStringStartsWith('5 &lt; 7 &amp; &quot;quotes&quot; <span', $prepared);
        $this->assertSame('5 < 7 & "quotes" {{date::Y}}', HtmlText::finish($prepared, $tags, false));
    }

    public function testFinishOfPlainTextDropsTagsTheServiceAdded(): void
    {
        $this->assertSame('Hello world', HtmlText::finish('<b>Hello</b> world', [], false));
    }

    #[DataProvider('batchCases')]
    public function testBatchesRespectTheLimits(array $texts, int $maxItems, int $maxCharacters, array $expected): void
    {
        $this->assertSame($expected, iterator_to_array(HtmlText::batches($texts, $maxItems, $maxCharacters), false));
    }

    /**
     * @return iterable<string, array{list<string>, int, int, list<list<string>>}>
     */
    public static function batchCases(): iterable
    {
        yield 'nothing' => [[], 3, 100, []];
        yield 'one batch' => [['a', 'b'], 3, 100, [['a', 'b']]];
        yield 'by number' => [['a', 'b', 'c', 'd'], 3, 100, [['a', 'b', 'c'], ['d']]];
        yield 'by characters' => [['aaaa', 'bbbb', 'cc'], 10, 8, [['aaaa', 'bbbb'], ['cc']]];
        yield 'a long text forms its own batch' => [['a', str_repeat('x', 20), 'b'], 10, 8, [['a'], [str_repeat('x', 20)], ['b']]];
        yield 'characters, not bytes' => [['ääää', 'öööö'], 10, 8, [['ääää', 'öööö']]];
    }
}
