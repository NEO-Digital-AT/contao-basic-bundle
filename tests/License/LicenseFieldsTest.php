<?php

declare(strict_types=1);

namespace Neo\BasicBundle\Tests\License;

use Contao\CoreBundle\Exception\ResponseException;
use Doctrine\DBAL\Connection;
use Neo\BasicBundle\Access\AccessPage;
use Neo\BasicBundle\Backend\RequestInput;
use Neo\BasicBundle\License\LicenseFields;
use Neo\BasicBundle\License\LicenseService;
use Neo\BasicBundle\Security\SecretCipher;
use Neo\BasicBundle\Tests\Support\BackendFixtures;
use Neo\BasicBundle\Tests\Support\DataContainers;
use Neo\BasicBundle\Tests\Support\Database;
use Neo\BasicBundle\Tests\Support\LanguageFiles;
use Neo\BasicBundle\Tests\Support\MemoryLogger;
use Neo\BasicBundle\Tests\Support\RecordingClient;
use Neo\BasicBundle\Tests\Support\Settings;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\RequestStack;

final class LicenseFieldsTest extends TestCase
{
    use BackendFixtures;
    use DataContainers;

    private Connection $db;

    private SecretCipher $cipher;

    protected function setUp(): void
    {
        LanguageFiles::load('tl_neo_access', 'neo_license');
        unset($GLOBALS['TL_DCA']);
        AccessPage::addLicense('superagent', 'acme/not-installed', 'SuperAgent');
        $this->db = Database::connection();
        $this->cipher = new SecretCipher(Settings::SECRET);
    }

    protected function tearDown(): void
    {
        unset($GLOBALS['TL_DCA']);
        LanguageFiles::reset();
    }

    public function testTheKeyFieldShowsTheShortenedKeyOnly(): void
    {
        $this->storeKey();

        $this->assertSame('SA-••••-••••-••••-DD12', $this->fields(new RecordingClient())->loadKey('', $this->dataContainer('license_superagent_key')));
    }

    public function testSavingTheUnchangedShortenedKeyAsksNobody(): void
    {
        $this->storeKey();
        $http = new RecordingClient();

        $this->assertSame('', $this->fields($http)->saveKey('SA-••••-••••-••••-DD12', $this->dataContainer('license_superagent_key')));
        $this->assertSame([], $http->requests);
    }

    public function testANewKeyIsActivatedWithThePackageOfTheExtension(): void
    {
        $http = new RecordingClient(RecordingClient::json([], 503));

        $this->assertSame('', $this->fields($http)->saveKey('sa-aaaa-bbbb-cccc-dd12', $this->dataContainer('license_superagent_key')));
        $this->assertSame('https://license.example.com/v1/licenses/activate', $http->requests[0]['url']);
        $this->assertSame('acme/not-installed', $this->db->fetchOne('SELECT package FROM tl_neo_license'));
    }

    public function testAnUnknownKeyIsRefusedWithAMessage(): void
    {
        $http = new RecordingClient(RecordingClient::json(['errorCode' => 'E601'], 404));

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage($GLOBALS['TL_LANG']['neo_license']['reason']['invalid_key']);

        $this->fields($http)->saveKey('SA-0000', $this->dataContainer('license_superagent_key'));
    }

    public function testCheckNowOnlyForExtensionsThatAddedALicenceRow(): void
    {
        $this->storeKey('websitecheck');
        $http = new RecordingClient();
        $stack = $this->backendRequest('POST', ['module' => 'websitecheck'], ['REQUEST_TOKEN' => 'valid-token'], true);

        $answer = $this->answerOf(fn () => $this->fields($http, $stack)->refreshOperation($this->dataContainer('')));

        $this->assertFalse($answer['ok']);
        $this->assertSame('neo_license_websitecheck', $answer['target']);
        $this->assertSame([], $http->requests);
    }

    public function testCheckNowNeedsAValidRequestToken(): void
    {
        $this->storeKey();
        $http = new RecordingClient();
        $stack = $this->backendRequest('POST', ['module' => 'superagent'], ['REQUEST_TOKEN' => 'forged'], true);

        $answer = $this->answerOf(fn () => $this->fields($http, $stack)->refreshOperation($this->dataContainer('')));

        $this->assertFalse($answer['ok']);
        $this->assertSame([], $http->requests);
    }

    public function testCheckNowAsksTheLicenceServiceAndAnswersWithTheNewStatus(): void
    {
        $this->storeKey();
        $http = new RecordingClient(RecordingClient::json(['errorCode' => 'E601'], 404));
        $stack = $this->backendRequest('POST', ['module' => 'superagent'], ['REQUEST_TOKEN' => 'valid-token'], true);

        $answer = $this->answerOf(fn () => $this->fields($http, $stack)->refreshOperation($this->dataContainer('')));

        $this->assertFalse($answer['ok']);
        $this->assertSame('https://license.example.com/v1/licenses/refresh', $http->requests[0]['url']);
        $this->assertStringContainsString(htmlspecialchars($GLOBALS['TL_LANG']['neo_license']['reason']['invalid_key'], ENT_QUOTES | ENT_HTML5), $answer['html']);
    }

