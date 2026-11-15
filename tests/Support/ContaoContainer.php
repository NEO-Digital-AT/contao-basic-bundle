<?php

declare(strict_types=1);

namespace Neo\BasicBundle\Tests\Support;

use Contao\CoreBundle\Routing\ScopeMatcher;
use Contao\System;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * The few services Contao's static helpers (Message) take from the container,
 * for a back-end request.
 */
trait ContaoContainer
{
    private function useContainer(RequestStack $stack): void
    {
        $matcher = $this->createStub(ScopeMatcher::class);
        $matcher->method('isBackendRequest')->willReturn(true);

        $container = new Container();
        $container->set('request_stack', $stack);
        $container->set('contao.routing.scope_matcher', $matcher);

        System::setContainer($container);
    }

    /**
     * @return list<string>
     */
    private function messages(RequestStack $stack, string $type): array
    {
        return $stack->getCurrentRequest()?->getSession()->getFlashBag()->peek('contao.BE.'.$type) ?? [];
    }

    private function dropContainer(): void
    {
        System::setContainer(new Container());
    }
}
