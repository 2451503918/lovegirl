#!/bin/bash

echo "=============================================="
echo "       前端综合功能测试报告"
echo "=============================================="
echo ""

echo "1. HTTP 状态码检查"
echo "----------------------------------------------"
INDEX_HTTP=$(curl -s -o /dev/null -w "%{http_code}" http://localhost:8000/index.php)
TESTUI_HTTP=$(curl -s -o /dev/null -w "%{http_code}" http://localhost:8000/test-ui.php)
echo "   index.php:   $INDEX_HTTP"
echo "   test-ui.php: $TESTUI_HTTP"
echo ""

echo "2. 核心组件渲染检查"
echo "----------------------------------------------"
check_component() {
    COUNT=$(curl -s http://localhost:8000/index.php | grep -c "$1")
    if [ $COUNT -gt 0 ]; then
        echo "   ✅ $2: 找到 $COUNT 个"
    else
        echo "   ❌ $2: 未找到"
    fi
}

check_component "lgnewui-day-fusion-card" "天数计数器"
check_component "lgnewui-smart-card" "智能卡片"
check_component "lgnewui-home-weather-card" "天气卡片"
check_component "lgnewui-map-card" "地图卡片"
check_component "lgnewui-visitor-stats" "访客统计"
check_component "lovers-panel" "情侣面板"
check_component "lgnewui-grid" "Grid布局"
check_component "lgnewui-section" "功能区块"
check_component "lgnewui-widget" "组件容器"
echo ""

echo "3. CSS/JS 资源检查"
echo "----------------------------------------------"
CSS_COUNT=$(curl -s http://localhost:8000/index.php | grep -oP 'href="[^"]*\.css[^"]*"' | wc -l)
JS_COUNT=$(curl -s http://localhost:8000/index.php | grep -oP 'src="[^"]*\.js[^"]*"' | wc -l)
echo "   CSS 文件: $CSS_COUNT 个"
echo "   JS 文件:  $JS_COUNT 个"
echo ""

echo "4. 页面大小与内容长度"
echo "----------------------------------------------"
PAGE_SIZE=$(curl -s http://localhost:8000/index.php | wc -c)
TEXT_LENGTH=$(curl -s http://localhost:8000/index.php | grep -oP '[\u4e00-\u9fff]' | wc -l)
echo "   总大小: $PAGE_SIZE bytes"
echo "   中文文本长度: ~$TEXT_LENGTH 字符"
echo ""

echo "5. 关键 CSS 类检查"
echo "----------------------------------------------"
check_css() {
    if curl -s http://localhost:8000/index.php | grep -q "$1"; then
        echo "   ✅ $2"
    else
        echo "   ❌ $2"
    fi
}
check_css "grid-template-columns" "响应式网格"
check_css "flex-direction" "Flex布局"
check_css "rgba" "半透明效果"
check_css "border-radius" "圆角"
check_css "box-shadow" "阴影"
check_css "gradient" "渐变"
echo ""

echo "6. 移动端适配检查 (head.php)"
echo "----------------------------------------------"
if grep -q "@media (max-width: 480px)" /workspace/head.php; then
    echo "   ✅ 480px 移动端断点"
else
    echo "   ❌ 480px 移动端断点"
fi
if grep -q "flex-direction.*column" /workspace/head.php; then
    echo "   ✅ 纵向布局适配"
else
    echo "   ❌ 纵向布局适配"
fi
if grep -q "375px" /workspace/head.php; then
    echo "   ✅ 375px 小屏优化"
else
    echo "   ❌ 375px 小屏优化"
fi
echo ""

echo "7. PHP 语法检查"
echo "----------------------------------------------"
PHP_ERRORS=$(find /workspace -name "*.php" -not -path "*/vendor/*" -type f 2>/dev/null | xargs php -l 2>&1 | grep -c "error\|Errors")
PHP_OK=$(find /workspace -name "*.php" -not -path "*/vendor/*" -type f 2>/dev/null | xargs php -l 2>&1 | grep -c "No syntax errors")
echo "   语法检查通过: $PHP_OK 个文件"
echo "   错误: $PHP_ERRORS"
echo ""

echo "=============================================="
echo "       测试报告完成"
echo "=============================================="
