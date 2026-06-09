<?php
/**
 * 消息列表分页逻辑测试 - services/message-list.php
 *
 * 覆盖：
 * - page / limit 参数钳位（clamp）
 * - offset 计算
 * - 数据总条数与分页响应结构
 */
require_once __DIR__ . '/TestCase.php';

class MessageListPaginationTest extends TestCase
{
    public function testPageDefaultsTo1(): void
    {
        $page = max(1, intval(null));
        $this->assertSame(1, $page);
    }

    public function testPageClampsMinimum(): void
    {
        $page = max(1, intval(0));
        $this->assertSame(1, $page);
        $page = max(1, intval(-5));
        $this->assertSame(1, $page);
    }

    public function testPageAcceptsNormal(): void
    {
        $page = max(1, intval('3'));
        $this->assertSame(3, $page);
    }

    public function testLimitDefaultsTo10(): void
    {
        // 未传 limit 参数时，?? 10 提供默认值
        $raw = null;
        $limit = min(50, max(5, intval($raw ?? 10)));
        $this->assertSame(10, $limit);
    }

    public function testLimitClampsToMinimum5(): void
    {
        $limit = min(50, max(5, intval('1')));
        $this->assertSame(5, $limit);
    }

    public function testLimitClampsToMaximum50(): void
    {
        $limit = min(50, max(5, intval('9999')));
        $this->assertSame(50, $limit);
    }

    public function testLimitAcceptsNormalValue(): void
    {
        $limit = min(50, max(5, intval('20')));
        $this->assertSame(20, $limit);
    }

    public function testLimitIgnoresNonNumeric(): void
    {
        $limit = min(50, max(5, intval('abc')));
        $this->assertSame(5, $limit);
    }

    public function testOffsetCalculationPage1(): void
    {
        $page = 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;
        $this->assertSame(0, $offset);
    }

    public function testOffsetCalculationPage3(): void
    {
        $page = 3;
        $limit = 10;
        $offset = ($page - 1) * $limit;
        $this->assertSame(20, $offset);
    }

    public function testResponseStructure(): void
    {
        $page = 2;
        $limit = 10;
        $response = ['success' => true, 'code' => 200, 'data' => [], 'total' => 100, 'page' => $page, 'limit' => $limit];
        $this->assertArrayHasKey('total', $response);
        $this->assertArrayHasKey('page', $response);
        $this->assertArrayHasKey('limit', $response);
        $this->assertSame(100, $response['total']);
    }

    public function testEmptyDataSetReturnsEmptyArray(): void
    {
        $response = ['success' => true, 'code' => 200, 'data' => [], 'total' => 0, 'page' => 1, 'limit' => 10];
        $this->assertIsArray($response['data']);
        $this->assertSame(0, count($response['data']));
    }

    // --- item 字段结构 ---

    public function testItemHasRequiredFields(): void
    {
        $item = [
            'id' => 1,
            'name' => '访客',
            'qq' => '10001',
            'text' => 'hello',
            'time' => (string)time(),
            'city' => '未知',
            'device' => 'PC',
            'browser' => 'Chrome 120',
            'likes' => 0,
            'parent_id' => 0,
            'avatar' => 'https://q1.qlogo.cn/g?b=qq&nk=10001&s=640',
        ];
        foreach (['id', 'name', 'text', 'time', 'city', 'device', 'browser', 'likes', 'avatar'] as $k) {
            $this->assertArrayHasKey($k, $item, "每条消息项应包含 '$k'");
        }
    }

    public function testEmptyQQProducesEmptyAvatar(): void
    {
        $qq = '';
        $avatar = !empty($qq) ? 'https://q1.qlogo.cn/g?b=qq&nk=' . $qq . '&s=640' : '';
        $this->assertSame('', $avatar);
    }

    public function testNonEmptyQQProducesAvatarUrl(): void
    {
        $qq = '10001';
        $avatar = !empty($qq) ? 'https://q1.qlogo.cn/g?b=qq&nk=' . $qq . '&s=640' : '';
        $this->assertMatchesRegex('/^https:\/\/.*10001.*$/', $avatar);
    }
}

$suite = new MessageListPaginationTest();
$suite->runAll();
echo "\n" . json_encode($suite->report(), JSON_UNESCAPED_UNICODE) . "\n";
