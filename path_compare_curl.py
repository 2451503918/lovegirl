#!/usr/bin/env python3
"""Compare CSS/JS paths using curl + regex."""
import subprocess
import re

REF = "https://love-really.kikiw.cn"
LOCAL = "http://localhost:8090"
PAGES = ["/", "/about.php", "/albums.php", "/articles.php", "/lovelist.php", "/timeline.php"]

def extract_paths(html, base_url):
    """Extract CSS and JS paths from HTML."""
    css_paths = re.findall(r'<link[^>]+href=["\']([^"\']+\.css[^"\']*)["\']', html)
    js_paths = re.findall(r'<script[^>]+src=["\']([^"\']+\.js[^"\']*)["\']', html)
    
    # Convert to path-only
    def to_path(url):
        if url.startswith('//'):
            url = 'https:' + url
        if url.startswith('http'):
            # Remove protocol and domain
            m = re.match(r'https?://[^/]+(/.*)', url)
            if m:
                path = m.group(1)
            else:
                return url
        else:
            path = url
        # Remove query string
        path = re.sub(r'\?.*$', '', path)
        return path
    
    return [to_path(p) for p in css_paths], [to_path(p) for p in js_paths]

all_ref_css = set()
all_ref_js = set()
all_local_css = set()
all_local_js = set()

for page_path in PAGES:
    name = page_path.replace("/", "").replace(".php", "") or "index"
    
    # Reference
    try:
        ref_html = subprocess.check_output(['curl', '-sL', f'{REF}{page_path}'], timeout=30).decode('utf-8', errors='ignore')
        ref_css, ref_js = extract_paths(ref_html, REF)
        all_ref_css.update(ref_css)
        all_ref_js.update(ref_js)
        print(f"[REF] {name}: {len(ref_css)} CSS, {len(ref_js)} JS")
    except Exception as e:
        print(f"[REF] {name}: Error - {e}")
    
    # Local
    try:
        local_html = subprocess.check_output(['curl', '-sL', f'{LOCAL}{page_path}'], timeout=30).decode('utf-8', errors='ignore')
        local_css, local_js = extract_paths(local_html, LOCAL)
        all_local_css.update(local_css)
        all_local_js.update(local_js)
        print(f"[LOCAL] {name}: {len(local_css)} CSS, {len(local_js)} JS")
    except Exception as e:
        print(f"[LOCAL] {name}: Error - {e}")

# Compare
print("\n" + "="*80)
print("CSS COMPARISON")
print("="*80)
missing_css = all_ref_css - all_local_css
extra_css = all_local_css - all_ref_css
print(f"  REF total: {len(all_ref_css)} | LOCAL total: {len(all_local_css)}")
print(f"  Missing in LOCAL: {len(missing_css)}")
for p in sorted(missing_css):
    print(f"    - {p}")
print(f"  Extra in LOCAL: {len(extra_css)}")
for p in sorted(extra_css):
    print(f"    + {p}")

print("\n" + "="*80)
print("JS COMPARISON")
print("="*80)
missing_js = all_ref_js - all_local_js
extra_js = all_local_js - all_ref_js
print(f"  REF total: {len(all_ref_js)} | LOCAL total: {len(all_local_js)}")
print(f"  Missing in LOCAL: {len(missing_js)}")
for p in sorted(missing_js):
    print(f"    - {p}")
print(f"  Extra in LOCAL: {len(extra_js)}")
for p in sorted(extra_js):
    print(f"    + {p}")

# Also check which local files actually exist
print("\n" + "="*80)
print("LOCAL FILE EXISTENCE CHECK")
print("="*80)
missing_files = []
for p in sorted(all_local_css | all_local_js):
    if p.startswith('/'):
        full_path = f'/workspace{p}'
        try:
            with open(full_path, 'rb') as f:
                size = len(f.read())
                if size < 50:
                    print(f"  SUSPICIOUS (only {size} bytes): {p}")
        except FileNotFoundError:
            missing_files.append(p)
            print(f"  MISSING FILE: {p}")

if missing_files:
    print(f"\n  Total missing files: {len(missing_files)}")
else:
    print("  All referenced local files exist!")
