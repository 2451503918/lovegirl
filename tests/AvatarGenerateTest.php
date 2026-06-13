<?php
/**
 * Regression tests for services/avatar-generate.php.
 *
 * This file was added in the recent "外部资源全量本地化" commit and has
 * no prior test coverage. It exposes an HTTP endpoint that renders a
 * colored initials avatar with the GD library. The behaviour we lock
 * down here is:
 *
 *  - Size parameter is clamped to [16, 512].
 *  - An empty `name` falls back to a single '?'.
 *  - The response is always a valid PNG with the expected Content-Type.
 *  - Same name yields the same body (deterministic hash-based color).
 *  - Different names produce different bodies (color slotting).
 *  - Non-ASCII / CJK initials do not blow up rendering.
 */
declare(strict_types=1);

class AvatarGenerateTest extends LgTestCase
{
    private const PNG_MAGIC = "\x89PNG\r\n\x1a\n";

    public function testDefaultNameFallsBackToQuestionMark(): void
    {
        // With no name, the script substitutes '?' and the response is
        // still a valid PNG. The rendered pixel grid encodes the same
        // color as it would for an explicit name='?'.
        $explicit = $this->request('/?__target=avatar-generate&name=' . urlencode('?'));
        $blank    = $this->request('/?__target=avatar-generate&name=');
        $this->assertEquals(strlen($explicit['body']), strlen($blank['body']));
        $this->assertEquals($explicit['body'], $blank['body']);
    }

    public function testSizeBelow16IsClamped(): void
    {
        // The script clamps $size to 16. A request for size=0 should
        // therefore produce a 16x16 image. We compare the byte length
        // of the PNG: a 16x16 image is smaller than the default 100x100.
        $small = $this->request('/?__target=avatar-generate&name=Hi&size=0');
        $default = $this->request('/?__target=avatar-generate&name=Hi');
        $this->assertTrue(strlen($small['body']) < strlen($default['body']),
            'size=0 should clamp to 16 and produce a smaller PNG than size=100');
        $this->assertTrue(strlen($small['body']) > 0);
    }

    public function testSizeAbove512IsClamped(): void
    {
        $big    = $this->request('/?__target=avatar-generate&name=Hi&size=9999');
        $default = $this->request('/?__target=avatar-generate&name=Hi');
        // 512x512 PNG must be strictly larger than the 100x100 default.
        $this->assertTrue(strlen($big['body']) > strlen($default['body']),
            'size=9999 should clamp to 512 and produce a larger PNG than the 100 default');
    }

    public function testDefaultSizeIs100(): void
    {
        // Default size 100 must differ from a 64 request (no clamp).
        $s100 = $this->request('/?__target=avatar-generate&name=Hi');
        $s64  = $this->request('/?__target=avatar-generate&name=Hi&size=64');
        $this->assertNotEquals($s100['body'], $s64['body']);
    }

    public function testResponseIsValidPngWithImageContentType(): void
    {
        $res = $this->request('/?__target=avatar-generate&name=Alice');
        $this->assertEquals(200, $res['status']);
        $this->assertStartsWith(self::PNG_MAGIC, $res['body']);
        $this->assertHeaderContains($res['headers'], 'Content-Type: image/png');
    }

    public function testSameNameIsDeterministic(): void
    {
        $a = $this->request('/?__target=avatar-generate&name=Alice');
        $b = $this->request('/?__target=avatar-generate&name=Alice');
        $this->assertEquals($a['body'], $b['body']);
    }

    public function testDifferentNamesProduceDifferentBodies(): void
    {
        // Two names hashing to different color slots should yield
        // visibly different PNG payloads.
        $a = $this->request('/?__target=avatar-generate&name=Alice');
        $b = $this->request('/?__target=avatar-generate&name=Bob');
        $this->assertNotEquals($a['body'], $b['body']);
    }

    public function testCjkNameDoesNotCrash(): void
    {
        // CJK input exercises the mb_substr path. The script must
        // still return a valid PNG and not throw an exception.
        $res = $this->request('/?__target=avatar-generate&name=' . urlencode('林徽因'));
        $this->assertEquals(200, $res['status']);
        $this->assertStartsWith(self::PNG_MAGIC, $res['body']);
    }

    public function testResponseIsCacheable(): void
    {
        // The script advertises a Cache-Control header so that clients
        // and proxies can cache the generated avatar. We just check
        // the header is present; the precise value can evolve.
        $res = $this->request('/?__target=avatar-generate&name=Alice');
        $this->assertHeaderContains($res['headers'], 'Cache-Control');
    }
}
