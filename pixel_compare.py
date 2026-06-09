#!/usr/bin/env python3
"""Pixel-level comparison of screenshots"""
import os
from PIL import Image
import numpy as np

SCREENSHOT_DIR = "/workspace/screenshots_compare"
PAGES = ["首页", "关于", "相册", "点滴", "清单", "轨迹", "留言"]

def compare_images(img1_path, img2_path):
    """Compare two images and return similarity percentage"""
    img1 = Image.open(img1_path).convert("RGB")
    img2 = Image.open(img2_path).convert("RGB")

    # Resize to same dimensions if needed
    if img1.size != img2.size:
        # Use the smaller dimensions
        w = min(img1.width, img2.width)
        h = min(img1.height, img2.height)
        img1 = img1.resize((w, h))
        img2 = img2.resize((w, h))

    arr1 = np.array(img1, dtype=np.float32)
    arr2 = np.array(img2, dtype=np.float32)

    # Calculate similarity
    diff = np.abs(arr1 - arr2)
    max_diff = 255.0
    similarity = 1.0 - (np.mean(diff) / max_diff)

    # Calculate percentage of identical pixels (within threshold)
    threshold = 10  # Allow small color differences
    identical = np.all(diff <= threshold, axis=2)
    identical_pct = np.mean(identical) * 100

    return similarity * 100, identical_pct

print(f"{'Page':<8} {'Similarity':>12} {'Identical%':>12} {'Status'}")
print("-" * 50)

total_sim = 0
total_ident = 0
count = 0

for name in PAGES:
    local_path = f"{SCREENSHOT_DIR}/local_{name}.png"
    ref_path = f"{SCREENSHOT_DIR}/ref_{name}.png"

    if not os.path.exists(local_path) or not os.path.exists(ref_path):
        print(f"{name:<8} {'MISSING':>12} {'':>12} ✗")
        continue

    sim, ident = compare_images(local_path, ref_path)
    total_sim += sim
    total_ident += ident
    count += 1

    status = "✓" if sim > 90 else "⚠" if sim > 70 else "✗"
    print(f"{name:<8} {sim:>11.1f}% {ident:>11.1f}% {status}")

if count > 0:
    print("-" * 50)
    avg_sim = total_sim / count
    avg_ident = total_ident / count
    print(f"{'AVG':<8} {avg_sim:>11.1f}% {avg_ident:>11.1f}% {'✓' if avg_sim > 90 else '⚠'}")
