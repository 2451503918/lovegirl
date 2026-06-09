"""捕获所有 404 请求（包括 CSS url() 引用）"""
from playwright.sync_api import sync_playwright

with sync_playwright() as p:
    browser = p.chromium.launch(headless=True)
    page = browser.new_page(viewport={"width": 1440, "height": 900})

    responses_404 = []
    failures = []
    console_404 = []

    def log_response(response):
        if response.status == 404 and not response.url.startswith('data:'):
            responses_404.append(response.url)

    def log_failed(request):
        if request.failure:
            url = request.url if request.url else "(no url)"
            failures.append(f"FAILED: {request.method} {url} -> {request.failure}")

    def log_console(msg):
        if msg.type == "error":
            console_404.append(msg.text)

    page.on("response", log_response)
    page.on("requestfailed", log_failed)
    page.on("console", log_console)
    page.on("pageerror", lambda e: print(f"PAGE: {e.message}"))

    print("Loading...")
    page.goto("http://localhost:8090/", timeout=30000, wait_until="networkidle")
    page.wait_for_timeout(3000)

    print("\n=== 本地 404 响应 ===")
    for u in sorted(set(responses_404)):
        if 'localhost' in u or not any(x in u for x in ['126.net', 'cos.']):
            print(f"  {u}")

    print(f"\n=== 请求失败 ===")
    for f in sorted(set(failures)):
        print(f"  {f}")

    print(f"\n=== Console 404/Error ===")
    for e in sorted(set(console_404)):
        if '404' in e or 'EGATIVE' in e or 'failed' in e.lower() or 'Failed' in e:
            print(f"  {e}")

    browser.close()