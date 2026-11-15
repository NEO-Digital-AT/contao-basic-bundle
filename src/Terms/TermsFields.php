<?php

declare(strict_types=1);

namespace Neo\BasicBundle\Terms;

use Contao\BackendUser;
use Contao\Config;
use Contao\DataContainer;
use Contao\Date;
use Contao\Message;
use Contao\StringUtil;
use Neo\BasicBundle\Backend\RequestInput;
use Neo\BasicBundle\License\LicenseService;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Section "Terms of the extensions" on "Access keys and licences": per extension
 * the passages that apply to its current edition, each with its version and
 * text, the state of the confirmation and, while something is open, a checkbox
 * and the button "Confirm".
 *
 * The texts come from the extension's translation domain (keys
 * "<passage>.title" and "<passage>.text", paragraphs separated by a blank line)
 * in the language of the back end; the German text is binding and shown next
 * to a translation.
 *
 * Extensions call AccessPage::addTerms() in their contao/dca/tl_neo_access.php.
 */
final class TermsFields
{
    /**
     * Language of the binding text.
     */
    public const BINDING_LOCALE = 'de';

    public function __construct(
        private readonly TermsService $terms,
        private readonly LicenseService $licenses,
        private readonly TranslatorInterface $translator,
        private readonly RequestStack $requestStack,
        private readonly Security $security,
    ) {
    }

    /**
     * @param array<string, int> $passages passage => version, in order (see TermsService)
     */
    public static function addTo(string $table, string $module, string $extension, string $domain, array $passages): void
    {
        $dca = &$GLOBALS['TL_DCA'][$table];
        $dca['config']['neo_terms'][$module] = ['name' => $extension, 'domain' => $domain, 'passages' => $passages];
        $dca['fields']['terms_'.$module] = ['input_field_callback' => [self::class, 'render']];

        $callback = [self::class, 'confirm'];

        if (!\in_array($callback, $dca['config']['onsubmit_callback'] ?? [], true)) {
            $dca['config']['onsubmit_callback'][] = $callback;
        }
    }

    public function render(DataContainer $dc): string
    {
        $lang = $GLOBALS['TL_LANG'][$dc->table];
        $module = substr((string) $dc->field, \strlen('terms_'));
        $config = $this->config($dc->table, $module);
        $edition = $this->licenses->state($module, '')->edition();
        $locale = $this->locale();
        $translated = self::BINDING_LOCALE !== $locale;

        $open = [];
        $renewed = false;
        $latest = null;
        $items = '';

        foreach (TermsService::required($config['passages'], $edition) as $passage => $version) {
            $acceptance = $this->terms->acceptance($module, $passage);
            $accepted = null !== $acceptance && $acceptance['version'] >= $version;
            $badge = '';

            if ($accepted) {
                $latest = null === $latest || $acceptance['time'] > $latest['time'] ? $acceptance : $latest;
            } else {
                $open[$passage] = $version;
                $renewed = $renewed || null !== $acceptance;
                $badge = ' <strong>'.StringUtil::specialchars($lang[null !== $acceptance ? 'terms_badge_changed' : 'terms_badge_new'] ?? '').'</strong>';
            }

            $german = '';

            if ($translated) {
                $german = \sprintf(
                    '<details style="margin:6px 0 0 18px"><summary>%s</summary><div lang="de" style="margin:6px 0 0 18px">%s</div></details>',
                    StringUtil::specialchars($lang['terms_show_german'] ?? ''),
                    $this->paragraphs($config['domain'], $passage, self::BINDING_LOCALE),
                );
            }

            $items .= \sprintf(
                '<details style="margin:8px 0"><summary><strong>%s</strong> (%s)%s · %s</summary><div style="margin:6px 0 0 18px">%s</div>%s</details>',
                StringUtil::specialchars($this->translator->trans($passage.'.title', [], $config['domain'], $locale)),
                StringUtil::specialchars(\sprintf($lang['terms_version'] ?? '%d', $version)),
                $badge,
                StringUtil::specialchars($lang['terms_show'] ?? ''),
                $this->paragraphs($config['domain'], $passage, $locale),
                $german,
            );
        }

        $editionLabel = $GLOBALS['TL_LANG']['neo_license']['editions'][$edition] ?? $edition;

        $heading = '' === $edition
            ? \sprintf($lang['terms_heading_free'] ?? '%s', $config['name'])
            : \sprintf($lang['terms_heading'] ?? '%s · %s', $config['name'], $editionLabel);

        if ([] === $open) {
            $state = \sprintf(
                '<p class="tl_confirm">%s</p>',
                StringUtil::specialchars(\sprintf(
                    $lang['terms_done'] ?? '%s %s',
                    $latest['username'] ?? '',
                    $this->date($latest['time'] ?? 0),
                )),
            );
        } else {
            $state = \sprintf('<p class="tl_error">%s</p>', StringUtil::specialchars($lang[$renewed ? 'terms_renew' : 'terms_open'] ?? ''));
        }

        $html = '<div class="widget clr"><h3>'.StringUtil::specialchars($heading).'</h3><div class="tl_message">'.$state.'</div>';

        if ($translated) {
            $html .= \sprintf('<p class="tl_help tl_tip">%s</p>', StringUtil::specialchars($lang['terms_binding'] ?? ''));
        }

        $html .= $items;
        $html .= \sprintf('<p class="tl_help tl_tip">%s</p>', StringUtil::specialchars($lang['terms_full'] ?? ''));

        if ([] !== $open) {
            $id = 'neo_terms_ok_'.$module;

            $html .= \sprintf(
                '<div class="tl_checkbox_single_container" style="margin-top:12px"><input type="checkbox" name="%1$s" id="%1$s" class="tl_checkbox" value="1" style="margin-right:8px"><label for="%1$s">%2$s</label></div>'
                .'<p style="margin-top:10px"><button type="submit" name="neo_terms_accept" value="%3$s" class="tl_submit" data-neo-action>%4$s</button></p>',
                $id,
                StringUtil::specialchars($lang['terms_check'] ?? ''),
                StringUtil::specialchars($module),
                StringUtil::specialchars($lang['terms_accept'] ?? ''),
            );
        }

        return $html.'</div>';
    }

