import subprocess
import json
from playwright.async_api import async_playwright
import asyncio

async def main():
    async with async_playwright() as p:
        browser = await p.chromium.launch()
        
        # Desktop
        desktop = await browser.new_page(viewport={"width": 1440, "height": 900})
        await desktop.goto("http://localhost:8000/index.php")
        await asyncio.sleep(2)
        
        errors = []
        desktop.on("pageerror", lambda e: errors.append(str(e)))
        
        desktop_info = await desktop.evaluate("""() => {
            const grid = document.querySelector('.lgnewui-grid');
            const gs = grid ? getComputedStyle(grid) : null;
            const lovers = document.querySelector('.lovers-panel');
            const ls = lovers ? getComputedStyle(lovers) : null;
            return {
                title: document.title,
                elements: document.querySelectorAll('div,section,main,nav').length,
                bodyLen: document.body.innerText.length,
                gridCols: gs ? gs.gridTemplateColumns : 'N/A',
                loversDir: ls ? ls.flexDirection : 'N/A'
            };
        }""")
        
        await desktop.screenshot(path="/workspace/check-desktop.png", full_page=True)
        await desktop.close()
        
        # Mobile
        mobile = await browser.new_page(viewport={"width": 375, "height": 812})
        await mobile.goto("http://localhost:8000/index.php")
        await asyncio.sleep(2)
        
        mobile_errors = []
        mobile.on("pageerror", lambda e: mobile_errors.append(str(e)))
        
        mobile_info = await mobile.evaluate("""() => {
            const grids = document.querySelectorAll('.lgnewui-grid');
            const lovers = document.querySelector('.lovers-panel');
            return {
                grids: grids.length,
                loversDir: lovers ? getComputedStyle(lovers).flexDirection : 'N/A',
                loversPad: lovers ? getComputedStyle(lovers).padding : 'N/A',
                bodyHeight: document.body.scrollHeight,
                elements: document.querySelectorAll('*').length
            };
        }""")
        
        await mobile.screenshot(path="/workspace/check-mobile.png", full_page=True)
        await mobile.close()
        await browser.close()
        
        print("=== Desktop (1440px) ===")
        print(f"Title: {desktop_info['title']}")
        print(f"Elements: {desktop_info['elements']}")
        print(f"Body text length: {desktop_info['bodyLen']}")
        print(f"Grid columns: {desktop_info['gridCols']}")
        print(f"Lovers panel: {desktop_info['loversDir']}")
        print(f"Errors: {len(errors)}")
        print(f"Screenshot: /workspace/check-desktop.png")
        
        print("\n=== Mobile (375px) ===")
        print(f"Grids: {mobile_info['grids']}")
        print(f"Lovers direction: {mobile_info['loversDir']}")
        print(f"Lovers padding: {mobile_info['loversPad']}")
        print(f"Body height: {mobile_info['bodyHeight']}")
        print(f"Total elements: {mobile_info['elements']}")
        print(f"Errors: {len(mobile_errors)}")
        print(f"Screenshot: /workspace/check-mobile.png")

asyncio.run(main())
