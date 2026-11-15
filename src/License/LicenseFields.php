<?php

declare(strict_types=1);

namespace Neo\BasicBundle\License;

use Contao\Config;
use Contao\DataContainer;
use Contao\Date;
use Contao\StringUtil;
use Contao\System;
use Neo\BasicBundle\Access\AccessPage;
use Neo\BasicBundle\Backend\AccessActions;
use Neo\BasicBundle\Backend\RequestInput;

/**
 * The licence rows on "Access keys and licences": per paid extension the key
 * field and, next to it, a compact status (edition, valid until, domains, last
 * check) with "Check now". Key and certificate are stored by LicenseService
 * (tl_neo_license), not in the page's own table.
 *
 * Extensions call AccessPage::addLicense() in their contao/dca/tl_neo_access.php.
 * The status line ends with a link to the page of the extension in the store
 * (Store), where its editions are compared and sold.
 */
final class LicenseFields
{
    public const OPERATION = 'neo_license_refresh';

    /**
     * Paid until this date or later: a licence without time limit ("unlimited").
     */
    private const UNLIMITED_FROM = 32503680000; // 3000-01-01

    public function __construct(
        private readonly LicenseService $licenses,
        private readonly AccessActions $actions,
        private readonly RequestInput|null $input = null,
    ) {
    }

    public static function addTo(string $table, string $module, string $package, string $extension): void
    {
        System::loadLanguageFile('neo_license');

        $dca = &$GLOBALS['TL_DCA'][$table];
        $dca['config']['neo_license'][$module] = ['package' => $package, 'name' => $extension];

        // One sentence for all key fields at the start of the section.
        $dca['fields']['neo_license_intro'] = [
            'input_field_callback' => [self::class, 'intro'],
        ];

        $dca['fields']['license_'.$module.'_status'] = [
            'input_field_callback' => [self::class, 'status'],
        ];

        // No database column: load and save go through LicenseService. An empty
        // return value with doNotSaveEmpty keeps Contao from writing the field.
        $dca['fields']['license_'.$module.'_key'] = [
            'label' => [
                $extension.': '.($GLOBALS['TL_LANG']['neo_license']['key'][0] ?? 'Licence key'),
                '',
            ],
            'inputType' => 'text',
            'eval' => ['maxlength' => 64, 'doNotSaveEmpty' => true, 'doNotCopy' => true, 'decodeEntities' => true, 'tl_class' => 'w50 clr'],
            'load_callback' => [[self::class, 'loadKey']],
            'save_callback' => [[self::class, 'saveKey']],
        ];

        $GLOBALS['TL_LANG'][$table]['neo_license_legend'] = &$GLOBALS['TL_LANG']['neo_license']['legend'];
    }

    public function loadKey(mixed $value, DataContainer $dc): string
    {
        return $this->licenses->keyHint($this->module($dc));
    }

    /**
     * Activates a new key, removes the licence when the field is emptied, keeps it when the shortened key is unchanged.
     */
    public function saveKey(mixed $value, DataContainer $dc): string
    {
        $module = $this->module($dc);
        $value = trim(html_entity_decode((string) $value, ENT_QUOTES | ENT_HTML5, 'UTF-8'));

        if ('' !== $value && $value === $this->licenses->keyHint($module)) {
            return '';
        }

        $state = $this->licenses->saveKey($module, $this->config($dc->table, $module)['package'], $value);

        if (LicenseState::INVALID_KEY === $state->reason) {
            throw new \RuntimeException($GLOBALS['TL_LANG']['neo_license']['reason'][LicenseState::INVALID_KEY] ?? 'Invalid licence key.');
        }

        return '';
    }

    /**
     * Where the key is and how a licence is removed, once for all rows.
     */
    public function intro(): string
    {
        return \sprintf('<div class="widget clr"><p class="tl_help">%s</p></div>', StringUtil::specialchars($GLOBALS['TL_LANG']['neo_license']['intro'] ?? ''));
    }

    /**
     * Status of one extension's licence next to its key field: what it covers, when it was checked, "Check now".
     */
    public function status(DataContainer $dc): string
    {
        $module = $this->module($dc);
        $state = $this->licenses->state($module, '');
        $target = self::target($module);

        // Without a key there is nothing to check: the button appears once a key is stored, on the line of the
        // key field with the status right next to it, so the answer appears where the click was.
        $button = '';

        if ('' !== $state->keyHint) {
            $lang = $GLOBALS['TL_LANG']['neo_license'];
            $button = $this->actions->button(self::OPERATION, $target, $lang['refresh'] ?? 'Check now', $lang['refreshing'] ?? 'Checking …', ['module' => $module]);
        }

        // The empty label line puts the row on the line of the key field (public/access.css).
        return \sprintf(
            '<div class="widget w50 neo-license-status" id="%s"><h3 aria-hidden="true">&nbsp;</h3><div class="neo-license-status__row">%s<div data-neo-access-result role="status"><div>%s</div></div></div></div>',
            StringUtil::specialchars($target),
            $button,
            $this->statusLines($module, $this->config($dc->table, $module)['package']),
        );
    }

