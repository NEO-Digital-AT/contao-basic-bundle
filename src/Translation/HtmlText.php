<?php

declare(strict_types=1);

namespace Neo\BasicBundle\Translation;

/**
 * What every translator does with the texts before and after the service:
 * plain text goes as escaped HTML, Contao's insert tags are protected (in text
 * as an element with translate="no", inside a tag such as a link's href as a
 * placeholder) and put back afterwards, and the texts are split into batches.
 *
 * @internal
 */
final class HtmlText
{
    /**
     * The text as HTML with its insert tags protected.
     *
     * @return array{0: string, 1: list<string>} prepared text and the insert tags by number
     */
    public static function prepare(string $text, bool $html): array
    {
        return self::protect($html ? $text : htmlspecialchars($text, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
    }

    /**
     * The translation in the form of the original text, with its insert tags back.
     *
     * @param list<string> $tags
     */
    public static function finish(string $translated, array $tags, bool $html): string
    {
        $restored = self::restore($translated, $tags);

        return $html ? $restored : html_entity_decode(strip_tags($restored), ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    /**
     * Replaces insert tags: inside a tag by a placeholder, in text by an element the services leave alone.
     *
     * @return array{0: string, 1: list<string>} prepared text and the insert tags by number
     */
    public static function protect(string $html): array
    {
        $tags = [];
        $parts = preg_split('/(<[^>]*>)/', $html, -1, PREG_SPLIT_DELIM_CAPTURE) ?: [$html];

        foreach ($parts as $index => $part) {
            $insideTag = str_starts_with($part, '<');

            $parts[$index] = (string) preg_replace_callback(
                '/\{\{[^{}]+\}\}/',
                static function (array $match) use (&$tags, $insideTag): string {
                    $tags[] = $match[0];
                    $number = \count($tags) - 1;

                    return $insideTag ? 'contao-insert-tag-'.$number : '<span translate="no" class="notranslate" data-insert-tag="'.$number.'">'.$number.'</span>';
                },
                $part,
            );
        }

        return [implode('', $parts), $tags];
    }

    /**
     * @param list<string> $tags
     */
    public static function restore(string $html, array $tags): string
    {
        $html = (string) preg_replace_callback('/<span[^>]*data-insert-tag="(\d+)"[^>]*>.*?<\/span>/s', static fn (array $match): string => $tags[(int) $match[1]] ?? '', $html);

        return (string) preg_replace_callback('/contao-insert-tag-(\d+)/', static fn (array $match): string => $tags[(int) $match[1]] ?? '', $html);
    }

    /**
     * The texts in batches of at most $maxItems texts and $maxCharacters characters
     * (a longer text forms a batch of its own).
     *
     * @param list<string> $texts
     *
     * @return iterable<list<string>>
     */
    public static function batches(array $texts, int $maxItems, int $maxCharacters): iterable
    {
        $batch = [];
        $length = 0;

        foreach ($texts as $text) {
            if ([] !== $batch && (\count($batch) >= $maxItems || $length + mb_strlen($text) > $maxCharacters)) {
                yield $batch;
                $batch = [];
                $length = 0;
            }

            $batch[] = $text;
            $length += mb_strlen($text);
        }

        if ([] !== $batch) {
            yield $batch;
        }
    }
}
