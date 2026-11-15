<?php

declare(strict_types=1);

namespace Neo\BasicBundle\EventListener;

use Contao\CoreBundle\Routing\ScopeMatcher;
use Contao\PageModel;
use Neo\BasicBundle\Cache\PageCacheTags;
use Neo\BasicBundle\License\LicenseService;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Tags every page of the front end with the licence tag. LicenseService
 * invalidates it when a licence changes what pages show (credit line, functions
 * of an edition), so Contao's page cache never keeps the old state. Runs before
 * the tags are written to the response (priority above 0).
 */
#[AsEventListener(event: KernelEvents::RESPONSE, priority: 8)]
final class LicenseCacheTagListener
{
    public function __construct(
        private readonly ScopeMatcher $scopeMatcher,
        private readonly PageCacheTags $cacheTags,
    ) {
    }

    public function __invoke(ResponseEvent $event): void
    {
        if ($this->scopeMatcher->isFrontendMainRequest($event) && $event->getRequest()->attributes->get('pageModel') instanceof PageModel) {
            $this->cacheTags->tag([LicenseService::CACHE_TAG]);
        }
    }
}
