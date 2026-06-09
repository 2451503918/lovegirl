"""定位 400 和 404 以及 forEach 错误"""
from playwright.sync_api import sync_playwright

with sync_playwright() as p:
    browser = p.chromium.launch(headless=True)
    page = browser.new_page(viewport={"width": 1440, "height": 900})

    # Capture ALL network requests and failures
    bad_requests = []

    def log_request(request):
        pass  # we use response instead

    def log_response(response):
        if response.status >= 400:
            bad_requests.append(f"  HTTP {response.status}: {response.request.method} {response.url}")

    page.on("response", log_response)
    page.on("pageerror", lambda err: print(f"  PAGE_ERROR: {err.message}"))

    print("Loading page...")
    page.goto("http://localhost:8090/", timeout=30000, wait_until="networkidle")
    page.wait_for_timeout(2000)

    print("\n=== 所有 >= 400 的 HTTP 响应 ===")
    for r in sorted(set(bad_requests)):
        print(r)

    # Check the navItems/mobileItems sources
    content = page.content()
    
    # Find where navItems is declared
    nav_items_decl = [l.strip()[:200] for l in content.split('\n') if 'navItems' in l and ('var' in l or 'let' in l or 'const' in l or '=' in l)]
    mobile_items_decl = [l.strip()[:200] for l in content.split('\n') if 'mobileItems' in l and ('var' in l or 'let' in l or 'const' in l or '=' in l)]
    
    print("\n=== navItems 声明 ===")
    for l in nav_items_decl:
        print(f"  {l}")
    
    print("\n=== mobileItems 声明 ===")
    for l in mobile_items_decl:
        print(f"  {l}")

    browser.close()