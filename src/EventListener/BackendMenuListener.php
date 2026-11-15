<?php

declare(strict_types=1);

namespace Neo\BasicBundle\EventListener;

use Contao\CoreBundle\Event\ContaoCoreEvents;
use Contao\CoreBundle\Event\MenuEvent;
use Neo\BasicBundle\Access\AccessPage;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

/**
 * "Access keys and licences" is always the last entry of the menu group "neo",
 * whatever the order in which extensions register their modules, and it is
 * left out while no installed extension adds a section to it. Runs after
 * Contao's own listener that builds the main menu (priority 10).
 */
#[AsEventListener(ContaoCoreEvents::BACKEND_MENU_BUILD, priority: -10)]
final class BackendMenuListener
{
    public function __invoke(MenuEvent $event): void
    {
        $tree = $event->getTree();

        if ('mainMenu' !== $tree->getName()) {
            return;
        }

        $group = $tree->getChild('neo');
        $entry = $group?->getChild(AccessPage::MODULE);

        if (null === $group || null === $entry) {
            return;
        }

        $group->removeChild($entry);

        if (AccessPage::hasSections()) {
            $group->addChild($entry);
        } elseif (0 === \count($group->getChildren())) {
            // Only the bundle itself is installed: no empty group heading.
            $tree->removeChild($group);
        }
    }
}
