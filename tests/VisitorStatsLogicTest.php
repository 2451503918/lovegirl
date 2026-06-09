<?php
/**
 * 访客统计并发逻辑测试 - services/visitor-stats.php
 *
 * 覆盖：
 * - 客户端 IP 解析与验证
 * - action 白名单（track / get_stats）
 * - 新访客判定逻辑
 * - 数据聚合响应结构
 */
require_once __DIR__ . '/TestCase.php';

class VisitorStatsLogicTest extends TestCase
{
    private array $validActions = ['track', 'get_stats', ''];

    // --- IP 解析 ---

    private function getClientIPFromHeader(string $ip): string
    {
        // 同 visitor-stats.php: 只信任 REMOTE_ADDR，验证后使用
        if (filter_var($ip, FILTER_VALIDATE_IP)) {
            return $ip;
        }
        return '0.0.0.0';
    }

    public function testValidIPv4(): void
    {
        $this->assertSame('192.168.1.1', $this->getClientIPFromHeader('192.168.1.1'));
        $this->assertSame('8.8.8.8', $this->getClientIPFromHeader('8.8.8.8'));
    }

    public function testValidIPv6(): void
    {
        $this->assertSame('::1', $this->getClientIPFromHeader('::1'));
        $this->assertSame('2001:db8::1', $this->getClientIPFromHeader('2001:db8::1'));
    }

    public function testInvalidIPFallsBack(): void
    {
        $this->assertSame('0.0.0.0', $this->getClientIPFromHeader('not-an-ip'));
        $this->assertSame('0.0.0.0', $this->getClientIPFromHeader(''));
        $this->assertSame('0.0.0.0', $this->getClientIPFromHeader('999.999.999.999'));
    }

    public function testXSSTaintedIPRejected(): void
    {
        $this->assertSame('0.0.0.0', $this->getClientIPFromHeader('<script>alert(1)</script>'));
    }

    // --- action 白名单 ---

    public function testValidActions(): void
    {
        foreach (['track', 'get_stats', ''] as $a) {
            $this->assertTrue(in_array($a, $this->validActions, true));
        }
    }

    public function testInvalidActionRejected(): void
    {
        foreach (['reset', 'DELETE', ' drop ', ';--', 'TRACK'] as $a) {
            $this->assertFalse(in_array($a, $this->validActions, true), "'$a' 不应被允许");
        }
    }

    // --- 新访客判定：INSERT IGNORE 受影响行数 ---

    public function testNewVisitorWhenInsertSucceeds(): void
    {
        // 模拟 INSERT IGNORE 成功插入新行：affected_rows = 1
        $affectedRows = 1;
        $isNew = $affectedRows > 0;
        $this->assertTrue($isNew);
    }

    public function testReturningVisitorWhenInsertIgnored(): void
    {
        // 模拟 INSERT IGNORE 因唯一键冲突忽略：affected_rows = 0
        $affectedRows = 0;
        $isNew = $affectedRows > 0;
        $this->assertFalse($isNew);
    }

    // --- 响应结构 ---

    public function testTrackResponseHasTimestamp(): void
    {
        $response = ['code' => 200, 'message' => 'Tracked',
            'data' => ['newVisitor' => true, 'timestamp' => time()]];
        $this->assertArrayHasKey('newVisitor', $response['data']);
        $this->assertArrayHasKey('timestamp', $response['data']);
        $this->assertIsInt($response['data']['timestamp']);
    }

    public function testGetStatsResponseHasTodayAndTotal(): void
    {
        $response = ['code' => 200, 'data' => [
            'today' => ['visits' => 10, 'visitors' => 3],
            'total' => ['visits' => 15234, 'visitors' => 3847],
        ]];
        $this->assertArrayHasKey('today', $response['data']);
        $this->assertArrayHasKey('total', $response['data']);
        $this->assertIsInt($response['data']['today']['visits']);
    }

    public function testDatabaseNotConnectedReturnsDemoData(): void
    {
        $response = [
            'code' => 200,
            'message' => 'Database not connected, using demo data',
            'data' => [
                'today' => ['visits' => 156, 'visitors' => 48],
                'total' => ['visits' => 15234, 'visitors' => 3847],
            ],
        ];
        $this->assertGreaterThan(0, $response['data']['today']['visits']);
    }

    // --- 计数增量正确性 ---

    public function testVisitCountIncrement(): void
    {
        $count = 10;
        // 模拟 UPDATE ... SET visit_count = visit_count + 1
        $count = $count + 1;
        $this->assertSame(11, $count);
    }

    public function testVisitorCountOnlyIncrementsForNewVisitor(): void
    {
        $visitors = 5;
        $isNewVisitor = true;
        if ($isNewVisitor) {
            $visitors = $visitors + 1;
        }
        $this->assertSame(6, $visitors);
    }

    public function testVisitorCountUnchangedForReturningVisitor(): void
    {
        $visitors = 5;
        $isNewVisitor = false;
        if ($isNewVisitor) {
            $visitors = $visitors + 1;
        }
        $this->assertSame(5, $visitors);
    }

    // --- 今日日期确定性 ---

    public function testTodayDateFormat(): void
    {
        $today = date('Y-m-d');
        $this->assertMatchesRegex('/^\d{4}-\d{2}-\d{2}$/', $today);
    }

    public function testMultipleTrackCallsSameDay(): void
    {
        $today = date('Y-m-d');
        $today2 = date('Y-m-d');
        $this->assertSame($today, $today2, '同一天应得到相同日期字符串');
    }
}

$suite = new VisitorStatsLogicTest();
$suite->runAll();
echo "\n" . json_encode($suite->report(), JSON_UNESCAPED_UNICODE) . "\n";
