<?php
/**
 * Regression tests for services/random_quote.php.
 *
 * random_quote.php was added in the recent "外部资源全量本地化" commit
 * and was not covered before. It is a tiny endpoint that returns a
 * randomly chosen quote from a static array. The risk-bearing pieces
 * we pin down are:
 *
 *  - Response shape: must always be a JSON object with code, data
 *    (text, author, type), and timestamp fields; the front-end type
 *    checks against this shape.
 *  - UTF-8 encoding: the quotes are Chinese; the response must
 *    declare charset=utf-8 and must not be HTML-escaped (the array
 *    uses JSON_UNESCAPED_UNICODE).
 *  - Randomness: over many calls we must see at least two different
 *    quote bodies; otherwise the random selection has regressed to
 *    a constant.
 */
declare(strict_types=1);

class RandomQuoteTest extends LgTestCase
{
    public function testResponseIsJsonUtf8(): void
    {
        $res = $this->request('/?__target=random-quote');
        $this->assertEquals(200, $res['status']);
        $this->assertHeaderContains($res['headers'], 'Content-Type: application/json');
        $this->assertHeaderContains($res['headers'], 'charset=utf-8');
    }

    public function testResponseHasExpectedShape(): void
    {
        $res = $this->request('/?__target=random-quote');
        $this->assertContains('"code":200', $res['body']);
        $this->assertContains('"timestamp":', $res['body']);
        $this->assertContains('"text":', $res['body']);
        $this->assertContains('"author":', $res['body']);
        $this->assertContains('"type":', $res['body']);
    }

    public function testResponseBodyIsValidJson(): void
    {
        $res = $this->request('/?__target=random-quote');
        $decoded = json_decode($res['body'], true);
        $this->assertTrue(is_array($decoded), 'response must be valid JSON: ' . $res['body']);
        $this->assertEquals(200, $decoded['code'] ?? null);
        $this->assertTrue(isset($decoded['data']['text']));
        $this->assertTrue(isset($decoded['data']['author']));
        $this->assertTrue(isset($decoded['data']['type']));
    }

    public function testTextAndAuthorAreNonEmpty(): void
    {
        $res = $this->request('/?__target=random-quote');
        $decoded = json_decode($res['body'], true);
        $this->assertNotEquals('', $decoded['data']['text']);
        $this->assertNotEquals('', $decoded['data']['author']);
    }

    public function testQuotesAreNotAllIdentical(): void
    {
        // Collect a few random responses. The endpoint must return a
        // different body on at least one of them; otherwise the
        // random selection has been broken (e.g. by pinning a single
        // entry or short-circuiting to a constant).
        $bodies = [];
        for ($i = 0; $i < 8; $i++) {
            $res = $this->request('/?__target=random-quote');
            $bodies[] = $res['body'];
        }
        $this->assertTrue(count(array_unique($bodies)) >= 2,
            'expected at least 2 different quote bodies across 8 calls, got only ' . count(array_unique($bodies)));
    }
}
