<?php

declare(strict_types=1);

namespace Neo\BasicBundle\Backend;

use Contao\CoreBundle\Csrf\ContaoCsrfTokenManager;
use Contao\CoreBundle\Exception\ResponseException;
use Contao\StringUtil;
use Neo\BasicBundle\Access\AccessPage;
use Neo\BasicBundle\Access\AccessSettings;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\FlashBagAwareSessionInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Csrf\CsrfToken;

/**
 * The buttons of "Access keys and licences" that run something without saving
 * the form: "Check now" of a licence and "Test connection" of a service. They
 * are back-end operations of the page (key=…) and answer in two ways:
 *
 * - With JavaScript (public/access.js) the button sends a POST with the request
 *   token of the form and gets JSON: the new content of its block, which
 *   replaces the old one in place. The page is not left, nothing typed is lost.
 * - Without JavaScript the button is a link with the request token in the
 *   address (rt); afterwards the page opens again at the block (anchor), with
 *   the result shown there once.
 *
 * Both need a valid request token.
 */
final class AccessActions
{
    private const FLASH = 'neo_access_result.';

    public function __construct(
        private readonly RequestStack $requestStack,
        private readonly RouterInterface $router,
        private readonly ContaoCsrfTokenManager $tokenManager,
        private readonly AccessSettings $settings,
        #[Autowire('%contao.csrf_token_name%')]
        private readonly string $tokenName,
    ) {
    }

    /**
     * The button of an action: a link with the request token for the round trip
     * without JavaScript, which the script of the page turns into a button.
     * With a reason it is a disabled button instead, the reason right next to it
     * (e.g. nothing to test without a key).
     *
     * @param array<string, string> $query further parameters of the operation
     */
    public function button(string $key, string $target, string $label, string $busyLabel, array $query = [], string $disabledReason = ''): string
    {
        if ('' !== $disabledReason) {
            return \sprintf(
                '<button type="button" class="tl_submit" disabled aria-describedby="%1$s_reason">%2$s</button> <span class="tl_help" id="%1$s_reason">%3$s</span>',
                StringUtil::specialchars($target),
                StringUtil::specialchars($label),
                StringUtil::specialchars($disabledReason),
            );
        }

        $url = $this->router->generate('contao_backend', ['do' => AccessPage::MODULE, 'key' => $key, ...$query, 'rt' => $this->tokenManager->getDefaultTokenValue()]);

        return \sprintf(
            '<a href="%s" class="tl_submit" data-neo-access-action data-neo-access-target="%s" data-neo-access-busy="%s" data-neo-access-failed="%s">%s</a>',
            StringUtil::specialchars($url),
            StringUtil::specialchars($target),
            StringUtil::specialchars($busyLabel),
            StringUtil::specialchars($GLOBALS['TL_LANG'][AccessPage::TABLE]['action_failed'] ?? ''),
            StringUtil::specialchars($label),
        );
    }

    /**
     * Checks the request token of an action: REQUEST_TOKEN in the body of the
     * POST of the script, rt in the address of the link. Answers itself when it
     * is missing or wrong (JSON, or Contao's page "Invalid request token").
     *
     * @throws ResponseException
     */
    public function requireToken(string $target, string $invalidText): void
    {
        $request = $this->request();
        $value = $request->isMethod('POST') ? $request->request->get('REQUEST_TOKEN') : $request->query->get('rt');

        if (\is_string($value) && '' !== $value && $this->tokenManager->isTokenValid(new CsrfToken($this->tokenName, $value))) {
            return;
        }

        if ($this->wantsJson($request)) {
            throw new ResponseException($this->json(['ok' => false, 'target' => $target, 'html' => \sprintf('<div class="tl_message"><p class="tl_error">%s</p></div>', StringUtil::specialchars($invalidText))], 400));
        }

        // As Contao does for its own operations with an invalid token.
        $request->getSession()->set('INVALID_TOKEN_URL', $request->getRequestUri());

        throw new ResponseException(new RedirectResponse($this->router->generate('contao_backend_confirm')));
    }

    /**
     * Answers an action: JSON with the new content of the block for the script,
     * otherwise back to the block in the form, where result() shows it once.
     *
     * @throws ResponseException
     */
    public function respond(string $target, string $html, bool $ok): never
    {
        $request = $this->request();

        // One wrapper, so the lines keep Contao's sizes of help texts and messages inside a widget.
        $html = '' !== $html ? '<div>'.$html.'</div>' : '';

        if ($this->wantsJson($request)) {
            throw new ResponseException($this->json(['ok' => $ok, 'target' => $target, 'html' => $html]));
        }

        $session = $request->getSession();

        if ($session instanceof FlashBagAwareSessionInterface) {
            $session->getFlashBag()->set(self::FLASH.$target, $html);
        }

        throw new ResponseException(new RedirectResponse($this->formUrl().'#'.rawurlencode($target)));
    }

    /**
     * The result of the last action without JavaScript for a block, '' when there is none. Shown once.
     */
    public function result(string $target): string
    {
        $session = $this->requestStack->getCurrentRequest()?->getSession();

        if (!$session instanceof FlashBagAwareSessionInterface || !$session->isStarted()) {
            return '';
        }

        return implode('', array_map('strval', $session->getFlashBag()->get(self::FLASH.$target)));
    }

    /**
     * Address of the form of the page.
     */
    public function formUrl(): string
    {
        return $this->router->generate('contao_backend', ['do' => AccessPage::MODULE, 'act' => 'edit', 'id' => $this->settings->ensureRow(), 'nb' => 1]);
    }

    private function wantsJson(Request $request): bool
    {
        return $request->isMethod('POST') && \in_array('application/json', $request->getAcceptableContentTypes(), true);
    }

    private function request(): Request
    {
        return $this->requestStack->getCurrentRequest() ?? throw new \LogicException('No request.');
    }

    /**
     * @param array<string, mixed> $data
     */
    private function json(array $data, int $status = 200): JsonResponse
    {
        $response = new JsonResponse($data, $status);
        $response->setPrivate();
        $response->headers->addCacheControlDirective('no-store');

        return $response;
    }
}
