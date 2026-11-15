<?php

declare(strict_types=1);

namespace Neo\BasicBundle\Tests\EventListener;

use Contao\CoreBundle\Routing\ScopeMatcher;
use Contao\PageModel;
use Neo\BasicBundle\Cache\PageCacheTags;
use Neo\BasicBundle\EventListener\LicenseCacheTagListener;
use Neo\BasicBundle\License\LicenseService;
use Neo\BasicBundle\Tests\Support\ServiceMap;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

final class LicenseCacheTagListenerTest extends TestCase
{
    public function testFrontEndPagesCarryTheLicenceTag(): void
    {
        $this->assertSame([[LicenseService::CACHE_TAG]], $this->tagsOf(true, true));
    }

    public function testOtherResponsesAreNotTagged(): void
    {
        $this->assertSame([], $this->tagsOf(false, true), 'back end or sub-request');
        $this->assertSame([], $this->tagsOf(true, false), 'front-end route without page');
    }

    /**
     * @return list<list<string>>
     */
    private function tagsOf(bool $frontend, bool $page): array
    {
        $manager = new class {
            /** @var list<list<string>> */
            public array $tags = [];

            /** @param list<string> $tags */
            public function tagWith(array $tags): void
            {
                $this->tags[] = $tags;
            }
        };

        $matcher = $this->createStub(ScopeMatcher::class);
        $matcher->method('isFrontendMainRequest')->willReturn($frontend);

        $request = Request::create('https://example.com/');

        if ($page) {
            $request->attributes->set('pageModel', $this->createStub(PageModel::class));
        }

        $event = new ResponseEvent($this->createStub(HttpKernelInterface::class), $request, HttpKernelInterface::MAIN_REQUEST, new Response());

        (new LicenseCacheTagListener($matcher, new PageCacheTags(new ServiceMap(['tag_manager' => $manager]))))($event);

        return $manager->tags;
    }
}
