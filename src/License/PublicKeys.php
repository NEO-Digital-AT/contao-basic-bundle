<?php

declare(strict_types=1);

namespace Neo\BasicBundle\License;

/**
 * Public keys of the licence service (ECDSA P-256, PEM), by key id.
 *
 * Fixed in the code on purpose: a key that could come from the configuration
 * would let anybody sign their own certificate. A new key of the licence
 * service therefore needs a new release of this bundle; old keys stay listed
 * so certificates issued with them keep working.
 *
 * The same keys are listed in public/credit.js (as base64 SubjectPublicKeyInfo),
 * where the browser checks the certificate before it leaves out the credit line.
 *
 * Source: GET <licence service>/v1/licenses/public-keys.
 */
final class PublicKeys
{
    public const KEYS = [
        // Production key since November 2026.
        '2026-11-c6bc62' => <<<'PEM'
            -----BEGIN PUBLIC KEY-----
            MFkwEwYHKoZIzj0CAQYIKoZIzj0DAQcDQgAEs/CgUyc2Jon/+PSd5pmnihVy9zQa
            oQ1hlVEDIU5TlEYrVZEdGizPwLINKbF0DJqvCmrL8g1ihBDzuKQJJBRbLg==
            -----END PUBLIC KEY-----
            PEM,
    ];
}
