<?php

declare(strict_types=1);

namespace Neo\BasicBundle\Tests\Access;

use Doctrine\DBAL\Connection;
use Neo\BasicBundle\Access\AccessCallbacks;
use Neo\BasicBundle\Access\AccessPage;
use Neo\BasicBundle\Access\AccessSettings;
use Neo\BasicBundle\Ai\AiConnection;
use Neo\BasicBundle\Backend\RequestInput;
use Neo\BasicBundle\EventListener\AccessPaletteListener;
use Neo\BasicBundle\License\LicenseService;
use Neo\BasicBundle\Security\SecretCipher;
use Neo\BasicBundle\Terms\TermsService;
use Neo\BasicBundle\Tests\Support\BackendFixtures;
use Neo\BasicBundle\Tests\Support\DataContainers;
use Neo\BasicBundle\Tests\Support\Database;
use Neo\BasicBundle\Tests\Support\LanguageFiles;
use Neo\BasicBundle\Tests\Support\MemoryLogger;
use Neo\BasicBundle\Tests\Support\RecordingClient;
use Neo\BasicBundle\Tests\Support\Settings;
use Neo\BasicBundle\Translation\AzureConnection;
use Neo\BasicBundle\Translation\DeepLConnection;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\RequestStack;

final class AccessCallbacksTest extends TestCase
{
    use BackendFixtures;
    use DataContainers;

    private Connection $db;

    private SecretCipher $cipher;

    protected function setUp(): void
    {
        LanguageFiles::load('tl_neo_access', 'neo_license');
        unset($GLOBALS['TL_DCA']);
        $this->db = Database::connection();
        $this->cipher = new SecretCipher(Settings::SECRET);
    }

    protected function tearDown(): void
    {
        unset($GLOBALS['TL_DCA']);
        LanguageFiles::reset();
    }

    public function testAStoredSecretIsShownAsAMaskOnly(): void
    {
        $callbacks = $this->callbacks();

        $this->assertSame('••••••••', $callbacks->maskSecret($this->cipher->encrypt('sk-test')));
        $this->assertSame('', $callbacks->maskSecret(''));
        $this->assertSame('', $callbacks->maskSecret(null));
    }

    public function testTheUnchangedMaskOrAnEmptyFieldKeepsTheStoredSecret(): void
    {
        $stored = $this->cipher->encrypt('sk-stored');
        $this->db->insert('tl_neo_access', ['id' => 3, 'ai_api_key' => $stored]);
        $callbacks = $this->callbacks();
        $dc = $this->dataContainer('ai_api_key', 3);

        $this->assertSame($stored, $callbacks->saveSecret('••••••••', $dc));
        $this->assertSame($stored, $callbacks->saveSecret('  ', $dc));
    }

    public function testANewSecretIsStoredEncryptedExactlyAsTyped(): void
    {
        $this->db->insert('tl_neo_access', ['id' => 3]);
        $saved = $this->callbacks()->saveSecret(' sk-a&amp;b&lt;c ', $this->dataContainer('ai_api_key', 3));

        $this->assertStringStartsWith('enc:', $saved);
        $this->assertSame('sk-a&b<c', $this->cipher->decrypt($saved));
    }

    public function testTheStatusOfASectionWithSecrets(): void
    {
        AccessPage::addSection('x_legend', ['x_api_key', 'x_secret'], 'Extension A');
        AccessPage::addSection('opt_legend', ['x_secret'], 'Extension B', true);
        $settings = $this->settings(['x_api_key' => 'key'], ['x_api_key']);
        $callbacks = $this->callbacks($settings);

        $this->assertStringContainsString('<p class="tl_error">Not stored yet.</p>', $callbacks->sectionStatus($this->dataContainer('x_status')));
        $this->assertStringContainsString('Used by Extension A.', $callbacks->sectionStatus($this->dataContainer('x_status')));
        $this->assertStringContainsString('<p class="tl_info">Not stored. Only needed for some functions.</p>', $callbacks->sectionStatus($this->dataContainer('opt_status')));

        $settings->store(['x_secret' => 'secret'], ['x_secret']);

        $this->assertStringContainsString('<p class="tl_confirm">Stored.</p>', $this->callbacks($settings)->sectionStatus($this->dataContainer('x_status')));
    }

    public function testWithoutKeyTheTestButtonIsDisabledWithTheReason(): void
    {
        AccessPage::useAi('SuperAgent');

        $html = $this->callbacks()->aiStatus();

        $this->assertStringContainsString('No key stored yet.', $html);
        $this->assertStringContainsString('disabled', $html);
        $this->assertStringContainsString('Can be tested once the access is saved.', $html);
        $this->assertStringContainsString('Used by SuperAgent.', $html);
    }

    public function testWithKeyTheStatusNamesTheAddressAndOffersTheTest(): void
    {
        $html = $this->callbacks($this->settings(['ai_route' => 'openai', 'ai_api_key' => 'sk-test'], ['ai_api_key']))->aiStatus();

        $this->assertStringContainsString('Key stored (encrypted). Address: https://api.openai.com/v1', $html);
        $this->assertStringContainsString('key=neo_ai_test', $html);
        $this->assertStringNotContainsString('sk-test', $html);
    }

