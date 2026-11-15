<?php

declare(strict_types=1);

namespace Neo\BasicBundle\Tests\License;

use Neo\BasicBundle\License\Certificate;
use Neo\BasicBundle\License\LicenseState;
use Neo\BasicBundle\Tests\Support\TestKeys;
use PHPUnit\Framework\TestCase;

final class LicenseStateTest extends TestCase
{
    public function testAValidLicenceHasTheEditionOfItsCertificate(): void
    {
        $state = $this->state(LicenseState::OK);

        $this->assertTrue($state->isLicensed());
        $this->assertSame('plus', $state->edition());
        $this->assertTrue($state->hasEdition('plus'));
        $this->assertTrue($state->hasEdition('basic', 'plus'));
        $this->assertFalse($state->hasEdition('pro'));
        $this->assertFalse($state->hasEdition());
    }

    public function testEveryOtherReasonCountsAsUnlicensedEvenWithACertificate(): void
    {
        foreach ([LicenseState::DOMAIN, LicenseState::EXPIRED, LicenseState::REVOKED, LicenseState::WRONG_MODULE, LicenseState::UNVERIFIABLE] as $reason) {
            $state = $this->state($reason);

            $this->assertFalse($state->isLicensed(), $reason);
            $this->assertSame('', $state->edition(), $reason);
            $this->assertFalse($state->hasEdition('plus'), $reason);
        }
    }

    public function testWithoutCertificateThereIsNoEdition(): void
    {
        $state = new LicenseState('superagent', LicenseState::OK, null, null, '', 0, '');

        $this->assertSame('', $state->edition());
        $this->assertFalse($state->hasEdition('plus'));
    }

    private function state(string $reason): LicenseState
    {
        $signed = TestKeys::certificate();
        $certificate = Certificate::verify($signed['payload'], $signed['signature'], $signed['keyId'], TestKeys::publicKeys());

        return new LicenseState('superagent', $reason, $certificate, $signed, 'SA-••••-••••-••••-DD12', time(), '');
    }
}