    public function testWithoutKeyTheStatusLineLinksToThePageOfTheExtensionInTheStore(): void
    {
        unset($GLOBALS['TL_DCA']);
        AccessPage::addLicense('superagent', 'neo-digital/contao-superagent-bundle', 'SuperAgent');
        $GLOBALS['TL_LANG']['tl_neo_access']['license_superagent_unlicensed'] = 'All basic functions work.';

        $html = $this->fields(new RecordingClient())->status($this->dataContainer('license_superagent_status'));

        $this->assertStringContainsString('class="widget w50 neo-license-status" id="neo_license_superagent"><h3 aria-hidden="true">&nbsp;</h3>', $html);
        // One line: the state and, in the same line, where to buy a key.
        $this->assertStringContainsString('<p class="tl_info"><span>'.htmlspecialchars($GLOBALS['TL_LANG']['neo_license']['reason']['no_key'], ENT_QUOTES | ENT_HTML5).' <a href="https://store.neo-digital.at/extensions/superagent" target="_blank" rel="noopener">'.$GLOBALS['TL_LANG']['neo_license']['store_buy'].'</a></span></p>', $html);
        $this->assertStringNotContainsString($GLOBALS['TL_LANG']['neo_license']['store_manage'], $html);
        // What the editions include is on the store page, not in the row.
        $this->assertStringNotContainsString('All basic functions work.', $html);
        $this->assertStringNotContainsString('<details', $html);
        $this->assertStringNotContainsString('key=neo_license_refresh', $html, 'nothing to check without a key');
    }

    public function testCheckNowAnswersWithTheStoreLinkInTheStatusLine(): void
    {
        $this->storeKey();
        $http = new RecordingClient(RecordingClient::json(['errorCode' => 'E601'], 404));
        $stack = $this->backendRequest('POST', ['module' => 'superagent'], ['REQUEST_TOKEN' => 'valid-token'], true);

        $answer = $this->answerOf(fn () => $this->fields($http, $stack)->refreshOperation($this->dataContainer('')));

        $this->assertStringContainsString('href="https://store.neo-digital.at"', $answer['html'], 'acme/not-installed has no page: start page of the store');
    }

    public function testTheRowSaysPerDomainWhyAndHowManyInstallationsAreInUse(): void
    {
        $this->db->insert('tl_neo_license', [
            'module' => 'superagent',
            'package' => 'acme/not-installed',
            'license_key' => $this->cipher->encrypt('SA-AAAA-BBBB-CCCC-DD12'),
            'coverage' => json_encode([
                ['domain' => 'www.example.com', 'covered' => true, 'reason' => null],
                ['domain' => 'example.org', 'covered' => false, 'reason' => 'notLicensed'],
                ['domain' => 'example.net', 'covered' => false, 'reason' => 'installationsExhausted'],
                ['domain' => 'example.info', 'covered' => false, 'reason' => 'domainsPerInstallationExceeded'],
                ['domain' => 'example.biz', 'covered' => false, 'reason' => 'somethingNew'],
            ]),
            'allotment' => json_encode(['installations' => ['allowed' => 3, 'used' => 3], 'domainsPerInstallation' => 2]),
        ]);
        $lang = $GLOBALS['TL_LANG']['neo_license'];
        $e = static fn (string $text): string => htmlspecialchars($text, ENT_QUOTES | ENT_HTML5);

        $html = $this->fields(new RecordingClient())->status($this->dataContainer('license_superagent_status'));

        $this->assertStringContainsString('<li><strong>www.example.com</strong>: '.$e($lang['domain_valid']).'</li>', $html);
        $this->assertStringContainsString('<li><strong>example.org</strong>: '.$e($lang['domain_reason']['notLicensed']).'</li>', $html);
        $this->assertStringContainsString('<li><strong>example.net</strong>: '.$e($lang['domain_reason']['installationsExhausted']).'</li>', $html);
        $this->assertStringContainsString('<li><strong>example.info</strong>: '.$e($lang['domain_reason']['domainsPerInstallationExceeded']).'</li>', $html);
        $this->assertStringContainsString('<li><strong>example.biz</strong>: '.$e($lang['domain_reason']['other']).'</li>', $html, 'a reason of a later licence service');
        $this->assertStringContainsString($e(\sprintf($lang['installations'], 3, 3).' · '.\sprintf($lang['domains_per_installation'][1], 2)), $html);
        $this->assertStringNotContainsString(\sprintf($lang['uncovered'], ''), $html, 'no second, older line about uncovered domains');
    }

