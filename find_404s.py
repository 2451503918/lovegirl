#!/usr/bin/env python3
"""Find exact 404 URLs on local site."""
import asyncio
from playwright.async_api import async_playwright

LOCAL = "http://localhost:8090"
PAGES = ["/", "/about.php", "/loveImg.php", "/articles.php", "/list.php", "/timeline.php"]

async def main():
    async with async_playwright() as p:
        browser = await p.chromium.launch(args=['--no-sandbox', '--disable-setuid-sandbox'])
        context = await browser.new_context(viewport={"width": 1440, "height": 900}, locale="zh-CN")
        
        for page_path in PAGES:
            name = page_path.replace("/", "").replace(".php", "") or "index"
            page = await context.new_page()
            
            # Track all responses with non-200 status
            bad_responses = []
            def on_response(response):
                if response.status >= 400:
                    bad_responses.append({"url": response.url, "status": response.status})
            page.on("response", on_response)
            
            try:
                await page.goto(f"{LOCAL}{page_path}", wait_until="load", timeout=60000)
                await page.wait_for_timeout(5000)
            except Exception as e:
                print(f"{name}: Error loading page: {e}")
            
            print(f"\n--- {name} ---")
            if bad_responses:
                for r in bad_responses:
                    print(f"  [{r['status']}] {r['url']}")
            else:
                print("  No bad responses")
            
            await page.close()
        
        await browser.close()

asyncio.run(main())
