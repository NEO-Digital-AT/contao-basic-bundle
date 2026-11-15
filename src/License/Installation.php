<?php

declare(strict_types=1);

namespace Neo\BasicBundle\License;

use Doctrine\DBAL\Connection;
use Neo\BasicBundle\Access\AccessPage;

/**
 * The identifier of this Contao installation for the licence service, which counts
 * installations per licence (one key, several installations for agencies; each
 * installation with its own number of domains).
 *
 * - Created once, as 32 random hexadecimal characters, when a licence is first
 *   activated or checked, and stored in the record of the page "Access keys and
 *   licences" (tl_neo_access.installation_id). It never changes afterwards, also not
 *   on updates or when the cache is cleared; a copy of the database (staging) carries
 *   the same identifier on purpose.
 * - Never derived from a domain, a path or the server; not a secret, but not shown in
 *   the back end and never written to a log.
 * - The host of a request is kept as well (installation_host): it stands for root
 *   pages without a domain name when the check runs without a request (cron, console).
 */
final class Installation
{
    public const COLUMN = 'installation_id';
    public const HOST_COLUMN = 'installation_host';

    private string|null $id = null;

    public function __construct(private readonly Connection $db)
    {
    }

    /**
     * The identifier, created on first use; '' while the database is not updated yet
     * (the licence service then checks as before, without installations).
     */
    public function id(): string
    {
        if (null !== $this->id) {
            return $this->id;
        }

        try {
            $row = $this->record();
            $current = (string) $row[self::COLUMN];

            if (!self::isValid($current)) {
                // Only an empty (or broken) value is replaced: two requests at the same
                // time end up with the identifier of the one that wrote first.
                $this->db->executeStatement(
                    'UPDATE '.AccessPage::TABLE.' SET '.self::COLUMN.' = ? WHERE id = ? AND '.self::COLUMN.' = ?',
                    [bin2hex(random_bytes(16)), (int) $row['id'], $current],
                );

                $current = (string) $this->db->fetchOne('SELECT '.self::COLUMN.' FROM '.AccessPage::TABLE.' WHERE id = ?', [(int) $row['id']]);
            }
        } catch (\Throwable) {
            return '';
        }

        return $this->id = self::isValid($current) ? $current : '';
    }

    /**
     * The host that stands for root pages without a domain name, '' when none was seen yet.
     */
    public function host(): string
    {
        try {
            return (string) ($this->record()[self::HOST_COLUMN] ?? '');
        } catch (\Throwable) {
            return '';
        }
    }

    public function rememberHost(string $host): void
    {
        if ('' === $host || $host === $this->host()) {
            return;
        }

        try {
            $this->db->update(AccessPage::TABLE, [self::HOST_COLUMN => mb_substr($host, 0, 255)], ['id' => (int) $this->record()['id']]);
        } catch (\Throwable) {
            // Before the database update the column does not exist; the request host is used directly.
        }
    }

    /**
     * What the licence service accepts: 8 to 64 characters of A-Z, a-z, 0-9, "_" and "-".
     */
    public static function isValid(string $id): bool
    {
        return 1 === preg_match('/^[A-Za-z0-9_-]{8,64}$/', $id);
    }

    /**
     * @return array<string, mixed>
     */
    private function record(): array
    {
        $row = $this->db->fetchAssociative('SELECT * FROM '.AccessPage::TABLE.' ORDER BY id ASC LIMIT 1');

        if (false === $row) {
            $this->db->insert(AccessPage::TABLE, ['tstamp' => time()]);
            $row = $this->db->fetchAssociative('SELECT * FROM '.AccessPage::TABLE.' ORDER BY id ASC LIMIT 1');
        }

        if (false === $row || !\array_key_exists(self::COLUMN, $row)) {
            throw new \RuntimeException('The record of the access page has no installation column yet.');
        }

        return $row;
    }
}
