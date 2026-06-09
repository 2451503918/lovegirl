"""定位最后的 404 和 articles 错误"""
from playwright.sync_api import sync_playwright

with sync_playwright() as p:
    browser = p.chromium.launch(headless=True)

    # --- 追踪首页的 404 ---
    page = browser.new_page(viewport={"width": 1440, "height": 900})
    bad_responses = []

    def log_response(response):
        if response.status == 404:
            bad_responses.append(f"404: {response.request.method} {response.url}")

    page.on("response", log_response)
    page.on("pageerror", lambda e: print(f"PAGE: {e.message}"))

    print("=== 首页 404 资源 ===")
    page.goto("http://localhost:8090/", timeout=30000, wait_until="networkidle")
    page.wait_for_timeout(2000)
    for r in sorted(set(bad_responses)):
        print(f"  {r}")
    page.close()

    # --- 追踪 articles 的 iterable 错误 ---
    page2 = browser.new_page(viewport={"width": 1440, "height": 900})
    page2.add_init_script("""
        window.__lg_errs2 = [];
        window.addEventListener('error', function(e) {
            window.__lg_errs2.push({
                msg: e.message,
                file: e.filename,
                line: e.lineno,
                col: e.colno,
                stack: e.error ? e.error.stack : null
            });
        });
    """)

    print("\n=== Articles 页面错误 ===")
    page2.goto("http://localhost:8090/articles.php", timeout=30000, wait_until="networkidle")
    page2.wait_for_timeout(2000)
    captured = page2.evaluate("() => window.__lg_errs2")
    for e in captured:
        if 'iterable' in e['msg'].lower() or 'forEach' in e['msg'].lower():
            print(f"  Message: {e['msg']}")
            print(f"  File: {e['file']}:{e['line']}:{e['col']}")
            if e['stack']:
                for line in e['stack'].split('\n')[:8]:
                    print(f"    {line}")
            print()

    page2.close()
    browser.close()