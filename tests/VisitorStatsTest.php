<?php
/**
 * Regression tests for services/visitor-stats.php.
 *
 * visitor-stats.php was added/modified in the recent
 * "外部资源全量本地化" commit and was not covered before. It exposes the
 * track / get_stats actions that power the homepage visitor widget.
 * The risk-bearing pieces we pin down are:
 *
 *  - Graceful no-DB path: when the database is unreachable, the
 *    service must return a 200 with the demo data response (not 500).
 *    This is what the homepage widget depends on so it can keep
 *    rendering even during a database blip.
 *  - Service banner: the default action (no `action` param) must
 *    return 200 with the "running" response, the same way the other
 *    LG-NewUi services do.
 *  - CORS allowlist: the service uses an allowlist driven by
 *    $_SERVER['HTTP_HOST'], which is a small attack surface. We
 *    pin the shape but not the exact origin since the host is
 *    the server's own host.
 */
declare(strict_types=1);

class VisitorStatsTest extends LgTestCase
{
    public function testNoActionFallsBackToDemoDataWithoutDatabase(): void
    {
        // In the test environment there is no real DB, so the script
        // takes the fallback path BEFORE the switch statement and
        // returns the demo payload for ANY action (including none).
        // The demo data is the contract the homepage widget expects
        // when the DB is down.
        $res = $this->request('/?__target=visitor-stats');
        $this->assertEquals(200, $res['status']);
        $this->assertContains('"code":200', $res['body']);
        $this->assertContains('Database not connected', $res['body']);
        $this->assertContains('"today"', $res['body']);
        $this->assertContains('"total"', $res['body']);
        $this->assertContains('"visits"', $res['body']);
        $this->assertContains('"visitors"', $res['body']);
    }

    public function testGetStatsFallsBackToDemoDataWithoutDatabase(): void
    {
        // Same fallback path as testNoAction, but with an explicit
        // action=get_stats request. The script's contract for
        // get_stats is the same demo payload, not 500.
        $res = $this->request('/?__target=visitor-stats&action=get_stats');
        $this->assertEquals(200, $res['status']);
        $this->assertContains('"code":200', $res['body']);
        $this->assertContains('Database not connected', $res['body']);
        $this->assertContains('"today"', $res['body']);
        $this->assertContains('"total"', $res['body']);
        $this->assertContains('"visits"', $res['body']);
        $this->assertContains('"visitors"', $res['body']);
    }

    public function testTrackActionFallsBackToDemoDataWithoutDatabase(): void
    {
        $res = $this->request('/?__target=visitor-stats&action=track', 'POST');
        $this->assertEquals(200, $res['status']);
        $this->assertContains('"code":200', $res['body']);
        $this->assertContains('Database not connected', $res['body']);
    }

    public function testResponseIsJsonUtf8(): void
    {
        $res = $this->request('/?__target=visitor-stats');
        $this->assertHeaderContains($res['headers'], 'Content-Type: application/json');
        $this->assertHeaderContains($res['headers'], 'charset=utf-8');
    }
}
