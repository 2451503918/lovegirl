#!/usr/bin/env python3
"""Screenshot comparison between local and reference site"""
import asyncio
import os
from playwright.async_api import async_playwright

LOCAL = "http://localhost:8090"
REF = "https://love-really.kikiw.cn"

PAGES = [
    ("/", "首页"),
    ("/about.php", "关于"),
    ("/albums.php", "相册"),
    ("/articles.php", "点滴"),
    ("/lovelist.php", "清单"),
    ("/timeline.php", "轨迹"),
    ("/messages.php", "留言"),
]

SCREENSHOT_DIR = "/workspace/screenshots_compare"
os.makedirs(SCREENSHOT_DIR, exist_ok=True)

async def compare():
    async with async_playwright() as p:
        browser = await p.chromium.launch(headless=True, args=['--no-sandbox'])
        context = await browser.new_context(viewport={"width": 1280, "height": 800})

        for path, name in PAGES:
            # Local screenshot
            page_local = await context.new_page()
            try:
                await page_local.goto(LOCAL + path, wait_until="networkidle", timeout=15000)
                # Wait a bit for animations
                await page_local.wait_for_timeout(2000)
                await page_local.screenshot(path=f"{SCREENSHOT_DIR}/local_{name}.png", full_page=False)
                print(f"  ✓ Local screenshot: {name}")
            except Exception as e:
                print(f"  ✗ Local screenshot failed: {name} - {e}")
            await page_local.close()

            # Reference screenshot
            page_ref = await context.new_page()
            try:
                await page_ref.goto(REF + path, wait_until="networkidle", timeout=15000)
                await page_ref.wait_for_timeout(2000)
                await page_ref.screenshot(path=f"{SCREENSHOT_DIR}/ref_{name}.png", full_page=False)
                print(f"  ✓ Ref screenshot: {name}")
            except Exception as e:
                print(f"  ✗ Ref screenshot failed: {name} - {e}")
            await page_ref.close()

        await browser.close()

    print(f"\nScreenshots saved to {SCREENSHOT_DIR}/")
    print("Compare: local_*.png vs ref_*.png")

if __name__ == "__main__":
    asyncio.run(compare())
