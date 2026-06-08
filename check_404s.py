#!/usr/bin/env python3
"""Check exact 404 URLs on local site"""
import asyncio
from playwright.async_api import async_playwright

LOCAL = "http://localhost:8090"

PAGES = ["/", "/about.php", "/albums.php", "/articles.php", "/lovelist.php", "/timeline.php", "/messages.php"]

async def check():
    async with async_playwright() as p:
        browser = await p.chromium.launch(headless=True, args=['--no-sandbox'])
        context = await browser.new_context(viewport={"width": 1280, "height": 800})

        for path in PAGES:
            page = await context.new_page()
            failed_requests = []

            def on_response(resp):
                if resp.status >= 400:
                    failed_requests.append(f"{resp.status} {resp.url}")

            page.on("response", on_response)

            try:
                await page.goto(LOCAL + path, wait_until="networkidle", timeout=15000)
            except:
                pass

            print(f"\n{path}:")
            if failed_requests:
                for f in failed_requests:
                    is_internal = LOCAL in f or (not f.startswith("http") and f.startswith("/"))
                    tag = "INTERNAL" if is_internal else "EXTERNAL"
                    print(f"  [{tag}] {f}")
            else:
                print("  No 404s")

            await page.close()

        await browser.close()

if __name__ == "__main__":
    asyncio.run(check())
