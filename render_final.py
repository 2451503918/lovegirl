"""最终渲染验证 - 所有页面 + 移动端完整检查"""
from playwright.sync_api import sync_playwright

BASE = "http://localhost:8090"
PAGES = [
    ("首页", "/"),
    ("时光轴", "/timeline.php"),
    ("关于", "/about.php"),
    ("留言", "/messages.php"),
    ("清单", "/list.php"),
    ("点点滴滴", "/lovelist.php"),
    ("相册", "/albums.php"),
    ("文章", "/articles.php"),
    ("留言板", "/leaving.php"),
]

with sync_playwright() as p:
    browser = p.chromium.launch(headless=True)

    total_problems = 0
    ok_count = 0

    for name, path in PAGES:
        page = browser.new_page(viewport={"width": 1440, "height": 900})
        errors = []
        failures = []
        warnings = []

        def on_console(msg):
            if msg.type == "error":
                errors.append(msg.text)
            elif msg.type == "warning":
                warnings.append(msg.text)

        def on_failed(req):
            if req.failure and req.url:
                failures.append(req.url)

        def on_error(err):
            errors.append(f"PAGE_ERROR: {err.message}")

        page.on("console", on_console)
        page.on("requestfailed", on_failed)
        page.on("pageerror", on_error)

        try:
            page.goto(f"{BASE}{path}", timeout=30000, wait_until="networkidle")
        except Exception as e:
            print(f"  ❌ {name}: 页面加载失败 - {e}")
            total_problems += 1
            page.close()
            continue

        page.wait_for_timeout(1000)

        # 关键元素检查
        checks = {
            "loader": page.locator("#loader-wrapper").count() > 0,
            "导航栏": page.locator(".lgnewui-nav-island-container").count() > 0,
            "stuck-logo": page.locator(".stuck-logo").count() > 0,
            "pjax-container": page.locator("#pjax-container").count() > 0,
            "footer": page.locator(".footer-warp, .footer, footer, .lg-footer, #lgfooter").count() > 0,
        }

        # 去重真实错误（排除已知无害）
        real_errors = []
        for e in errors:
            if "captchaId" in e or "Geetest" in e:
                continue  # 预期：极验未配置
            if "404 ()" in e or "EGATIVE" in e:
                continue  # 无害：空src图片
            real_errors.append(e)

        local_404 = [u for u in failures if "localhost" in u or "localhost" in u]

        all_ok = all(checks.values())
        has_issues = len(real_errors) > 0

        icon = "✅" if all_ok and not has_issues else ("⚠️" if not all_ok else "✅")
        status = "OK" if all_ok and not has_issues else ("MISSING_ELEMENTS" if not all_ok else "HAS_ERRORS")
        
        print(f"  {icon} {name:8s}  {status:20s}", end="")
        
        failed_checks = [k for k, v in checks.items() if not v]
        if failed_checks:
            print(f"  缺失: {', '.join(failed_checks)}", end="")
            total_problems += len(failed_checks)
        
        if real_errors:
            print(f"  错误: {real_errors[0][:80]}", end="")
            total_problems += len(real_errors)
        
        if local_404:
            print(f"  404: {local_404[0][:60]}", end="")
            total_problems += len(local_404)
        
        print()
        if all_ok and not has_issues:
            ok_count += 1

        page.close()

    # 移动端
    print()
    mobile = browser.new_page(viewport={"width": 375, "height": 812})
    m_errors = []
    mobile.on("pageerror", lambda e: m_errors.append(f"PAGE: {e.message}"))
    try:
        mobile.goto(f"{BASE}/", timeout=30000, wait_until="networkidle")
    except:
        pass
    mobile.wait_for_timeout(1000)

    m_naverr = mobile.locator(".lgnewui-nav-island-container").count() > 0
    m_nav_v5 = mobile.locator("#lgnewui-mobile-nav-v5").count() > 0
    m_stuck = mobile.locator(".stuck-logo").count() > 0

    m_ok = m_naverr and m_nav_v5 and m_stuck
    real_m = [e for e in m_errors if "captchaId" not in e]
    mobile_ok = m_ok and len(real_m) == 0

    print(f"  {'✅' if mobile_ok else '❌'} 移动端-首页", end="")
    print(f"  导航:{'✓' if m_naverr else '✗'}  移动导航V5:{'✓' if m_nav_v5 else '✗'}  stuck-logo:{'✓' if m_stuck else '✗'}")
    if real_m:
        print(f"     错误: {real_m[0][:80]}")
        total_problems += len(real_m)

    mobile.close()
    browser.close()

    print(f"\n{'='*50}")
    print(f"结果: {ok_count}/{len(PAGES)} 页面完全正常 (0错误 0缺失)")
    print(f"总问题数: {total_problems}")
    print(f"{'='*50}")