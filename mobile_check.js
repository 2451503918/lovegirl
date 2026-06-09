const { chromium, devices } = require('playwright');
const fs = require('fs');
const path = require('path');

const SCREENSHOT_DIR = '/workspace/screenshots_mobile';
const BASE_URL = 'http://localhost:8090';

const pages = [
  { name: 'index', url: '/', file: 'index_mobile.png' },
  { name: 'about', url: '/about.php', file: 'about_mobile.png' },
  { name: 'timeline', url: '/timeline.php', file: 'timeline_mobile.png' },
  { name: 'messages', url: '/messages.php', file: 'messages_mobile.png' },
  { name: 'loveImg', url: '/loveImg.php', file: 'loveImg_mobile.png' },
  { name: 'list', url: '/list.php', file: 'list_mobile.png' },
  { name: 'articles', url: '/articles.php', file: 'articles_mobile.png' },
];

async function runChecks(page, pageName) {
  const results = {};

  // 1. Horizontal overflow check
  results.horizontalOverflow = await page.evaluate(() => {
    const bodyScrollW = document.body.scrollWidth;
    const bodyClientW = document.body.clientWidth;
    const docScrollW = document.documentElement.scrollWidth;
    const docClientW = document.documentElement.clientWidth;
    const overflow = Math.max(bodyScrollW - bodyClientW, docScrollW - docClientW);

    const overflowElements = [];
    const allElements = document.querySelectorAll('*');
    for (const el of allElements) {
      const rect = el.getBoundingClientRect();
      if (rect.right > window.innerWidth + 1) {
        overflowElements.push({
          selector: getSelector(el),
          right: Math.round(rect.right),
          overflowBy: Math.round(rect.right - window.innerWidth),
          width: Math.round(rect.width),
          tagName: el.tagName,
        });
      }
    }

    return {
      bodyScrollWidth: bodyScrollW,
      bodyClientWidth: bodyClientW,
      docScrollWidth: docScrollW,
      docClientWidth: docClientW,
      overflowPixels: overflow,
      overflowElements: overflowElements.slice(0, 20),
    };
  });

  // 2. Navigation check
  results.navigation = await page.evaluate(() => {
    const navs = document.querySelectorAll('nav, [role="navigation"], .navbar, .nav, .menu, .header');
    const navInfo = [];
    for (const nav of navs) {
      const rect = nav.getBoundingClientRect();
      const links = nav.querySelectorAll('a, button');
      const linkInfo = [];
      for (const link of links) {
        const lr = link.getBoundingClientRect();
        linkInfo.push({
          text: link.textContent ? link.textContent.trim().substring(0, 30) : '',
          width: Math.round(lr.width),
          height: Math.round(lr.height),
          visible: lr.width > 0 && lr.height > 0,
        });
      }
      navInfo.push({
        selector: getSelector(nav),
        visible: rect.width > 0 && rect.height > 0,
        width: Math.round(rect.width),
        height: Math.round(rect.height),
        linkCount: links.length,
        links: linkInfo.slice(0, 10),
        overflow: rect.right > window.innerWidth,
      });
    }

    const hamburgerBtns = document.querySelectorAll('.hamburger, .menu-toggle, .menu-btn, [aria-label*="menu"], [aria-label*="Menu"], .navbar-toggler, .nav-toggle');
    const hamburgerInfo = [];
    for (const btn of hamburgerBtns) {
      const r = btn.getBoundingClientRect();
      hamburgerInfo.push({
        selector: getSelector(btn),
        visible: r.width > 0 && r.height > 0,
        width: Math.round(r.width),
        height: Math.round(r.height),
      });
    }

    return { navs: navInfo, hamburgerButtons: hamburgerInfo };
  });

  // 3. Text readability check
  results.textReadability = await page.evaluate(() => {
    const textElements = document.querySelectorAll('p, span, a, li, td, th, label, h1, h2, h3, h4, h5, h6, div');
    const smallTexts = [];
    const checked = new Set();
    for (const el of textElements) {
      const style = window.getComputedStyle(el);
      const fontSize = parseFloat(style.fontSize);
      if (fontSize < 12 && !checked.has(el)) {
        checked.add(el);
        const text = el.textContent ? el.textContent.trim().substring(0, 50) : '';
        if (text) {
          smallTexts.push({
            selector: getSelector(el),
            fontSize: fontSize + 'px',
            text,
          });
        }
      }
    }
    return { smallTexts: smallTexts.slice(0, 20) };
  });

  // 4. Touch targets check
  results.touchTargets = await page.evaluate(() => {
    const interactiveElements = document.querySelectorAll('a, button, input, select, textarea, [role="button"], [onclick]');
    const smallTargets = [];
    for (const el of interactiveElements) {
      const rect = el.getBoundingClientRect();
      if (rect.width > 0 && rect.height > 0 && (rect.width < 44 || rect.height < 44)) {
        smallTargets.push({
          selector: getSelector(el),
          text: (el.textContent ? el.textContent.trim().substring(0, 30) : '') || el.getAttribute('aria-label') || '',
          width: Math.round(rect.width),
          height: Math.round(rect.height),
          tagName: el.tagName,
          type: el.getAttribute('type') || '',
        });
      }
    }
    return { smallTargets: smallTargets.slice(0, 30) };
  });

  // 5. Images check
  results.images = await page.evaluate(() => {
    const images = document.querySelectorAll('img');
    const imageInfo = [];
    for (const img of images) {
      const rect = img.getBoundingClientRect();
      const srcName = img.src ? img.src.substring(img.src.lastIndexOf('/') + 1).substring(0, 50) : '';
      imageInfo.push({
        selector: getSelector(img),
        src: srcName,
        naturalWidth: img.naturalWidth,
        naturalHeight: img.naturalHeight,
        displayWidth: Math.round(rect.width),
        displayHeight: Math.round(rect.height),
        overflowRight: rect.right > window.innerWidth,
        visible: rect.width > 0 && rect.height > 0,
      });
    }
    const overflowImages = imageInfo.filter(i => i.overflowRight);
    return { totalImages: images.length, overflowImages, allImages: imageInfo.slice(0, 15) };
  });

  // 6. Layout check
  results.layout = await page.evaluate(() => {
    const fixedInfo = [];
    const allElements = document.querySelectorAll('*');
    for (const el of allElements) {
      const style = window.getComputedStyle(el);
      if ((style.position === 'fixed' || style.position === 'sticky') && style.display !== 'none' && style.visibility !== 'hidden') {
        const rect = el.getBoundingClientRect();
        if (rect.width > 0 && rect.height > 0) {
          fixedInfo.push({
            selector: getSelector(el),
            position: style.position,
            top: style.top,
            bottom: style.bottom,
            left: style.left,
            right: style.right,
            width: Math.round(rect.width),
            height: Math.round(rect.height),
            zIndex: style.zIndex,
          });
        }
      }
    }

    const truncatedElements = [];
    const textContainers = document.querySelectorAll('p, h1, h2, h3, h4, h5, h6, span, a, li, td');
    for (const el of textContainers) {
      const style = window.getComputedStyle(el);
      if (style.overflow === 'hidden' && style.textOverflow === 'ellipsis') {
        if (el.scrollWidth > el.clientWidth + 2) {
          truncatedElements.push({
            selector: getSelector(el),
            scrollWidth: el.scrollWidth,
            clientWidth: el.clientWidth,
            truncatedBy: el.scrollWidth - el.clientWidth,
            text: el.textContent ? el.textContent.trim().substring(0, 40) : '',
          });
        }
      }
    }

    const viewportMeta = document.querySelector('meta[name="viewport"]');
    const viewportContent = viewportMeta ? viewportMeta.getAttribute('content') : null;

    return {
      fixedElements: fixedInfo.slice(0, 15),
      truncatedElements: truncatedElements.slice(0, 10),
      viewportMeta: viewportContent,
    };
  });

  // 7. Bottom navigation check
  results.bottomNav = await page.evaluate(() => {
    const bottomNavs = document.querySelectorAll('.bottom-nav, .footer-nav, nav[class*="bottom"], [class*="tab-bar"], [class*="tabbar"], [class*="bottom-bar"], footer nav, .mobile-nav');
    const info = [];
    for (const nav of bottomNavs) {
      const rect = nav.getBoundingClientRect();
      const style = window.getComputedStyle(nav);
      info.push({
        selector: getSelector(nav),
        visible: rect.width > 0 && rect.height > 0,
        position: style.position,
        bottom: style.bottom,
        width: Math.round(rect.width),
        height: Math.round(rect.height),
        bottomPosition: Math.round(rect.bottom),
      });
    }

    const allFixed = document.querySelectorAll('*');
    const bottomFixed = [];
    for (const el of allFixed) {
      const style = window.getComputedStyle(el);
      if ((style.position === 'fixed' || style.position === 'sticky') && style.bottom !== 'auto' && parseInt(style.bottom) < 100) {
        const rect = el.getBoundingClientRect();
        if (rect.width > 0 && rect.height > 0) {
          bottomFixed.push({
            selector: getSelector(el),
            position: style.position,
            bottom: style.bottom,
            height: Math.round(rect.height),
          });
        }
      }
    }

    return { explicitBottomNav: info, bottomFixedElements: bottomFixed.slice(0, 10) };
  });

  // 8. Modal CSS check
  results.modalCss = await page.evaluate(() => {
    const modals = document.querySelectorAll('.modal, [class*="modal"], [role="dialog"], .popup, [class*="popup"], .overlay, [class*="overlay"]');
    const info = [];
    for (const modal of modals) {
      const style = window.getComputedStyle(modal);
      const rect = modal.getBoundingClientRect();
      info.push({
        selector: getSelector(modal),
        display: style.display,
        position: style.position,
        width: style.width,
        maxWidth: style.maxWidth,
        height: style.height,
        maxHeight: style.maxHeight,
        top: style.top,
        left: style.left,
        transform: style.transform,
        overflow: style.overflow,
        visible: rect.width > 0 && rect.height > 0 && style.display !== 'none',
      });
    }
    return { modals: info.slice(0, 10) };
  });

  return results;
}

