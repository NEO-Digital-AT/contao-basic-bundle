<?php

declare(strict_types=1);

namespace Neo\BasicBundle\EventListener;

use Contao\Controller;
use Contao\CoreBundle\Event\ContaoCoreEvents;
use Contao\CoreBundle\Event\MenuEvent;
use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\System;
use Knp\Menu\ItemInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;

/**
 * Breadcrumb of the modules in the menu group "neo" without clicks that stay on the page.
 *
 * Contao links the module name to the list of the current table. Two cases lead nowhere:
 *
 * - A further table of a module (texts, scan results, log): its list is the page itself. The module
 *   name now leads to the module start, followed by the name of the button that opened the table.
 * - A table with only one record (DCA config "neoSingleRecord" => true), whose list opens the form of
 *   that record: on that form as module start the breadcrumb is the module name only, without a link;
 *   as a further table the module name leads to the module start and the form is named like its
 *   button. Child tables of the record keep Contao's items.
 *
 * Contao's own BackendBreadcrumbListener (priority 0) builds the items, this one runs after it. The
 * core is not changed; modules outside the group keep Contao's breadcrumb.
 */
#[AsEventListener(ContaoCoreEvents::BACKEND_MENU_BUILD, priority: -10)]
final class BreadcrumbListener
{
    /** DCA config key of a table with one record whose list opens the form (set by the extensions). */
    public const SINGLE_RECORD = 'neoSingleRecord';

    private const GROUP = 'neo';

    private const TABLE_ITEM = 'neo_table';

    public function __construct(
        private readonly RequestStack $requestStack,
        private readonly RouterInterface $router,
        private readonly ContaoFramework $framework,
    ) {
    }

    public function __invoke(MenuEvent $event): void
    {
        $tree = $event->getTree();
        $request = $this->requestStack->getCurrentRequest();

        if ('breadcrumbMenu' !== $tree->getName() || null === $request) {
            return;
        }

        $do = (string) $request->query->get('do');
        $tables = array_values((array) ($GLOBALS['BE_MOD'][self::GROUP][$do]['tables'] ?? []));
        $module = $tree->getChild('current_0');

        if ([] === $tables || null === $module) {
            return;
        }

        $start = (string) $tables[0];
        $table = (string) $request->query->get('table') ?: $start;

        if (!\in_array($table, $tables, true)) {
            return;
        }

        $root = $this->rootTable($table, $tables);

        if ($root === $start) {
            if ($table === $start && $this->isSingleRecord($start)) {
                // The module start is the form of its only record, the page shown now (not one of its child tables).
                $module->setUri(null);

                foreach ($tree->getChildren() as $name => $child) {
                    if ($child !== $module) {
                        $tree->removeChild($name);
                    }
                }
            }

            return;
        }

        $module->setUri($this->router->generate('contao_backend', ['do' => $do]));
        $label = $this->buttonLabel($start, $root);

        if (null === $label) {
            return;
        }

        if ($this->isSingleRecord($root)) {
            // Its list is no page of its own: the form is the last item, named like the button.
            $children = $tree->getChildren();
            $last = end($children);

            if ($table === $root && $last instanceof ItemInterface && $last !== $module) {
                $last->setLabel($label);
            }

            return;
        }

        $tree->addChild(self::TABLE_ITEM, [
            'label' => $label,
            'uri' => $this->router->generate('contao_backend', ['do' => $do, 'table' => $root]),
        ]);

        $order = array_keys($tree->getChildren());
        $order = array_values(array_diff($order, [self::TABLE_ITEM]));
        array_splice($order, (int) array_search('current_0', $order, true) + 1, 0, [self::TABLE_ITEM]);
        $tree->reorderChildren($order);
    }

    /**
     * The table of the module the current one belongs to (via ptable), e.g. the texts for a text.
     *
     * @param list<string> $tables
     */
    private function rootTable(string $table, array $tables): string
    {
        $seen = [];

        while (!isset($seen[$table])) {
            $seen[$table] = true;
            $this->loadDca($table);
            $ptable = (string) ($GLOBALS['TL_DCA'][$table]['config']['ptable'] ?? '');

            if ('' === $ptable || !\in_array($ptable, $tables, true)) {
                break;
            }

            $table = $ptable;
        }

        return $table;
    }

    private function isSingleRecord(string $table): bool
    {
        $this->loadDca($table);

        return true === ($GLOBALS['TL_DCA'][$table]['config'][self::SINGLE_RECORD] ?? null);
    }

    /** Label of the button in the list of the module start that opens the table. */
    private function buttonLabel(string $start, string $table): string|null
    {
        $this->loadDca($start);
        $this->framework->getAdapter(System::class)->loadLanguageFile($start);

        foreach ((array) ($GLOBALS['TL_DCA'][$start]['list']['global_operations'] ?? []) as $name => $operation) {
            parse_str((string) ($operation['href'] ?? ''), $query);

            if (($query['table'] ?? null) !== $table) {
                continue;
            }

            $label = $operation['label'] ?? $GLOBALS['TL_LANG'][$start][$name] ?? null;
            $label = trim((string) (\is_array($label) ? ($label[0] ?? '') : $label));

            return '' === $label ? null : $label;
        }

        return null;
    }

    private function loadDca(string $table): void
    {
        $this->framework->getAdapter(Controller::class)->loadDataContainer($table);
    }
}
