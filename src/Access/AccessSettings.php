<?php

declare(strict_types=1);

namespace Neo\BasicBundle\Access;

use Doctrine\DBAL\Connection;
use Neo\BasicBundle\Security\SecretCipher;

/**
 * Read and write access to the one record of "Access keys and licences"
 * (tl_neo_access). Secrets are stored encrypted with the scope of this bundle
 * and returned in plain text, exactly as typed.
 */
final class AccessSettings
{
    /**
     * @var array<string, mixed>|null
     */
    private array|null $row = null;

    public function __construct(
        private readonly Connection $db,
        private readonly SecretCipher $cipher,
    ) {
    }

    public function value(string $column): string
    {
        return trim((string) ($this->row()[$column] ?? ''));
    }

    /**
     * A stored secret in plain text, '' when none is stored or it cannot be decrypted.
     */
    public function secret(string $column): string
    {
        $plain = $this->cipher->decrypt($this->value($column));

        // Contao encodes "=", "&", "<" … in posted values; keys must be used exactly as typed.
        return trim(html_entity_decode($plain, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
    }

    public function hasValue(string $column): bool
    {
        return '' !== $this->value($column);
    }

    /**
     * Stores values in the record (creates it when missing). Secrets are passed
     * in plain text and stored encrypted.
     *
     * @param array<string, string> $values
     * @param list<string>          $secrets columns of $values to encrypt
     */
    public function store(array $values, array $secrets = []): void
    {
        foreach ($secrets as $column) {
            $values[$column] = $this->cipher->encrypt((string) $values[$column]);
        }

        $id = $this->ensureRow();
        $this->db->update(AccessPage::TABLE, $values + ['tstamp' => time()], ['id' => $id]);
        $this->row = null;
    }

    /**
     * Whether the table and the given columns exist. On a new installation the
     * first pass of the migrations runs before the database update creates
     * them; a takeover migration waits for the second pass.
     */
    public function hasColumns(string ...$columns): bool
    {
        try {
            $schema = $this->db->createSchemaManager();

            if (!$schema->tablesExist([AccessPage::TABLE])) {
                return false;
            }

            $existing = $schema->listTableColumns(AccessPage::TABLE);
        } catch (\Throwable) {
            return false;
        }

        $existing = array_change_key_case($existing, CASE_LOWER);

        foreach ($columns as $column) {
            if (!isset($existing[strtolower($column)])) {
                return false;
            }
        }

        return true;
    }

    /**
     * The takeover migrations of the extensions record here that they ran, so
     * each runs once (also when several extensions are updated together) and a
     * key removed later does not come back.
     */
    public function isAdopted(string $source): bool
    {
        return \in_array($source, explode(',', $this->value('adopted')), true);
    }

    public function markAdopted(string $source): void
    {
        $sources = array_filter(explode(',', $this->value('adopted')));
        $sources[] = $source;

        $this->store(['adopted' => implode(',', array_unique($sources))]);
    }

    /**
     * Id of the one record, created on first use.
     */
    public function ensureRow(): int
    {
        $id = (int) $this->db->fetchOne('SELECT id FROM '.AccessPage::TABLE.' ORDER BY id ASC LIMIT 1');

        if ($id > 0) {
            return $id;
        }

        $this->db->insert(AccessPage::TABLE, ['tstamp' => time()]);
        $this->row = null;

        return (int) $this->db->lastInsertId();
    }

    /**
     * @return array<string, mixed>
     */
    private function row(): array
    {
        if (null === $this->row) {
            try {
                $this->row = $this->db->fetchAssociative('SELECT * FROM '.AccessPage::TABLE.' ORDER BY id ASC LIMIT 1') ?: [];
            } catch (\Throwable) {
                // Table not created yet (before the database update).
                return [];
            }
        }

        return $this->row;
    }
}