    public function testDeepLStatusNamesThePlanOfTheKey(): void
    {
        $html = $this->callbacks($this->settings(['deepl_key' => 'k:fx'], ['deepl_key']))->deeplStatus();

        $this->assertStringContainsString('tl_confirm', $html);
        $this->assertStringContainsString('https://api-free.deepl.com', $html);
    }

    public function testCompleteSectionsStartCollapsedOpenOnesStayOpen(): void
    {
        AccessPage::useAi('SuperAgent');
        AccessPage::useAzureTranslator('SuperAgent');
        AccessPage::useDeepL('SuperAgent');
        AccessPage::addSection('x_legend', ['x_api_key'], 'Extension A');
        AccessPage::addSection('opt_legend', ['x_secret'], 'Extension B', true);
        AccessPage::addLicense('superagent', 'acme/superagent-bundle', 'SuperAgent');
        AccessPage::addTerms('superagent', 'SuperAgent', 'domain', ['all' => 1]);
        (new AccessPaletteListener())('tl_neo_access');

        $settings = $this->settings(['ai_api_key' => 'sk-test', 'azure_translator_key' => 'k', 'azure_translator_location' => 'custom'], ['ai_api_key', 'azure_translator_key']);
        $this->callbacks($settings, ['act' => 'edit', 'id' => '1'])->collapseComplete();

        $palette = $GLOBALS['TL_DCA']['tl_neo_access']['palettes']['default'];
        $this->assertStringContainsString('{neo_license_legend},', $palette, 'licences always open');
        $this->assertStringContainsString('{neo_terms_legend},', $palette, 'terms not confirmed yet');
        $this->assertStringContainsString('{ai_legend:hide}', $palette);
        $this->assertStringContainsString('{azure_translator_legend},', $palette, 'key without the own address');
        $this->assertStringContainsString('{deepl_legend:hide}', $palette);
        $this->assertStringContainsString('{x_legend},', $palette, 'key missing');
        $this->assertStringContainsString('{opt_legend:hide}', $palette, 'optional and empty');
    }

    public function testConfirmedTermsCollapse(): void
    {
        AccessPage::addTerms('superagent', 'SuperAgent', 'domain', ['all' => 1, 'basic' => 1]);
        (new AccessPaletteListener())('tl_neo_access');
        (new TermsService($this->db))->accept('superagent', ['all' => 1], 1, 'max.mustermann', 'de');

        $this->callbacks(null, ['act' => 'edit'])->collapseComplete();

        $this->assertStringContainsString('{neo_terms_legend:hide}', $GLOBALS['TL_DCA']['tl_neo_access']['palettes']['default'], 'without licence only the first passage applies');
    }

    public function testThePaletteIsLeftAloneOutsideTheForm(): void
    {
        AccessPage::useDeepL('SuperAgent');
        (new AccessPaletteListener())('tl_neo_access');
        $before = $GLOBALS['TL_DCA']['tl_neo_access']['palettes']['default'];

        $this->callbacks(null, ['act' => 'edit', 'key' => 'neo_deepl_test'])->collapseComplete();
        $this->callbacks(null, [])->collapseComplete();

        $this->assertSame($before, $GLOBALS['TL_DCA']['tl_neo_access']['palettes']['default']);
    }

    public function testTheFormHasOnlySaveSinceThereIsNoListToCloseTo(): void
    {
        $buttons = ['save' => '<button>Save</button>', 'saveNclose' => '<button>Save and close</button>'];

        $this->assertSame(['save' => '<button>Save</button>'], $this->callbacks(Settings::with())->editButtons($buttons));
    }

    public function testRouteAndLocationOptions(): void
    {
        $callbacks = $this->callbacks();

        $this->assertSame(['requesty_eu', 'requesty_global', 'openai', 'custom'], $callbacks->routeOptions());
        $this->assertSame(['eur', 'global', 'nam', 'apc', 'custom'], $callbacks->azureLocationOptions());
    }

    /**
     * @param array<string, string> $values
     * @param list<string>          $secrets
     */
    private function settings(array $values, array $secrets = []): AccessSettings
    {
        $settings = new AccessSettings($this->db, $this->cipher);
        $settings->store($values, $secrets);

        return $settings;
    }

    /**
     * @param array<string, string> $query
     */
    private function callbacks(AccessSettings|null $settings = null, array $query = []): AccessCallbacks
    {
        $settings ??= new AccessSettings($this->db, $this->cipher);
        $stack = $this->backendRequest('GET', $query);
        $licenses = new LicenseService($this->db, $this->cipher, (new RecordingClient())->client, new RequestStack(), new MemoryLogger(), 'https://license.example.com');

        return new AccessCallbacks(
            $settings,
            new AiConnection($settings),
            $this->cipher,
            new AzureConnection($settings),
            $this->db,
            new DeepLConnection($settings),
            $this->actions($stack, $settings),
            $licenses,
            new TermsService($this->db),
            new RequestInput($stack),
        );
    }
}
