<?php

declare(strict_types=1);

namespace Neo\BasicBundle\Access;

use Contao\Backend;
use Contao\DataContainer;
use Contao\StringUtil;
use Contao\System;
use Doctrine\DBAL\Connection;
use Neo\BasicBundle\Ai\AiConnection;
use Neo\BasicBundle\Backend\AccessActions;
use Neo\BasicBundle\Backend\AiTestOperation;
use Neo\BasicBundle\Backend\AzureTestOperation;
use Neo\BasicBundle\Backend\DeepLTestOperation;
use Neo\BasicBundle\Backend\RequestInput;
use Neo\BasicBundle\License\LicenseService;
use Neo\BasicBundle\Security\SecretCipher;
use Neo\BasicBundle\Terms\TermsService;
use Neo\BasicBundle\Translation\AzureConnection;
use Neo\BasicBundle\Translation\DeepLConnection;

/**
 * DCA callbacks of "Access keys and licences" (tl_neo_access).
 */
final class AccessCallbacks
{
    private const MASK = '••••••••';

    private const COMPLETE = 'complete';

    private const OPTIONAL = 'optional';

    private const MISSING = 'missing';

    public function __construct(
        private readonly AccessSettings $settings,
        private readonly AiConnection $connection,
        private readonly SecretCipher $cipher,
        private readonly AzureConnection $azure,
        private readonly Connection $db,
        private readonly DeepLConnection $deepl,
        private readonly AccessActions $actions,
        private readonly LicenseService $licenses,
        private readonly TermsService $terms,
        private readonly RequestInput|null $input = null,
    ) {
    }

    /**
     * One record only: create it and open the edit mask instead of a list.
     */
    public function openRecord(): void
    {
        // No installed extension adds anything: the menu has no entry, and the
        // address alone leads to the start page instead of an empty form.
        if (!AccessPage::hasSections()) {
            Backend::redirect(System::getContainer()->get('router')->generate('contao_backend'));
        }

        $id = $this->settings->ensureRow();

        if ($this->query('act') || $this->query('key')) {
            return;
        }

        // nb=1: without a list there is nowhere to go back to; Contao then shows no "Go back".
        Backend::redirect(Backend::addToUrl('act=edit&id='.$id.'&nb=1'));
    }

    /**
     * Only "Save": "Save and close" would lead to the list, which opens this form again.
     *
     * @param array<string, string> $buttons
     *
     * @return array<string, string>
     */
    public function editButtons(array $buttons): array
    {
        unset($buttons['saveNclose']);

        return $buttons;
    }

    /**
     * A mask for a stored secret, never the secret itself.
     */
    public function maskSecret(mixed $value): string
    {
        return '' !== (string) $value ? self::MASK : '';
    }

    /**
     * An empty field or the unchanged mask keeps the stored secret; a new one is stored encrypted, exactly as typed.
     */
    public function saveSecret(mixed $value, DataContainer $dc): string
    {
        $value = trim(html_entity_decode(str_replace(self::MASK, '', (string) $value), ENT_QUOTES | ENT_HTML5, 'UTF-8'));

        if ('' === $value) {
            // quoteSingleIdentifier() of the platform: DBAL 3 (Contao 5.3 to 5.6) and DBAL 4 have it;
            // Connection::quoteIdentifier() is deprecated since DBAL 4.3 (Contao 6.0).
            $column = $this->db->getDatabasePlatform()->quoteSingleIdentifier((string) $dc->field);

            return (string) $this->db->fetchOne('SELECT '.$column.' FROM '.AccessPage::TABLE.' WHERE id = ?', [(int) $dc->id]);
        }

        return $this->cipher->encrypt($value);
    }

    /**
     * @return list<string>
     */
    public function routeOptions(): array
    {
        return array_keys(AiConnection::ROUTES);
    }

