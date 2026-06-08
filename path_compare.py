#!/usr/bin/env python3
"""Extract and compare all CSS/JS paths between reference and local site."""
import asyncio
import re
from playwright.async_api import async_playwright

REF = "https://love-really.kikiw.cn"
LOCAL = "http://localhost:8090"
PAGES = ["/", "/about.php", "/albums.php", "/articles.php", "/lovelist.php", "/timeline.php"]

async def extract_resources(page, url):
    """Extract all CSS and JS resource URLs from a page."""
    result = await page.evaluate("""() => {
        const css = Array.from(document.querySelectorAll('link[rel="stylesheet"]')).map(e => e.href);
        const js = Array.from(document.querySelectorAll('script[src]')).map(e => e.src);
        return {css, js};
    }""")
    return result

async def main():
    async with async_playwright() as p:
        browser = await p.chromium.launch(args=['--no-sandbox', '--disable-setuid-sandbox'])
        context = await browser.new_context(viewport={"width": 1440, "height": 900}, locale="zh-CN")
        
        all_ref_css = set()
        all_ref_js = set()
        all_local_css = set()
        all_local_js = set()
        
        for page_path in PAGES:
            name = page_path.replace("/", "").replace(".php", "") or "index"
            
            # Reference site
            page1 = await context.new_page()
            try:
                await page1.goto(f"{REF}{page_path}", wait_until="load", timeout=60000)
                await page1.wait_for_timeout(3000)
                ref_res = await extract_resources(page1, f"{REF}{page_path}")
                # Convert to path-only
                ref_css_paths = [re.sub(r'^https?://[^/]+', '', u) for u in ref_res['css']]
                ref_js_paths = [re.sub(r'^https?://[^/]+', '', u) for u in ref_res['js']]
                # Remove query strings for comparison
                ref_css_clean = [re.sub(r'\?.*$', '', p) for p in ref_css_paths if p.startswith('/')]
                ref_js_clean = [re.sub(r'\?.*$', '', p) for p in ref_js_paths if p.startswith('/')]
                all_ref_css.update(ref_css_clean)
                all_ref_js.update(ref_js_clean)
                print(f"[REF] {name}: {len(ref_css_clean)} CSS, {len(ref_js_clean)} JS")
            except Exception as e:
                print(f"[REF] {name}: Error - {e}")
            await page1.close()
            
            # Local site
            page2 = await context.new_page()
            try:
                await page2.goto(f"{LOCAL}{page_path}", wait_until="load", timeout=60000)
                await page2.wait_for_timeout(3000)
                local_res = await extract_resources(page2, f"{LOCAL}{page_path}")
                local_css_paths = [re.sub(r'^https?://[^/]+', '', u) for u in local_res['css']]
                local_js_paths = [re.sub(r'^https?://[^/]+', '', u) for u in local_res['js']]
                local_css_clean = [re.sub(r'\?.*$', '', p) for p in local_css_paths if p.startswith('/')]
                local_js_clean = [re.sub(r'\?.*$', '', p) for p in local_js_paths if p.startswith('/')]
                all_local_css.update(local_css_clean)
                all_local_js.update(local_js_clean)
                print(f"[LOCAL] {name}: {len(local_css_clean)} CSS, {len(local_js_clean)} JS")
            except Exception as e:
                print(f"[LOCAL] {name}: Error - {e}")
            await page2.close()
        
        await browser.close()
        
        # Compare
        print("\n" + "="*80)
        print("CSS COMPARISON")
        print("="*80)
        missing_css = all_ref_css - all_local_css
        extra_css = all_local_css - all_ref_css
        print(f"  REF total: {len(all_ref_css)} | LOCAL total: {len(all_local_css)}")
        print(f"  Missing in LOCAL (in REF but not LOCAL): {len(missing_css)}")
        for p in sorted(missing_css):
            print(f"    - {p}")
        print(f"  Extra in LOCAL (in LOCAL but not REF): {len(extra_css)}")
        for p in sorted(extra_css):
            print(f"    + {p}")
        
        print("\n" + "="*80)
        print("JS COMPARISON")
        print("="*80)
        missing_js = all_ref_js - all_local_js
        extra_js = all_local_js - all_ref_js
        print(f"  REF total: {len(all_ref_js)} | LOCAL total: {len(all_local_js)}")
        print(f"  Missing in LOCAL (in REF but not LOCAL): {len(missing_js)}")
        for p in sorted(missing_js):
            print(f"    - {p}")
        print(f"  Extra in LOCAL (in LOCAL but not REF): {len(extra_js)}")
        for p in sorted(extra_js):
            print(f"    + {p}")

asyncio.run(main())
