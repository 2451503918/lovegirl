#!/usr/bin/env python3
"""Screenshot comparison with slower, more reliable loading."""
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

        results = {}
        for page_path in PAGES:
            name = page_path.replace("/", "").replace(".php", "") or "index"
            results[name] = {"local_console_errors": [], "local_404s": [], "ref_404s": []}

            # Screenshot reference site
            page1 = await context.new_page()
            ref_404s = []
            page1.on("requestfailed", lambda req: ref_404s.append(req.url))
            try:
                await page1.goto(f"{REF}{page_path}", wait_until="load", timeout=60000)
                await page1.wait_for_timeout(5000)
                await page1.screenshot(path=f"/workspace/screenshots/ref_{name}.png", full_page=False)
                print(f"[REF] {name} - done")
            except Exception as e:
                print(f"[REF] {name} - error: {e}")
            results[name]["ref_404s"] = ref_404s
            await page1.close()

            # Screenshot local site
            page2 = await context.new_page()
            local_404s = []
            local_errors = []
            page2.on("requestfailed", lambda req: local_404s.append(req.url))
            page2.on("console", lambda msg: local_errors.append(f"[{msg.type}] {msg.text}") if msg.type == "error" else None)
            try:
                await page2.goto(f"{LOCAL}{page_path}", wait_until="load", timeout=60000)
                await page2.wait_for_timeout(5000)
                await page2.screenshot(path=f"/workspace/screenshots/local_{name}.png", full_page=False)
                print(f"[LOCAL] {name} - done")
            except Exception as e:
                print(f"[LOCAL] {name} - error: {e}")
            results[name]["local_404s"] = local_404s
            results[name]["local_console_errors"] = local_errors
            await page2.close()

        await browser.close()

        # Print results
        print("\n" + "="*80)
        print("COMPARISON RESULTS")
        print("="*80)
        for name, data in results.items():
            print(f"\n--- {name} ---")
            print(f"  LOCAL 404s: {len(data['local_404s'])}")
            if data['local_404s']:
                for u in data['local_404s'][:10]:
                    print(f"    - {u}")
            print(f"  LOCAL console errors: {len(data['local_console_errors'])}")
            if data['local_console_errors']:
                for e in data['local_console_errors'][:10]:
                    print(f"    - {e}")

        with open("/workspace/screenshots/results2.json", "w") as f:
            json.dump(results, f, indent=2, ensure_ascii=False)

asyncio.run(main())
