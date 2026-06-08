#!/usr/bin/env python3
"""Full verification with correct page paths matching the reference site"""
import asyncio
from playwright.async_api import async_playwright

LOCAL = "http://localhost:8090"
REF = "https://love-really.kikiw.cn"

# Use the same paths as the reference site
PAGES = [
    ("/", "首页"),
    ("/about.php", "关于"),
    ("/albums.php", "相册"),
    ("/articles.php", "点滴"),
    ("/lovelist.php", "清单"),
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
        "local_404s_internal": [],
        "local_404s_external": [],
        "local_console_errors": [],
        "title_mismatches": [],
        "element_mismatches": [],
        "page_results": {}
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
            page.on("response", lambda resp, errs=local_404s: errs.append(resp.url) if resp.status >= 400 else None)
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

            # Categorize 404s
            for u in local_404s:
                if LOCAL in u or u.startswith("/"):
                    results["local_404s_internal"].append((path, u))
                else:
                    results["local_404s_external"].append((path, u))

            # Filter console errors (ignore Geetest and external 404s)
            real_errors = []
            for e in local_errors:
                if "Geetest" in e:
                    continue
                if "Failed to load resource" in e and "music.126.net" in e:
                    continue
                if "GroupMarkerNotSet" in e:
                    continue
                real_errors.append(e)
            results["local_console_errors"].extend([(path, e) for e in real_errors])

            # --- Check ref page ---
            ref_title = ""
            ref_elements = {}

            page2 = await context.new_page()
            try:
                await page2.goto(ref_url, wait_until="networkidle", timeout=15000)
            except Exception as e:
                pass

            ref_title = await page2.title()

            for sel in KEY_ELEMENTS:
                try:
                    exists = await page2.query_selector(sel) is not None
                    ref_elements[sel] = exists
                except:
                    ref_elements[sel] = False

            await page2.close()

            # Compare titles - only compare page name part (before " — ")
            local_parts = local_title.split(" — ")
            ref_parts = ref_title.split(" — ")
            local_page_name = local_parts[0] if len(local_parts) > 1 else local_title
            ref_page_name = ref_parts[0] if len(ref_parts) > 1 else ref_title

            title_match = local_page_name == ref_page_name
            if not title_match:
                results["title_mismatches"].append({
                    "path": path, "name": name,
                    "local": local_title, "ref": ref_title,
                    "local_page_name": local_page_name, "ref_page_name": ref_page_name
                })

            # Compare elements
            for sel in KEY_ELEMENTS:
                if local_elements.get(sel) != ref_elements.get(sel):
                    results["element_mismatches"].append({
                        "path": path, "selector": sel,
                        "local": local_elements.get(sel), "ref": ref_elements.get(sel)
                    })

            results["page_results"][path] = {
                "name": name,
                "local_title": local_title,
                "ref_title": ref_title,
                "title_match": title_match,
                "local_404_count": len(local_404s),
                "real_error_count": len(real_errors),
                "elements_ok": all(local_elements.get(s) == ref_elements.get(s) for s in KEY_ELEMENTS)
            }

            status = "✓" if (title_match and all(local_elements.get(s) == ref_elements.get(s) for s in KEY_ELEMENTS)) else "✗"
            print(f"  {name} ({path}): {status} | title={'✓' if title_match else '✗'} | 404s={len(local_404s)} | real_errors={len(real_errors)} | elements={'✓' if all(local_elements.get(s) == ref_elements.get(s) for s in KEY_ELEMENTS) else '✗'}")

        await browser.close()

    # Print summary
    print("\n" + "="*60)
    print("VERIFICATION SUMMARY")
    print("="*60)

    print(f"\nInternal 404 errors: {len(results['local_404s_internal'])}")
    for p, u in results["local_404s_internal"]:
        print(f"  [{p}] {u}")

    print(f"\nExternal 404 errors (not our issue): {len(results['local_404s_external'])}")

    print(f"\nReal console errors (excl. Geetest/external): {len(results['local_console_errors'])}")
    for path, err in results["local_console_errors"]:
        print(f"  [{path}] {err[:120]}")

    print(f"\nTitle mismatches: {len(results['title_mismatches'])}")
    for m in results["title_mismatches"]:
        print(f"  [{m['name']}] local_page='{m['local_page_name']}' | ref_page='{m['ref_page_name']}'")
        print(f"    local: '{m['local']}' | ref: '{m['ref']}'")

    print(f"\nElement mismatches: {len(results['element_mismatches'])}")
    for m in results["element_mismatches"]:
        print(f"  [{m['path']}] {m['selector']}: local={m['local']} ref={m['ref']}")

    # Overall
    all_titles_ok = len(results["title_mismatches"]) == 0
    all_elements_ok = len(results["element_mismatches"]) == 0
    no_internal_404s = len(results["local_404s_internal"]) == 0
    no_real_errors = len(results["local_console_errors"]) == 0

    print(f"\n{'='*60}")
    print(f"RESULT: Titles={'✓' if all_titles_ok else '✗'} | Elements={'✓' if all_elements_ok else '✗'} | No Internal 404s={'✓' if no_internal_404s else '✗'} | No Real Errors={'✓' if no_real_errors else '✗'}")
    print(f"{'='*60}")

    return results

if __name__ == "__main__":
    asyncio.run(verify())
