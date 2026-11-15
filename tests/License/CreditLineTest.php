<?php

declare(strict_types=1);

namespace Neo\BasicBundle\Tests\License;

use Neo\BasicBundle\License\CreditLine;
use Neo\BasicBundle\License\LicenseService;
use Neo\BasicBundle\Security\SecretCipher;
use Neo\BasicBundle\Tests\Support\Database;
use Neo\BasicBundle\Tests\Support\MemoryLogger;
use Neo\BasicBundle\Tests\Support\RecordingClient;
use Neo\BasicBundle\Tests\Support\Settings;
use Neo\BasicBundle\Tests\Support\TestKeys;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\RequestStack;

final class CreditLineTest extends TestCase
{
    protected function tearDown(): void
    {
        unset($GLOBALS['TL_JAVASCRIPT']);
    }

    public function testTheSharedScriptIsAddedToThePageScripts(): void
    {
        $this->line()->register();

        $this->assertSame('bundles/neobasic/credit.js|async|static', $GLOBALS['TL_JAVASCRIPT']['neo_credit']);
    }

    public function testWithoutValidLicenceThereIsNoProofSoTheLineIsDrawn(): void
    {
        $db = Database::connection();
        $cipher = new SecretCipher(Settings::SECRET);
        $signed = TestKeys::certificate();
        $db->insert('tl_neo_license', ['module' => 'superagent', 'license_key' => $cipher->encrypt('SA-1'), 'payload' => $signed['payload'], 'signature' => $signed['signature'], 'key_id' => $signed['keyId']]);

        $line = $this->line(new LicenseService($db, $cipher, (new RecordingClient())->client, new RequestStack(), new MemoryLogger(), 'https://license.example.com'));

        $this->assertNull($line->proof('superagent', 'example.com'), 'a certificate not signed by the licence service');
        $this->assertNull($line->proof('websitecheck', 'example.com'), 'no licence at all');
    }

    private function line(LicenseService|null $licenses = null): CreditLine
    {
        return new CreditLine($licenses ?? new LicenseService(Database::connection(), new SecretCipher(Settings::SECRET), (new RecordingClient())->client, new RequestStack(), new MemoryLogger(), 'https://license.example.com'));
    }
}
