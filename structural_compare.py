#!/usr/bin/env python3
"""Structural comparison: check layout, CSS classes, element positions"""
import asyncio
from playwright.async_api import async_playwright

LOCAL = "http://localhost:8090"
REF = "https://love-really.kikiw.cn"

PAGES = [
    ("/", "首页"),
    ("/about.php", "关于"),
    ("/albums.php", "相册"),
    ("/articles.php", "点滴"),
    ("/lovelist.php", "清单"),
    ("/timeline.php", "轨迹"),
    ("/messages.php", "留言"),
]

# Key structural elements to compare
STRUCTURAL_CHECKS = [
    # (selector, property_to_check)
    (".lgnewui-nav-dynamic-island", "Navigation Dynamic Island"),
    ("#lgnewuiHeaderWrap", "Header Wrapper"),
    (".lgnewui-header-inner", "Header Inner"),
    (".lgnewui-capsule", "Capsule Nav"),
    (".lgnewui-base-nav", "Base Nav"),
    ("#lgnewuiFloatingActions", "Floating Actions"),
    ("#footer-animal", "Footer Animal"),
    ("#mask", "Mask Overlay"),
    ("#musicPlaylist", "Music Playlist"),
    ("#musicModal", "Music Modal"),
    ("#lgMapOverlay", "Map Overlay"),
]

async def get_element_info(page, selector):
    """Get element existence, classes, and position"""
    el = await page.query_selector(selector)
    if not el:
        return {"exists": False}

    box = await el.bounding_box()
    classes = await el.get_attribute("class")
    tag = await el.evaluate("el => el.tagName")

    return {
        "exists": True,
        "tag": tag,
        "classes": classes,
        "box": box  # x, y, width, height
    }

async def compare():
    async with async_playwright() as p:
        browser = await p.chromium.launch(headless=True, args=['--no-sandbox'])
        context = await browser.new_context(viewport={"width": 1280, "height": 800})

        for path, name in PAGES:
            print(f"\n{'='*60}")
            print(f"Page: {name} ({path})")
            print(f"{'='*60}")

            # Local
            page_local = await context.new_page()
            try:
                await page_local.goto(LOCAL + path, wait_until="networkidle", timeout=15000)
                await page_local.wait_for_timeout(1000)
            except:
                pass

            # Ref
            page_ref = await context.new_page()
            try:
                await page_ref.goto(REF + path, wait_until="networkidle", timeout=15000)
                await page_ref.wait_for_timeout(1000)
            except:
                pass

            # Compare structural elements
            mismatches = 0
            matches = 0

            for selector, desc in STRUCTURAL_CHECKS:
                local_info = await get_element_info(page_local, selector)
                ref_info = await get_element_info(page_ref, selector)

                local_exists = local_info.get("exists", False)
                ref_exists = ref_info.get("exists", False)

                if local_exists == ref_exists:
                    # Both exist or both don't
                    if local_exists and ref_exists:
                        # Compare position (relative to viewport)
                        local_box = local_info.get("box")
                        ref_box = ref_info.get("box")

                        if local_box and ref_box:
                            # Compare relative position (allow some tolerance for different content)
                            local_rel_y = local_box["y"]
                            ref_rel_y = ref_box["y"]
                            y_diff = abs(local_rel_y - ref_rel_y)

                            local_width = local_box["width"]
                            ref_width = ref_box["width"]
                            w_diff = abs(local_width - ref_width)

                            pos_ok = y_diff < 50 and w_diff < 100
                            status = "✓" if pos_ok else "⚠"
                            if not pos_ok:
                                mismatches += 1
                            else:
                                matches += 1

                            print(f"  {status} {desc}: exists=both | y_diff={y_diff:.0f}px w_diff={w_diff:.0f}px")
                        else:
                            matches += 1
                            print(f"  ✓ {desc}: exists=both | no bounding box")
                    else:
                        matches += 1
                        print(f"  ✓ {desc}: exists=neither")
                else:
                    mismatches += 1
                    print(f"  ✗ {desc}: local={local_exists} ref={ref_exists}")

            # Compare page-level CSS properties
            print(f"\n  --- CSS Properties ---")

            # Check body background
            local_bg = await page_local.evaluate("getComputedStyle(document.body).backgroundColor")
            ref_bg = await page_ref.evaluate("getComputedStyle(document.body).backgroundColor")
            bg_match = local_bg == ref_bg
            print(f"  {'✓' if bg_match else '⚠'} body background: local={local_bg} ref={ref_bg}")

            # Check nav position
            local_nav_pos = await page_local.evaluate("""
                const nav = document.querySelector('.lgnewui-nav-dynamic-island');
                if (!nav) return null;
                const rect = nav.getBoundingClientRect();
                return {top: rect.top, left: rect.left, width: rect.width, height: rect.height};
            """)
            ref_nav_pos = await page_ref.evaluate("""
                const nav = document.querySelector('.lgnewui-nav-dynamic-island');
                if (!nav) return null;
                const rect = nav.getBoundingClientRect();
                return {top: rect.top, left: rect.left, width: rect.width, height: rect.height};
            """)

            if local_nav_pos and ref_nav_pos:
                nav_ok = (abs(local_nav_pos["top"] - ref_nav_pos["top"]) < 10 and
                         abs(local_nav_pos["left"] - ref_nav_pos["left"]) < 10 and
                         abs(local_nav_pos["width"] - ref_nav_pos["width"]) < 50)
                print(f"  {'✓' if nav_ok else '⚠'} Nav position: local={local_nav_pos} ref={ref_nav_pos}")
            else:
                print(f"  ⚠ Nav position: local={local_nav_pos} ref={ref_nav_pos}")

            # Check font loading
            local_fonts = await page_local.evaluate("""
                document.fonts.ready.then(() => document.fonts.size)
            """)
            ref_fonts = await page_ref.evaluate("""
                document.fonts.ready.then(() => document.fonts.size)
            """)
            print(f"  {'✓' if abs(local_fonts - ref_fonts) < 5 else '⚠'} Font count: local={local_fonts} ref={ref_fonts}")

            print(f"\n  Summary: {matches} matches, {mismatches} mismatches")

            await page_local.close()
            await page_ref.close()

        await browser.close()

if __name__ == "__main__":
    asyncio.run(compare())
