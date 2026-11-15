<?php

declare(strict_types=1);

namespace Neo\BasicBundle\Tests\Support;

use Contao\CoreBundle\Csrf\ContaoCsrfTokenManager;
use Neo\BasicBundle\Access\AccessSettings;
use Neo\BasicBundle\Backend\AccessActions;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Csrf\CsrfToken;

/**
 * A back-end request of "Access keys and licences" with a session, a router
 * that writes the route and its parameters as address, and a token manager
 * that accepts the request token "valid-token" only.
 */
trait BackendFixtures
{
    private static string $token = 'valid-token';

    /**
     * @param array<string, string> $query
     * @param array<string, string> $post
     */
    private function backendRequest(string $method = 'GET', array $query = [], array $post = [], bool $json = false): RequestStack
    {
        $request = Request::create('https://admin.example.com/contao', $method, 'GET' === $method ? $query : $post);
        $request->query->replace($query);
        $request->setSession(new Session(new MockArraySessionStorage()));

        if ($json) {
            $request->headers->set('Accept', 'application/json');
        }

        $stack = new RequestStack();
        $stack->push($request);

        return $stack;
    }

    private function actions(RequestStack $stack, AccessSettings|null $settings = null): AccessActions
    {
        return new AccessActions($stack, $this->router(), $this->tokenManager(), $settings ?? Settings::with(), 'contao_csrf_token');
    }

    private function router(): RouterInterface
    {
        $router = $this->createStub(RouterInterface::class);
        $router->method('generate')->willReturnCallback(
            static fn (string $name, array $parameters = []): string => '/'.$name.([] !== $parameters ? '?'.http_build_query($parameters) : ''),
        );

        return $router;
    }

    private function tokenManager(): ContaoCsrfTokenManager
    {
        $tokens = $this->createStub(ContaoCsrfTokenManager::class);
        $tokens->method('getDefaultTokenValue')->willReturn(self::$token);
        $tokens->method('isTokenValid')->willReturnCallback(
            static fn (CsrfToken $token): bool => 'contao_csrf_token' === $token->getId() && self::$token === $token->getValue(),
        );

        return $tokens;
    }
}