(async () => {
  const browser = await chromium.launch({ headless: true });
  const iPhone14Pro = devices['iPhone 14 Pro'];

  const allResults = {};

  // Mobile checks
  const mobileContext = await browser.newContext({
    ...iPhone14Pro,
  });

  // Inject getSelector into every page in this context
  await mobileContext.addInitScript(() => {
    window.getSelector = function(el) {
      if (!el || !el.tagName) return 'unknown';
      if (el.id) return '#' + el.id;
      let selector = el.tagName.toLowerCase();
      if (el.className && typeof el.className === 'string') {
        const classes = el.className.trim().split(/\s+/).slice(0, 2).filter(c => c.length > 0);
        if (classes.length > 0) selector += '.' + classes.join('.');
      }
      if (el.parentElement) {
        const siblings = Array.from(el.parentElement.children).filter(c => c.tagName === el.tagName);
        if (siblings.length > 1) {
          const index = siblings.indexOf(el) + 1;
          selector += ':nth-of-type(' + index + ')';
        }
      }
      return selector;
    };
  });

  const mobilePage = await mobileContext.newPage();

  for (const pageInfo of pages) {
    const url = BASE_URL + pageInfo.url;
    console.log(`\n=== Checking ${pageInfo.name}: ${url} ===`);

    try {
      await mobilePage.goto(url, { waitUntil: 'networkidle', timeout: 15000 });
    } catch (e) {
      console.log(`  Warning: page load issue: ${e.message}`);
      try {
        await mobilePage.goto(url, { waitUntil: 'domcontentloaded', timeout: 10000 });
      } catch (e2) {
        console.log(`  Error loading page: ${e2.message}`);
        continue;
      }
    }

    await mobilePage.waitForTimeout(1500);

    await mobilePage.screenshot({
      path: path.join(SCREENSHOT_DIR, pageInfo.file),
      fullPage: true,
    });
    console.log(`  Screenshot saved: ${pageInfo.file}`);

    const checkResults = await runChecks(mobilePage, pageInfo.name);
    allResults[pageInfo.name] = checkResults;

    if (checkResults.horizontalOverflow.overflowPixels > 0) {
      console.log(`  ⚠️  Horizontal overflow: ${checkResults.horizontalOverflow.overflowPixels}px`);
      checkResults.horizontalOverflow.overflowElements.forEach(el => {
        console.log(`    - ${el.selector} overflows by ${el.overflowBy}px (right: ${el.right})`);
      });
    } else {
      console.log(`  ✓ No horizontal overflow`);
    }

    if (checkResults.textReadability.smallTexts.length > 0) {
      console.log(`  ⚠️  Small text found: ${checkResults.textReadability.smallTexts.length} elements`);
    } else {
      console.log(`  ✓ Text readability OK`);
    }

    if (checkResults.touchTargets.smallTargets.length > 0) {
      console.log(`  ⚠️  Small touch targets: ${checkResults.touchTargets.smallTargets.length} elements`);
    } else {
      console.log(`  ✓ Touch targets OK`);
    }

    if (checkResults.images.overflowImages.length > 0) {
      console.log(`  ⚠️  Overflow images: ${checkResults.images.overflowImages.length}`);
    } else {
      console.log(`  ✓ Images OK`);
    }

    if (!checkResults.layout.viewportMeta) {
      console.log(`  ⚠️  No viewport meta tag!`);
    } else {
      console.log(`  ✓ Viewport meta: ${checkResults.layout.viewportMeta}`);
    }
  }

  await mobileContext.close();

  // Desktop screenshot for comparison
  console.log('\n=== Desktop screenshot of index ===');
  const desktopContext = await browser.newContext({
    viewport: { width: 1920, height: 1080 },
  });
  const desktopPage = await desktopContext.newPage();
  try {
    await desktopPage.goto(BASE_URL + '/', { waitUntil: 'networkidle', timeout: 15000 });
    await desktopPage.screenshot({
      path: path.join(SCREENSHOT_DIR, 'index_desktop.png'),
      fullPage: true,
    });
    console.log('  Desktop screenshot saved');
  } catch (e) {
    console.log(`  Error: ${e.message}`);
  }
  await desktopContext.close();

  await browser.close();

  fs.writeFileSync(
    path.join(SCREENSHOT_DIR, 'check_results.json'),
    JSON.stringify(allResults, null, 2)
  );
  console.log('\nResults saved to check_results.json');
})();
