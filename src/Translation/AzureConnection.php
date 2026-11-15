<?php

declare(strict_types=1);

namespace Neo\BasicBundle\Translation;

use Neo\BasicBundle\Access\AccessSettings;

/**
 * The one Azure Translator access of the installation, entered on "Access
 * keys and licences" and shared by every extension that translates texts:
 * key (encrypted), processing location (a geography endpoint of Microsoft
 * or the resource's own address) and region of the resource.
 *
 * Stored in tl_neo_access: azure_translator_key, azure_translator_location,
 * azure_translator_url (only for the location "custom"), azure_translator_region.
 */
final class AzureConnection
{
    public const KEY = 'azure_translator_key';

    public const LOCATION = 'azure_translator_location';

    public const URL = 'azure_translator_url';

    public const REGION = 'azure_translator_region';

    public const CUSTOM = 'custom';

    public const DEFAULT_LOCATION = 'eur';

    /**
     * Microsoft's endpoints by processing location (Text Translation v3).
     */
    public const LOCATIONS = [
        'eur' => 'https://api-eur.cognitive.microsofttranslator.com',
        'global' => 'https://api.cognitive.microsofttranslator.com',
        'nam' => 'https://api-nam.cognitive.microsofttranslator.com',
        'apc' => 'https://api-apc.cognitive.microsofttranslator.com',
    ];

    public function __construct(private readonly AccessSettings $settings)
    {
    }

    /**
     * The key in plain text, '' when none is stored.
     */
    public function key(): string
    {
        return $this->settings->secret(self::KEY);
    }

    public function hasKey(): bool
    {
        return $this->settings->hasValue(self::KEY);
    }

    /**
     * Region of the resource (e.g. "westeurope"), '' for a global resource.
     */
    public function region(): string
    {
        return trim($this->settings->value(self::REGION));
    }

    public function location(): string
    {
        $location = $this->settings->value(self::LOCATION);

        return isset(self::LOCATIONS[$location]) || self::CUSTOM === $location ? $location : self::DEFAULT_LOCATION;
    }

    /**
     * Address the requests go to, without a trailing slash; '' for an own address that is not filled in.
     */
    public function baseUrl(): string
    {
        $location = $this->location();
        $url = self::CUSTOM === $location ? $this->settings->value(self::URL) : self::LOCATIONS[$location];

        return rtrim(trim(html_entity_decode($url, ENT_QUOTES | ENT_HTML5, 'UTF-8')), '/');
    }

    public function isConfigured(): bool
    {
        return '' !== $this->key() && '' !== $this->baseUrl();
    }
}
