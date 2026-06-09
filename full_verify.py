#!/usr/bin/env python3
"""Full verification: screenshots + 404 + console errors + DOM element checks."""
import asyncio
import json
from playwright.async_api import async_playwright

REF = "https://love-really.kikiw.cn"
LOCAL = "http://localhost:8090"
PAGES = ["/", "/about.php", "/albums.php", "/articles.php", "/lovelist.php", "/timeline.php"]

async def main():
    async with async_playwright() as p:
        browser = await p.chromium.launch(args=['--no-sandbox', '--disable-setuid-sandbox'])
        context = await browser.new_context(viewport={"width": 1440, "height": 900}, locale="zh-CN")
        
        results = {}
        for page_path in PAGES:
            name = page_path.replace("/", "").replace(".php", "") or "index"
            results[name] = {
                "ref_ok": False, "local_ok": False,
                "local_404s": [], "local_errors": [],
                "ref_elements": {}, "local_elements": {},
                "mismatches": []
            }
            
            # === REFERENCE SITE ===
            page1 = await context.new_page()
            try:
                await page1.goto(f"{REF}{page_path}", wait_until="load", timeout=60000)
                await page1.wait_for_timeout(5000)
                await page1.screenshot(path=f"/workspace/screenshots/verify_ref_{name}.png", full_page=False)
                
                ref_data = await page1.evaluate("""() => {
                    const checks = {
                        title: document.title,
                        nav: !!document.querySelector('.lgnewui-nav-island-container'),
                        header_wrap: !!document.querySelector('.header-wrap'),
                        sticky_sentinel: !!document.getElementById('lgnewuiStickySentinel'),
                        weather_btn: !!document.getElementById('lgHeaderVisitorWeather'),
                        map_btn: !!document.getElementById('lgMapOpenBtn'),
                        more_btn: !!document.getElementById('lgHeaderMoreBtn'),
                        more_panel: !!document.getElementById('lgHeaderMorePanel'),
                        footer: !!document.querySelector('.footer-warp'),
                        footer_animal: !!document.getElementById('footer-animal'),
                        music_player: !!document.querySelector('#nav-music'),
                        floating_actions: !!document.getElementById('lgnewuiFloatingActions'),
                        map_overlay: !!document.getElementById('lgMapOverlay'),
                        mask: !!document.getElementById('mask'),
                        music_info_time: !!document.getElementById('musicInfoTime'),
                        music_audio: !!document.getElementById('music'),
                        emoji_panel: !!document.getElementById('lgmsgEmojiPanel'),
                        message_modal: !!document.getElementById('lgmsgCommentModal'),
                        music_playlist: !!document.getElementById('musicPlaylist'),
                        music_modal: !!document.getElementById('musicModal'),
                        loader: !!document.getElementById('loader-wrapper'),
                        page_header: !!document.querySelector('.lgnewui-page-header'),
                        hero_title: !!document.getElementById('lgnewuiHeroTitle'),
                        confirm_overlay: !!document.getElementById('lgmsgConfirmOverlay'),
                        mes: !!document.getElementById('mes'),
                    };
                    return checks;
                }""")
                results[name]["ref_elements"] = ref_data
                results[name]["ref_ok"] = True
                print(f"[REF] {name} - OK")
            except Exception as e:
                print(f"[REF] {name} - Error: {e}")
            await page1.close()
            
            # === LOCAL SITE ===
            page2 = await context.new_page()
            
            def on_response(response):
                if response.status >= 400 and 'localhost' in response.url:
                    results[name]["local_404s"].append({"url": response.url, "status": response.status})
            
            def on_console(msg):
                if msg.type == "error":
                    text = msg.text[:200]
                    if 'Geetest' not in text and 'music.126.net' not in text:
                        results[name]["local_errors"].append(text)
            
            page2.on("response", on_response)
            page2.on("console", on_console)
            
            try:
                await page2.goto(f"{LOCAL}{page_path}", wait_until="load", timeout=60000)
                await page2.wait_for_timeout(5000)
                await page2.screenshot(path=f"/workspace/screenshots/verify_local_{name}.png", full_page=False)
                
                local_data = await page2.evaluate("""() => {
                    const checks = {
                        title: document.title,
                        nav: !!document.querySelector('.lgnewui-nav-island-container'),
                        header_wrap: !!document.querySelector('.header-wrap'),
                        sticky_sentinel: !!document.getElementById('lgnewuiStickySentinel'),
                        weather_btn: !!document.getElementById('lgHeaderVisitorWeather'),
                        map_btn: !!document.getElementById('lgMapOpenBtn'),
                        more_btn: !!document.getElementById('lgHeaderMoreBtn'),
                        more_panel: !!document.getElementById('lgHeaderMorePanel'),
                        footer: !!document.querySelector('.footer-warp'),
                        footer_animal: !!document.getElementById('footer-animal'),
                        music_player: !!document.querySelector('#nav-music'),
                        floating_actions: !!document.getElementById('lgnewuiFloatingActions'),
                        map_overlay: !!document.getElementById('lgMapOverlay'),
                        mask: !!document.getElementById('mask'),
                        music_info_time: !!document.getElementById('musicInfoTime'),
                        music_audio: !!document.getElementById('music'),
                        emoji_panel: !!document.getElementById('lgmsgEmojiPanel'),
                        message_modal: !!document.getElementById('lgmsgCommentModal'),
                        music_playlist: !!document.getElementById('musicPlaylist'),
                        music_modal: !!document.getElementById('musicModal'),
                        loader: !!document.getElementById('loader-wrapper'),
                        page_header: !!document.querySelector('.lgnewui-page-header'),
                        hero_title: !!document.getElementById('lgnewuiHeroTitle'),
                        confirm_overlay: !!document.getElementById('lgmsgConfirmOverlay'),
                        mes: !!document.getElementById('mes'),
                    };
                    return checks;
                }""")
                results[name]["local_elements"] = local_data
                results[name]["local_ok"] = True
                print(f"[LOCAL] {name} - OK")
            except Exception as e:
                print(f"[LOCAL] {name} - Error: {e}")
            await page2.close()
            
            # Compare elements
            ref = results[name]["ref_elements"]
            local = results[name]["local_elements"]
            for key in ref:
                if ref.get(key, False) != local.get(key, False):
                    results[name]["mismatches"].append(f"{key}: REF={ref.get(key,False)}, LOCAL={local.get(key,False)}")
        
        await browser.close()
        
        # Print results
        print("\n" + "="*80)
        print("FULL VERIFICATION RESULTS")
        print("="*80)
        
        all_pass = True
        for name, data in results.items():
            n404 = len(data["local_404s"])
            nerr = len(data["local_errors"])
            nmis = len(data["mismatches"])
            
            page_ok = data["ref_ok"] and data["local_ok"] and n404 == 0 and nerr == 0 and nmis == 0
            if not page_ok:
                all_pass = False
            
            status = "PASS" if page_ok else "FAIL"
            print(f"\n--- {name} [{status}] ---")
            print(f"  REF loaded: {data['ref_ok']} | LOCAL loaded: {data['local_ok']}")
            print(f"  Local 404s: {n404} | Local errors: {nerr} | Element mismatches: {nmis}")
            
            if data["local_404s"]:
                for r in data["local_404s"][:5]:
                    print(f"    404 [{r['status']}]: {r['url']}")
            if data["local_errors"]:
                for e in data["local_errors"][:5]:
                    print(f"    ERROR: {e[:150]}")
            if data["mismatches"]:
                for m in data["mismatches"]:
                    print(f"    MISMATCH: {m}")
        
        print(f"\n{'='*80}")
        if all_pass:
            print("ALL PAGES PASS! All elements match reference site.")
        else:
            print("SOME PAGES HAVE ISSUES - see details above")

asyncio.run(main())
