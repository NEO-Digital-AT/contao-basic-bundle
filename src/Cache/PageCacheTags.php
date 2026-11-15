<?php

declare(strict_types=1);

namespace Neo\BasicBundle\Cache;

use Psr\Container\ContainerInterface;

/**
 * Cache tags of front-end pages on every supported Contao version.
 *
 * Contao 5.5 introduced the CacheTagManager and deprecated EntityCacheTags,
 * which Contao 5.3 and 5.4 provide instead. This service uses the manager when
 * it exists and falls back to EntityCacheTags otherwise, so extensions can tag
 * pages and invalidate tags without knowing the Contao version. Without either
 * service (no HTTP cache), both methods do nothing.
 */
final class PageCacheTags
{
    private object|null $tags = null;

    private bool $resolved = false;

    public function __construct(private readonly ContainerInterface $services)
    {
    }

    /**
     * Adds the tags to the current response.
     *
     * @param list<string> $tags
     */
    public function tag(array $tags): void
    {
        $this->resolve()?->tagWith($tags);
    }

    /**
     * Invalidates every cached page that carries one of the tags.
     *
     * @param list<string> $tags
     */
    public function invalidate(array $tags): void
    {
        $service = $this->resolve();

        if (null === $service) {
            return;
        }

        if (method_exists($service, 'invalidateTags')) {
            $service->invalidateTags($tags);
        } else {
            $service->invalidateTagsFor($tags);
        }
    }

    private function resolve(): object|null
    {
        if (!$this->resolved) {
            $this->resolved = true;

            foreach (['tag_manager', 'entity_tags'] as $id) {
                if ($this->services->has($id)) {
                    $this->tags = $this->services->get($id);
                    break;
                }
            }
        }

        return $this->tags;
    }
}
