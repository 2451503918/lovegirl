<?php
/**
 * Regression tests for services/avatar-proxy.php.
 *
 * avatar-proxy.php was added in the recent "外部资源全量本地化" commit
 * and was not covered by any automated test before. It is the public
 * endpoint used by the front-end to resolve user avatars, and it
 * reaches into the filesystem (cache dir, default avatar). The
 * branches that are at risk of regressing and that we lock down here:
 *
 *  - Routing: unknown / missing `type` falls back to the default
 *    avatar; we verify the X-Avatar-Source header flips to "default".
 *  - QQ branch: input validation rejects anything that is not a pure
 *    digit string (no path traversal, no negative numbers, no
 *    floating point, no encoded payloads).
 *  - Gravatar branch: empty email is treated as default; valid emails
 *    with no cache file also fall back to default.
 *  - Initials branch: empty name uses '?', so a request with no name
 *    and one with name='?' must produce the exact same body.
 *  - Cache hit: when the cache file exists for a known QQ or gravatar
 *    hash, the proxy must read the file directly and tag the response
 *    with `X-Avatar-Source: cache`.
 */
declare(strict_types=1);

class AvatarProxyTest extends LgTestCase
{
    private const PNG_MAGIC = "\x89PNG\r\n\x1a\n";

    private string $cacheDir;

    public function setUp(): void
    {
        $this->cacheDir = dirname(__DIR__) . '/assets/img/avatars/cache/';
    }

    public function testNoTypeServesDefault(): void
    {
        $res = $this->request('/?__target=avatar-proxy');
        $this->assertEquals(200, $res['status']);
        $this->assertStartsWith(self::PNG_MAGIC, $res['body']);
        $this->assertHeaderContains($res['headers'], 'Content-Type: image/png');
        $this->assertHeaderContains($res['headers'], 'X-Avatar-Source: default');
    }

    public function testUnknownTypeServesDefault(): void
    {
        $res = $this->request('/?__target=avatar-proxy&type=bogus');
        $this->assertHeaderContains($res['headers'], 'X-Avatar-Source: default');
    }

    public function testQqWithEmptyValueServesDefault(): void
    {
        $res = $this->request('/?__target=avatar-proxy&type=qq&qq=');
        $this->assertHeaderContains($res['headers'], 'X-Avatar-Source: default');
    }

    public function testQqWithNonNumericValueServesDefault(): void
    {
        // The proxy's input validation uses preg_match('/^\d+$/', $qq).
        // Any non-digit payload must fall back to the default avatar.
        $cases = ['abc', '../etc/passwd', '12345abc', '12.34', '-1', '0x1f'];
        foreach ($cases as $bad) {
            $res = $this->request('/?__target=avatar-proxy&type=qq&qq=' . urlencode($bad));
            $this->assertHeaderContains(
                $res['headers'],
                'X-Avatar-Source: default',
                'non-numeric qq="' . $bad . '" should fall back to default'
            );
        }
    }

    public function testQqWithoutCacheServesDefault(): void
    {
        // A valid numeric QQ with no cached file must fall back to the
        // default avatar rather than echo back a 404 or leak a warning.
        $res = $this->request('/?__target=avatar-proxy&type=qq&qq=1000000001');
        $this->assertEquals(200, $res['status']);
        $this->assertHeaderContains($res['headers'], 'X-Avatar-Source: default');
        $this->assertStartsWith(self::PNG_MAGIC, $res['body']);
    }

    public function testQqCacheHitReturnsCachedFile(): void
    {
        if (!is_dir($this->cacheDir) && !@mkdir($this->cacheDir, 0755, true) && !is_dir($this->cacheDir)) {
            $this->fail('cache dir not available: ' . $this->cacheDir);
        }
        $qq = '900000001';
        $cacheFile = $this->cacheDir . 'qq_' . $qq . '.png';
        // 1x1 transparent PNG bytes.
        $payload = base64_decode(
            'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII='
        );
        $this->assertTrue(is_string($payload) && strlen($payload) > 0);
        file_put_contents($cacheFile, $payload);
        try {
            $res = $this->request('/?__target=avatar-proxy&type=qq&qq=' . $qq);
            $this->assertHeaderContains($res['headers'], 'X-Avatar-Source: cache');
            $this->assertEquals($payload, $res['body']);
        } finally {
            @unlink($cacheFile);
        }
    }