    /**
     * Status line of the AI section with the address in use, the extensions
     * that use it and the button "Test connection" (result in place).
     */
    public function aiStatus(): string
    {
        $lang = $GLOBALS['TL_LANG'][AccessPage::TABLE];
        $baseUrl = $this->connection->baseUrl();

        if ($this->connection->hasKey() && '' !== $baseUrl) {
            $line = $this->line('tl_confirm', \sprintf($lang['ai_ready'] ?? '%s', $baseUrl));
        } elseif ($this->connection->hasKey()) {
            $line = $this->line('tl_error', $lang['ai_no_url'] ?? '');
        } else {
            $line = $this->line('tl_error', $lang['ai_no_key'] ?? '');
        }

        return $this->statusBlock(AiTestOperation::TARGET, $line, 'ai', $lang['ai_model_note'] ?? '', AiTestOperation::KEY, $lang['ai_test'][0] ?? 'Test connection', $this->connection->isConfigured());
    }

    /**
     * @return list<string>
     */
    public function azureLocationOptions(): array
    {
        return [...array_keys(AzureConnection::LOCATIONS), AzureConnection::CUSTOM];
    }

    /**
     * Status line of the Azure Translator section with the address in use and
     * the button "Test connection". Without a key it is a note: translations
     * are only one function of the extensions that use it.
     */
    public function azureTranslatorStatus(): string
    {
        $lang = $GLOBALS['TL_LANG'][AccessPage::TABLE];
        $baseUrl = $this->azure->baseUrl();

        if ($this->azure->hasKey() && '' !== $baseUrl) {
            $line = $this->line('tl_confirm', \sprintf($lang['azure_translator_ready'] ?? '%s', $baseUrl));
        } elseif ($this->azure->hasKey()) {
            $line = $this->line('tl_error', $lang['azure_translator_no_url'] ?? '');
        } else {
            $line = $this->line('tl_info', $lang['azure_translator_optional'] ?? '');
        }

        return $this->statusBlock(AzureTestOperation::TARGET, $line, 'azure_translator', '', AzureTestOperation::KEY, $lang['azure_translator_test'][0] ?? 'Test connection', $this->azure->isConfigured());
    }

    /**
     * Status line of the DeepL section: which interface the key belongs to
     * (Free or Pro, from the key), and the button "Test connection". Without a
     * key it is a note, as for Azure Translator.
     */
    public function deeplStatus(): string
    {
        $lang = $GLOBALS['TL_LANG'][AccessPage::TABLE];

        if ($this->deepl->hasKey()) {
            $plan = $lang['deepl_plans'][$this->deepl->plan()] ?? $this->deepl->plan();
            $line = $this->line('tl_confirm', \sprintf($lang['deepl_ready'] ?? '%s %s', $plan, $this->deepl->baseUrl()));
        } else {
            $line = $this->line('tl_info', $lang['deepl_optional'] ?? '');
        }

        return $this->statusBlock(DeepLTestOperation::TARGET, $line, 'deepl', $lang['deepl_plan_note'] ?? '', DeepLTestOperation::KEY, $lang['deepl_test'][0] ?? 'Test connection', $this->deepl->isConfigured());
    }

    /**
     * Status line of a section with secrets: which of its keys are missing, and the extensions that use it.
     */
    public function sectionStatus(DataContainer $dc): string
    {
        $lang = $GLOBALS['TL_LANG'][AccessPage::TABLE];
        $legend = substr((string) $dc->field, 0, -\strlen('_status')).'_legend';
        $state = $this->sectionState($legend);

        $line = match ($state) {
            self::COMPLETE => $this->line('tl_confirm', $lang['section_ready'] ?? ''),
            self::OPTIONAL => $this->line('tl_info', $lang[substr($legend, 0, -\strlen('_legend')).'_optional'] ?? $lang['section_optional'] ?? ''),
            default => $this->line('tl_error', $lang['section_missing'] ?? ''),
        };

        return '<div class="widget clr">'.$line.$this->usedBy($legend).'</div>';
    }

