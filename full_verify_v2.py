#!/usr/bin/env python3
"""Full verification: 404 errors, console errors, page titles, DOM elements"""
import asyncio
import json
from playwright.async_api import async_playwright

LOCAL = "http://localhost:8090"
REF = "https://love-really.kikiw.cn"

PAGES = [
    ("/", "首页"),
    ("/about.php", "关于"),
    ("/loveImg.php", "相册"),
    ("/articles.php", "点滴"),
    ("/list.php", "清单"),
    ("/timeline.php", "轨迹"),
    ("/messages.php", "留言"),
]

KEY_ELEMENTS = [
    "#lgnewuiHeaderWrap",
    "#lgnewuiStickySentinel",
    "#musicInfoTime",
    "#music",
    "#mask",
    "#lgnewuiFloatingActions",
    "#footer-animal",
    ".lgnewui-nav-dynamic-island",
]

async def verify():
    results = {
        "local_404s": [],
        "local_console_errors": [],
        "ref_console_errors": [],
        "title_mismatches": [],
        "element_mismatches": [],
        "summary": {}
    }

    async with async_playwright() as p:
        browser = await p.chromium.launch(headless=True, args=['--no-sandbox'])
        context = await browser.new_context(viewport={"width": 1280, "height": 800})

        for path, name in PAGES:
            local_url = LOCAL + path
            ref_url = REF + path

            # --- Check local page ---
            local_404s = []
            local_errors = []
            local_title = ""
            local_elements = {}

            page = await context.new_page()

            page.on("response", lambda resp, errs=local_404s: errs.append(resp.url) if resp.status == 404 else None)
            page.on("console", lambda msg, errs=local_errors: errs.append(f"[{msg.type}] {msg.text}") if msg.type in ("error", "warning") else None)

            try:
                await page.goto(local_url, wait_until="networkidle", timeout=15000)
            except Exception as e:
                local_errors.append(f"Navigation error: {e}")

            local_title = await page.title()

            for sel in KEY_ELEMENTS:
                try:
                    exists = await page.query_selector(sel) is not None
                    local_elements[sel] = exists
                except:
                    local_elements[sel] = False

            await page.close()

            results["local_404s"].extend([(path, u) for u in local_404s])
            results["local_console_errors"].extend([(path, e) for e in local_errors])

            # --- Check ref page (just title and elements) ---
            ref_title = ""
            ref_elements = {}

            page2 = await context.new_page()
            ref_errors = []
            page2.on("console", lambda msg, errs=ref_errors: errs.append(f"[{msg.type}] {msg.text}") if msg.type in ("error", "warning") else None)

            try:
                await page2.goto(ref_url, wait_until="networkidle", timeout=15000)
            except Exception as e:
                ref_errors.append(f"Navigation error: {e}")

            ref_title = await page2.title()

            for sel in KEY_ELEMENTS:
                try:
                    exists = await page2.query_selector(sel) is not None
                    ref_elements[sel] = exists
                except:
                    ref_elements[sel] = False

            await page2.close()
            results["ref_console_errors"].extend([(path, e) for e in ref_errors])

            # Compare titles
            # Extract the part before " — " for comparison
            local_parts = local_title.split(" — ")
            ref_parts = ref_title.split(" — ")
            local_page_name = local_parts[0] if len(local_parts) > 1 else local_title
            ref_page_name = ref_parts[0] if len(ref_parts) > 1 else ref_title

            title_match = local_page_name == ref_page_name
            if not title_match:
                results["title_mismatches"].append({
                    "path": path,
                    "name": name,
                    "local": local_title,
                    "ref": ref_title,
                    "local_page_name": local_page_name,
                    "ref_page_name": ref_page_name
                })

            # Compare elements
            for sel in KEY_ELEMENTS:
                if local_elements.get(sel) != ref_elements.get(sel):
                    results["element_mismatches"].append({
                        "path": path,
                        "selector": sel,
                        "local": local_elements.get(sel),
                        "ref": ref_elements.get(sel)
                    })

            results["summary"][path] = {
                "name": name,
                "local_title": local_title,
                "ref_title": ref_title,
                "title_match": title_match,
                "local_404_count": len(local_404s),
                "local_error_count": len(local_errors),
                "elements_ok": all(local_elements.get(s) == ref_elements.get(s) for s in KEY_ELEMENTS)
            }

            print(f"  {name} ({path}): title={'✓' if title_match else '✗'} | 404s={len(local_404s)} | errors={len(local_errors)} | elements={'✓' if all(local_elements.get(s) == ref_elements.get(s) for s in KEY_ELEMENTS) else '✗'}")

        await browser.close()

    # Print summary
    print("\n" + "="*60)
    print("VERIFICATION SUMMARY")
    print("="*60)

    # Filter out external 404s
    local_404s_internal = [(p, u) for p, u in results["local_404s"] if LOCAL in u or u.startswith("/")]
    local_404s_external = [(p, u) for p, u in results["local_404s"] if LOCAL not in u and not u.startswith("/")]

    print(f"\nLocal 404 errors (internal): {len(local_404s_internal)}")
    for p, u in local_404s_internal:
        print(f"  [{p}] {u}")

    print(f"\nLocal 404 errors (external): {len(local_404s_external)}")
    for p, u in local_404s_external[:5]:
        print(f"  [{p}] {u}")
    if len(local_404s_external) > 5:
        print(f"  ... and {len(local_404s_external)-5} more")

    print(f"\nLocal console errors/warnings: {len(results['local_console_errors'])}")
    # Group by type
    error_types = {}
    for path, err in results["local_console_errors"]:
        key = f"[{path}] {err[:80]}"
        error_types[key] = error_types.get(key, 0) + 1
    for k, v in sorted(error_types.items(), key=lambda x: -x[1])[:15]:
        print(f"  ({v}x) {k}")

    print(f"\nTitle mismatches: {len(results['title_mismatches'])}")
    for m in results["title_mismatches"]:
        print(f"  [{m['name']}] local: '{m['local_page_name']}' | ref: '{m['ref_page_name']}'")

    print(f"\nElement mismatches: {len(results['element_mismatches'])}")
    for m in results["element_mismatches"]:
        print(f"  [{m['path']}] {m['selector']}: local={m['local']} ref={m['ref']}")

    # Overall
    all_titles_ok = len(results["title_mismatches"]) == 0
    all_elements_ok = len(results["element_mismatches"]) == 0
    no_internal_404s = len(local_404s_internal) == 0

    print(f"\n{'='*60}")
    print(f"RESULT: Titles={'✓' if all_titles_ok else '✗'} | Elements={'✓' if all_elements_ok else '✗'} | No Internal 404s={'✓' if no_internal_404s else '✗'}")
    print(f"{'='*60}")

    return results

if __name__ == "__main__":
    asyncio.run(verify())
