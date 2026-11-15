<?php

declare(strict_types=1);

namespace Neo\BasicBundle\Tests\Dock;

use Contao\PageModel;
use Neo\BasicBundle\Dock\Dock;
use Neo\BasicBundle\Tests\Support\Database;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Translation\TranslatorInterface;

final class DockTest extends TestCase
{
    protected function tearDown(): void
    {
        unset($GLOBALS['TL_HEAD'], $GLOBALS['TL_CSS'], $GLOBALS['TL_JAVASCRIPT']);
    }

    public function testRegisterAddsScriptStyleAndSettingOfTheLayoutToThePage(): void
    {
        $db = Database::connection();
        $db->insert('tl_layout', ['name' => 'Stacked', 'neo_dock' => 'vertical']);
        $request = new Request();
        $stack = new RequestStack();
        $stack->push($request);

        $dock = new Dock($db, $stack, $this->translator(['de' => 'Einstellungen']));
        $dock->register($this->page(['layoutId' => 1, 'language' => 'de']));
        $dock->register($this->page(['layoutId' => 1, 'language' => 'de']));

        $this->assertSame('<meta name="neo-dock" content="vertical" data-neo-dock-label="Einstellungen">', $GLOBALS['TL_HEAD']['neo_dock']);
        $this->assertStringStartsWith('bundles/neobasic/dock.css|all|static|', $GLOBALS['TL_CSS']['neo_dock']);
        $this->assertStringStartsWith('bundles/neobasic/dock.js|defer|static|', $GLOBALS['TL_JAVASCRIPT']['neo_dock']);
        $this->assertCount(1, $GLOBALS['TL_JAVASCRIPT'], 'registered once, however many buttons');
        $this->assertSame(['arrangement' => 'vertical', 'label' => 'Einstellungen'], $request->attributes->get(Dock::ATTRIBUTE));
    }

    public function testThePageOfTheRequestIsUsedWithoutAPage(): void
    {
        $db = Database::connection();
        $db->insert('tl_layout', ['name' => 'Side by side', 'neo_dock' => 'horizontal']);
        $request = new Request();
        $request->attributes->set('pageModel', $this->page(['layoutId' => 1, 'language' => 'en']));
        $stack = new RequestStack();
        $stack->push($request);

        (new Dock($db, $stack, $this->translator(['en' => 'Settings'])))->register();

        $this->assertSame('<meta name="neo-dock" content="horizontal" data-neo-dock-label="Settings">', $GLOBALS['TL_HEAD']['neo_dock']);
    }

    public function testTheMenuIsTheDefault(): void
    {
        $db = Database::connection();
        $db->insert('tl_layout', ['name' => 'Default']);
        $db->insert('tl_layout', ['name' => 'Old value', 'neo_dock' => 'sideways']);
        $dock = new Dock($db, new RequestStack(), $this->translator([]));

        $this->assertSame('menu', $dock->arrangement($this->page(['layoutId' => 1])), 'new column, default value');
        $this->assertSame('menu', $dock->arrangement($this->page(['layoutId' => 2])), 'unknown value');
        $this->assertSame('menu', $dock->arrangement($this->page(['layoutId' => 7])), 'layout gone');
        $this->assertSame('menu', $dock->arrangement($this->page([])), 'page without layout');
        $this->assertSame('menu', $dock->arrangement(null), 'no page');
    }

    public function testTheInheritedLayoutCountsWhenThePageWasNotRenderedYet(): void
    {
        $db = Database::connection();
        $db->insert('tl_layout', ['name' => 'Stacked', 'neo_dock' => 'vertical']);

        $dock = new Dock($db, new RequestStack(), $this->translator([]));

        $this->assertSame('vertical', $dock->arrangement($this->page(['layout' => 1])));
    }

    public function testADatabaseThatWasNotUpdatedGivesTheMenu(): void
    {
        $dock = new Dock(Database::connection(Database::PAGE), new RequestStack(), $this->translator([]));

        $this->assertSame('menu', $dock->arrangement($this->page(['layoutId' => 1])));
    }

    public function testTheLabelIsEscapedAndHasAFallback(): void
    {
        $dock = new Dock(Database::connection(), new RequestStack(), $this->translator(['fr' => 'Réglages "<b>"']));

        $this->assertSame('Réglages "<b>"', $dock->label($this->page(['language' => 'fr'])));
        $this->assertSame('Settings', $dock->label($this->page(['language' => 'xx'])), 'missing translation');
        $this->assertSame(
            '<meta name="neo-dock" content="menu" data-neo-dock-label="Réglages &quot;&lt;b&gt;&quot;">',
            Dock::meta(['arrangement' => 'menu', 'label' => 'Réglages "<b>"']),
        );
    }

    public function testEveryLanguageNamesTheSettingsButton(): void
    {
        foreach (glob(\dirname(__DIR__, 2).'/contao/languages/*', GLOB_ONLYDIR) as $dir) {
            $GLOBALS['TL_LANG'] = [];
            include $dir.'/default.php';
            include $dir.'/tl_layout.php';

            $this->assertNotSame('', $GLOBALS['TL_LANG']['MSC']['neo_dock_settings'] ?? '', basename($dir));
            $this->assertSame(Dock::ARRANGEMENTS, array_keys($GLOBALS['TL_LANG']['tl_layout']['neo_dock_options']), basename($dir));
            $this->assertCount(2, $GLOBALS['TL_LANG']['tl_layout']['neo_dock'], basename($dir));
            $this->assertNotSame('', $GLOBALS['TL_LANG']['tl_layout']['neo_dock_legend'], basename($dir));
        }

        unset($GLOBALS['TL_LANG']);
    }

    /**
     * @param array<string, mixed> $data
     */
    private function page(array $data): PageModel
    {
        $page = $this->createStub(PageModel::class);
        $page->method('__get')->willReturnCallback(static fn (string $key): mixed => $data[$key] ?? null);

        return $page;
    }

    /**
     * @param array<string, string> $labels label of MSC.neo_dock_settings per language
     */
    private function translator(array $labels): TranslatorInterface
    {
        $translator = $this->createStub(TranslatorInterface::class);
        $translator->method('trans')->willReturnCallback(static fn (string $id, array $parameters = [], string|null $domain = null, string|null $locale = null): string => 'MSC.neo_dock_settings' === $id && 'contao_default' === $domain ? ($labels[$locale ?? 'en'] ?? $id) : $id);

        return $translator;
    }
}