    /**
     * Back-end operation "Check now" (key=neo_license_refresh&module=…): asks the
     * licence service and answers with the new status of the row, in place or
     * by a round trip to the row (AccessActions).
     */
    public function refreshOperation(DataContainer $dc): never
    {
        System::loadLanguageFile(AccessPage::TABLE);

        // From the request object; Input::get() is deprecated in Contao 6.
        $module = (string) ($this->input ?? new RequestInput(System::getContainer()->get('request_stack')))->query('module');
        $target = self::target($module);

        $this->actions->requireToken($target, $GLOBALS['TL_LANG'][AccessPage::TABLE]['action_invalid_token'] ?? '');

        // Only the extensions that added a licence row, never a module code from the address alone.
        if (!isset($GLOBALS['TL_DCA'][$dc->table]['config']['neo_license'][$module])) {
            $this->actions->respond($target, '', false);
        }

        $state = $this->licenses->refresh($module);

        $this->actions->respond($target, $this->statusLines($module, $this->config($dc->table, $module)['package']), $state->isLicensed() && '' === $state->lastError);
    }

    /**
     * Id of the row's block on the page, also the anchor after a round trip.
     */
    public static function target(string $module): string
    {
        return 'neo_license_'.preg_replace('/[^a-z0-9_]/', '', strtolower($module));
    }

    /**
     * The status lines of a licence: state, domains not covered, a failed check, the time of the last check.
     */
    private function statusLines(string $module, string $package): string
    {
        $lang = $GLOBALS['TL_LANG']['neo_license'];

        // The back end may run under another address than the website: the domain
        // check uses the domains reported to the licence service (see below).
        $state = $this->licenses->state($module, '');
        $certificate = $state->certificate;

        $class = match ($state->reason) {
            LicenseState::OK => 'tl_confirm',
            LicenseState::NO_KEY => 'tl_info',
            default => 'tl_error',
        };

        $text = match ($state->reason) {
            LicenseState::OK => \sprintf(
                $this->okText($certificate),
                $lang['editions'][$state->edition()] ?? $state->edition(),
                $this->date($certificate->paidUntil),
                implode(', ', $certificate->domains),
                $this->date($certificate->graceUntil),
            ),
            LicenseState::DOMAIN => \sprintf($lang['reason'][LicenseState::DOMAIN], implode(', ', $certificate->domains ?? [])),
            LicenseState::NO_KEY => $lang['reason'][LicenseState::NO_KEY],
            default => $lang['reason'][$state->reason] ?? $state->reason,
        };

        // A licence that covers none of the reported domains (e.g. all installations taken) is no success.
        if (LicenseState::OK === $state->reason && [] !== $state->coverage && !\in_array(true, $state->coverage, true)) {
            $class = 'tl_error';
        }

        // One span: the line is a flex box (public/access.css), text and link stay one run.
        $lines = [\sprintf('<p class="%s"><span>%s %s</span></p>', $class, StringUtil::specialchars($text), $this->storeLink($package, $state->isLicensed()))];

        if (\in_array(LicenseState::INSTALLATIONS_EXHAUSTED, $state->reasons, true)) {
            // All installations of the licence are taken: only the manufacturer can raise the number.
            $lines[] = $this->installationsExhaustedLine();
        }

        if ([] !== $state->reasons) {
            // The licence service says per domain whether it is covered and why not.
            $lines[] = $this->domainLines($state);
        } else {
            $uncovered = array_keys(array_filter($state->coverage, static fn (bool $covered): bool => !$covered));

            if ($state->isLicensed() && [] !== $uncovered) {
                $lines[] = \sprintf('<p class="tl_info">%s</p>', StringUtil::specialchars(\sprintf($lang['uncovered'], implode(', ', $uncovered))));
            }
        }

        $allotment = $this->allotmentLine($state);

        if ('' !== $allotment) {
            $lines[] = $allotment;
        }

        if ('' !== $state->lastError && LicenseState::INVALID_KEY !== $state->reason) {
            // Technical details go to the system log; the editor gets a sentence.
            $lines[] = \sprintf('<p class="tl_error">%s</p>', StringUtil::specialchars($lang['service_unreachable']));
        }

        $html = '<div class="tl_message">'.implode('', $lines).'</div>';

        if ($state->checkedAt > 0) {
            $html .= \sprintf('<p class="tl_help">%s</p>', StringUtil::specialchars(\sprintf($lang['checked_at'], $this->date($state->checkedAt, true))));
        }

        return $html;
    }

