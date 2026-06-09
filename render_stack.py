"""捕获 forEach 错误的堆栈"""
from playwright.sync_api import sync_playwright

with sync_playwright() as p:
    browser = p.chromium.launch(headless=True)
    page = browser.new_page(viewport={"width": 1440, "height": 900})

    errors = []
    page.on("pageerror", lambda err: errors.append(f"PAGE: {err.message}"))

    # Inject error handler to capture stack
    page.add_init_script("""
        window.__lg_errs = [];
        window.addEventListener('error', function(e) {
            window.__lg_errs.push({
                msg: e.message,
                stack: e.error ? e.error.stack : null,
                file: e.filename,
                line: e.lineno,
                col: e.colno
            });
        });
    """)

    page.goto("http://localhost:8090/", timeout=30000, wait_until="networkidle")
    page.wait_for_timeout(3000)

    captured = page.evaluate("() => window.__lg_errs")
    print("=== 捕获的错误(含堆栈) ===")
    for e in captured:
        print(f"  Message: {e['msg']}")
        print(f"  File: {e['file']}:{e['line']}:{e['col']}")
        if e['stack']:
            for line in e['stack'].split('\n')[:5]:
                print(f"    {line}")
        print()

    browser.close()