#!/usr/bin/env python3
"""Check all local CSS/JS files for correctness."""
import os
import subprocess

BASE = "/workspace"
# All CSS and JS paths from the comparison
CSS_PATHS = [
    "/Style/vendor/google-fonts/fonts-non-google.css",
    "/Style/vendor/fontawesome/css/all.min.css",
    "/Style/css/leaving.css",
    "/Style/css/leav.css",
    "/Style/css/lg-message.css",
    "/Style/css/index.css",
    "/Style/css/little.css",
    "/Style/css/loveImg.css",
    "/Style/css/list.css",
    "/Style/Font/font_list/iconfont.css",
    "/Style/toastify/toastify.min.css",
    "/Style/css/APlayer.min.css",
    "/Style/css/aplayer.css",
    "/Style/css/loadinglike.css",
    "/Style/vendor/aos/aos.css",
    "/Style/css/plyr.css",
    "/Style/css/kicode.css",
    "/Style/css/phosphor-regular.css",
    "/Style/css/phosphor-icons.css",
    "/Style/css/phosphor-fill.css",
    "/Style/css/phosphor-duotone.css",
    "/Style/vendor/qweather-icons/qweather-icons.css",
    "/Style/css/nprogress.css",
    "/Style/vendor/remixicon/remixicon.css",
    "/Style/css/lg-tooltip.css",
    "/Style/css/lg-interaction.css",
    "/Style/css/lgnewui-home-style.css",
    "/Style/css/lgnewui-detail.css",
    "/Style/css/lg-mobile-nav.css",
    "/Style/css/lg-header.css",
    "/Style/css/lg-context-menu.css",
    "/Style/css/lg-map.css",
    "/Style/dplayer/DPlayer.min.css",
    "/Style/css/video-modal.css",
    "/Style/Font/font_footer/iconfont.css",
    "/Style/LoveListStyle/styleCarousel.css",
    "/Style/css/timeline.css",
    "/Style/css/lg-chat.css",
    "/assets/css/lg-mini-map.css",
]

JS_PATHS = [
    "/Style/jquery/jquery.min.js",
    "/Style/Font/font_leav/iconfont.js",
    "/Style/js/jquery.pjax.js",
    "/Style/js/plyr.js",
    "/Style/vendor/aos/aos.js",
    "/Style/js/highlight.min.js",
    "/Style/js/lazyload.min.js",
    "/Style/js/masonry.pkgd.min.js",
    "/Style/js/imagesloaded.pkgd.min.js",
    "/Style/js/loading.js",
    "/Style/js/LGNewUiOwO.js",
    "/Style/dplayer/DPlayer.min.js",
    "/Style/js/video-modal.js",
    "/Style/js/geetest-helper.js",
    "/Style/js/nprogress.js",
    "/Style/vendor/confetti/confetti.browser.min.js",
    "/Style/vendor/qrcode/qrcode.min.js",
    "/Style/vendor/qr-code-styling/qr-code-styling.min.js",
    "/assets/js/lg-app.js",
    "/assets/js/lg-components.js",
    "/assets/js/lg-pjax.js",
    "/assets/js/page-messages.js",
    "/Style/toastify/lucide.min.js",
    "/Style/toastify/toastify.js",
    "/Style/js/clipboard.min.js",
    "/assets/js/lg-clipboard.js",
    "/assets/js/lg-tooltip.js",
    "/Style/js/view-image.min.js",
    "/Style/js/mian.js",
    "/Style/LoveListStyle/carousel.umd.js",
    "/Style/LoveListStyle/carousel.thumbs.umd.js",
    "/Style/LoveListStyle/fancybox.umd.js",
    "/assets/js/page-lovelist.js",
    "/assets/js/page-detail.js",
    "/assets/js/page-album-detail.js",
    "/assets/js/page-albums.js",
    "/assets/js/page-articles.js",
    "/assets/js/page-index.js",
    "/assets/js/html2canvas.min.js",
    "/assets/js/lg-chat.js",
    "/assets/js/lg-visitor-hash.js",
    "/assets/js/lg-map.js",
    "/assets/js/lg-interaction.js",
    "/assets/js/lg-context-menu.js",
    "/Style/js/APlayer.min.js",
    "/Style/js/color-thief.min.js",
    "/Style/js/meting.js",
    "/assets/js/music-player.js",
    "/assets/js/lg-mobile-nav.js",
    "/Style/js/wavesurfer.min.js",
    "/assets/js/page-timeline.js",
]

issues = []

for paths, label in [(CSS_PATHS, "CSS"), (JS_PATHS, "JS")]:
    for p in paths:
        full = BASE + p
        if not os.path.exists(full):
            issues.append(f"MISSING: {p}")
            continue
        
        size = os.path.getsize(full)
        
        # Check for suspiciously small files
        if size < 100:
            issues.append(f"SUSPICIOUS ({size}B): {p}")
            continue
        
        # Check for HTML error pages instead of actual content
        try:
            with open(full, 'r', errors='ignore') as f:
                content = f.read(200)
                if '<!DOCTYPE' in content or '<html' in content:
                    issues.append(f"HTML INSTEAD OF {label}: {p}")
                elif '404 Not Found' in content or '403 Forbidden' in content:
                    issues.append(f"ERROR PAGE: {p}")
                elif 'Failed to fetch' in content:
                    issues.append(f"FETCH ERROR: {p}")
        except:
            pass

if issues:
    print(f"Found {len(issues)} issues:")
    for i in issues:
        print(f"  {i}")
else:
    print("All files are valid!")
