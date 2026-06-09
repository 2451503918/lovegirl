#!/usr/bin/env python3
"""Comprehensive structural verification"""
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

async def check_page_structure(context, url, label):
    """Check key structural elements and their CSS properties"""
    page = await context.new_page()
    try:
        await page.goto(url, wait_until="networkidle", timeout=15000)
        await page.wait_for_timeout(1500)
    except:
        await page.close()
        return {"error": "navigation failed"}

    result = {}

    # 1. Check navigation structure
    nav_info = await page.evaluate("""() => {
        const nav = document.querySelector('.lgnewui-nav-dynamic-island');
        if (!nav) return {exists: false};
        const rect = nav.getBoundingClientRect();
        const style = getComputedStyle(nav);
        return {
            exists: true,
            position: style.position,
            width: Math.round(rect.width),
            height: Math.round(rect.height),
            top: Math.round(rect.top),
            left: Math.round(rect.left),
            borderRadius: style.borderRadius,
            backdropFilter: style.backdropFilter,
        };
    }""")
    result["nav"] = nav_info

    # 2. Check header
    header_info = await page.evaluate("""() => {
        const header = document.querySelector('#lgnewuiHeaderWrap') || document.querySelector('.lgnewui-header-wrap');
        if (!header) return {exists: false};
        const rect = header.getBoundingClientRect();
        const style = getComputedStyle(header);
        return {
            exists: true,
            width: Math.round(rect.width),
            height: Math.round(rect.height),
            top: Math.round(rect.top),
            position: style.position,
            backdropFilter: style.backdropFilter,
        };
    }""")
    result["header"] = header_info

    # 3. Check floating actions
    fa_info = await page.evaluate("""() => {
        const fa = document.querySelector('#lgnewuiFloatingActions');
        if (!fa) return {exists: false};
        const rect = fa.getBoundingClientRect();
        return {
            exists: true,
            width: Math.round(rect.width),
            height: Math.round(rect.height),
            bottom: Math.round(window.innerHeight - rect.bottom),
            right: Math.round(window.innerWidth - rect.right),
        };
    }""")
    result["floating_actions"] = fa_info

    # 4. Check body background
    body_bg = await page.evaluate("getComputedStyle(document.body).backgroundColor")
    result["body_bg"] = body_bg

    # 5. Check font loading status
    font_status = await page.evaluate("""() => {
        return document.fonts.ready.then(() => {
            const fonts = Array.from(document.fonts);
            const loaded = fonts.filter(f => f.status === 'loaded').length;
            const total = fonts.length;
            return {loaded, total};
        });
    }""")
    result["fonts"] = font_status

    # 6. Check key CSS custom properties
    css_vars = await page.evaluate("""() => {
        const root = document.documentElement;
        const style = getComputedStyle(root);
        return {
            'lg-primary': style.getPropertyValue('--lg-primary').trim(),
            'lg-bg': style.getPropertyValue('--lg-bg').trim(),
            'lg-header-bg': style.getPropertyValue('--lg-header-bg').trim(),
        };
    }""")
    result["css_vars"] = css_vars

    # 7. Check key elements existence
    elements = await page.evaluate("""() => {
        const selectors = [
            '#lgnewuiStickySentinel',
            '#musicInfoTime',
            '#music',
            '#mask',
            '#musicPlaylist',
            '#musicModal',
            '#lgMapOverlay',
            '#footer-animal',
            '.lgnewui-capsule-back',
        ];
        const result = {};
        for (const sel of selectors) {
            result[sel] = document.querySelector(sel) !== null;
        }
        return result;
    }""")
    result["elements"] = elements

    # 8. Check page-specific elements
    page_specific = await page.evaluate("""() => {
        const result = {};
        // Home page specific
        result['homePage'] = document.querySelector('#homePage') !== null;
        result['carousel'] = document.querySelector('.list.mask_black, .list') !== null;
        result['avatarArea'] = document.querySelector('.avatarArea') !== null;
        result['mosaicGrid'] = document.querySelector('.lgnewui-mosaic-grid') !== null;
        result['weatherCard'] = document.querySelector('.lgnewui-home-weather-card') !== null;
        // About page
        result['chatContainer'] = document.querySelector('.lgnewui-chat-container, .botui-container') !== null;
        // Albums page
        result['masonryLayout'] = document.querySelector('.masonry, .gallery') !== null;
        // Timeline
        result['timelineContainer'] = document.querySelector('.timeline, #timeline') !== null;
        // Messages
        result['messageForm'] = document.querySelector('.leaving-form, #leavingForm') !== null;
        return result;
    }""")
    result["page_specific"] = page_specific

    await page.close()
    return result

