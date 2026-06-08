#!/usr/bin/env python3
"""Screenshot comparison between reference site and local site."""
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
            results[name] = {"ref_console": [], "local_console": [], "ref_404s": [], "local_404s": []}

            # Screenshot reference site
            page1 = await context.new_page()
            page1.on("console", lambda msg, n=name: results[n]["ref_console"].append(f"[{msg.type}] {msg.text}"))
            page1.on("requestfailed", lambda req, n=name: results[n]["ref_404s"].append(req.url))
            try:
                await page1.goto(f"{REF}{page_path}", wait_until="networkidle", timeout=30000)
                await page1.wait_for_timeout(3000)
                await page1.screenshot(path=f"/workspace/screenshots/ref_{name}.png", full_page=True)
                print(f"[REF] {name} - done")
            except Exception as e:
                print(f"[REF] {name} - error: {e}")
            await page1.close()

            # Screenshot local site
            page2 = await context.new_page()
            page2.on("console", lambda msg, n=name: results[n]["local_console"].append(f"[{msg.type}] {msg.text}"))
            page2.on("requestfailed", lambda req, n=name: results[n]["local_404s"].append(req.url))
            try:
                await page2.goto(f"{LOCAL}{page_path}", wait_until="networkidle", timeout=30000)
                await page2.wait_for_timeout(3000)
                await page2.screenshot(path=f"/workspace/screenshots/local_{name}.png", full_page=True)
                print(f"[LOCAL] {name} - done")
            except Exception as e:
                print(f"[LOCAL] {name} - error: {e}")
            await page2.close()

        await browser.close()

        # Print results
        print("\n" + "="*80)
        print("COMPARISON RESULTS")
        print("="*80)
        for name, data in results.items():
            print(f"\n--- {name} ---")
            ref_errs = [e for e in data["ref_console"] if "[error]" in e.lower()]
            local_errs = [e for e in data["local_console"] if "[error]" in e.lower()]
            print(f"  REF 404s: {len(data['ref_404s'])} | LOCAL 404s: {len(data['local_404s'])}")
            print(f"  REF console errors: {len(ref_errs)} | LOCAL console errors: {len(local_errs)}")
            if data["local_404s"]:
                print(f"  LOCAL 404s:")
                for u in data["local_404s"][:20]:
                    print(f"    - {u}")
            if local_errs:
                print(f"  LOCAL console errors:")
                for e in local_errs[:20]:
                    print(f"    - {e}")

        with open("/workspace/screenshots/results.json", "w") as f:
            json.dump(results, f, indent=2, ensure_ascii=False)

asyncio.run(main())
