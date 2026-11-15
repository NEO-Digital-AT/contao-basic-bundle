<?php

declare(strict_types=1);

namespace Neo\BasicBundle\EventListener;

use Contao\CoreBundle\Routing\ScopeMatcher;
use Neo\BasicBundle\Dock\Dock;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Adds the files of the dock to a page that asked for them (Dock::register())
 * but came out without them: the button was written after the page assets of
 * Contao were processed (an event listener of the response), or the page has
 * no page layout that processes them. Runs after the listeners of the
 * extensions (priority -96).
 */
#[AsEventListener(event: KernelEvents::RESPONSE, priority: -96)]
final class DockResponseListener
{
    public function __construct(private readonly ScopeMatcher $scopeMatcher)
    {
    }

    public function __invoke(ResponseEvent $event): void
    {
        if (!$this->scopeMatcher->isFrontendMainRequest($event)) {
            return;
        }

        $config = $event->getRequest()->attributes->get(Dock::ATTRIBUTE);
        $response = $event->getResponse();

        if (!\is_array($config) || $response instanceof StreamedResponse || $response instanceof BinaryFileResponse
            || !str_contains((string) $response->headers->get('Content-Type', 'text/html'), 'text/html')) {
            return;
        }

        $html = $response->getContent();

        if (!\is_string($html) || !str_contains($html, '</head>') || !str_contains($html, '</body>') || str_contains($html, '<meta name="neo-dock"')) {
            return;
        }

        $head = Dock::meta($config).sprintf('<link rel="stylesheet" href="/%s?v=%s">', Dock::STYLE, self::version('dock.css'));
        $body = sprintf('<script src="/%s?v=%s" defer></script>', Dock::SCRIPT, self::version('dock.js'));

        $html = preg_replace('#</head>#i', str_replace(['\\', '$'], ['\\\\', '\\$'], $head).'</head>', $html, 1) ?? $html;
        $pos = strripos($html, '</body>');
        $html = substr($html, 0, $pos).$body.substr($html, $pos);

        $response->setContent($html);
    }

    private static function version(string $file): string
    {
        return ltrim(Dock::version($file), '|') ?: '1';
    }
}