    public function testGravatarWithEmptyEmailServesDefault(): void
    {
        $res = $this->request('/?__target=avatar-proxy&type=gravatar&email=');
        $this->assertHeaderContains($res['headers'], 'X-Avatar-Source: default');
    }

    public function testGravatarWithoutCacheServesDefault(): void
    {
        $res = $this->request(
            '/?__target=avatar-proxy&type=gravatar&email=' . md5('nobody-' . bin2hex(random_bytes(4)) . '@example.com')
        );
        $this->assertEquals(200, $res['status']);
        $this->assertHeaderContains($res['headers'], 'X-Avatar-Source: default');
    }

    public function testGravatarCacheHitReturnsCachedFile(): void
    {
        if (!is_dir($this->cacheDir) && !@mkdir($this->cacheDir, 0755, true) && !is_dir($this->cacheDir)) {
            $this->fail('cache dir not available: ' . $this->cacheDir);
        }
        $hash = md5('tester-' . bin2hex(random_bytes(4)) . '@example.com');
        $cacheFile = $this->cacheDir . 'gravatar_' . $hash . '.png';
        $payload = base64_decode(
            'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII='
        );
        file_put_contents($cacheFile, $payload);
        try {
            $res = $this->request('/?__target=avatar-proxy&type=gravatar&email=' . $hash);
            $this->assertHeaderContains($res['headers'], 'X-Avatar-Source: cache');
            $this->assertEquals($payload, $res['body']);
        } finally {
            @unlink($cacheFile);
        }
    }

    public function testInitialsEmptyNameUsesQuestionMark(): void
    {
        // avatar-proxy uses '?' as the fallback initial. The body for
        // an empty name must therefore match the body for an explicit '?'.
        $empty  = $this->request('/?__target=avatar-proxy&type=initials&name=');
        $mark   = $this->request('/?__target=avatar-proxy&type=initials&name=' . urlencode('?'));
        $this->assertEquals($empty['body'], $mark['body']);
    }

    public function testInitialsAreCachedForADay(): void
    {
        // The initials branch sets Cache-Control: max-age=86400 (1 day).
        // The cached-image branch uses 604800 (1 week). This test pins
        // the initials value so a future refactor cannot silently
        // re-use the wrong constant and double-up the cache lifetime.
        $res = $this->request('/?__target=avatar-proxy&type=initials&name=Alice');
        $this->assertHeaderContains($res['headers'], 'Cache-Control:');
        $this->assertHeaderContains($res['headers'], 'max-age=86400');
    }

    public function testCachedImageIsCachedForAWeek(): void
    {
        // When a real cache file exists, the proxy serves it via
        // serveCachedImage() which advertises a 1-week TTL.
        if (!is_dir($this->cacheDir) && !@mkdir($this->cacheDir, 0755, true) && !is_dir($this->cacheDir)) {
            $this->fail('cache dir not available: ' . $this->cacheDir);
        }
        $qq = '900000002';
        $cacheFile = $this->cacheDir . 'qq_' . $qq . '.png';
        $payload = base64_decode(
            'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII='
        );
        file_put_contents($cacheFile, $payload);
        try {
            $res = $this->request('/?__target=avatar-proxy&type=qq&qq=' . $qq);
            $this->assertHeaderContains($res['headers'], 'max-age=604800');
        } finally {
            @unlink($cacheFile);
        }
    }

    public function testDefaultAvatarIsCacheableForAnHour(): void
    {
        $res = $this->request('/?__target=avatar-proxy');
        $this->assertHeaderContains($res['headers'], 'max-age=3600');
    }
}
