<?php

declare(strict_types=1);

namespace Neo\BasicBundle\Terms;

use Doctrine\DBAL\Connection;

/**
 * Confirmation of the terms of an extension (table tl_neo_terms).
 *
 * An extension splits its terms into passages, in order: the first applies to
 * every edition (also without a licence), each further one to the edition of
 * the same name and above, e.g. ['all' => 1, 'basic' => 1, 'plus' => 1,
 * 'pro' => 1] (passage => version). Raising a version asks for a new
 * confirmation of that passage.
 *
 * Every confirmation is kept: who accepted which version of which passage,
 * when, and in which language the text was shown. The German text is the
 * binding one.
 */
final class TermsService
{
    public const TABLE = 'tl_neo_terms';

    /**
     * @var array<string, array<string, array{version: int, username: string, time: int, language: string}>>
     */
    private array $latest = [];

    public function __construct(private readonly Connection $db)
    {
    }

    /**
     * The passages that apply to an edition, in order: the first one and every
     * passage up to the edition ('' or an unknown edition: the first only).
     *
     * @param array<string, int> $passages
     *
     * @return array<string, int>
     */
    public static function required(array $passages, string $edition): array
    {
        $position = array_search($edition, array_keys($passages), true);

        return \array_slice($passages, 0, false === $position ? 1 : $position + 1, true);
    }

    /**
     * The latest confirmation of a passage, whatever its version.
     *
     * @return array{version: int, username: string, time: int, language: string}|null
     */
    public function acceptance(string $module, string $passage): array|null
    {
        return $this->load($module)[$passage] ?? null;
    }

    public function isAccepted(string $module, string $passage, int $version): bool
    {
        return ($this->acceptance($module, $passage)['version'] ?? 0) >= $version;
    }

    /**
     * The last passage, among those required for the edition, that is accepted
     * in its current version together with all passages before it; null when
     * not even the first one is.
     *
     * @param array<string, int> $passages
     */
    public function acceptedUpTo(string $module, array $passages, string $edition): string|null
    {
        $last = null;

        foreach (self::required($passages, $edition) as $passage => $version) {
            if (!$this->isAccepted($module, $passage, $version)) {
                break;
            }

            $last = $passage;
        }

        return $last;
    }

    /**
     * @param array<string, int> $passages
     */
    public function allAccepted(string $module, array $passages, string $edition): bool
    {
        $required = self::required($passages, $edition);

        return array_key_last($required) === $this->acceptedUpTo($module, $passages, $edition);
    }

    /**
     * Records the confirmation of passages (passage => version).
     *
     * @param array<string, int> $passages
     */
    public function accept(string $module, array $passages, int $userId, string $username, string $language): void
    {
        $now = time();

        foreach ($passages as $passage => $version) {
            $this->db->insert(self::TABLE, [
                'tstamp' => $now,
                'module' => $module,
                'passage' => (string) $passage,
                'version' => $version,
                'user_id' => $userId,
                'username' => mb_substr($username, 0, 64),
                'language' => mb_substr($language, 0, 8),
            ]);
        }

        unset($this->latest[$module]);
    }

    /**
     * @return array<string, array{version: int, username: string, time: int, language: string}>
     */
    private function load(string $module): array
    {
        if (isset($this->latest[$module])) {
            return $this->latest[$module];
        }

        try {
            $rows = $this->db->fetchAllAssociative(
                'SELECT passage, version, username, tstamp, language FROM '.self::TABLE.' WHERE module = ? ORDER BY id',
                [$module],
            );
        } catch (\Throwable) {
            // Table not created yet (before the database update): nothing confirmed.
            $rows = [];
        }

        $latest = [];

        foreach ($rows as $row) {
            $latest[(string) $row['passage']] = [
                'version' => (int) $row['version'],
                'username' => (string) $row['username'],
                'time' => (int) $row['tstamp'],
                'language' => (string) $row['language'],
            ];
        }

        return $this->latest[$module] = $latest;
    }
}
