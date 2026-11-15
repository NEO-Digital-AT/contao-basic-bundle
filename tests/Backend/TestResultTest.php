<?php

declare(strict_types=1);

namespace Neo\BasicBundle\Tests\Backend;

use Neo\BasicBundle\Backend\TestResult;
use PHPUnit\Framework\TestCase;

final class TestResultTest extends TestCase
{
    public function testMaskHidesTheKeyAndItsUrlEncodedForm(): void
    {
        $key = 'sk-a/b+c==';

        $masked = TestResult::mask('key sk-a/b+c== in body, sk-a%2Fb%2Bc%3D%3D in URL', $key);

        $this->assertSame('key *** in body, *** in URL', $masked);
    }

    public function testVeryShortKeysAreNotMaskedSoTheMessageStaysReadable(): void
    {
        $this->assertSame('error 12345', TestResult::mask('error 12345', '12345'));
    }

    public function testFingerprintShowsLengthStartAndEndOnly(): void
    {
        $this->assertSame('22 · SA-A…12', TestResult::fingerprint('SA-AAAA-BBBB-CCCC-DD12'));
        $this->assertStringNotContainsString('BBBB', TestResult::fingerprint('SA-AAAA-BBBB-CCCC-DD12'));
    }

    public function testAnswerIsShortenedMaskedAndEscaped(): void
    {
        $key = 'secret-key-123';
        $body = '<b>'.$key.'</b>'.str_repeat('x', 500);

        $answer = TestResult::answer($body, $key);

        $this->assertStringStartsWith('<pre class="tl_help" style="white-space:pre-wrap">&lt;b&gt;***&lt;/b&gt;', $answer);
        $this->assertStringNotContainsString($key, $answer);
        $this->assertLessThan(500, \strlen($answer));
        $this->assertSame('', TestResult::answer('   ', $key));
    }

    public function testLineAndDetailsAreEscaped(): void
    {
        $this->assertSame('<div class="tl_message"><p class="tl_error">a &lt;b&gt;</p></div>', TestResult::line('tl_error', 'a <b>'));
        $this->assertSame('<p class="tl_help">Address: https://example.com/?a=1&amp;b=2 · Key: 8 · abcd…gh</p>', TestResult::details(['Address' => 'https://example.com/?a=1&b=2', 'Key' => '8 · abcd…gh']));
    }
}
