<?php
/**
 * 轻量级测试基类 - 为原生 PHP 项目提供确定性测试能力
 *
 * 设计目标：
 * - 不依赖 PHPUnit 等外部包（项目未使用 Composer）
 * - 支持隔离的断言、测试计数、失败详情收集
 * - 每个测试方法可独立运行，互不污染
 */
class TestCase
{
    protected int $passed = 0;
    protected int $failed = 0;
    protected array $failures = [];
    protected string $currentTest = '';

    protected function setUp(): void {}
    protected function tearDown(): void {}

    /** 测试入口：执行所有以 test 开头的方法，捕获并汇总失败 */
    public function runAll(): void
    {
        $methods = array_filter(
            get_class_methods($this),
            fn($m) => str_starts_with($m, 'test')
        );
        foreach ($methods as $method) {
            $this->currentTest = $method;
            try {
                $this->setUp();
                $this->$method();
                $this->tearDown();
                $this->passed++;
            } catch (Throwable $e) {
                $this->failed++;
                $this->failures[] = [
                    'test' => $method,
                    'error' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                ];
            }
        }
    }

    protected function assertSame($expected, $actual, string $msg = ''): void
    {
        if ($expected !== $actual) {
            throw new RuntimeException(
                ($msg ?: 'assertSame') . "\n  expected: " . var_export($expected, true)
                . "\n  actual:   " . var_export($actual, true)
            );
        }
    }

    protected function assertEquals($expected, $actual, string $msg = ''): void
    {
        if ($expected != $actual) {
            throw new RuntimeException(
                ($msg ?: 'assertEquals') . "\n  expected: " . var_export($expected, true)
                . "\n  actual:   " . var_export($actual, true)
            );
        }
    }

    protected function assertTrue(bool $value, string $msg = ''): void
    {
        if (!$value) {
            throw new RuntimeException($msg ?: 'assertTrue failed: value is false');
        }
    }

    protected function assertFalse(bool $value, string $msg = ''): void
    {
        if ($value) {
            throw new RuntimeException($msg ?: 'assertFalse failed: value is true');
        }
    }

    protected function assertNull($value, string $msg = ''): void
    {
        if ($value !== null) {
            throw new RuntimeException($msg ?: 'assertNull failed');
        }
    }

    protected function assertNotEmpty($value, string $msg = ''): void
    {
        if (empty($value)) {
            throw new RuntimeException($msg ?: 'assertNotEmpty failed');
        }
    }

    protected function assertIsArray($value, string $msg = ''): void
    {
        if (!is_array($value)) {
            throw new RuntimeException($msg ?: 'assertIsArray failed: ' . gettype($value));
        }
    }

    protected function assertIsString($value, string $msg = ''): void
    {
        if (!is_string($value)) {
            throw new RuntimeException($msg ?: 'assertIsString failed: ' . gettype($value));
        }
    }

    protected function assertIsInt($value, string $msg = ''): void
    {
        if (!is_int($value)) {
            throw new RuntimeException($msg ?: 'assertIsInt failed: ' . gettype($value));
        }
    }

    protected function assertArrayHasKey(string $key, array $arr, string $msg = ''): void
    {
        if (!array_key_exists($key, $arr)) {
            throw new RuntimeException(($msg ?: 'assertArrayHasKey') . ": missing key '$key' in " . json_encode($arr, JSON_UNESCAPED_UNICODE));
        }
    }

    protected function assertMatchesRegex(string $pattern, string $subject, string $msg = ''): void
    {
        if (!preg_match($pattern, $subject)) {
            throw new RuntimeException(($msg ?: 'assertMatchesRegex') . ": pattern '$pattern' does not match '$subject'");
        }
    }

    protected function assertGreaterThan(float $min, $value, string $msg = ''): void
    {
        if ($value <= $min) {
            throw new RuntimeException(($msg ?: 'assertGreaterThan') . ": $value <= $min");
        }
    }

    protected function assertBetween(float $min, float $max, $value, string $msg = ''): void
    {
        if ($value < $min || $value > $max) {
            throw new RuntimeException(($msg ?: 'assertBetween') . ": $value not in [$min, $max]");
        }
    }

    protected function expectException(callable $fn, string $msg = ''): void
    {
        $thrown = false;
        try {
            $fn();
        } catch (Throwable $e) {
            $thrown = true;
        }
        if (!$thrown) {
            throw new RuntimeException($msg ?: 'expectException: no exception thrown');
        }
    }

    public function report(): array
    {
        return [
            'class' => get_class($this),
            'passed' => $this->passed,
            'failed' => $this->failed,
            'failures' => $this->failures,
        ];
    }
}
