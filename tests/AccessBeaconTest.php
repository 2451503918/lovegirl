<?php
/**
 * Regression tests for services/access-beacon.php.
 *
 * access-beacon.php was added in the recent "外部资源全量本地化" commit
 * and was not covered by automated tests before. It is the front-end's
 * dwell-time beacon; misbehavior here silently breaks the visitor
 * analytics we surface on the homepage. The risk-bearing pieces we
 * pin down are:
 *
 *  - Preflight handling: CORS OPTIONS must return 204 and not echo
 *    a JSON error body (browsers reject preflights with non-empty
 *    bodies).
 *  - Wrong-method rejection: any non-OPTIONS, non-POST request must
 *    return 405 so the front-end can detect mis-wired requests.
 *  - POST with no payload must still succeed (the front-end may
 *    send a zero-duration beacon after a quick page exit).
 */
declare(strict_types=1);

class AccessBeaconTest extends LgTestCase
{
    public function testOptionsPreflightReturns204(): void
    {
        $res = $this->request('/?__target=access-beacon', 'OPTIONS');
        $this->assertEquals(204, $res['status']);
    }

    public function testGetReturns405(): void
    {
        $res = $this->request('/?__target=access-beacon', 'GET');
        $this->assertEquals(405, $res['status']);
        $this->assertContains('Method not allowed', $res['body']);
    }

    public function testPostWithNoBodyReturns204(): void
    {
        // A zero-duration (or missing) duration must still produce a
        // 204 No Content. The front-end fires beacons on page hide
        // and may pass an empty body.
        $res = $this->request('/?__target=access-beacon', 'POST');
        $this->assertEquals(204, $res['status']);
    }

    public function testPostWithDurationReturns204(): void
    {
        $res = $this->request(
            '/?__target=access-beacon',
            'POST',
            ['duration' => '12345']
        );
        $this->assertEquals(204, $res['status']);
    }

    public function testResponseIsJsonUtf8(): void
    {
        // Even the 405 path returns a JSON-shaped body. We assert the
        // Content-Type because front-end code may switch on it.
        $res = $this->request('/?__target=access-beacon', 'GET');
        $this->assertHeaderContains($res['headers'], 'Content-Type: application/json');
    }
}
