<?php

declare(strict_types=1);

namespace Neo\BasicBundle\Translation;

/**
 * A translation service for the texts of a website: Azure Translator, DeepL or
 * a language model of the shared AI connection (see Translators). Texts go as
 * HTML or plain text; Contao's insert tags ({{…}}) come back unchanged.
 */
interface TranslatorInterface
{
    /**
     * @param list<string> $texts HTML (rich text) or plain text, as given by $html
     *
     * @return list<string> translations in the same order and form
     *
     * @throws TranslationException
     */
    public function translate(array $texts, string $from, string $to, bool $html = true): array;

    /**
     * Whether the service can translate: its access is set up on "Access keys
     * and licences" (and, for a language model, a model is given).
     */
    public function isConfigured(): bool;
}
