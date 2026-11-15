<?php

declare(strict_types=1);

namespace Neo\BasicBundle\Dock;

use Contao\PageModel;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception as DbalException;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Buttons at the edge of the screen (dock), shared by the extensions: the
 * button to reopen the cookie settings, the button of the accessibility
 * panel, the button of an AI chat … Each extension keeps its button, its
 * panel and its own setting for the corner; public/dock.js arranges the
 * buttons that share a corner as set in the page layout (field neo_dock): one
 * settings button with a menu (default), stacked one above the other, or
 * side by side.
 *
 * Usage in an extension: register() where its button is rendered, and the
 * data-neo-dock attributes on the button (docs/en/developers.md, "Buttons at
 * the edge of the screen"). DockPalette::apply() in its contao/dca/tl_layout.php
 * shows the setting in the page layout.
 */
final class Dock
{
    public const ARRANGEMENTS = ['menu', 'vertical', 'horizontal'];

    public const DEFAULT_ARRANGEMENT = 'menu';

    public const SCRIPT = 'bundles/neobasic/dock.js';

    public const STYLE = 'bundles/neobasic/dock.css';

    /** Request attribute: the configuration once register() ran (read by DockResponseListener). */
    public const ATTRIBUTE = '_neo_dock';

    public function __construct(
        private readonly Connection $connection,
        private readonly RequestStack $requestStack,
        private readonly TranslatorInterface $translator,
    ) {
    }

    /**
     * Adds the script, its stylesheet and the setting of the page layout to the
     * page (with the other page assets, so "Combine scripts" takes them in).
     * Calling it again on the same page changes nothing. Code that writes its
     * button after the page was generated (an event listener of the response)
     * calls it as well: the files are then added to the finished page.
     */
    public function register(PageModel|null $page = null): void
    {
        $request = $this->requestStack->getMainRequest();
        $page ??= $request?->attributes->get('pageModel');
        $page = $page instanceof PageModel ? $page : null;

        $config = [
            'arrangement' => $this->arrangement($page),
            'label' => $this->label($page),
        ];

        $GLOBALS['TL_HEAD']['neo_dock'] = self::meta($config);
        $GLOBALS['TL_CSS']['neo_dock'] = self::STYLE.'|all|static'.self::version('dock.css');
        $GLOBALS['TL_JAVASCRIPT']['neo_dock'] = self::SCRIPT.'|defer|static'.self::version('dock.js');

        $request?->attributes->set(self::ATTRIBUTE, $config);
    }

    /**
     * Arrangement of several buttons in one corner, from the page layout of the page.
     */
    public function arrangement(PageModel|null $page): string
    {
        $layout = (int) ($page?->layoutId ?: $page?->layout ?: 0);

        if ($layout < 1) {
            return self::DEFAULT_ARRANGEMENT;
        }

        try {
            $value = $this->connection->fetchOne('SELECT neo_dock FROM tl_layout WHERE id = ?', [$layout]);
        } catch (DbalException) {
            // The database has not been updated yet.
            return self::DEFAULT_ARRANGEMENT;
        }

        return \in_array($value, self::ARRANGEMENTS, true) ? $value : self::DEFAULT_ARRANGEMENT;
    }

    /**
     * Name of the settings button in the language of the page (MSC.neo_dock_settings).
     */
    public function label(PageModel|null $page): string
    {
        $language = (string) ($page?->language ?: '');
        $label = $this->translator->trans('MSC.neo_dock_settings', [], 'contao_default', '' !== $language ? $language : null);

        return '' !== $label && 'MSC.neo_dock_settings' !== $label ? $label : 'Settings';
    }

    /**
     * The tag that hands the configuration to the script.
     *
     * @param array{arrangement: string, label: string} $config
     */
    public static function meta(array $config): string
    {
        return sprintf(
            '<meta name="neo-dock" content="%s" data-neo-dock-label="%s">',
            htmlspecialchars($config['arrangement'], ENT_QUOTES | ENT_HTML5),
            htmlspecialchars($config['label'], ENT_QUOTES | ENT_HTML5),
        );
    }

    /**
     * File time as the version of a file in public/, so a new file is not taken from the cache.
     */
    public static function version(string $file): string
    {
        $path = \dirname(__DIR__, 2).'/public/'.$file;

        return is_file($path) ? '|'.filemtime($path) : '';
    }
}
