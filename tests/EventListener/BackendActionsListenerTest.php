<?php

declare(strict_types=1);

namespace Neo\BasicBundle\Tests\EventListener;

use Neo\BasicBundle\EventListener\BackendActionsListener;
use PHPUnit\Framework\TestCase;

final class BackendActionsListenerTest extends TestCase
{
    private mixed $modules = null;

    protected function setUp(): void
    {
        $this->modules = $GLOBALS['BE_MOD'] ?? null;
    }

    protected function tearDown(): void
    {
        $GLOBALS['BE_MOD'] = $this->modules;
    }

    public function testEveryModuleOfTheGroupGetsTheScriptAfterItsOwnFilesOnce(): void
    {
        $GLOBALS['BE_MOD'] = [
            'neo' => [
                'with_own' => ['tables' => ['tl_a'], 'javascript' => 'bundles/own/a.js', 'stylesheet' => ['bundles/own/a.css']],
                'without' => ['tables' => ['tl_b']],
            ],
            'system' => ['other' => ['tables' => ['tl_c']]],
        ];

        $listener = new BackendActionsListener();
        $listener();
        $listener();

        $this->assertSame(['bundles/own/a.js', BackendActionsListener::SCRIPT], $GLOBALS['BE_MOD']['neo']['with_own']['javascript']);
        $this->assertSame(['bundles/own/a.css', BackendActionsListener::STYLESHEET], $GLOBALS['BE_MOD']['neo']['with_own']['stylesheet']);
        $this->assertSame([BackendActionsListener::SCRIPT], $GLOBALS['BE_MOD']['neo']['without']['javascript']);
        $this->assertArrayNotHasKey('javascript', $GLOBALS['BE_MOD']['system']['other']);
    }
}
