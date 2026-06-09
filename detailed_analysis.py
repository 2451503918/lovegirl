#!/usr/bin/env python3
"""Detailed page analysis - find exact 404 URLs and compare DOM structure."""
import asyncio
import json
from playwright.async_api import async_playwright

REF = "https://love-really.kikiw.cn"
LOCAL = "http://localhost:8090"
PAGES = ["/", "/about.php", "/loveImg.php", "/articles.php", "/list.php", "/timeline.php"]

async def main():
    async with async_playwright() as p:
        browser = await p.chromium.launch(args=['--no-sandbox', '--disable-setuid-sandbox'])
        context = await browser.new_context(
            viewport={"width": 1440, "height": 900},
            locale="zh-CN"
        )

        for page_path in PAGES:
            name = page_path.replace("/", "").replace(".php", "") or "index"
            print(f"\n{'='*80}")
            print(f"PAGE: {name}")
            print(f"{'='*80}")

            # Analyze local site
            page = await context.new_page()
            failed_requests = []
            all_requests = []
            
            def on_request(req):
                all_requests.append(req.url)
            
            def on_failed(req):
                failed_requests.append({"url": req.url, "failure": req.failure})
            
            page.on("request", on_request)
            page.on("requestfailed", on_failed)
            
            try:
                await page.goto(f"{LOCAL}{page_path}", wait_until="networkidle", timeout=30000)
                await page.wait_for_timeout(2000)
                
                # Get page title
                title = await page.title()
                print(f"  Title: {title}")
                
                # Check key DOM elements
                dom_checks = {
                    "nav": "nav, .lg-nav, .lgnewui-nav, #lgnewuiNav",
                    "hero": ".hero, .home-hero, .lg-hero, #hero",
                    "footer": "footer, .lg-footer, #footer",
                    "music_player": "#nav-music, .aplayer",
                    "floating_actions": "#lgnewuiFloatingActions",
                    "footer_animal": "#footer-animal",
                    "map_overlay": "#lgMapOverlay",
                    "mask": "#mask",
                }
                
                for label, selector in dom_checks.items():
                    count = await page.locator(selector).count()
                    status = "OK" if count > 0 else "MISSING"
                    print(f"  [{status}] {label} ({selector}): {count}")
                
                # Print failed requests
                if failed_requests:
                    print(f"\n  FAILED REQUESTS ({len(failed_requests)}):")
                    for req in failed_requests:
                        print(f"    - {req['url']}")
                        print(f"      Failure: {req['failure']}")
                
                # Check for local 404s (status code)
                local_404s = []
                for req_url in all_requests:
                    if req_url.startswith(f"{LOCAL}"):
                        try:
                            resp = await page.evaluate(f"""async () => {{
                                try {{
                                    const r = await fetch('{req_url}', {{method: 'HEAD'}});
                                    return r.status;
                                }} catch(e) {{ return 0; }}
                            }}""")
                            if resp == 404:
                                local_404s.append(req_url)
                        except:
                            pass
                
                if local_404s:
                    print(f"\n  LOCAL 404s ({len(local_404s)}):")
                    for u in local_404s[:20]:
                        print(f"    - {u}")
                        
            except Exception as e:
                print(f"  Error: {e}")
            
            await page.close()

        await browser.close()

asyncio.run(main())
