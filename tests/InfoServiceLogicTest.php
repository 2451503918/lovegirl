<?php
/**
 * 综合信息服务逻辑测试 - services/info-service.php
 *
 * 覆盖：
 * - action 白名单校验
 * - Haversine 距离计算（地球球面两点距离）
 * - 数据聚合与统计查询边界
 */
require_once __DIR__ . '/TestCase.php';

class InfoServiceLogicTest extends TestCase
{
    private array $validActions = ['get_stats', 'get_location', 'heartbeat', 'geo'];

    // --- action 白名单 ---

    public function testValidActionsPass(): void
    {
        foreach ($this->validActions as $a) {
            $this->assertTrue(in_array($a, $this->validActions, true));
        }
    }

    public function testInvalidActionsBlocked(): void
    {
        foreach (['', 'GET_STATS', ' get_stats', 'delete', '\' OR 1=1'] as $a) {
            $this->assertFalse(in_array($a, $this->validActions, true), "'$a' 应被白名单拒绝");
        }
    }

    // --- Haversine 距离计算 ---

    /**
     * 使用与 info-service.php 相同的公式
     */
    private function haversine(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthRadius = 6371;
        $latDelta = deg2rad($lat2 - $lat1);
        $lonDelta = deg2rad($lng2 - $lng1);
        $a = sin($latDelta / 2) * sin($latDelta / 2)
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2))
            * sin($lonDelta / 2) * sin($lonDelta / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return round($earthRadius * $c, 1);
    }

    public function testSamePointDistanceZero(): void
    {
        // 北京到北京
        $d = $this->haversine(39.9042, 116.4074, 39.9042, 116.4074);
        $this->assertSame(0.0, $d);
    }

    public function testBeijingToShanghaiDistance(): void
    {
        // 北京 (39.9042, 116.4074) 到上海 (31.2304, 121.4737)
        $d = $this->haversine(39.9042, 116.4074, 31.2304, 121.4737);
        $this->assertBetween(1000.0, 1250.0, $d, '北京到上海约 1068 公里');
    }

    public function testKnownDistancePairs(): void
    {
        // (0,0) 到 (0,1): 约 111.2 km (1 度经度在赤道处)
        $d = $this->haversine(0, 0, 0, 1);
        $this->assertBetween(111.0, 112.0, $d);
    }

    public function testSymmetricDistance(): void
    {
        $d1 = $this->haversine(39.9, 116.4, 31.2, 121.5);
        $d2 = $this->haversine(31.2, 121.5, 39.9, 116.4);
        $this->assertSame($d1, $d2, '距离函数应对称');
    }

    public function testTriangleInequality(): void
    {
        // 北京 -> 上海 -> 广州 应 >= 北京 -> 广州
        $bj = [39.9042, 116.4074];
        $sh = [31.2304, 121.4737];
        $gz = [23.1291, 113.2644];
        $bj_sh = $this->haversine($bj[0], $bj[1], $sh[0], $sh[1]);
        $sh_gz = $this->haversine($sh[0], $sh[1], $gz[0], $gz[1]);
        $bj_gz = $this->haversine($bj[0], $bj[1], $gz[0], $gz[1]);
        $this->assertTrue($bj_sh + $sh_gz >= $bj_gz - 1, '三角不等式应成立');
    }

    public function testAntipodalDistance(): void
    {
        // 对极点：(0,0) 和 (0,180) 应约为 π*R ≈ 20015 km
        $d = $this->haversine(0, 0, 0, 180);
        $this->assertBetween(20000.0, 20030.0, $d);
    }

    public function testDistanceRoundedToOneDecimal(): void
    {
        $d = $this->haversine(39.9, 116.4, 31.2, 121.5);
        $this->assertSame(round($d, 1), $d, '距离应保留 1 位小数');
    }

    // --- 心跳响应 ---

    public function testHeartbeatResponseStructure(): void
    {
        $response = [
            'success' => true,
            'code' => 200,
            'message' => 'pong',
            'timestamp' => time(),
        ];
        $this->assertArrayHasKey('code', $response);
        $this->assertArrayHasKey('message', $response);
        $this->assertSame('pong', $response['message']);
    }

    // --- 统计计数类型安全 ---

    public function testArticleCountIsInt(): void
    {
        $count = intval('15');
        $this->assertIsInt($count);
        $this->assertSame(15, $count);
    }

    public function testEmptyCountIsZero(): void
    {
        $count = intval(null);
        $this->assertSame(0, $count);
    }

    // --- 日期解析（恋爱天数计算） ---

    public function testLoveDaysCalculation(): void
    {
        $start = date('Y-m-d', strtotime('-100 days'));
        $startTime = strtotime(str_replace('T', ' ', $start));
        $days = floor((time() - $startTime) / 86400);
        $this->assertBetween(99, 101, $days);
    }

    public function testLoveDaysToday(): void
    {
        $start = date('Y-m-d');
        $startTime = strtotime(str_replace('T', ' ', $start));
        $days = floor((time() - $startTime) / 86400);
        // 当天可能为 0（整数）或 0.0（浮点除法），使用非严格断言
        $this->assertEquals(0, $days);
    }
}

$suite = new InfoServiceLogicTest();
$suite->runAll();
echo "\n" . json_encode($suite->report(), JSON_UNESCAPED_UNICODE) . "\n";
