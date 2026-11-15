<?php

declare(strict_types=1);

namespace Neo\BasicBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Shared foundation of the extensions: AI connection, key encryption, model
 * list, licence check and the back-end menu group.
 */
final class NeoBasicBundle extends Bundle
{
    public function getPath(): string
    {
        return \dirname(__DIR__);
    }
}