    /**
     * onload_callback: in the form, every section with nothing left to do starts
     * collapsed ({legend:hide}); sections with something missing stay open, the
     * licences always. A section the user opened or closed keeps that state
     * (Contao remembers it in the session).
     */
    public function collapseComplete(): void
    {
        if ('edit' !== $this->query('act') || $this->query('key')) {
            return;
        }

        $config = $GLOBALS['TL_DCA'][AccessPage::TABLE]['config']['neo_access'] ?? [];

        $done = [
            AccessPage::AI_LEGEND => $this->connection->isConfigured(),
            // Optional: collapsed while empty, open while half filled in.
            AccessPage::AZURE_TRANSLATOR_LEGEND => !$this->azure->hasKey() || '' !== $this->azure->baseUrl(),
            AccessPage::DEEPL_LEGEND => true,
            AccessPage::TERMS_LEGEND => $this->termsDone(),
        ];

        foreach (array_keys($config['sections'] ?? []) as $legend) {
            $done[$legend] = self::MISSING !== $this->sectionState($legend);
        }

        $palette = &$GLOBALS['TL_DCA'][AccessPage::TABLE]['palettes']['default'];

        foreach ($done as $legend => $isDone) {
            if ($isDone) {
                $palette = str_replace('{'.$legend.'}', '{'.$legend.':hide}', (string) $palette);
            }
        }
    }

    /**
     * One block with status line, the extensions that use the part, a note,
     * the button of the test and the place of its result. Without what the
     * test needs, the button is disabled with the reason next to it.
     */
    private function statusBlock(string $target, string $line, string $part, string $note, string $operation, string $label, bool $testable): string
    {
        $lang = $GLOBALS['TL_LANG'][AccessPage::TABLE];

        return \sprintf(
            '<div class="widget clr" id="%s">%s%s%s<p>%s</p><div data-neo-access-result role="status">%s</div></div>',
            StringUtil::specialchars($target),
            $line,
            $this->usedBy($part),
            '' !== $note ? '<p class="tl_help">'.StringUtil::specialchars($note).'</p>' : '',
            $this->actions->button($operation, $target, $label, $lang['action_testing'] ?? 'Testing …', [], $testable ? '' : ($lang['action_needs_access'] ?? '')),
            $this->actions->result($target),
        );
    }

    private function line(string $class, string $text): string
    {
        return \sprintf('<div class="tl_message"><p class="%s">%s</p></div>', $class, StringUtil::specialchars($text));
    }

    /**
     * "Used by …": the extensions that use a part of the page, named once.
     */
    private function usedBy(string $part): string
    {
        $users = AccessPage::users($part);

        if ([] === $users) {
            return '';
        }

        return \sprintf('<p class="tl_help">%s</p>', StringUtil::specialchars(\sprintf($GLOBALS['TL_LANG'][AccessPage::TABLE]['used_by'] ?? '%s', implode(', ', $users))));
    }

    /**
     * COMPLETE: every key of the section is stored; OPTIONAL: an optional
     * section is empty; MISSING: something the extensions need is missing.
     */
    private function sectionState(string $legend): string
    {
        $section = $GLOBALS['TL_DCA'][AccessPage::TABLE]['config']['neo_access']['sections'][$legend] ?? [];
        $fields = $section['fields'] ?? [];
        $missing = array_filter($fields, fn (string $field): bool => !$this->settings->hasValue($field));

        if ([] === $missing) {
            return self::COMPLETE;
        }

        return !empty($section['optional']) && \count($missing) === \count($fields) ? self::OPTIONAL : self::MISSING;
    }

    /**
     * Whether every extension's terms are confirmed for its current edition.
     */
    private function termsDone(): bool
    {
        foreach ($GLOBALS['TL_DCA'][AccessPage::TABLE]['config']['neo_terms'] ?? [] as $module => $config) {
            if (!$this->terms->allAccepted((string) $module, $config['passages'], $this->licenses->state((string) $module, '')->edition())) {
                return false;
            }
        }

        return true;
    }

    /**
     * A query parameter as Input::get() returns it, from the request object
     * (Input::get() is deprecated in Contao 6). Without the injected helper (an
     * instance built by hand) it comes from the container's request stack.
     */
    private function query(string $key): array|string|null
    {
        return ($this->input ?? new RequestInput(System::getContainer()->get('request_stack')))->query($key);
    }
}