    /**
     * Red warning: the licence cannot be activated here, all its installations are in use;
     * whom to contact to raise the number (new tab, the form stays as it is).
     */
    private function installationsExhaustedLine(): string
    {
        $text = $GLOBALS['TL_LANG']['neo_license']['installations_exhausted'] ?? 'Cannot be activated: the number of installations of this licence has been exceeded. Contact %s to raise the number of installations.';
        $link = \sprintf('<a href="%s" target="_blank" rel="noopener">%s</a>', StringUtil::specialchars(Store::CONTACT_URL), StringUtil::specialchars(Store::CONTACT_NAME));

        return \sprintf('<p class="tl_error neo-license-exhausted" role="alert"><span>%s</span></p>', \sprintf(StringUtil::specialchars($text), $link));
    }

    /**
     * One line per reported domain: "valid" or why the licence does not cover it.
     */
    private function domainLines(LicenseState $state): string
    {
        $lang = $GLOBALS['TL_LANG']['neo_license'];
        $items = '';
        $uncovered = false;

        foreach ($state->reasons as $domain => $reason) {
            $uncovered = $uncovered || null !== $reason;
            $text = null === $reason ? ($lang['domain_valid'] ?? 'valid') : ($lang['domain_reason'][$reason] ?? $lang['domain_reason']['other'] ?? $reason);

            $items .= \sprintf('<li><strong>%s</strong>: %s</li>', StringUtil::specialchars((string) $domain), StringUtil::specialchars($text));
        }

        return \sprintf('<div class="%s neo-license-domains"><ul>%s</ul></div>', $uncovered ? 'tl_info' : 'tl_confirm', $items);
    }

    /**
     * "Installations: m of n in use · up to n domains per installation", when the licence service says so.
     */
    private function allotmentLine(LicenseState $state): string
    {
        $lang = $GLOBALS['TL_LANG']['neo_license'];
        $parts = [];

        if (null !== $state->installations) {
            $parts[] = \sprintf($lang['installations'] ?? 'Installations: %1$d of %2$d in use', $state->installations['used'], $state->installations['allowed']);
        }

        if (null !== $state->domainsPerInstallation) {
            $forms = (array) ($lang['domains_per_installation'] ?? ['up to %d domain per installation', 'up to %d domains per installation']);
            $parts[] = \sprintf((string) (1 === $state->domainsPerInstallation ? $forms[0] : ($forms[1] ?? $forms[0])), $state->domainsPerInstallation);
        }

        return [] !== $parts ? \sprintf('<p class="tl_help">%s</p>', StringUtil::specialchars(implode(' · ', $parts))) : '';
    }

    /**
     * Where to buy a key, or a higher edition and the renewal once licensed. A new tab
     * keeps the form of this page as it is.
     */
    private function storeLink(string $package, bool $licensed): string
    {
        $lang = $GLOBALS['TL_LANG']['neo_license'];

        return \sprintf(
            '<a href="%s" target="_blank" rel="noopener">%s</a>',
            StringUtil::specialchars(Store::url($package)),
            StringUtil::specialchars($licensed ? ($lang['store_manage'] ?? 'Higher edition or renewal in the store') : ($lang['store_buy'] ?? 'Buy a licence key in the store')),
        );
    }

    private function okText(Certificate $certificate): string
    {
        $reasons = $GLOBALS['TL_LANG']['neo_license']['reason'];

        if ('active' === $certificate->status && $certificate->paidUntil >= self::UNLIMITED_FROM) {
            return $reasons[LicenseState::OK.'_unlimited'] ?? $reasons[LicenseState::OK];
        }

        return $reasons[LicenseState::OK.'_'.$certificate->status] ?? $reasons[LicenseState::OK];
    }

    /**
     * Module code from the field name: license_<module>_status / license_<module>_key.
     */
    private function module(DataContainer $dc): string
    {
        return (string) preg_replace('/^license_(.+)_(status|key)$/', '$1', (string) $dc->field);
    }

    /**
     * @return array{package: string, name: string}
     */
    private function config(string $table, string $module): array
    {
        return $GLOBALS['TL_DCA'][$table]['config']['neo_license'][$module] ?? ['package' => '', 'name' => $module];
    }

    private function date(int $timestamp, bool $withTime = false): string
    {
        return Date::parse((string) Config::get($withTime ? 'datimFormat' : 'dateFormat'), $timestamp);
    }
}
