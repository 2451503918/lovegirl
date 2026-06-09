"""重新渲染验证 - 修复后"""
from playwright.sync_api import sync_playwright

BASE = "http://localhost:8090"

with sync_playwright() as p:
    browser = p.chromium.launch(headless=True)

    pages = ["/", "/about.php", "/timeline.php", "/messages.php", "/list.php",
             "/lovelist.php", "/albums.php", "/articles.php", "/leaving.php"]

    total_404 = 0
    total_errors = 0
    results = {}

    for path in pages:
        page = browser.new_page(viewport={"width": 1440, "height": 900})
        errors = []
        not_found = []

        def log_console(msg):
            if msg.type == "error":
                errors.append(msg.text)

        def log_request_failed(request):
            if request.failure:
                not_found.append(request.url)

        page.on("console", log_console)
        page.on("requestfailed", log_request_failed)
        page.on("pageerror", lambda err: errors.append(f"PAGE_ERROR: {err.message}"))

        try:
            page.goto(f"{BASE}{path}", timeout=30000, wait_until="networkidle")
        except Exception as e:
            errors.append(f"LOAD_ERROR: {e}")

        page.wait_for_timeout(1500)

        # 去重错误
        unique_errors = list(set(errors))
        unique_404 = list(set(not_found))

        status = "OK"
        if unique_errors:
            status = "HAS_ERRORS"
        if unique_404:
            status = "HAS_404" if status == "OK" else status + "+404"

        results[path] = {
            "errors": unique_errors,
            "404s": unique_404,
            "status": status
        }

        print(f"  {status:12s} {path}   errors={len(unique_errors)}  404s={len(unique_404)}")
        if unique_errors:
            for e in unique_errors:
                print(f"    E: {e[:100]}")
        if unique_404:
            for n in unique_404:
                print(f"    404: {n[:100]}")
        print()

        total_errors += len(unique_errors)
        total_404 += len(unique_404)
        page.close()

    # Mobile check
    mobile_page = browser.new_page(viewport={"width": 375, "height": 812})
    mobile_errors = []
    mobile_404 = []

    def m_log_console(msg):
        if msg.type == "error":
            mobile_errors.append(msg.text)

    def m_log_failed(request):
        if request.failure:
            mobile_404.append(request.url)

    mobile_page.on("console", m_log_console)
    mobile_page.on("requestfailed", m_log_failed)
    mobile_page.on("pageerror", lambda err: mobile_errors.append(f"PAGE_ERROR: {err.message}"))

    try:
        mobile_page.goto(f"{BASE}/", timeout=30000, wait_until="networkidle")
    except Exception as e:
        mobile_errors.append(f"LOAD_ERROR: {e}")

    mobile_page.wait_for_timeout(1500)

    # Check mobile nav
    nav_v5 = mobile_page.locator("#lgnewui-mobile-nav-v5").count()

    mobile_page.screenshot(path="/workspace/screenshots_verify/mobile_fixed.png", full_page=True)
    mobile_page.close()
    browser.close()

    print(f"\n=== 总结 ===")
    print(f"  总错误: {total_errors}")
    print(f"  总404: {total_404}")
    print(f"  移动端导航V5: {'✓ 存在' if nav_v5 > 0 else '✗ 缺失'}")