<?php

declare(strict_types=1);

namespace Neo\BasicBundle\Ai;

use Neo\BasicBundle\Access\AccessSettings;

/**
 * The one AI connection of the installation: route (address of an
 * OpenAI-compatible API) and key, entered on "Access keys and licences".
 * Every extension that calls a language model takes address and key
 * from here and keeps only its own model choice.
 *
 * Stored in tl_neo_access: ai_route, ai_base_url (only for the route "custom")
 * and ai_api_key (encrypted).
 */
final class AiConnection
{
    public const CUSTOM = 'custom';

    public const DEFAULT_ROUTE = 'requesty_eu';

    /**
     * Known routes and their addresses; "custom" uses the address entered on the page.
     */
    public const ROUTES = [
        'requesty_eu' => 'https://router.eu.requesty.ai/v1',
        'requesty_global' => 'https://router.requesty.ai/v1',
        'openai' => 'https://api.openai.com/v1',
        self::CUSTOM => '',
    ];

    public function __construct(private readonly AccessSettings $settings)
    {
    }

    public function route(): string
    {
        $route = $this->settings->value('ai_route');

        return isset(self::ROUTES[$route]) ? $route : self::DEFAULT_ROUTE;
    }

    /**
     * Address without trailing slash, e.g. "https://router.eu.requesty.ai/v1"; '' when "custom" has no address.
     */
    public function baseUrl(): string
    {
        $route = $this->route();
        $url = self::CUSTOM === $route ? $this->settings->value('ai_base_url') : self::ROUTES[$route];

        return rtrim(trim(html_entity_decode($url, ENT_QUOTES | ENT_HTML5, 'UTF-8')), '/');
    }

    /**
     * The key in plain text, '' when none is stored.
     */
    public function apiKey(): string
    {
        return $this->settings->secret('ai_api_key');
    }

    public function hasKey(): bool
    {
        return $this->settings->hasValue('ai_api_key');
    }

    public function isConfigured(): bool
    {
        return '' !== $this->apiKey() && '' !== $this->baseUrl();
    }

    /**
     * Route and custom address for an address an extension used before, for the
     * one-time takeover.
     *
     * @return array{route: string, base_url: string}
     */
    public static function routeFor(string $baseUrl): array
    {
        $baseUrl = rtrim(trim($baseUrl), '/');

        foreach (self::ROUTES as $route => $url) {
            if ('' !== $url && 0 === strcasecmp($url, $baseUrl)) {
                return ['route' => $route, 'base_url' => ''];
            }
        }

        return '' === $baseUrl
            ? ['route' => self::DEFAULT_ROUTE, 'base_url' => '']
            : ['route' => self::CUSTOM, 'base_url' => $baseUrl];
    }

    /**
     * Stores route, address and key (encrypted). Used by the takeover migrations of the extensions.
     */
    public function store(string $route, string $baseUrl, string $plainKey): void
    {
        $this->settings->store(
            [
                'ai_route' => isset(self::ROUTES[$route]) ? $route : self::DEFAULT_ROUTE,
                'ai_base_url' => self::CUSTOM === $route ? $baseUrl : '',
                'ai_api_key' => $plainKey,
            ],
            ['ai_api_key'],
        );
    }

    /**
     * Whether the columns of the connection exist (see AccessSettings::hasColumns()).
     */
    public function isStorable(): bool
    {
        return $this->settings->hasColumns('adopted', 'ai_route', 'ai_base_url', 'ai_api_key');
    }

    /**
     * See AccessSettings::isAdopted().
     */
    public function isAdopted(string $source): bool
    {
        return $this->settings->isAdopted($source);
    }

    public function markAdopted(string $source): void
    {
        $this->settings->markAdopted($source);
    }
}
