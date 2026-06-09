"""精确定位 JS 错误和 404 资源"""
from playwright.sync_api import sync_playwright

BASE = "http://localhost:8090"

with sync_playwright() as p:
    browser = p.chromium.launch(headless=True)
    page = browser.new_page(viewport={"width": 1440, "height": 900})

    # 收集详细日志
    all_404 = set()
    all_errors = []

    def log_request_failed(request):
        if request.failure:
            all_404.add(f"{request.method} {request.url} -> {request.failure}")

    def log_console(msg):
        if msg.type == "error":
            all_errors.append(f"[{msg.type}] {msg.text}")
        elif msg.type == "warning":
            all_errors.append(f"[{msg.type}] {msg.text}")

    page.on("console", log_console)
    page.on("requestfailed", log_request_failed)
    page.on("pageerror", lambda err: all_errors.append(f"[PAGE_ERROR] {err.message}"))

    print(">>> 加载首页...")
    try:
        page.goto(f"{BASE}/", timeout=30000, wait_until="networkidle")
    except Exception as e:
        print(f"LOAD ERROR: {e}")

    page.wait_for_timeout(2000)

    print(f"\n=== 404 资源 ({len(all_404)} 个) ===")
    for u in sorted(all_404):
        print(f"  {u}")

    print(f"\n=== 去重后的独特JS错误 ===")
    # 去重
    unique_errors = {}
    for e in all_errors:
        key = e.split(": ", 1)[-1] if ": " in e else e
        if key not in unique_errors:
            unique_errors[key] = 0
        unique_errors[key] += 1

    for err, count in sorted(unique_errors.items(), key=lambda x: -x[1]):
        print(f"  [{count}x] {err}")

    # 检查关键元素
    print(f"\n=== 关键元素检查 ===")
    for sel in ["#lgnewuiMobileNav", "#lgnewuiMobileNavSentinel", ".lg-footer",
                "#lgnewuiMobileNavRoot", ".lgnewui-mobile-nav",
                "#lgnewui-nav-mobile", "[data-mobile-nav]"]:
        count = page.locator(sel).count()
        print(f"  {sel}: {count}")

    # 检查footer结构
    footer_locator = page.locator("footer")
    if footer_locator.count() > 0:
        classes = footer_locator.first.get_attribute("class")
        print(f"\n  footer 元素 class: {classes}")
    else:
        print(f"\n  footer 元素: 不存在!")

    # 检查移动端导航 DOM
    mobile_nav_html = page.locator("*[id*='mobile' i], *[id*='Mobile']").all()
    print(f"\n  mobile 相关 ID: {[el.get_attribute('id') for el in mobile_nav_html[:10]]}")

    mobile_nav_class = page.locator("*[class*='mobile' i], *[class*='Mobile']").all()
    class_list = list(set([el.get_attribute('class') for el in mobile_nav_class if el.get_attribute('class')]))
    print(f"  mobile 相关 class: {class_list[:15]}")

    # 检查 FunLazy 和 getMusicSetting 引用位置
    page_content = page.content()
    if "FunLazy" in page_content:
        lines = [l.strip() for l in page_content.split('\n') if 'FunLazy' in l]
        print(f"\n=== FunLazy 引用 ===")
        for l in lines[:5]:
            print(f"  {l[:120]}")
    else:
        print(f"\n=== FunLazy 引用: 无 ===")

    if "getMusicSetting" in page_content:
        lines = [l.strip() for l in page_content.split('\n') if 'getMusicSetting' in l]
        print(f"\n=== getMusicSetting 引用 ===")
        for l in lines[:5]:
            print(f"  {l[:120]}")
    else:
        print(f"\n=== getMusicSetting 引用: 无 ===")

    if "forEach" in page_content:
        lines = [l.strip() for l in page_content.split('\n') if 'forEach' in l]
        print(f"\n=== forEach 引用 (前15) ===")
        for l in lines[:15]:
            print(f"  {l[:150]}")

    page.screenshot(path="/workspace/screenshots_verify/home_final_debug.png", full_page=True)
    browser.close()