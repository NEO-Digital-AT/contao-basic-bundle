<?php

declare(strict_types=1);

namespace Neo\BasicBundle\Tests\Cache;

use Neo\BasicBundle\Cache\PageCacheTags;
use Neo\BasicBundle\Tests\Support\ServiceMap;
use PHPUnit\Framework\TestCase;

final class PageCacheTagsTest extends TestCase
{
    public function testTheTagManagerOfContao55AndLaterIsPreferred(): void
    {
        $manager = $this->recorder();
        $entityTags = $this->recorder();

        $tags = new PageCacheTags(new ServiceMap(['tag_manager' => $manager, 'entity_tags' => $entityTags]));
        $tags->tag(['contao.db.tl_neo_license']);
        $tags->invalidate(['contao.db.tl_neo_license']);

        $this->assertSame([['tagWith', ['contao.db.tl_neo_license']], ['invalidateTags', ['contao.db.tl_neo_license']]], $manager->calls);
        $this->assertSame([], $entityTags->calls);
    }

    public function testEntityCacheTagsOfContao53AreUsedOtherwise(): void
    {
        $entityTags = new class {
            /** @var list<array{string, list<string>}> */
            public array $calls = [];

            /** @param list<string> $tags */
            public function tagWith(array $tags): void
            {
                $this->calls[] = ['tagWith', $tags];
            }

            /** @param list<string> $tags */
            public function invalidateTagsFor(array $tags): void
            {
                $this->calls[] = ['invalidateTagsFor', $tags];
            }
        };

        $tags = new PageCacheTags(new ServiceMap(['entity_tags' => $entityTags]));
        $tags->tag(['a']);
        $tags->invalidate(['a']);

        $this->assertSame([['tagWith', ['a']], ['invalidateTagsFor', ['a']]], $entityTags->calls);
    }

    public function testWithoutHttpCacheNothingHappens(): void
    {
        $tags = new PageCacheTags(new ServiceMap());

        $tags->tag(['a']);
        $tags->invalidate(['a']);

        $this->addToAssertionCount(1);
    }

    private function recorder(): object
    {
        return new class {
            /** @var list<array{string, list<string>}> */
            public array $calls = [];

            /** @param list<string> $tags */
            public function tagWith(array $tags): void
            {
                $this->calls[] = ['tagWith', $tags];
            }

            /** @param list<string> $tags */
            public function invalidateTags(array $tags): void
            {
                $this->calls[] = ['invalidateTags', $tags];
            }
        };
    }
}
