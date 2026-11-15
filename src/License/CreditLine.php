<?php

declare(strict_types=1);

namespace Neo\BasicBundle\License;

/**
 * Credit line of the free edition, the same for every extension: the shared
 * script public/credit.js draws it and keeps it visible; this service adds
 * the script to the page and hands over the signed certificate.
 *
 * Usage in an extension: register() where its own front-end assets are added,
 * proof() into the configuration of its script, and in the script
 * NeoCredit.attach(container, {module, proof, lang, onBlock}).
 */
final class CreditLine
{
    public const SCRIPT = 'bundles/neobasic/credit.js';

    public function __construct(private readonly LicenseService $licenses)
    {
    }

    /**
     * Adds the shared script to the page layout (combined with the other page scripts).
     */
    public function register(): void
    {
        $GLOBALS['TL_JAVASCRIPT']['neo_credit'] = self::SCRIPT.'|async|static';
    }

    /**
     * The certificate of the licence service when the licence of $module is
     * valid for $host, otherwise null. The script checks it again before it
     * leaves out the line, so a changed configuration does not remove it.
     *
     * @return array{payload: string, signature: string, keyId: string}|null
     */
    public function proof(string $module, string $host): array|null
    {
        $state = $this->licenses->state($module, $host);

        return $state->isLicensed() ? $state->signed : null;
    }
}
