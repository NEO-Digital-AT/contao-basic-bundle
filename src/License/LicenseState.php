<?php

declare(strict_types=1);

namespace Neo\BasicBundle\License;

/**
 * What an extension needs to know about its licence on this host. Functions of
 * a paid level ask hasEdition(); everything else counts as unlicensed.
 */
final class LicenseState
{
    /**
     * Reasons, also used as keys of the status texts (languages/…/neo_license.php).
     */
    public const OK = 'ok';
    public const NO_KEY = 'no_key';
    public const INVALID_KEY = 'invalid_key';
    public const NO_CERTIFICATE = 'no_certificate';
    public const UNVERIFIABLE = 'unverifiable';
    public const WRONG_MODULE = 'wrong_module';
    public const DOMAIN = 'domain';
    public const EXPIRED = 'expired';
    public const REVOKED = 'revoked';

    /**
     * Why the licence service does not cover a domain (LicenseState::$reasons).
     */
    public const NOT_LICENSED = 'notLicensed';
    public const INSTALLATIONS_EXHAUSTED = 'installationsExhausted';
    public const DOMAINS_PER_INSTALLATION_EXCEEDED = 'domainsPerInstallationExceeded';

    /**
     * @param array{payload: string, signature: string, keyId: string}|null $signed                 the stored certificate as received from the licence service
     * @param array<string, bool>                                           $coverage               domains reported at the last check => covered by the licence
     * @param array<string, string|null>                                    $reasons                domains reported at the last check => null when covered, otherwise why not (NOT_LICENSED, INSTALLATIONS_EXHAUSTED, DOMAINS_PER_INSTALLATION_EXCEEDED or a reason of a later licence service); empty when the service sends no reasons
     * @param array{allowed: int, used: int}|null                           $installations          installations of the licence and how many are in use; null when the service does not say
     * @param int|null                                                      $domainsPerInstallation domains allowed per installation; null when the service does not say
     */
    public function __construct(
        public readonly string $module,
        public readonly string $reason,
        public readonly Certificate|null $certificate,
        public readonly array|null $signed,
        public readonly string $keyHint,
        public readonly int $checkedAt,
        public readonly string $lastError,
        public readonly array $coverage = [],
        public readonly array $reasons = [],
        public readonly array|null $installations = null,
        public readonly int|null $domainsPerInstallation = null,
    ) {
    }

    public function isLicensed(): bool
    {
        return self::OK === $this->reason;
    }

    /**
     * Edition code of a valid licence ("basic", "plus" …), '' without one.
     */
    public function edition(): string
    {
        return $this->isLicensed() && null !== $this->certificate ? $this->certificate->edition : '';
    }

    public function hasEdition(string ...$editions): bool
    {
        return $this->isLicensed() && \in_array($this->edition(), $editions, true);
    }
}
