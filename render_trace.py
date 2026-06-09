"""精确定位剩余 forEach 和 404"""
from playwright.sync_api import sync_playwright

with sync_playwright() as p:
    browser = p.chromium.launch(headless=True)
    page = browser.new_page(viewport={"width": 1440, "height": 900})

    errors = []
    not_found = []

    def log_console(msg):
        if msg.type == "error":
            errors.append(msg.text)

    def log_failed(request):
        if request.failure:
            not_found.append(f"{request.method} {request.url} -> {request.failure}")

    def log_page_error(err):
        errors.append(f"PAGE_ERROR: {err.message}")

    page.on("console", log_console)
    page.on("requestfailed", log_failed)
    page.on("pageerror", log_page_error)

    page.goto("http://localhost:8090/", timeout=30000, wait_until="networkidle")
    page.wait_for_timeout(2000)

    print("=== 404 资源 ===")
    for u in sorted(set(not_found)):
        print(f"  {u}")

    print("\n=== 所有错误 ===")
    for e in sorted(set(errors)):
        print(f"  {e}")

    # 检查所有 forEach 相关代码在页面中是否存在
    content = page.content()
    forEach_lines = [l.strip()[:150] for l in content.split('\n') if '.forEach(' in l or 'forEach(' in l]
    print(f"\n=== forEach 调用 ({len(forEach_lines)} 处) ===")
    for l in forEach_lines[:15]:
        print(f"  {l}")

    # 检查还有哪些undefined引用
    undefineds = [l.strip()[:200] for l in content.split('\n') if 'undefined' in l.lower() and 'typeof' not in l.lower()]
    print(f"\n=== 包含 undefined 的代码 ({len(undefineds)} 处) ===")
    for l in undefineds[:10]:
        print(f"  {l}")

    browser.close()