    public function testAllInstallationsInUseShowAWarningWithTheContactOfTheManufacturer(): void
    {
        $this->db->insert('tl_neo_license', [
            'module' => 'superagent',
            'package' => 'acme/not-installed',
            'license_key' => $this->cipher->encrypt('SA-AAAA-BBBB-CCCC-DD12'),
            'coverage' => json_encode([['domain' => 'www.example.com', 'covered' => false, 'reason' => 'installationsExhausted']]),
            'allotment' => json_encode(['installations' => ['allowed' => 1, 'used' => 1]]),
        ]);
        $text = htmlspecialchars($GLOBALS['TL_LANG']['neo_license']['installations_exhausted'], ENT_QUOTES | ENT_HTML5);
        $link = '<a href="https://neo-digital.at" target="_blank" rel="noopener">NEO Digital</a>';

        $html = $this->fields(new RecordingClient())->status($this->dataContainer('license_superagent_status'));

        $this->assertStringContainsString('<p class="tl_error neo-license-exhausted" role="alert"><span>'.\sprintf($text, $link).'</span></p>', $html);
        $this->assertLessThan(strpos($html, 'neo-license-domains'), strpos($html, 'neo-license-exhausted'), 'the warning comes before the list of domains');
    }

    public function testOtherReasonsShowNoWarningAboutInstallations(): void
    {
        $this->db->insert('tl_neo_license', [
            'module' => 'superagent',
            'package' => 'acme/not-installed',
            'license_key' => $this->cipher->encrypt('SA-AAAA-BBBB-CCCC-DD12'),
            'coverage' => json_encode([['domain' => 'example.org', 'covered' => false, 'reason' => 'notLicensed']]),
        ]);

        $html = $this->fields(new RecordingClient())->status($this->dataContainer('license_superagent_status'));

        $this->assertStringNotContainsString('neo-license-exhausted', $html);
    }

    public function testWithoutReasonsAndInstallationsTheRowStaysAsBefore(): void
    {
        $this->db->insert('tl_neo_license', [
            'module' => 'superagent',
            'package' => 'acme/not-installed',
            'license_key' => $this->cipher->encrypt('SA-AAAA-BBBB-CCCC-DD12'),
            'coverage' => json_encode([['domain' => 'example.com', 'covered' => true]]),
        ]);

        $html = $this->fields(new RecordingClient())->status($this->dataContainer('license_superagent_status'));

        $this->assertStringNotContainsString('neo-license-domains', $html);
        $this->assertStringNotContainsString(\sprintf($GLOBALS['TL_LANG']['neo_license']['installations'], 0, 0), $html);
    }

    public function testOneDomainPerInstallationIsSaidInTheSingular(): void
    {
        $this->db->insert('tl_neo_license', [
            'module' => 'superagent',
            'package' => 'acme/not-installed',
            'license_key' => $this->cipher->encrypt('SA-AAAA-BBBB-CCCC-DD12'),
            'allotment' => json_encode(['domainsPerInstallation' => 1]),
        ]);

        $html = $this->fields(new RecordingClient())->status($this->dataContainer('license_superagent_status'));

        $this->assertStringContainsString(htmlspecialchars(\sprintf($GLOBALS['TL_LANG']['neo_license']['domains_per_installation'][0], 1), ENT_QUOTES | ENT_HTML5), $html);
    }

    public function testTargetKeepsOnlySafeCharacters(): void
    {
        $this->assertSame('neo_license_superagent', LicenseFields::target('SuperAgent'));
        $this->assertSame('neo_license_xscriptalert1script', LicenseFields::target('x"><script>alert(1)</script>'));
    }

    private function storeKey(string $module = 'superagent'): void
    {
        $this->db->insert('tl_neo_license', ['module' => $module, 'package' => 'acme/not-installed', 'license_key' => $this->cipher->encrypt('SA-AAAA-BBBB-CCCC-DD12')]);
    }

    private function fields(RecordingClient $http, RequestStack|null $stack = null): LicenseFields
    {
        $stack ??= $this->backendRequest();
        $licenses = new LicenseService($this->db, $this->cipher, $http->client, new RequestStack(), new MemoryLogger(), 'https://license.example.com');

        return new LicenseFields($licenses, $this->actions($stack, Settings::with()), new RequestInput($stack));
    }

    /**
     * @return array{ok: bool, target: string, html: string}
     */
    private function answerOf(callable $operation): array
    {
        try {
            $operation();
        } catch (ResponseException $e) {
            return json_decode((string) $e->getResponse()->getContent(), true);
        }

        $this->fail('No response');
    }
}
