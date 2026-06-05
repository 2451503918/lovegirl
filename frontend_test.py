#!/usr/bin/env python3
"""
综合 Playwright 前端测试脚本
检查 http://localhost:8000/index.php 和 http://localhost:8000/test-ui.php
在桌面 (1440x900) 和移动 (375x812) 视口下测试
"""

import os
import sys
from datetime import datetime
from playwright.sync_api import sync_playwright, Page, ConsoleMessage, Response

# 配置
PAGES = [
    {"url": "http://localhost:8000/index.php", "name": "index"},
    {"url": "http://localhost:8000/test-ui.php", "name": "test-ui"},
]
VIEWPORTS = [
    {"width": 1440, "height": 900, "label": "desktop"},
    {"width": 375, "height": 812, "label": "mobile"},
]
SCREENSHOT_DIR = "/tmp"

# 需要检查的关键元素选择器
KEY_ELEMENTS = {
    "day_counter": [
        ".day-counter",
        "#day-counter",
        "[class*='day']",
        "[class*='counter']",
        "h1", "h2",
    ],
    "grid": [
        ".grid",
        "#grid",
        "[class*='grid']",
        ".dashboard-grid",
        ".card-grid",
    ],
    "weather_cards": [
        ".weather-card",
        "[class*='weather']",
        ".card",
    ],
    "stats_cards": [
        ".stats-card",
        ".stat-card",
        "[class*='stats']",
        "[class*='stat']",
    ],
    "map_card": [
        ".map-card",
        "[class*='map']",
        "#map",
    ],
    "visitor_stats": [
        ".visitor-stats",
        "[class*='visitor']",
        "[class*='visitors']",
    ],
    "sections": [
        "section",
        ".section",
        "main",
    ],
}

# CSS 属性检查
CSS_CHECKS = {
    "body": {
        "selector": "body",
        "properties": ["background", "backgroundColor", "backgroundImage"],
    },
    "grid": {
        "selector": ".grid, #grid, [class*='grid']",
        "properties": ["display", "gridTemplateColumns", "gap", "gridTemplateRows"],
    },
}


