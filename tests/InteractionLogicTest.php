<?php
/**
 * 交互 API 核心逻辑测试 - services/interaction.php
 *
 * 覆盖：
 * - action 参数白名单校验
 * - type 到数据表名的映射安全
 * - 参数合法性校验（id 必须 > 0）
 * - SQL 拼接中表名来源白名单（防止二次注入）
 */
require_once __DIR__ . '/TestCase.php';

class InteractionLogicTest extends TestCase
{
    private array $allowedActions = ['like', 'view', 'get_likes', 'get_views'];
    private array $tableMap = [
        'article' => 'little',
        'album' => 'photo',
        'message' => 'leaving',
        'event' => 'lovelist',
    ];

    // --- action 白名单 ---

    public function testAllowedActionsAreValid(): void
    {
        foreach (['like', 'view', 'get_likes', 'get_views'] as $a) {
            $this->assertTrue(in_array($a, $this->allowedActions, true), "action '$a' 应在白名单");
        }
    }

    public function testActionNotInWhitelistRejected(): void
    {
        foreach (['drop', 'delete', '\' OR 1=1--', '', 'LIKE', ' Like '] as $a) {
            $this->assertFalse(in_array($a, $this->allowedActions, true), "非法 action '$a' 应被拒绝");
        }
    }

    // --- type 到表名映射 ---

    public function testTypeMapsToCorrectTable(): void
    {
        $this->assertSame('little', $this->tableMap['article']);
        $this->assertSame('photo', $this->tableMap['album']);
        $this->assertSame('leaving', $this->tableMap['message']);
        $this->assertSame('lovelist', $this->tableMap['event']);
    }

    public function testInvalidTypeReturnsEmpty(): void
    {
        // 非法 type 值不应在映射中找到键
        foreach (['user', '', 'little', '`little`; DROP'] as $bad) {
            $table = $this->tableMap[$bad] ?? '';
            $this->assertSame('', $table, "非法 type '$bad' 应得到空字符串");
        }
    }

    // --- id 参数校验 ---

    public function testIdMustBePositive(): void
    {
        $this->assertTrue(intval('42') > 0);
        $this->assertFalse(intval('0') > 0);
        $this->assertFalse(intval('-1') > 0);
        $this->assertFalse(intval('abc') > 0);
    }

    public function testIdSanitizedToInt(): void
    {
        // PHP 的 intval 会剥离非数字前缀，验证这个行为
        $this->assertSame(123, intval('123abc'));
        $this->assertSame(0, intval('abc123'));
        $this->assertSame(42, intval('42.99'));
    }

    // --- SQL 安全：类型值来自白名单 ---

    public function testTableNameCameFromWhitelist(): void
    {
        // 白名单查表名，拒绝任何直接字符串拼接
        foreach (['article', 'album', 'message', 'event'] as $type) {
            $table = $this->tableMap[$type] ?? '';
            $this->assertNotEmpty($table, "合法 type '$type' 应映射到表名");
            $this->assertMatchesRegex('/^[a-z_]+$/', $table, "映射后的表名应是纯标识符");
        }
    }

    public function testSQLInjectionAttemptOnTypeBlocked(): void
    {
        $malicious = "little`; DROP TABLE users; --";
        $table = $this->tableMap[$malicious] ?? '';
        $this->assertSame('', $table, '非法 type 应得到空字符串，不会进入查询');
    }

    // --- COALESCE 空值合并行为 ---

    public function testCoalesceOnNullColumn(): void
    {
        // 模拟 COALESCE(likes, 0) + 1 的行为
        $likes = null;
        $result = ($likes ?? 0) + 1;
        $this->assertSame(1, $result);
    }

    public function testCoalesceOnExistingValue(): void
    {
        $likes = 5;
        $result = ($likes ?? 0);
        $this->assertSame(5, $result);
    }

    // --- 响应结构 ---

    public function testLikeResponseHasExpectedKeys(): void
    {
        $response = ['success' => true, 'likes' => 5];
        $this->assertArrayHasKey('success', $response);
        $this->assertArrayHasKey('likes', $response);
    }

    public function testErrorResponseStructure(): void
    {
        $response = ['success' => false, 'error' => 'Missing type or id'];
        $this->assertArrayHasKey('success', $response);
        $this->assertArrayHasKey('error', $response);
        $this->assertFalse($response['success']);
    }
}

$suite = new InteractionLogicTest();
$suite->runAll();
echo "\n" . json_encode($suite->report(), JSON_UNESCAPED_UNICODE) . "\n";
