import { chromium } from 'playwright';

const browser = await chromium.launch({ headless: true });
const page = await browser.newPage({ viewport: { width: 1440, height: 900 } });

console.log('=== Navigating to https://love-really.kikiw.cn ===');
await page.goto('https://love-really.kikiw.cn', { waitUntil: 'networkidle', timeout: 60000 });
await page.waitForTimeout(3000);

// =============================================
// 1. CSS Links and JS Scripts
// =============================================
const cssLinks = await page.evaluate(() =>
  Array.from(document.querySelectorAll('link[rel="stylesheet"]')).map(l => ({ href: l.href, media: l.media, disabled: l.disabled }))
);
const scripts = await page.evaluate(() =>
  Array.from(document.querySelectorAll('script[src]')).map(s => s.src)
);
const googleFonts = await page.evaluate(() =>
  Array.from(document.querySelectorAll('link[href*="fonts.googleapis.com"]')).map(l => l.href)
);
const inlineStyles = await page.evaluate(() =>
  Array.from(document.querySelectorAll('style')).map(s => ({ length: s.textContent.length, preview: s.textContent.substring(0, 300) })).slice(0, 10)
);

console.log('\n========== CSS Links ==========');
console.log(JSON.stringify(cssLinks, null, 2));
console.log('\n========== Scripts ==========');
console.log(JSON.stringify(scripts, null, 2));
console.log('\n========== Google Fonts ==========');
console.log(JSON.stringify(googleFonts, null, 2));
console.log('\n========== Inline Styles ==========');
console.log(JSON.stringify(inlineStyles, null, 2));

// =============================================
// 2. Fetch CSS file content
// =============================================
console.log('\n========== CSS File Contents ==========');
for (const css of cssLinks) {
  console.log(`\n--- CSS: ${css.href} ---`);
  try {
    const response = await page.evaluate(async (url) => {
      const resp = await fetch(url);
      return await resp.text();
    }, css.href);
    console.log(response.substring(0, 5000)); // First 5000 chars per CSS file
  } catch (e) {
    console.log(`ERROR fetching: ${e.message}`);
  }
}

// =============================================
// 3. Fetch JS file content (key files only)
// =============================================
console.log('\n========== JS File Contents (key files) ==========');
for (const js of scripts) {
  console.log(`\n--- JS: ${js} ---`);
  try {
    const resp = await page.evaluate(async (url) => {
      const r = await fetch(url);
      return await r.text();
    }, js);
    console.log(`Size: ${resp.length} chars`);
    console.log(resp.substring(0, 3000)); // First 3000 chars
  } catch (e) {
    console.log(`ERROR fetching: ${e.message}`);
  }
}

// =============================================
// 4. Fonts loaded
// =============================================
const fonts = await page.evaluate(() =>
  Array.from(document.fonts).map(f => ({ family: f.family, weight: f.weight, style: f.style, status: f.status }))
);
const uniqueFonts = [...new Set(fonts.map(f => f.family))];
console.log('\n========== Loaded Fonts ==========');
console.log(JSON.stringify(uniqueFonts, null, 2));

// =============================================
// 5. CSS Variables from :root
// =============================================
const cssVars = await page.evaluate(() => {
  const vars = [];
  for (const sheet of document.styleSheets) {
    try {
      for (const rule of sheet.cssRules) {
        if (rule.selectorText === ':root' || rule.selectorText === 'html') {
          for (const prop of rule.style) {
            const v = prop.trim();
            if (v.startsWith('--')) {
              vars.push({ name: v, value: rule.style.getPropertyValue(v).trim() });
            }
          }
        }
      }
    } catch (e) { /* can't access */ }
  }
  return vars;
});
console.log('\n========== CSS Variables (:root) ==========');
console.log(JSON.stringify(cssVars, null, 2));

// =============================================
// 6. Body and HTML attributes
// =============================================
const bodyInfo = await page.evaluate(() => ({
  bodyClass: document.body.className,
  bodyDataset: Object.keys(document.body.dataset),
  bodyStyle: document.body.getAttribute('style'),
  htmlLang: document.documentElement.lang,
  htmlClass: document.documentElement.className,
  bodyId: document.body.id,
  bodyChildNodeCount: document.body.children.length,
}));
console.log('\n========== Body & HTML Info ==========');
console.log(JSON.stringify(bodyInfo, null, 2));

// =============================================
// 7. Meta Tags
// =============================================
const metas = await page.evaluate(() =>
  Array.from(document.querySelectorAll('meta')).map(m => ({
    name: m.name || m.getAttribute('property') || m.getAttribute('http-equiv') || m.getAttribute('charset'),
    content: m.content,
    charset: m.getAttribute('charset'),
    other: Object.entries(m.attributes).filter(([k]) => !['name', 'content', 'property', 'charset', 'http-equiv'].includes(k)).map(([,v]) => v.value || v.name)
  }))
);
console.log('\n========== Meta Tags ==========');
console.log(JSON.stringify(metas, null, 2));

