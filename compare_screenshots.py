#!/usr/bin/env python3
"""Compare screenshots pixel by pixel and generate diff images."""
from PIL import Image, ImageChops
import os

SCREENSHOTS_DIR = "/workspace/screenshots"
PAGES = ["index", "about", "loveImg", "articles", "list", "timeline"]

for name in PAGES:
    ref_path = os.path.join(SCREENSHOTS_DIR, f"ref_{name}.png")
    local_path = os.path.join(SCREENSHOTS_DIR, f"local_{name}.png")
    diff_path = os.path.join(SCREENSHOTS_DIR, f"diff_{name}.png")
    
    if not os.path.exists(ref_path) or not os.path.exists(local_path):
        print(f"{name}: Missing screenshot, skipping")
        continue
    
    ref_img = Image.open(ref_path)
    local_img = Image.open(local_path)
    
    print(f"\n--- {name} ---")
    print(f"  REF:   {ref_img.size} ({os.path.getsize(ref_path)} bytes)")
    print(f"  LOCAL: {local_img.size} ({os.path.getsize(local_path)} bytes)")
    
    # Resize to same size for comparison
    if ref_img.size != local_img.size:
        # Use the smaller dimensions
        min_w = min(ref_img.size[0], local_img.size[0])
        min_h = min(ref_img.size[1], local_img.size[1])
        ref_img = ref_img.resize((min_w, min_h), Image.LANCZOS)
        local_img = local_img.resize((min_w, min_h), Image.LANCZOS)
        print(f"  Resized both to {min_w}x{min_h} for comparison")
    
    # Calculate difference
    diff = ImageChops.difference(ref_img, local_img)
    
    # Calculate percentage of different pixels
    import numpy as np
    ref_arr = np.array(ref_img)
    local_arr = np.array(local_img)
    diff_arr = np.abs(ref_arr.astype(int) - local_arr.astype(int))
    
    # Count pixels with any channel difference > 10
    significant_diff = np.any(diff_arr > 10, axis=2)
    diff_percentage = np.mean(significant_diff) * 100
    
    print(f"  Difference: {diff_percentage:.1f}% of pixels differ (>10 per channel)")
    
    # Save diff image (amplified for visibility)
    diff_amplified = diff.point(lambda x: x * 3)  # Amplify differences
    diff_amplified.save(diff_path)
    print(f"  Diff saved to {diff_path}")
