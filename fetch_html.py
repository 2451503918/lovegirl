"""
抓取参考站和本地站的完整HTML源码，保存到文件供对比
"""
from playwright.sync_api import sync_playwright
import os

os.makedirs("/workspace/html_compare", exist_ok=True)

REF = "https://love-really.kikiw.cn"
LOCAL = "http://localhost:8090"

PAGES = [
    ("index", "/"),
    ("timeline", "/timeline.php"),
    ("about", "/about.php"),
    ("messages", "/messages.php"),
    ("albums", "/albums.php"),
    ("articles", "/articles.php"),
    ("leaving", "/leaving.php"),
    ("lovelist", "/lovelist.php"),
    ("list", "/list.php"),
]

with sync_playwright() as p:
    browser = p.chromium.launch(headless=True)
    
    for name, path in PAGES:
        # 参考站
        page = browser.new_page(viewport={"width": 1440, "height": 900})
        try:
            page.goto(f"{REF}{path}", timeout=60000, wait_until="networkidle")
            page.wait_for_timeout(1000)
            html = page.content()
            with open(f"/workspace/html_compare/ref_{name}.html", "w", encoding="utf-8") as f:
                f.write(html)
            print(f"  ✅ ref_{name}.html ({len(html)} chars)")
        except Exception as e:
            print(f"  ❌ ref_{name}: {e}")
        page.close()
        
        # 本地站
        page = browser.new_page(viewport={"width": 1440, "height": 900})
        try:
            page.goto(f"{LOCAL}{path}", timeout=30000, wait_until="networkidle")
            page.wait_for_timeout(1000)
            html = page.content()
            with open(f"/workspace/html_compare/local_{name}.html", "w", encoding="utf-8") as f:
                f.write(html)
            print(f"  ✅ local_{name}.html ({len(html)} chars)")
        except Exception as e:
            print(f"  ❌ local_{name}: {e}")
        page.close()
    
    browser.close()
    print("\n所有HTML源码已保存到 /workspace/html_compare/")