// =============================================
// 8. Favicon & Icons
// =============================================
const icons = await page.evaluate(() => ({
  favicon: document.querySelector('link[rel="icon"]')?.href || document.querySelector('link[rel="shortcut icon"]')?.href,
  appleIcon: document.querySelector('link[rel="apple-touch-icon"]')?.href,
  appleIcons: Array.from(document.querySelectorAll('link[rel="apple-touch-icon"]')).map(l => ({ href: l.href, sizes: l.getAttribute('sizes') })),
  manifest: document.querySelector('link[rel="manifest"]')?.href,
  maskIcon: document.querySelector('link[rel="mask-icon"]')?.href,
  msTileImage: document.querySelector('meta[name="msapplication-TileImage"]')?.content,
  ogImage: document.querySelector('meta[property="og:image"]')?.content,
  preconnects: Array.from(document.querySelectorAll('link[rel="preconnect"]')).map(l => l.href),
}));
console.log('\n========== Favicon & Icons ==========');
console.log(JSON.stringify(icons, null, 2));

// =============================================
// 9. Section HTML Structures
// =============================================
const sections = await page.evaluate(() =>
  Array.from(document.querySelectorAll('section.lgnewui-section, section.lgnewui-day-hero-section, .lgnewui-epilogue, section[class*="lgnewui"], div[class*="lgnewui-section"], div.lgnewui-day-hero-section')).map(s => ({
    tagName: s.tagName,
    className: s.className,
    id: s.id,
    childCount: s.children.length,
    innerHTML_preview: s.innerHTML.substring(0, 1000),
    style: s.getAttribute('style'),
    dataset: Object.keys(s.dataset).reduce((acc, k) => ({ ...acc, [k]: s.dataset[k] }), {}),
  }))
);
console.log('\n========== Section Structures ==========');
console.log(JSON.stringify(sections, null, 2));

// =============================================
// 10. Animation/Transition details
// =============================================
const animations = await page.evaluate(() => {
  const results = [];
  // Get all keyframe animations
  for (const sheet of document.styleSheets) {
    try {
      for (const rule of sheet.cssRules) {
        if (rule.type === CSSRule.KEYFRAMES_RULE || rule.type === 7) {
          results.push({ name: rule.name, cssText: rule.cssText.substring(0, 500) });
        }
        if (rule.type === CSSRule.STYLE_RULE || rule.type === 1) {
          const style = rule.style;
          const animationName = style.animationName || style.webkitAnimationName;
          const transition = style.transition || style.webkitTransition;
          if (animationName && animationName !== 'none') {
            results.push({
              selector: rule.selectorText,
              animation: animationName,
              animationDuration: style.animationDuration,
              animationTiming: style.animationTimingFunction,
              animationDelay: style.animationDelay,
              animationIteration: style.animationIterationCount,
              animationFillMode: style.animationFillMode,
            });
          }
          if (transition && transition !== 'all 0s ease 0s') {
            results.push({
              selector: rule.selectorText,
              transition: transition,
              transitionDuration: style.transitionDuration,
              transitionTiming: style.transitionTimingFunction,
              transitionDelay: style.transitionDelay,
            });
          }
        }
      }
    } catch (e) { /* cross-origin sheet */ }
  }
  return results;
});
console.log('\n========== Animations & Transitions ==========');
console.log(JSON.stringify(animations, null, 2));

// =============================================
// 11. Additional key CSS rules
// =============================================
const keyCssRules = await page.evaluate(() => {
  const rules = [];
  for (const sheet of document.styleSheets) {
    try {
      for (const rule of sheet.cssRules) {
        if (rule.type === CSSRule.STYLE_RULE || rule.type === 1) {
          const sel = rule.selectorText;
          if (sel.includes('lgnewui') || sel.includes('hero') || sel.includes('section') || sel.includes('.btn') || sel.includes('button') || sel.includes('.day') || sel.includes('swiper') || sel.includes('modal') || sel.includes('overlay')) {
            rules.push({ selector: sel, cssText: rule.cssText.substring(0, 400) });
          }
        }
      }
    } catch (e) { }
  }
  return rules.slice(0, 100);
});
console.log('\n========== Key CSS Rules ==========');
console.log(JSON.stringify(keyCssRules, null, 2));

// =============================================
// 12. Page Title and Full HTML head
// =============================================
const title = await page.title();
const headHTML = await page.evaluate(() => document.head.innerHTML.substring(0, 5000));
console.log('\n========== Page Title ==========');
console.log(title);
console.log('\n========== Head HTML ==========');
console.log(headHTML);

// =============================================
// 13. Get computed styles for body and key elements
// =============================================
const computedStyles = await page.evaluate(() => {
  const bodyStyles = getComputedStyle(document.body);
  const props = ['font-family', 'font-size', 'color', 'background-color', 'background-image', 'background-size', 'line-height', 'letter-spacing', 'overflow-x', 'overflow-y', 'position', 'margin', 'padding'];
  const result = {};
  for (const prop of props) {
    result[prop] = bodyStyles.getPropertyValue(prop);
  }
  return result;
});
console.log('\n========== Body Computed Styles ==========');
console.log(JSON.stringify(computedStyles, null, 2));

// =============================================
// 14. Screenshot (full page)
// =============================================
await page.screenshot({ path: '/workspace/lovereally_full.png', fullPage: true });
console.log('\n========== Full page screenshot saved to /workspace/lovereally_full.png ==========');

await browser.close();
console.log('\n=== Extraction Complete ===');