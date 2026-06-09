#!/usr/bin/env python3
"""Final verification: screenshots + 404 + console errors."""
import asyncio
import json
from playwright.async_api import async_playwright

REF = "https://love-really.kikiw.cn"
LOCAL = "http://localhost:8090"
PAGES = ["/", "/about.php", "/albums.php", "/articles.php", "/lovelist.php", "/timeline.php"]

async def main():
    async with async_playwright() as p:
        browser = await p.chromium.launch(args=['--no-sandbox', '--disable-setuid-sandbox'])
        context = await browser.new_context(viewport={"width": 1440, "height": 900}, locale="zh-CN")
        
        results = {}
        for page_path in PAGES:
            name = page_path.replace("/", "").replace(".php", "") or "index"
            results[name] = {"local_404s": [], "local_errors": [], "local_warnings": []}
            
            # Screenshot local site
            page = await context.new_page()
            
            def on_response(response):
                if response.status >= 400 and response.url.startswith('http://localhost'):
                    results[name]["local_404s"].append({"url": response.url, "status": response.status})
            
            def on_console(msg):
                if msg.type == "error":
                    results[name]["local_errors"].append(msg.text[:200])
                elif msg.type == "warning":
                    text = msg.text[:200]
                    # Filter out common non-critical warnings
                    if 'invalid sfntVersion' not in text and 'DevTools' not in text:
                        results[name]["local_warnings"].append(text)
            
            page.on("response", on_response)
            page.on("console", on_console)
            
            try:
                await page.goto(f"{LOCAL}{page_path}", wait_until="load", timeout=60000)
                await page.wait_for_timeout(5000)
                await page.screenshot(path=f"/workspace/screenshots/final_{name}.png", full_page=False)
                print(f"[LOCAL] {name} - done")
            except Exception as e:
                print(f"[LOCAL] {name} - error: {e}")
            
            await page.close()
        
        await browser.close()
        
        # Print results
        print("\n" + "="*80)
        print("FINAL VERIFICATION RESULTS")
        print("="*80)
        
        total_404s = 0
        total_errors = 0
        total_warnings = 0
        
        for name, data in results.items():
            n404 = len(data["local_404s"])
            nerr = len(data["local_errors"])
            nwarn = len(data["local_warnings"])
            total_404s += n404
            total_errors += nerr
            total_warnings += nwarn
            
            status = "OK" if n404 == 0 and nerr == 0 else "ISSUE"
            print(f"\n--- {name} [{status}] ---")
            print(f"  404s: {n404} | Errors: {nerr} | Warnings: {nwarn}")
            
            if data["local_404s"]:
                for r in data["local_404s"][:5]:
                    print(f"    404 [{r['status']}]: {r['url']}")
            if data["local_errors"]:
                for e in data["local_errors"][:5]:
                    print(f"    ERROR: {e[:150]}")
            if data["local_warnings"]:
                for w in data["local_warnings"][:3]:
                    print(f"    WARN: {w[:150]}")
        
        print(f"\n{'='*80}")
        print(f"TOTAL: 404s={total_404s} | Errors={total_errors} | Warnings={total_warnings}")
        if total_404s == 0 and total_errors == 0:
            print("ALL CHECKS PASSED!")
        else:
            print("SOME CHECKS FAILED - see details above")

asyncio.run(main())
