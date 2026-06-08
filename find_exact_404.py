#!/usr/bin/env python3
"""Find the exact 404 URL."""
import asyncio
from playwright.async_api import async_playwright

LOCAL = "http://localhost:8090"

async def main():
    async with async_playwright() as p:
        browser = await p.chromium.launch(args=['--no-sandbox', '--disable-setuid-sandbox'])
        context = await browser.new_context(viewport={"width": 1440, "height": 900}, locale="zh-CN")
        
        page = await context.new_page()
        
        bad_responses = []
        def on_response(response):
            if response.status >= 400:
                bad_responses.append({"url": response.url, "status": response.status})
        page.on("response", on_response)
        
        await page.goto(f"{LOCAL}/", wait_until="load", timeout=60000)
        await page.wait_for_timeout(5000)
        
        print(f"Bad responses ({len(bad_responses)}):")
        for r in bad_responses:
            print(f"  [{r['status']}] {r['url']}")
        
        if not bad_responses:
            print("  No bad responses found!")
        
        await browser.close()

asyncio.run(main())
