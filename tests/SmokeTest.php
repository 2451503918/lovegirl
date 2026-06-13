<?php
/**
 * Smoke test for the harness itself.
 *
 * This file is auto-discovered by runner.php. It exists to make sure the
 * built-in PHP server is reachable and the test request() helper returns
 * the expected response shape. It does not exercise any production code.
 */
declare(strict_types=1);

class SmokeTest extends LgTestCase
{
    public function testServerResponds(): void
    {
        $res = $this->request('/?__target=missing-target');
        // Unknown target should produce a 400 with plain text "unknown target".
        $this->assertEquals(400, $res['status']);
        $this->assertContains('unknown target', $res['body']);
    }

    public function testRequestReturnsExpectedShape(): void
    {
        $res = $this->request('/?__target=missing-target');
        $this->assertTrue(is_array($res['headers']));
        $this->assertTrue(is_string($res['body']));
        $this->assertTrue(is_int($res['status']));
    }
}
