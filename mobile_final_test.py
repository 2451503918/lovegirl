import asyncio
import time
from playwright.async_api import async_playwright

async def main():
    timestamp = int(time.time() * 1000)
    url = f"http://localhost:8090/?t={timestamp}"

    async with async_playwright() as p:
        browser = await p.chromium.launch(headless=True)
        context = await browser.new_context(
            viewport={"width": 390, "height": 844},
            device_scale_factor=3,
            user_agent="Mozilla/5.0 (iPhone; CPU iPhone OS 16_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.0 Mobile/15E148 Safari/604.1",
            is_mobile=True,
            has_touch=True,
        )
        page = await context.new_page()

        # 禁用浏览器缓存
        cdp = await page.context.new_cdp_session(page)
        await cdp.send('Network.setCacheDisabled', {'cacheDisabled': True})

        print(f"正在访问: {url}")
        await page.goto(url, wait_until="networkidle", timeout=30000)
        # 额外等待确保渲染完成
        await page.wait_for_timeout(3000)

        # 定义要检查的元素和属性
        checks = [
            (".lgnewui-smart-card__album-link", ["width", "height"]),
            (".lgnewui-smart-card__switch-btn", ["width", "height"]),
            (".lgnewui-link-more", ["width", "height"]),
            (".lgnewui-day-date-label-small", ["font-size"]),
            (".lgnewui-day-timer-label", ["font-size"]),
            (".lgnewui-epilogue__tool-btn", ["width", "height"]),
            ("#nav-music", ["bottom", "position"]),
            (".lgnewui-nav-music-btn", ["width", "height"]),
            (".lgnewui-music-playlist-close", ["width", "height"]),
            (".lgnewui-ios-tab", ["min-height", "height"]),
        ]

        print("\n" + "=" * 60)
        print("移动端适配最终验证结果 (iPhone 14 Pro - 390x844)")
        print("=" * 60)

        for selector, props in checks:
            try:
                result = await page.evaluate("""(args) => {
                    const [selector, props] = args;
                    let el;
                    if (selector.startsWith('#')) {
                        el = document.querySelector(selector);
                    } else {
                        el = document.querySelector(selector);
                    }
                    if (!el) return { found: false, selector: selector };
                    const computed = window.getComputedStyle(el);
                    const values = {};
                    for (const p of props) {
                        values[p] = computed.getPropertyValue(p);
                    }
                    return { found: true, selector: selector, values: values };
                }""", [selector, props])

                if result["found"]:
                    print(f"\n✅ {selector}")
                    for prop, value in result["values"].items():
                        print(f"   {prop}: {value}")
                else:
                    print(f"\n❌ {selector} - 元素未找到")
            except Exception as e:
                print(f"\n❌ {selector} - 检查出错: {e}")

        # 截图
        await page.screenshot(path="/workspace/screenshots_mobile_final2/index.png", full_page=True)
        print(f"\n截图已保存到: /workspace/screenshots_mobile_final2/index.png")

        await browser.close()

asyncio.run(main())