async def main():
    async with async_playwright() as p:
        browser = await p.chromium.launch(headless=True, args=['--no-sandbox'])
        context = await browser.new_context(viewport={"width": 1280, "height": 800})

        for path, name in PAGES:
            print(f"\n{'='*60}")
            print(f"Page: {name} ({path})")
            print(f"{'='*60}")

            local = await check_page_structure(context, LOCAL + path, "local")
            ref = await check_page_structure(context, REF + path, "ref")

            # Compare nav
            print(f"\n  Navigation:")
            if local.get("nav", {}).get("exists") and ref.get("nav", {}).get("exists"):
                ln = local["nav"]
                rn = ref["nav"]
                w_diff = abs(ln.get("width", 0) - rn.get("width", 0))
                h_diff = abs(ln.get("height", 0) - rn.get("height", 0))
                pos_match = ln.get("position") == rn.get("position")
                print(f"    Width diff: {w_diff}px | Height diff: {h_diff}px | Position: {'✓' if pos_match else '✗'} ({ln.get('position')} vs {rn.get('position')})")
                print(f"    BorderRadius: local={ln.get('borderRadius')} ref={rn.get('borderRadius')}")
                print(f"    BackdropFilter: local={ln.get('backdropFilter','')[:30]} ref={rn.get('backdropFilter','')[:30]}")
            else:
                print(f"    local exists: {local.get('nav',{}).get('exists')} | ref exists: {ref.get('nav',{}).get('exists')}")

            # Compare header
            print(f"\n  Header:")
            if local.get("header", {}).get("exists") and ref.get("header", {}).get("exists"):
                lh = local["header"]
                rh = ref["header"]
                h_diff = abs(lh.get("height", 0) - rh.get("height", 0))
                print(f"    Height diff: {h_diff}px | BackdropFilter: local={lh.get('backdropFilter','')[:30]} ref={rh.get('backdropFilter','')[:30]}")
            else:
                print(f"    local exists: {local.get('header',{}).get('exists')} | ref exists: {ref.get('header',{}).get('exists')}")

            # Compare floating actions
            print(f"\n  Floating Actions:")
            if local.get("floating_actions", {}).get("exists") and ref.get("floating_actions", {}).get("exists"):
                lfa = local["floating_actions"]
                rfa = ref["floating_actions"]
                b_diff = abs(lfa.get("bottom", 0) - rfa.get("bottom", 0))
                r_diff = abs(lfa.get("right", 0) - rfa.get("right", 0))
                print(f"    Bottom diff: {b_diff}px | Right diff: {r_diff}px")
            else:
                print(f"    local exists: {local.get('floating_actions',{}).get('exists')} | ref exists: {ref.get('floating_actions',{}).get('exists')}")

            # Compare elements
            print(f"\n  Shared Elements:")
            local_el = local.get("elements", {})
            ref_el = ref.get("elements", {})
            for sel in sorted(set(list(local_el.keys()) + list(ref_el.keys()))):
                l = local_el.get(sel, False)
                r = ref_el.get(sel, False)
                status = "✓" if l == r else "✗"
                print(f"    {status} {sel}: local={l} ref={r}")

            # Page-specific elements
            print(f"\n  Page-specific:")
            local_ps = local.get("page_specific", {})
            ref_ps = ref.get("page_specific", {})
            for key in sorted(set(list(local_ps.keys()) + list(ref_ps.keys()))):
                l = local_ps.get(key, False)
                r = ref_ps.get(key, False)
                if l or r:  # Only show if at least one exists
                    status = "✓" if l == r else "⚠"
                    print(f"    {status} {key}: local={l} ref={r}")

            # CSS vars
            print(f"\n  CSS Variables:")
            local_vars = local.get("css_vars", {})
            ref_vars = ref.get("css_vars", {})
            for key in sorted(set(list(local_vars.keys()) + list(ref_vars.keys()))):
                l = local_vars.get(key, "")
                r = ref_vars.get(key, "")
                status = "✓" if l == r else "⚠"
                print(f"    {status} {key}: local={l} ref={r}")

        await browser.close()

if __name__ == "__main__":
    asyncio.run(main())
