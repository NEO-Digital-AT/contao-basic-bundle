<?php

declare(strict_types=1);

namespace Neo\BasicBundle\Page;

use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\PageModel;
use Doctrine\DBAL\Connection;

/**
 * The language version of a page in the website a visitor is on.
 *
 * Websites in several languages (one root page per language) link the pages of
 * their languages with terminal42/contao-changelanguage: every page of another
 * language names its page of the fallback language in "languageMain". A setting
 * that holds one page (privacy policy, accessibility statement, …) then works
 * for every language: this class returns the page linked to it in the root of
 * the current page.
 *
 * Without that extension, without a linked page in that root, or for a page
 * that already belongs to the root, the page itself is returned.
 */
final class LanguageVersion
{
    private bool|null $linked = null;

    public function __construct(
        private readonly Connection $db,
        private readonly ContaoFramework $framework,
    ) {
    }

    /** ID of the page linked to $pageId in the website with the root page $rootId; $pageId otherwise. */
    public function in(int $pageId, int $rootId): int
    {
        if ($pageId <= 0 || $rootId <= 0 || !$this->languagesLinked()) {
            return $pageId;
        }

        $pages = $this->framework->getAdapter(PageModel::class);
        $page = $pages->findWithDetails($pageId);

        if (null === $page || (int) $page->rootId === $rootId) {
            return $pageId;
        }

        // The page of the fallback language is the main page of the group.
        $main = (int) $page->languageMain ?: $pageId;

        foreach ($this->db->fetchFirstColumn('SELECT id FROM tl_page WHERE id = ? OR languageMain = ?', [$main, $main]) as $id) {
            $candidate = $pages->findWithDetails((int) $id);

            if (null !== $candidate && (int) $candidate->rootId === $rootId) {
                return (int) $candidate->id;
            }
        }

        return $pageId;
    }

    /** Whether tl_page has the column of terminal42/contao-changelanguage. */
    private function languagesLinked(): bool
    {
        if (null === $this->linked) {
            try {
                $this->linked = isset($this->db->createSchemaManager()->listTableColumns('tl_page')['languagemain']);
            } catch (\Throwable) {
                $this->linked = false;
            }
        }

        return $this->linked;
    }
}
