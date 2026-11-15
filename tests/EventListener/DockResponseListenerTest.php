<?php

declare(strict_types=1);

namespace Neo\BasicBundle\Tests\EventListener;

use Contao\CoreBundle\Routing\ScopeMatcher;
use Neo\BasicBundle\Dock\Dock;
use Neo\BasicBundle\EventListener\DockResponseListener;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

final class DockResponseListenerTest extends TestCase
{
    private const PAGE = '<!DOCTYPE html><html><head><title>Page</title></head><body><p>Text</p></body></html>';

    public function testAPageThatAskedForTheDockButCameOutWithoutItGetsTheFiles(): void
    {
        $response = $this->respond(['arrangement' => 'horizontal', 'label' => 'Settings'], self::PAGE);
        $html = (string) $response->getContent();

        $this->assertMatchesRegularExpression('#<meta name="neo-dock" content="horizontal" data-neo-dock-label="Settings"><link rel="stylesheet" href="/bundles/neobasic/dock.css\?v=\d+"></head>#', $html);
        $this->assertMatchesRegularExpression('#<script src="/bundles/neobasic/dock.js\?v=\d+" defer></script></body>#', $html);
    }

    public function testAPageWithTheDockFromThePageAssetsStaysAsItIs(): void
    {
        $page = str_replace('</head>', '<meta name="neo-dock" content="menu" data-neo-dock-label="Settings"></head>', self::PAGE);

        $this->assertSame($page, $this->respond(['arrangement' => 'menu', 'label' => 'Settings'], $page)->getContent());
    }

    public function testPagesThatDidNotAskForTheDockStayAsTheyAre(): void
    {
        $this->assertSame(self::PAGE, $this->respond(null, self::PAGE)->getContent());
        $this->assertSame('{"a":1}', $this->respond(['arrangement' => 'menu', 'label' => 'Settings'], '{"a":1}', 'application/json')->getContent());
        $this->assertSame(self::PAGE, $this->respond(['arrangement' => 'menu', 'label' => 'Settings'], self::PAGE, 'text/html', false)->getContent(), 'not the front end');
    }

    /**
     * @param array{arrangement: string, label: string}|null $config
     */
    private function respond(array|null $config, string $content, string $type = 'text/html; charset=UTF-8', bool $frontend = true): Response
    {
        $matcher = $this->createStub(ScopeMatcher::class);
        $matcher->method('isFrontendMainRequest')->willReturn($frontend);

        $request = new Request();
        if (null !== $config) {
            $request->attributes->set(Dock::ATTRIBUTE, $config);
        }

        $response = new Response($content, 200, ['Content-Type' => $type]);
        $event = new ResponseEvent($this->createStub(HttpKernelInterface::class), $request, HttpKernelInterface::MAIN_REQUEST, $response);

        (new DockResponseListener($matcher))($event);

        return $response;
    }
}
