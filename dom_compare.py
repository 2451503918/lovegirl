#!/usr/bin/env python3
"""Compare DOM structure between reference and local site."""
import asyncio
import json
from playwright.async_api import async_playwright

REF = "https://love-really.kikiw.cn"
LOCAL = "http://localhost:8090"
PAGES = ["/", "/about.php", "/loveImg.php", "/articles.php", "/list.php", "/timeline.php"]

async def analyze_page(page, url, name):
    """Analyze a page's DOM structure."""
    result = await page.evaluate("""() => {
        const data = {
            title: document.title,
            body_classes: document.body.className,
            // Check navigation
            nav_exists: !!document.querySelector('nav, .lg-nav, .lgnewui-nav, #lgnewuiNav, .lg-header'),
            nav_html_length: (document.querySelector('nav, .lg-nav, .lgnewui-nav, #lgnewuiNav, .lg-header') || {}).innerHTML?.length || 0,
            // Check hero section
            hero_exists: !!document.querySelector('.hero, .home-hero, .lg-hero, #hero, .lgnewui-home-hero'),
            hero_classes: (document.querySelector('.hero, .home-hero, .lg-hero, #hero, .lgnewui-home-hero') || {}).className || '',
            // Check footer
            footer_exists: !!document.querySelector('footer, .lg-footer, #footer, .site-footer'),
            footer_html_length: (document.querySelector('footer, .lg-footer, #footer, .site-footer') || {}).innerHTML?.length || 0,
            // Check music player
            music_player_exists: !!document.querySelector('#nav-music, .aplayer, .lg-music-player'),
            // Check floating actions
            floating_actions_exists: !!document.querySelector('#lgnewuiFloatingActions, .floating-actions'),
            // Check footer animal
            footer_animal_exists: !!document.querySelector('#footer-animal, .footer-animal'),
            // Check map overlay
            map_overlay_exists: !!document.querySelector('#lgMapOverlay, .lg-map-overlay'),
            // Check mask
            mask_exists: !!document.querySelector('#mask, .mask-overlay'),
            // Check message modal
            message_modal_exists: !!document.querySelector('#lgmsgCommentModal, .lgmsg-comment-modal'),
            // Check music playlist
            music_playlist_exists: !!document.querySelector('#musicPlaylist, .music-playlist'),
            // Check music modal
            music_modal_exists: !!document.querySelector('#musicModal, .music-modal'),
            // Check emoji panel
            emoji_panel_exists: !!document.querySelector('#lgmsgEmojiPanel, .lgmsg-emoji-panel'),
            // Check confirm overlay
            confirm_overlay_exists: !!document.querySelector('#lgmsgConfirmOverlay, .lgmsg-confirm-overlay'),
            // Check mes overlay
            mes_exists: !!document.querySelector('#mes, .mes-overlay'),
            // Get all CSS classes used in body
            all_section_ids: Array.from(document.querySelectorAll('[id]')).map(e => e.id).slice(0, 50),
            // Check main content area
            main_content_classes: (document.querySelector('main, .main-content, .lg-content, #content, .container') || {}).className || '',
            // Check specific page elements
            page_specific: {}
        };
        
        // Page-specific checks
        if (window.location.pathname === '/' || window.location.pathname === '') {
            const hero = document.querySelector('.lgnewui-home-hero, .hero, .home-hero');
            data.page_specific = {
                hero_height: hero ? window.getComputedStyle(hero).height : 'N/A',
                hero_bg: hero ? window.getComputedStyle(hero).backgroundImage.slice(0, 80) : 'N/A',
                avatar_count: document.querySelectorAll('.avatar, .boy-avatar, .girl-avatar, .lg-avatar').length,
                card_count: document.querySelectorAll('.card, .lg-card, .timeline-card').length,
            };
        } else if (window.location.pathname.includes('about')) {
            data.page_specific = {
                chat_exists: !!document.querySelector('.lgnewui-chat, .botui, .chat-container, #lgChat'),
                chat_classes: (document.querySelector('.lgnewui-chat, .botui, .chat-container, #lgChat') || {}).className || '',
            };
        } else if (window.location.pathname.includes('loveImg') || window.location.pathname.includes('album')) {
            data.page_specific = {
                masonry_exists: !!document.querySelector('.masonry, .grid, .gallery-grid, .album-grid'),
                image_count: document.querySelectorAll('.gallery-item, .album-item, .grid-item, .masonry-item').length,
            };
        } else if (window.location.pathname.includes('article')) {
            data.page_specific = {
                article_count: document.querySelectorAll('.article-card, .article-item, .post-card').length,
            };
        } else if (window.location.pathname.includes('list')) {
            data.page_specific = {
                list_count: document.querySelectorAll('.list-item, .lovelist-item, .love-list-item').length,
            };
        } else if (window.location.pathname.includes('timeline')) {
            data.page_specific = {
                timeline_exists: !!document.querySelector('.timeline, .lg-timeline'),
                timeline_items: document.querySelectorAll('.timeline-item, .lg-timeline-item').length,
            };
        }
        
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
            print(f"PAGE: {name}")
            print(f"{'='*80}")
            
            # Analyze reference
            page1 = await context.new_page()
            try:
                await page1.goto(f"{REF}{page_path}", wait_until="load", timeout=60000)
                await page1.wait_for_timeout(3000)
                ref_data = await analyze_page(page1, f"{REF}{page_path}", name)
                print(f"  [REF] Title: {ref_data.get('title', 'N/A')}")
                print(f"  [REF] Nav: {ref_data.get('nav_exists', False)}")
                print(f"  [REF] Hero: {ref_data.get('hero_exists', False)}")
                print(f"  [REF] Footer: {ref_data.get('footer_exists', False)}")
                print(f"  [REF] Music Player: {ref_data.get('music_player_exists', False)}")
                print(f"  [REF] Floating Actions: {ref_data.get('floating_actions_exists', False)}")
                print(f"  [REF] Footer Animal: {ref_data.get('footer_animal_exists', False)}")
                print(f"  [REF] Map Overlay: {ref_data.get('map_overlay_exists', False)}")
                print(f"  [REF] Mask: {ref_data.get('mask_exists', False)}")
                print(f"  [REF] IDs: {ref_data.get('all_section_ids', [])[:20]}")
                print(f"  [REF] Page Specific: {json.dumps(ref_data.get('page_specific', {}), ensure_ascii=False)}")
            except Exception as e:
                print(f"  [REF] Error: {e}")
                ref_data = {}
            await page1.close()
            
            # Analyze local
            page2 = await context.new_page()
            try:
                await page2.goto(f"{LOCAL}{page_path}", wait_until="load", timeout=60000)
                await page2.wait_for_timeout(3000)
                local_data = await analyze_page(page2, f"{LOCAL}{page_path}", name)
                print(f"  [LOCAL] Title: {local_data.get('title', 'N/A')}")
                print(f"  [LOCAL] Nav: {local_data.get('nav_exists', False)}")
                print(f"  [LOCAL] Hero: {local_data.get('hero_exists', False)}")
                print(f"  [LOCAL] Footer: {local_data.get('footer_exists', False)}")
                print(f"  [LOCAL] Music Player: {local_data.get('music_player_exists', False)}")
                print(f"  [LOCAL] Floating Actions: {local_data.get('floating_actions_exists', False)}")
                print(f"  [LOCAL] Footer Animal: {local_data.get('footer_animal_exists', False)}")
                print(f"  [LOCAL] Map Overlay: {local_data.get('map_overlay_exists', False)}")
                print(f"  [LOCAL] Mask: {local_data.get('mask_exists', False)}")
                print(f"  [LOCAL] IDs: {local_data.get('all_section_ids', [])[:20]}")
                print(f"  [LOCAL] Page Specific: {json.dumps(local_data.get('page_specific', {}), ensure_ascii=False)}")
            except Exception as e:
                print(f"  [LOCAL] Error: {e}")
                local_data = {}
            await page2.close()
            
            # Compare
            print(f"\n  --- DIFFERENCES ---")
            checks = ['nav_exists', 'hero_exists', 'footer_exists', 'music_player_exists', 
                      'floating_actions_exists', 'footer_animal_exists', 'map_overlay_exists', 'mask_exists',
                      'emoji_panel_exists', 'confirm_overlay_exists', 'mes_exists', 'music_playlist_exists', 'music_modal_exists']
            for check in checks:
                ref_val = ref_data.get(check, False)
                local_val = local_data.get(check, False)
                if ref_val != local_val:
                    print(f"  MISMATCH {check}: REF={ref_val}, LOCAL={local_val}")

        await browser.close()

asyncio.run(main())
