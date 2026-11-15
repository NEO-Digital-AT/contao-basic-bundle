<?php

declare(strict_types=1);

namespace Neo\BasicBundle\Tests\Support;

use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * A service locator with fixed entries.
 */
final class ServiceMap implements ContainerInterface
{
    /**
     * @param array<string, object> $services
     */
    public function __construct(private readonly array $services = [])
    {
    }

    public function get(string $id): object
    {
        return $this->services[$id] ?? throw new class(\sprintf('No service "%s".', $id)) extends \RuntimeException implements NotFoundExceptionInterface {
        };
    }

    public function has(string $id): bool
    {
        return isset($this->services[$id]);
    }
}
