<?php

declare(strict_types=1);

namespace Neo\BasicBundle\Translation;

use Neo\BasicBundle\Access\AccessSettings;

/**
 * The one DeepL access of the installation, entered on "Access keys and
 * licences" and shared by every extension that translates texts: the
 * authentication key (encrypted). Which interface it belongs to follows from
 * the key: keys of the free plan end in ":fx" and go to DeepL API Free, all
 * others to DeepL API Pro.
 *
 * Stored in tl_neo_access: deepl_key. A test installation can send the
 * requests to another address (neo_basic.deepl_url).
 */
final class DeepLConnection
{
    public const KEY = 'deepl_key';

    public const FREE = 'free';

    public const PRO = 'pro';

    /**
     * Addresses of the two interfaces of DeepL, without the version.
     */
    public const URLS = [
        self::FREE => 'https://api-free.deepl.com',
        self::PRO => 'https://api.deepl.com',
    ];

    public function __construct(
        private readonly AccessSettings $settings,
        private readonly string $url = '',
    ) {
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
     * "free" for a key of the free plan (ending in ":fx"), otherwise "pro".
     */
    public function plan(): string
    {
        return str_ends_with($this->key(), ':fx') ? self::FREE : self::PRO;
    }

    /**
     * Address the requests go to, without the version and a trailing slash,
     * e.g. "https://api-free.deepl.com"; '' without a key.
     */
    public function baseUrl(): string
    {
        if ('' !== trim($this->url)) {
            return rtrim(trim($this->url), '/');
        }

        return '' !== $this->key() ? self::URLS[$this->plan()] : '';
    }

    public function isConfigured(): bool
    {
        return '' !== $this->key() && '' !== $this->baseUrl();
    }
}
