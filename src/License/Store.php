<?php

declare(strict_types=1);

namespace Neo\BasicBundle\License;

/**
 * Where a licence key is bought: the page of the extension in the
 * manufacturer's store. The address has no language; the store forwards to
 * the page in the visitor's browser language. Like the address of the licence
 * service (LicenseService::DEFAULT_URL) a fixed address of the manufacturer,
 * on purpose: without it nobody finds out where a licence is sold. A package
 * without a page here links to the start page of the store.
 *
 * CONTACT_URL and CONTACT_NAME: whom to contact when a licence has to cover more
 * installations than it allows (the number is set by the manufacturer, one by default).
 */
final class Store
{
    public const URL = 'https://store.neo-digital.at';
    public const CONTACT_URL = 'https://neo-digital.at';
    public const CONTACT_NAME = 'NEO Digital';

    /**
     * Package => page under /extensions/.
     */
    private const PAGES = [
        'neo-digital/contao-superagent-bundle' => 'superagent',
        'neo-digital/contao-frontendeditor-bundle' => 'frontend-editor',
        'neo-digital/contao-websitecheck-bundle' => 'website-check',
        'neo-digital/contao-cookieconsent-bundle' => 'cookie-consent',
        'neo-digital/contao-accessibility-bundle' => 'accessibility',
        'neo-digital/contao-brevo-bundle' => 'brevo-newsletter',
        'neo-digital/contao-llms-bundle' => 'llms',
    ];

    public static function url(string $package): string
    {
        $page = self::PAGES[$package] ?? null;

        return null === $page ? self::URL : self::URL.'/extensions/'.$page;
    }
}
