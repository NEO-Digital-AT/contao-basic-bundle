<?php

declare(strict_types=1);

namespace Neo\BasicBundle\Tests\Backend;

use Contao\CoreBundle\Exception\ResponseException;
use Neo\BasicBundle\Tests\Support\BackendFixtures;
use Neo\BasicBundle\Tests\Support\LanguageFiles;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

final class AccessActionsTest extends TestCase
{
    use BackendFixtures;

    protected function setUp(): void
    {
        LanguageFiles::load('tl_neo_access');
    }

    protected function tearDown(): void
    {
        LanguageFiles::reset();
    }

    public function testTheButtonIsALinkWithTheRequestToken(): void
    {
        $html = $this->actions($this->backendRequest())->button('neo_ai_test', 'neo_access_ai', 'Test <now>', 'Testing …', ['module' => 'superagent']);

        $this->assertStringContainsString('href="/contao_backend?do=neo_access&amp;key=neo_ai_test&amp;module=superagent&amp;rt=valid-token"', $html);
        $this->assertStringContainsString('data-neo-access-target="neo_access_ai"', $html);
        $this->assertStringContainsString('>Test &lt;now&gt;</a>', $html);
    }

    public function testWithAReasonTheButtonIsDisabledAndSaysWhy(): void
    {
        $html = $this->actions($this->backendRequest())->button('neo_ai_test', 'neo_access_ai', 'Test', 'Testing …', [], 'Save the key first.');

        $this->assertStringContainsString('<button type="button" class="tl_submit" disabled aria-describedby="neo_access_ai_reason">Test</button>', $html);
        $this->assertStringContainsString('<span class="tl_help" id="neo_access_ai_reason">Save the key first.</span>', $html);
        $this->assertStringNotContainsString('valid-token', $html);
    }

    public function testAValidTokenInTheFormOrInTheAddressIsAccepted(): void
    {
        $this->actions($this->backendRequest('POST', [], ['REQUEST_TOKEN' => 'valid-token'], true))->requireToken('neo_access_ai', 'Invalid');
        $this->actions($this->backendRequest('GET', ['rt' => 'valid-token']))->requireToken('neo_access_ai', 'Invalid');

        $this->addToAssertionCount(2);
    }

    public function testATokenInTheAddressDoesNotCountForAPost(): void
    {
        $response = $this->responseOf(fn () => $this->actions($this->backendRequest('POST', ['rt' => 'valid-token'], [], true))->requireToken('neo_access_ai', 'Invalid'));

        $this->assertSame(400, $response->getStatusCode());
    }

    public function testTheScriptGetsJsonWhenTheTokenIsWrong(): void
    {
        $response = $this->responseOf(fn () => $this->actions($this->backendRequest('POST', [], ['REQUEST_TOKEN' => 'forged'], true))->requireToken('neo_access_ai', 'Page <out of date>'));

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame(400, $response->getStatusCode());
        $this->assertSame(['ok' => false, 'target' => 'neo_access_ai', 'html' => '<div class="tl_message"><p class="tl_error">Page &lt;out of date&gt;</p></div>'], json_decode((string) $response->getContent(), true));
        $this->assertStringContainsString('no-store', (string) $response->headers->get('Cache-Control'));
    }

    public function testTheLinkLeadsToContaosPageForInvalidTokens(): void
    {
        $stack = $this->backendRequest('GET', ['do' => 'neo_access', 'key' => 'neo_ai_test']);

        $response = $this->responseOf(fn () => $this->actions($stack)->requireToken('neo_access_ai', 'Invalid'));

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertSame('/contao_backend_confirm', $response->getTargetUrl());
        $this->assertStringContainsString('key=neo_ai_test', (string) $stack->getCurrentRequest()?->getSession()->get('INVALID_TOKEN_URL'));
    }

    public function testRespondGivesTheScriptTheNewBlock(): void
    {
        $response = $this->responseOf(fn () => $this->actions($this->backendRequest('POST', [], [], true))->respond('neo_access_ai', '<p>Done</p>', true));

        $this->assertSame(['ok' => true, 'target' => 'neo_access_ai', 'html' => '<div><p>Done</p></div>'], json_decode((string) $response->getContent(), true));
    }

    public function testWithoutJavaScriptTheResultIsShownOnceAtTheBlock(): void
    {
        $stack = $this->backendRequest('GET', ['rt' => 'valid-token']);
        $actions = $this->actions($stack);

        $response = $this->responseOf(fn () => $actions->respond('neo_access_ai', '<p>Done</p>', true));

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertSame('/contao_backend?do=neo_access&act=edit&id=1&nb=1#neo_access_ai', $response->getTargetUrl());
        $this->assertSame('<div><p>Done</p></div>', $actions->result('neo_access_ai'));
        $this->assertSame('', $actions->result('neo_access_ai'));
        $this->assertSame('', $actions->result('neo_access_other'));
    }

    private function responseOf(callable $action): Response
    {
        try {
            $action();
        } catch (ResponseException $e) {
            return $e->getResponse();
        }

        $this->fail('No response');
    }
}
