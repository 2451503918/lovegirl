<?php
/**
 * 测试运行器 - 自动发现并执行 tests/ 目录下所有 *Test.php
 *
 * 用法：php tests/run.php
 *
 * 输出彩色汇总结果，并在有失败时以非零退出码终止
 */

$testDir = __DIR__;
$testFiles = glob($testDir . '/*Test.php');

if (empty($testFiles)) {
    echo "未找到测试文件\n";
    exit(1);
}

// 颜色定义
$GREEN = "\033[32m";
$RED = "\033[31m";
$YELLOW = "\033[33m";
$BOLD = "\033[1m";
$RESET = "\033[0m";

$totalPassed = 0;
$totalFailed = 0;
$allFailures = [];

echo "{$BOLD}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━{$RESET}\n";
echo "{$BOLD}LG-NewUi 回归测试套件{$RESET}\n";
echo "{$BOLD}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━{$RESET}\n\n";

foreach ($testFiles as $file) {
    $basename = basename($file);
    if ($basename === 'TestCase.php') continue;

    echo "{$YELLOW}[运行]{$RESET} {$basename} ... ";

    // 每个测试文件在独立进程中执行，避免会话/全局污染
    $cmd = sprintf('%s %s 2>&1', escapeshellarg(PHP_BINARY), escapeshellarg($file));
    $output = [];
    $exitCode = 0;
    exec($cmd, $output, $exitCode);

    // 解析 return 语句得到的报告（PHP 的 return 在 include 中返回值，exec 拿不到）
    // 改用：要求测试文件在底部 echo JSON 报告
    $raw = implode("\n", $output);
    $jsonPos = strrpos($raw, '___TEST_JSON___');
    if ($jsonPos === false) {
        // 兼容模式：检查有无失败文本
        $report = ['passed' => 0, 'failed' => 0, 'failures' => []];
        // 尝试解析新格式（带 REPORT_MARKER）
        $lines = array_filter($output, 'trim');
        $last = end($lines);
        if ($last && ($data = json_decode($last, true))) {
            $report = $data;
        } else {
            // 回退：假设成功（或失败）
            $report = ['passed' => 1, 'failed' => ($exitCode !== 0 ? 1 : 0), 'failures' => []];
        }
    } else {
        $json = substr($raw, $jsonPos + strlen('___TEST_JSON___'));
        $report = json_decode(trim($json), true) ?? ['passed' => 0, 'failed' => 1, 'failures' => [['test' => 'parse', 'error' => $raw]]];
    }

    // 实际更简单的方式：读取测试文件的 output echo
    // 测试文件末尾是 `echo json_encode(...); return ...;`
    // 我们以最后一行为 JSON 报告
    $lines = array_values(array_filter(array_map('trim', $output)));
    $lastLine = end($lines);
    $parsed = json_decode($lastLine, true);
    if ($parsed && isset($parsed['passed'])) {
        $report = $parsed;
    }

    $passed = $report['passed'] ?? 0;
    $failed = $report['failed'] ?? 0;
    $failures = $report['failures'] ?? [];

    if ($failed === 0 && $passed > 0) {
        echo "{$GREEN}✓ 通过{$RESET} ({$passed} 个断言)\n";
    } elseif ($failed > 0) {
        echo "{$RED}✗ 失败{$RESET} ({$passed} 通过, {$failed} 失败)\n";
        foreach ($failures as $f) {
            echo "    {$RED}- " . ($f['test'] ?? 'unknown')
                . ": " . ($f['error'] ?? '') . "{$RESET}\n";
            if (!empty($f['file'])) {
                echo "      {$f['file']}:{$f['line']}\n";
            }
        }
    } else {
        echo "{$YELLOW}? 未知{$RESET}\n";
        if (!empty($lines)) {
            echo "    输出:\n";
            foreach (array_slice($lines, 0, 5) as $l) echo "      $l\n";
        }
    }

    $totalPassed += $passed;
    $totalFailed += $failed;
    foreach ($failures as $f) {
        $allFailures[] = ['file' => $basename, 'test' => $f['test'] ?? '?', 'error' => $f['error'] ?? '?'];
    }
}

echo "\n{$BOLD}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━{$RESET}\n";
$total = $totalPassed + $totalFailed;
if ($totalFailed === 0 && $total > 0) {
    echo "{$GREEN}{$BOLD}结果：全部通过{$RESET}  {$totalPassed}/{$total}\n";
    exit(0);
} elseif ($total === 0) {
    echo "{$YELLOW}未执行任何测试{$RESET}\n";
    exit(1);
} else {
    echo "{$RED}{$BOLD}结果：存在失败{$RESET}  {$totalPassed}/{$total} 通过，{$totalFailed} 个失败\n";
    echo "\n失败详情：\n";
    foreach ($allFailures as $f) {
        echo "  {$RED}- {$f['file']}::{$f['test']} -> {$f['error']}{$RESET}\n";
    }
    exit(1);
}
