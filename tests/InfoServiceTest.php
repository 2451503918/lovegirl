<?php
/**
 * Regression tests for services/info-service.php.
 *
 * info-service.php was added in the recent "外部资源全量本地化" commit
 * and had no automated coverage before. It implements a small JSON API
 * (action whitelist, CORS allowlist, several actions including stats,
 * location and heartbeat). The pieces that are easy to break and that
 * we pin down here are:
 *
 *  - Action whitelist: unknown actions must be rejected with HTTP 400
 *    and a clear JSON error, even when the rest of the body is empty.
 *  - CORS: responses must echo back an Allow-Origin header only for
 *    origins on the allowlist, and must NOT leak the header for
 *    arbitrary cross-origin callers.
 *  - Heartbeat: a known action with no DB dependency should still
 *    return a successful pong.
 *  - Default service-running response: when no action is provided,
 *    the service returns its own banner JSON, not an error.
 *  - Graceful degradation: get_stats with no DB must return zeros
 *    rather than 500; this is what the homepage widget depends on.
 */
declare(strict_types=1);

class InfoServiceTest extends LgTestCase
{
    public function testInvalidActionReturns400WithError(): void
    {
        // The whitelist is: get_stats, get_location, heartbeat, geo.
        // Anything else must be rejected with 400 and an error message.
        $res = $this->request('/?__target=info-service&action=rm_-rf');
        $this->assertEquals(400, $res['status']);
        $this->assertHeaderContains($res['headers'], 'Content-Type: application/json');
        $this->assertContains('"success":false', $res['body']);
        $this->assertContains('Invalid action parameter', $res['body']);
    }

    public function testEmptyActionReturnsServiceBanner(): void
    {
        // No action at all must NOT error; it should return the
        // service-running banner with success=true and code=200.
        $res = $this->request('/?__target=info-service');
        $this->assertEquals(200, $res['status']);
        $this->assertContains('"success":true', $res['body']);
        $this->assertContains('Service is running', $res['body']);
    }

    public function testHeartbeatReturnsPong(): void
    {
        // Heartbeat is a known action with no DB requirement. It must
        // answer with code 200 and the literal "pong" message.
        $res = $this->request('/?__target=info-service&action=heartbeat');
        $this->assertEquals(200, $res['status']);
        $this->assertContains('"message":"pong"', $res['body']);
    }

    public function testCorsEchoedForWhitelistedOrigin(): void
    {
        $res = $this->request(
            '/?__target=info-service&action=heartbeat',
            'GET',
            [],
            ['HTTP_ORIGIN' => 'http://localhost']
        );
        $this->assertHeaderContains($res['headers'], 'Access-Control-Allow-Origin: http://localhost');
    }

    public function testCorsNotEchoedForUnlistedOrigin(): void
    {
        // The allowlist is: lovedemo.54oimx.top, love.54oimx.top,
        // localhost, 127.0.0.1. An attacker host must not receive an
        // Allow-Origin echo, otherwise the response can be read by
        // arbitrary cross-origin scripts.
        $res = $this->request(
            '/?__target=info-service&action=heartbeat',
            'GET',
            [],
            ['HTTP_ORIGIN' => 'http://evil.example.com']
        );
        $this->assertHeaderNotContains($res['headers'], 'Access-Control-Allow-Origin: http://evil.example.com');
    }

    public function testResponseIsJsonUtf8(): void
    {
        $res = $this->request('/?__target=info-service&action=heartbeat');
        $this->assertHeaderContains($res['headers'], 'Content-Type: application/json');
        $this->assertHeaderContains($res['headers'], 'charset=utf-8');
    }

    public function testGetStatsFallsBackToZerosWithoutDatabase(): void
    {
        // When the DB is unavailable (sandbox case), the action must
        // still answer with success=true and zero counters rather
        // than 500. This protects the homepage widget from a single
        // database blip.
        $res = $this->request('/?__target=info-service&action=get_stats');
        $this->assertEquals(200, $res['status']);
        $this->assertContains('"success":true', $res['body']);
        $this->assertContains('"articles":0', $res['body']);
        $this->assertContains('"photos":0', $res['body']);
        $this->assertContains('"messages":0', $res['body']);
        $this->assertContains('"days":0', $res['body']);
    }

    public function testGetLocationFallsBackToDefaultsWithoutDatabase(): void
    {
        // get_location must return a successful response with the
        // default city coordinates when no DB is reachable. This is
        // what the map widget on the about page consumes.
        $res = $this->request('/?__target=info-service&action=get_location');
        $this->assertEquals(200, $res['status']);
        $this->assertContains('"success":true', $res['body']);
        $this->assertContains('"boyCity":"北京"', $res['body']);
        $this->assertContains('"girlCity":"上海"', $res['body']);
    }
}
