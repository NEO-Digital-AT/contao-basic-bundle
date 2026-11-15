<?php

declare(strict_types=1);

namespace Neo\BasicBundle\Tests\EventListener;

use Contao\CoreBundle\Event\MenuEvent;
use Contao\CoreBundle\Framework\Adapter;
use Contao\CoreBundle\Framework\ContaoFramework;
use Knp\Menu\ItemInterface;
use Knp\Menu\MenuFactory;
use Neo\BasicBundle\EventListener\BreadcrumbListener;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;

final class BreadcrumbListenerTest extends TestCase
{
    private array $globals = [];

    protected function setUp(): void
    {
        $this->globals = [$GLOBALS['BE_MOD'] ?? null, $GLOBALS['TL_DCA'] ?? null, $GLOBALS['TL_LANG'] ?? null];

        $GLOBALS['BE_MOD'] = [
            'neo' => [
                'neo_access' => ['tables' => ['tl_access', 'tl_access_row']],
                'neo_consent' => ['tables' => ['tl_service', 'tl_settings', 'tl_text', 'tl_text_row']],
            ],
            'system' => ['other' => ['tables' => ['tl_other', 'tl_other_log']]],
        ];

        $GLOBALS['TL_DCA'] = [
            'tl_access' => ['config' => [BreadcrumbListener::SINGLE_RECORD => true, 'ctable' => ['tl_access_row']]],
            'tl_access_row' => ['config' => ['ptable' => 'tl_access']],
            'tl_service' => ['list' => ['global_operations' => [
                'settings' => ['href' => 'table=tl_settings&act=edit'],
                'texts' => ['href' => 'table=tl_text'],
            ]]],
            'tl_settings' => ['config' => [BreadcrumbListener::SINGLE_RECORD => true]],
            'tl_text' => ['config' => []],
            'tl_text_row' => ['config' => ['ptable' => 'tl_text']],
        ];

        $GLOBALS['TL_LANG']['tl_service'] = ['settings' => ['Settings', 'Edit the settings'], 'texts' => ['Texts', 'Texts per language']];
    }

    protected function tearDown(): void
    {
        [$GLOBALS['BE_MOD'], $GLOBALS['TL_DCA'], $GLOBALS['TL_LANG']] = $this->globals;
    }

    public function testTheOnlyRecordOfTheModuleStartIsTheModuleNameWithoutALink(): void
    {
        $tree = $this->breadcrumb('do=neo_access&act=edit&id=1', [
            ['Access keys', '/contao?do=neo_access&table=tl_access'],
            ['All extensions', '/contao?do=neo_access&id=1&table=tl_access&act=edit'],
        ]);

        $this->assertSame([['Access keys', null]], $this->items($tree));
    }

    public function testChildTablesOfTheOnlyRecordKeepContaosItems(): void
    {
        $items = [
            ['Access keys', '/contao?do=neo_access&table=tl_access'],
            ['All extensions', '/contao?do=neo_access&id=1&table=tl_access_row'],
        ];

        $this->assertSame($items, $this->items($this->breadcrumb('do=neo_access&table=tl_access_row&id=1', $items)));
    }

    public function testAFurtherTableLeadsToTheModuleStartAndNamesItsButton(): void
    {
        $tree = $this->breadcrumb('do=neo_consent&table=tl_text', [
            ['Consent', '/contao?do=neo_consent&table=tl_text'],
        ]);

        $this->assertSame([
            ['Consent', '/contao?do=neo_consent'],
            ['Texts', '/contao?do=neo_consent&table=tl_text'],
        ], $this->items($tree));
    }

    public function testRecordsOfAFurtherTableAndItsChildrenFollowTheButton(): void
    {
        $tree = $this->breadcrumb('do=neo_consent&table=tl_text_row&act=edit&id=7', [
            ['Consent', '/contao?do=neo_consent&table=tl_text'],
            ['German', '/contao?do=neo_consent&id=3&table=tl_text_row'],
            ['Row 7', '/contao?do=neo_consent&id=7&table=tl_text_row&act=edit'],
        ]);

        $this->assertSame([
            ['Consent', '/contao?do=neo_consent'],
            ['Texts', '/contao?do=neo_consent&table=tl_text'],
            ['German', '/contao?do=neo_consent&id=3&table=tl_text_row'],
            ['Row 7', '/contao?do=neo_consent&id=7&table=tl_text_row&act=edit'],
        ], $this->items($tree));
    }

    public function testTheFormOfASingleRecordFurtherTableIsNamedLikeItsButton(): void
    {
        $tree = $this->breadcrumb('do=neo_consent&table=tl_settings&act=edit&id=1', [
            ['Consent', '/contao?do=neo_consent&table=tl_settings'],
            ['Edit the settings.', '/contao?do=neo_consent&id=1&table=tl_settings&act=edit'],
        ]);

        $this->assertSame([
            ['Consent', '/contao?do=neo_consent'],
            ['Settings', '/contao?do=neo_consent&id=1&table=tl_settings&act=edit'],
        ], $this->items($tree));
    }

    public function testTheModuleStartAndOtherGroupsKeepContaosBreadcrumb(): void
    {
        $start = [['Consent', '/contao?do=neo_consent&table=tl_service']];
        $other = [['Other', '/contao?do=other&table=tl_other_log']];

        $this->assertSame($start, $this->items($this->breadcrumb('do=neo_consent', $start)));
        $this->assertSame($other, $this->items($this->breadcrumb('do=other&table=tl_other_log', $other)));
    }

    /**
     * @param list<array{string, string}> $items label and link of Contao's items
     */
    private function breadcrumb(string $query, array $items): ItemInterface
    {
        $factory = new MenuFactory();
        $tree = $factory->createItem('breadcrumbMenu');

        foreach ($items as $level => [$label, $uri]) {
            $tree->addChild('current_'.$level, ['label' => $label, 'uri' => $uri]);
        }

        $requests = new RequestStack();
        $requests->push(Request::create('https://example.com/contao?'.$query));

        $router = $this->createStub(RouterInterface::class);
        $router->method('generate')->willReturnCallback(static fn (string $route, array $parameters): string => '/contao?'.http_build_query($parameters));

        $framework = $this->createStub(ContaoFramework::class);
        $framework->method('getAdapter')->willReturn(new Adapter(NoLoader::class));

        (new BreadcrumbListener($requests, $router, $framework))(new MenuEvent($factory, $tree));

        return $tree;
    }

    /**
     * @return list<array{string, string|null}>
     */
    private function items(ItemInterface $tree): array
    {
        return array_values(array_map(static fn (ItemInterface $item): array => [$item->getLabel(), $item->getUri()], $tree->getChildren()));
    }
}

/** The DCA and the language files of the test are in place already. */
final class NoLoader
{
    public static function loadDataContainer(string $table): void
    {
    }

    public static function loadLanguageFile(string $name): void
    {
    }
}
