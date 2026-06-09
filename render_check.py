"""检查所有页面渲染是否正确 - 截图 + 控制台错误 + 关键元素验证"""
from playwright.sync_api import sync_playwright
import json, os, sys

BASE = "http://localhost:8090"
PAGES = {
    "首页": "/",
    "时光轴": "/timeline.php",
    "关于": "/about.php",
    "留言": "/messages.php",
    "清单": "/list.php",
    "点点滴滴": "/lovelist.php",
    "相册": "/albums.php",
    "文章": "/articles.php",
    "留言板": "/leaving.php",
}

os.makedirs("/workspace/screenshots_verify", exist_ok=True)

CRITICAL_IDS = [
    "loader-wrapper", "lgnewuiNavWrapper", "pjax-container",
    "lgHeaderMorePanel", "lgnewuiMobileNav"
]

CRITICAL_CLASSES = [
    "lgnewui-nav-island-container", "stuck-logo", "lg-footer",
    "lg-header-more-panel", "lgnewui-mobile-nav-root"
]

results = {}

with sync_playwright() as p:
    browser = p.chromium.launch(headless=True)
    context = browser.new_context(
        viewport={"width": 1440, "height": 900},
        device_scale_factor=1,
    )
    page = context.new_page()
    errors_all = []

    for name, path in PAGES.items():
        url = f"{BASE}{path}"
        print(f"\n{'='*60}")
        print(f"检查: {name} ({url})")
        print(f"{'='*60}")

        errors = []
        page.on("console", lambda msg: errors.append(f"[{msg.type}] {msg.text}") if msg.type in ("error", "warning") else None)
        page.on("pageerror", lambda err: errors.append(f"[PAGE_ERROR] {err.message}"))

        try:
            page.goto(url, timeout=30000, wait_until="networkidle")
        except Exception as e:
            errors.append(f"[LOAD_ERROR] {e}")
            results[name] = {"status": "LOAD_FAILED", "errors": [str(e)]}
            continue

        page.wait_for_timeout(1000)

        # Screenshot
        safe_name = name.replace("/", "_")
        screenshot_path = f"/workspace/screenshots_verify/{safe_name}.png"
        try:
            page.screenshot(path=screenshot_path, full_page=True)
            print(f"  截图: {screenshot_path}")
        except Exception as e:
            print(f"  截图失败: {e}")

        # Check critical IDs
        found_ids = []
        missing_ids = []
        for cid in CRITICAL_IDS:
            try:
                count = page.locator(f"#{cid}").count()
                if count > 0:
                    found_ids.append(f"{cid}({count})")
                else:
                    missing_ids.append(cid)
            except:
                missing_ids.append(cid)

        # Check critical classes
        found_classes = []
        missing_classes = []
        for ccls in CRITICAL_CLASSES:
            try:
                count = page.locator(f".{ccls}").count()
                if count > 0:
                    found_classes.append(f"{ccls}({count})")
                else:
                    missing_classes.append(ccls)
            except:
                missing_classes.append(ccls)

        # Check JS errors
        js_errors = [e for e in errors if "error" in e.lower() or "PAGE_ERROR" in e.lower() or "LOAD_ERROR" in e.lower()]
        js_warnings = [e for e in errors if "warning" in e.lower()]

        # Check 404s in console
        not_found = [e for e in errors if "404" in e or "Failed to load resource" in e or "net::ERR" in e]

        # Page size
        body_html = page.content()
        html_size = len(body_html)

        result = {
            "url": url,
            "html_size": html_size,
            "found_ids": found_ids,
            "missing_ids": missing_ids,
            "found_classes": found_classes,
            "missing_classes": missing_classes,
            "js_errors": js_errors[:10],
            "js_warnings": js_warnings[:5],
            "not_found": not_found[:10],
            "all_console": errors[:20],
        }

        status = "OK"
        issues = []
        if missing_ids:
            issues.append(f"缺失ID: {missing_ids}")
            status = "WARN"
        if js_errors:
            issues.append(f"JS错误: {len(js_errors)}个")
            status = "ERROR"
        if not_found:
            issues.append(f"资源404: {len(not_found)}个")
            status = "ERROR"

        result["status"] = status
        result["issues"] = issues
        results[name] = result

        print(f"  状态: {status}")
        print(f"  HTML大小: {html_size:,} bytes")
        print(f"  找到ID: {found_ids}")
        if missing_ids:
            print(f"  缺失ID: {missing_ids}")
        if found_classes:
            print(f"  找到CSS类: {found_classes}")
        if js_errors:
            print(f"  JS错误: {js_errors[:3]}")
        if not_found:
            print(f"  资源404: {not_found[:5]}")
        print()

    # Mobile check for homepage
    print(f"\n{'='*60}")
    print("移动端检查 (375x812)")
    print(f"{'='*60}")

    mobile_context = browser.new_context(
        viewport={"width": 375, "height": 812},
        device_scale_factor=2,
    )
    mobile_page = mobile_context.new_page()
    mobile_errors = []
    mobile_page.on("console", lambda msg: mobile_errors.append(f"[{msg.type}] {msg.text}") if msg.type in ("error", "warning") else None)
    mobile_page.on("pageerror", lambda err: mobile_errors.append(f"[PAGE_ERROR] {err.message}"))

    try:
        mobile_page.goto(f"{BASE}/", timeout=30000, wait_until="networkidle")
    except Exception as e:
        mobile_errors.append(f"[LOAD_ERROR] {e}")

    mobile_page.wait_for_timeout(1000)
    mobile_page.screenshot(path="/workspace/screenshots_verify/mobile_home.png", full_page=True)

    # Check mobile nav visibility
    mobile_nav = mobile_page.locator("#lgnewuiMobileNav").count()
    mobile_sentinel = mobile_page.locator("#lgnewuiMobileNavSentinel").count()

    results["移动端-首页"] = {
        "status": "OK",
        "mobile_nav_present": mobile_nav > 0,
        "mobile_sentinel_present": mobile_sentinel > 0,
        "viewport": "375x812",
        "js_errors": [e for e in mobile_errors if "error" in e.lower()][:10],
        "not_found": [e for e in mobile_errors if "404" in e][:10],
    }
    print(f"  移动端导航: {'✓' if mobile_nav > 0 else '✗'}")
    print(f"  移动端哨兵: {'✓' if mobile_sentinel > 0 else '✗'}")

    mobile_context.close()
    browser.close()

# Summary
print(f"\n{'='*60}")
print("汇总报告")
print(f"{'='*60}")

ok_count = sum(1 for r in results.values() if r.get("status") == "OK")
warn_count = sum(1 for r in results.values() if r.get("status") == "WARN")
err_count = sum(1 for r in results.values() if r.get("status") == "ERROR")

print(f"\n总计 {len(results)} 个检查项")
print(f"  OK: {ok_count}")
print(f"  WARN: {warn_count}")
print(f"  ERROR: {err_count}")
print()

for name, r in results.items():
    icon = {"OK": "✅", "WARN": "⚠️", "ERROR": "❌"}.get(r.get("status", ""), "❓")
    print(f"  {icon} {name}: {r.get('status', '?')}")
    if r.get("issues"):
        for issue in r["issues"]:
            print(f"     - {issue}")
    if r.get("not_found"):
        for nf in r["not_found"][:3]:
            print(f"     - 404: {nf}")

# Save results
with open("/workspace/screenshots_verify/render_results.json", "w") as f:
    json.dump(results, f, ensure_ascii=False, indent=2)

print(f"\n详细结果: /workspace/screenshots_verify/render_results.json")
print(f"截图目录: /workspace/screenshots_verify/")