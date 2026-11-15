<?php

declare(strict_types=1);

namespace Neo\BasicBundle\Tests\Backend;

use Neo\BasicBundle\Backend\RequestInput;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

final class RequestInputTest extends TestCase
{
    public function testValuesAreEncodedAsContaoDoes(): void
    {
        $stack = new RequestStack();
        $stack->push(Request::create('/contao?module=<b>x</b>&list[]=a"b', 'POST', ['name' => '{{date}} & <i>', 'empty' => '']));
        $input = new RequestInput($stack);

        $this->assertSame('&#60;b&#62;x&#60;/b&#62;', $input->query('module'));
        $this->assertSame(['a&#34;b'], $input->query('list'));
        $this->assertStringNotContainsString('<i>', (string) $input->post('name'));
        $this->assertStringContainsString('{{date}}', (string) $input->post('name'), 'insert tags in form values as Input::post() keeps them in the back end');
        $this->assertSame('', $input->post('empty'));
        $this->assertNull($input->query('missing'));
        $this->assertNull($input->post('missing'));
    }

    public function testWithoutRequestEverythingIsMissing(): void
    {
        $input = new RequestInput(new RequestStack());

        $this->assertNull($input->query('act'));
        $this->assertNull($input->post('act'));
    }
}