class FrontendTester:
    def __init__(self):
        self.results = {}
        self.all_issues = []

    def log_issue(self, page_name, viewport_label, category, message, severity="warning"):
        issue = {
            "page": page_name,
            "viewport": viewport_label,
            "category": category,
            "message": message,
            "severity": severity,
        }
        self.all_issues.append(issue)
        icon = "🔴" if severity == "error" else "🟡"
        print(f"  {icon} [{severity.upper()}] {category}: {message}")

    def log_ok(self, message):
        print(f"  ✅ {message}")

    def test_page_viewport(self, page, page_config, viewport_config):
        page_name = page_config["name"]
        url = page_config["url"]
        vp_label = viewport_config["label"]
        combo_key = f"{page_name}_{vp_label}"

        print(f"\n{'='*60}")
        print(f"测试: {page_name} @ {vp_label} ({viewport_config['width']}x{viewport_config['height']})")
        print(f"URL: {url}")
        print(f"{'='*60}")

        # 收集控制台消息
        console_errors = []
        console_warnings = []
        network_errors = []

        def on_console(msg):
            if msg.type == "error":
                console_errors.append({
                    "text": msg.text,
                    "location": f"{msg.location.get('url', '')}:{msg.location.get('lineNumber', '')}"
                })
            elif msg.type == "warning":
                console_warnings.append({
                    "text": msg.text,
                    "location": f"{msg.location.get('url', '')}:{msg.location.get('lineNumber', '')}"
                })

        def on_response(response):
            if response.status >= 400:
                network_errors.append({
                    "url": response.url,
                    "status": response.status,
                    "statusText": response.status_text
                })

        page.on("console", on_console)
        page.on("response", on_response)

        # 设置视口并导航
        page.set_viewport_size({"width": viewport_config["width"], "height": viewport_config["height"]})
        try:
            page.goto(url, wait_until="networkidle", timeout=30000)
        except Exception as e:
            self.log_issue(page_name, vp_label, "navigation", f"页面加载失败: {e}", "error")
            try:
                page.screenshot(path=os.path.join(SCREENSHOT_DIR, f"{combo_key}_error.png"))
            except Exception:
                pass
            return

        # 额外等待
        page.wait_for_timeout(2000)

        # 1. 控制台错误
        print("\n📋 控制台消息:")
        if console_errors:
            for err in console_errors:
                self.log_issue(page_name, vp_label, "console_error", f"{err['text']}", "error")
        else:
            self.log_ok("无控制台错误")

        if console_warnings:
            for warn in console_warnings[:10]:
                self.log_issue(page_name, vp_label, "console_warning", f"{warn['text']}", "warning")
        else:
            self.log_ok("无控制台警告")

        # 2. 网络错误
        print("\n🌐 网络请求:")
        if network_errors:
            for nerr in network_errors:
                self.log_issue(
                    page_name, vp_label, "network_error",
                    f"请求失败: {nerr['url']} -> {nerr['status']} {nerr['statusText']}", "error"
                )
        else:
            self.log_ok("无网络错误")

        # 3. 关键元素可见性
        print("\n🔍 关键元素可见性:")
        for elem_name, selectors in KEY_ELEMENTS.items():
            found = False
            for sel in selectors:
                try:
                    count = page.locator(sel).count()
                    if count > 0:
                        visible = page.locator(sel).first.is_visible()
                        if visible:
                            self.log_ok(f"{elem_name} (选择器: {sel}) - 可见 (共{count}个)")
                            found = True
                            break
                        else:
                            self.log_issue(
                                page_name, vp_label, "element_hidden",
                                f"{elem_name} 存在但不可见 (选择器: {sel})", "warning"
                            )
                            found = True
                            break
                except Exception:
                    continue
            if not found:
                self.log_issue(
                    page_name, vp_label, "element_missing",
                    f"{elem_name} 未找到 (尝试了所有选择器)", "warning"
                )

        # 4. CSS 属性检查
        print("\n🎨 CSS属性检查:")
        for css_name, css_config in CSS_CHECKS.items():
            try:
                el = page.locator(css_config["selector"]).first
                if el.count() > 0:
                    for prop in css_config["properties"]:
                        value = el.evaluate(
                            f"el => getComputedStyle(el).{prop}"
                        )
                        self.log_ok(f"  {css_name}.{prop} = {value}")
                else:
                    self.log_issue(page_name, vp_label, "css", f"CSS选择器未匹配: {css_name} ({css_config['selector']})", "warning")
            except Exception as e:
                self.log_issue(
                    page_name, vp_label, "css",
                    f"CSS检查失败: {css_name} - {e}", "warning"
                )

        # 5. Body CSS 额外检查
        try:
            body_bg = page.evaluate("() => getComputedStyle(document.body).background")
            body_bgcolor = page.evaluate("() => getComputedStyle(document.body).backgroundColor")
            print(f"  body.background: {body_bg}")
            print(f"  body.backgroundColor: {body_bgcolor}")
        except Exception as e:
            self.log_issue(page_name, vp_label, "css_body", f"Body CSS检查失败: {e}", "warning")

        # 6. 布局溢出检查
        try:
            overflow = page.evaluate("""() => {
                const body = document.body;
                const html = document.documentElement;
                return {
                    scrollWidth: body.scrollWidth,
                    clientWidth: html.clientWidth,
                    hasHorizontalScroll: body.scrollWidth > html.clientWidth,
                };
            }""")
            if overflow["hasHorizontalScroll"]:
                self.log_issue(page_name, vp_label, "layout", f"页面存在水平滚动条 (scrollWidth={overflow['scrollWidth']}, clientWidth={overflow['clientWidth']})", "warning")
            else:
                self.log_ok(f"无水平溢出 (scrollWidth={overflow['scrollWidth']}, clientWidth={overflow['clientWidth']})")
        except Exception as e:
            self.log_issue(page_name, vp_label, "layout_check", f"布局检查失败: {e}", "warning")

        # 7. 截图
        screenshot_path = os.path.join(SCREENSHOT_DIR, f"{combo_key}.png")
        try:
            page.screenshot(path=screenshot_path, full_page=True)
            print(f"\n📸 截图已保存: {screenshot_path}")
        except Exception as e:
            self.log_issue(page_name, vp_label, "screenshot", f"截图失败: {e}", "warning")

        self.results[combo_key] = {
            "console_errors": len(console_errors),
            "console_warnings": len(console_warnings),
            "network_errors": len(network_errors),
            "screenshot": screenshot_path,
        }

    def run(self):
        print("=" * 70)
        print("  前端综合测试 - Playwright")
        print(f"  时间: {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}")
        print("=" * 70)

        with sync_playwright() as p:
            browser = p.chromium.launch(headless=True)
            context = browser.new_context()

            for page_config in PAGES:
                for viewport_config in VIEWPORTS:
                    page = context.new_page()
                    try:
                        self.test_page_viewport(page, page_config, viewport_config)
                    except Exception as e:
                        self.log_issue(
                            page_config["name"], viewport_config["label"],
                            "test_error", f"测试执行异常: {e}", "error"
                        )
                    finally:
                        page.close()

            browser.close()

        # 汇总报告
        self.print_summary()

    def print_summary(self):
        print("\n\n")
        print("=" * 70)
        print("  📊 测试汇总报告")
        print("=" * 70)

        # 按页面+视口汇总
        for combo, data in self.results.items():
            print(f"\n  {combo}:")
            print(f"    控制台错误: {data['console_errors']}")
            print(f"    控制台警告: {data['console_warnings']}")
            print(f"    网络错误: {data['network_errors']}")
            print(f"    截图: {data['screenshot']}")

        # 问题列表
        if self.all_issues:
            print(f"\n\n  🚨 发现的问题 ({len(self.all_issues)} 个):")
            print("  " + "-" * 60)

            errors = [i for i in self.all_issues if i["severity"] == "error"]
            warnings = [i for i in self.all_issues if i["severity"] == "warning"]

            if errors:
                print(f"\n  🔴 错误 ({len(errors)} 个):")
                for i, issue in enumerate(errors, 1):
                    print(f"    {i}. [{issue['page']}/{issue['viewport']}] {issue['category']}: {issue['message']}")

            if warnings:
                print(f"\n  🟡 警告 ({len(warnings)} 个):")
                for i, issue in enumerate(warnings, 1):
                    print(f"    {i}. [{issue['page']}/{issue['viewport']}] {issue['category']}: {issue['message']}")
        else:
            print("\n  ✅ 未发现任何问题！")

        print("\n" + "=" * 70)


if __name__ == "__main__":
    tester = FrontendTester()
    tester.run()