    /**
     * onsubmit_callback of the page: records the confirmation when the button
     * "Confirm" of an extension was pressed with its checkbox set.
     */
    public function confirm(DataContainer $dc): void
    {
        // From the request object; Input::post() is deprecated in Contao 6.
        $input = new RequestInput($this->requestStack);
        $module = (string) $input->post('neo_terms_accept');
        $config = $GLOBALS['TL_DCA'][$dc->table]['config']['neo_terms'][$module] ?? null;

        // Only extensions that added terms, never a module code from the form alone.
        if ('' === $module || null === $config) {
            return;
        }

        $lang = $GLOBALS['TL_LANG'][$dc->table];

        if ('1' !== $input->post('neo_terms_ok_'.$module)) {
            Message::addError($lang['terms_check_missing'] ?? '');

            return;
        }

        $user = $this->security->getUser();

        if (!$user instanceof BackendUser) {
            return;
        }

        $edition = $this->licenses->state($module, '')->edition();

        $open = array_filter(
            TermsService::required($config['passages'], $edition),
            fn (int $version, string $passage): bool => !$this->terms->isAccepted($module, $passage, $version),
            ARRAY_FILTER_USE_BOTH,
        );

        if ([] !== $open) {
            $this->terms->accept($module, $open, (int) $user->id, (string) $user->username, $this->locale());
        }

        Message::addConfirmation(\sprintf($lang['terms_accepted'] ?? '%s', $config['name']));
    }

    /**
     * Text of a passage as paragraphs; "Label: text" gets a bold label.
     */
    private function paragraphs(string $domain, string $passage, string $locale): string
    {
        $text = $this->translator->trans($passage.'.text', [], $domain, $locale);
        $html = '';

        foreach (preg_split('/\R\s*\R/', trim($text)) ?: [] as $paragraph) {
            $paragraph = trim($paragraph);

            if (preg_match('/^([^:.]{2,48}):\s(.+)$/su', $paragraph, $m)) {
                $html .= '<p><strong>'.StringUtil::specialchars($m[1]).':</strong> '.StringUtil::specialchars($m[2]).'</p>';
            } elseif ('' !== $paragraph) {
                $html .= '<p>'.StringUtil::specialchars($paragraph).'</p>';
            }
        }

        return $html;
    }

    /**
     * Primary code of the back-end language ("de", "en" …).
     */
    private function locale(): string
    {
        $locale = $this->requestStack->getCurrentRequest()?->getLocale() ?? ($GLOBALS['TL_LANGUAGE'] ?? 'en');

        return strtolower((string) strtok(str_replace('-', '_', (string) $locale), '_')) ?: 'en';
    }

    /**
     * @return array{name: string, domain: string, passages: array<string, int>}
     */
    private function config(string $table, string $module): array
    {
        return $GLOBALS['TL_DCA'][$table]['config']['neo_terms'][$module] ?? ['name' => $module, 'domain' => '', 'passages' => []];
    }

    private function date(int $timestamp): string
    {
        return Date::parse((string) Config::get('datimFormat'), $timestamp);
    }
}
