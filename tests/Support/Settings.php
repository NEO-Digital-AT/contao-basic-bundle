<?php

declare(strict_types=1);

namespace Neo\BasicBundle\Tests\Support;

use Neo\BasicBundle\Access\AccessSettings;
use Neo\BasicBundle\Security\SecretCipher;

/**
 * "Access keys and licences" with the given values, secrets encrypted as the page stores them.
 */
final class Settings
{
    public const SECRET = 'app-secret-for-tests';

    /**
     * @param array<string, string> $values
     * @param list<string>          $secrets columns of $values that are secrets
     */
    public static function with(array $values = [], array $secrets = []): AccessSettings
    {
        $settings = new AccessSettings(Database::connection(Database::ACCESS), new SecretCipher(self::SECRET));

        if ([] !== $values) {
            $settings->store($values, $secrets);
        }

        return $settings;
    }
}
