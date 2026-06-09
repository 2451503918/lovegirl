#!/usr/bin/env python3
"""Detailed DOM comparison between reference and local site for each page."""
import asyncio
import json
from playwright.async_api import async_playwright

REF = "https://love-really.kikiw.cn"
LOCAL = "http://localhost:8090"
PAGES = ["/", "/about.php", "/albums.php", "/articles.php", "/lovelist.php", "/timeline.php"]

async def analyze_page(page):
    """Analyze a page's DOM structure in detail."""
    result = await page.evaluate("""() => {
        const data = {
            title: document.title,
            body_classes: document.body.className,
            // Header structure
            header_ids: Array.from(document.querySelectorAll('.header-wrap [id]')).map(e => e.id),
            header_classes: Array.from(document.querySelectorAll('.header-wrap [class]')).map(e => 
                Array.from(e.classList).filter(c => !c.startsWith('ph-') && !c.startsWith('lg-') && c.length > 3).join('.')
            ).filter(c => c.length > 0).slice(0, 20),
            // Nav structure
            nav_item_count: document.querySelectorAll('.lgnewui-nav-island-item').length,
            nav_items: Array.from(document.querySelectorAll('.lgnewui-nav-island-item')).map(a => ({
                href: a.getAttribute('href'),
                text: a.textContent.trim(),
                page: a.dataset.page
            })),
            // Key elements existence
            weather_btn: !!document.getElementById('lgHeaderVisitorWeather'),
            map_btn: !!document.getElementById('lgMapOpenBtn'),
            more_btn: !!document.getElementById('lgHeaderMoreBtn'),
            more_panel: !!document.getElementById('lgHeaderMorePanel'),
            capsule_back: !!document.querySelector('.lg-capsule-back'),
            header_divider: !!document.querySelector('.lgnewui-header-divider'),
            // Footer
            footer_exists: !!document.querySelector('.footer-warp, footer, #footer'),
            footer_animal: !!document.getElementById('footer-animal'),
            // Music player
            music_player: !!document.querySelector('#nav-music, .aplayer'),
            // Floating actions
            floating_actions: !!document.getElementById('lgnewuiFloatingActions'),
            // Map overlay
            map_overlay: !!document.getElementById('lgMapOverlay'),
            // Mask
            mask: !!document.getElementById('mask'),
            // Message modal
            message_modal: !!document.getElementById('lgmsgCommentModal'),
            // Music playlist
            music_playlist: !!document.getElementById('musicPlaylist'),
            // Music modal
            music_modal: !!document.getElementById('musicModal'),
            // Emoji panel
            emoji_panel: !!document.getElementById('lgmsgEmojiPanel'),
            // Confirm overlay
            confirm_overlay: !!document.getElementById('lgmsgConfirmOverlay'),
            // Mes overlay
            mes: !!document.getElementById('mes'),
            // Loader
            loader: !!document.getElementById('loader-wrapper'),
            // Page header
            page_header: !!document.querySelector('.lgnewui-page-header'),
            hero_title: !!document.getElementById('lgnewuiHeroTitle'),
            // Inline style count
            inline_style_count: document.querySelectorAll('style').length,
            // All top-level IDs
            all_ids: Array.from(document.querySelectorAll('[id]')).map(e => e.id).slice(0, 60),
        };
        return data;
    }""")
    return result

async def main():
    async with async_playwright() as p:
        browser = await p.chromium.launch(args=['--no-sandbox', '--disable-setuid-sandbox'])
        context = await browser.new_context(viewport={"width": 1440, "height": 900}, locale="zh-CN")
        
        for page_path in PAGES:
            name = page_path.replace("/", "").replace(".php", "") or "index"
            print(f"\n{'='*80}")
            print(f"PAGE: {name} ({page_path})")
            print(f"{'='*80}")
            
            # Reference
            page1 = await context.new_page()
            try:
                await page1.goto(f"{REF}{page_path}", wait_until="load", timeout=60000)
                await page1.wait_for_timeout(3000)
                ref = await analyze_page(page1)
            except Exception as e:
                print(f"  [REF] Error: {e}")
                ref = {}
            await page1.close()
            
            # Local
            page2 = await context.new_page()
            try:
                await page2.goto(f"{LOCAL}{page_path}", wait_until="load", timeout=60000)
                await page2.wait_for_timeout(3000)
                local = await analyze_page(page2)
            except Exception as e:
                print(f"  [LOCAL] Error: {e}")
                local = {}
            await page2.close()
            
            # Compare key elements
            checks = [
                'weather_btn', 'map_btn', 'more_btn', 'more_panel', 
                'capsule_back', 'header_divider',
                'footer_exists', 'footer_animal', 'music_player',
                'floating_actions', 'map_overlay', 'mask',
                'message_modal', 'music_playlist', 'music_modal',
                'emoji_panel', 'confirm_overlay', 'mes',
                'loader', 'page_header', 'hero_title'
            ]
            
            mismatches = []
            for check in checks:
                ref_val = ref.get(check, False)
                local_val = local.get(check, False)
                if ref_val != local_val:
                    mismatches.append(f"  {check}: REF={ref_val}, LOCAL={local_val}")
            
            if mismatches:
                print(f"  MISMATCHES ({len(mismatches)}):")
                for m in mismatches:
                    print(m)
            else:
                print(f"  All key elements match!")
            
            # Compare nav items
            ref_nav = ref.get('nav_items', [])
            local_nav = local.get('nav_items', [])
            if ref_nav != local_nav:
                print(f"  NAV ITEMS DIFFER:")
                print(f"    REF: {json.dumps(ref_nav, ensure_ascii=False)}")
                print(f"    LOCAL: {json.dumps(local_nav, ensure_ascii=False)}")
            
            # Compare IDs that are in ref but not local
            ref_ids = set(ref.get('all_ids', []))
            local_ids = set(local.get('all_ids', []))
            missing_ids = ref_ids - local_ids
            if missing_ids:
                # Filter out data-dependent IDs
                important_missing = [i for i in missing_ids if not any(x in i for x in ['day-counter', 'weather', 'timeline', 'moment', 'event-', 'loveday'])]
                if important_missing:
                    print(f"  MISSING IDs in LOCAL: {important_missing}")

        await browser.close()

asyncio.run(main())
