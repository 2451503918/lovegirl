const { chromium, devices } = require('playwright');
const fs = require('fs');
const path = require('path');

const SCREENSHOT_DIR = '/workspace/screenshots_mobile_fixed';
const BASE_URL = 'http://localhost:8090';

const pages = [
  { name: '首页', url: '/', file: 'home' },
  { name: '关于页', url: '/about.php', file: 'about' },
  { name: '时间线', url: '/timeline.php', file: 'timeline' },
  { name: '留言板', url: '/messages.php', file: 'messages' },
  { name: '相册', url: '/loveImg.php', file: 'loveImg' },
  { name: '清单', url: '/list.php', file: 'list' },
  { name: '点滴', url: '/articles.php', file: 'articles' },
];

const report = {
  timestamp: new Date().toISOString(),
  viewport: { width: 390, height: 844, device: 'iPhone 14 Pro' },
  pages: {},
  checks: {
    fontSize: {},
    musicControl: {},
    touchTargets: {},
    bottomNav: {},
    horizontalOverflow: {},
  },
};

function parsePx(val) {
  if (!val || val === 'auto' || val === 'none') return null;
  return parseFloat(val);
}

(async () => {
  const iPhone14Pro = devices['iPhone 14 Pro'];
  const browser = await chromium.launch({ headless: true });
  const context = await browser.newContext({
    ...iPhone14Pro,
    viewport: { width: 390, height: 844 },
  });

  for (const pageConfig of pages) {
    const page = await context.newPage();
    const pageReport = { url: `${BASE_URL}${pageConfig.url}`, checks: {} };

    try {
      console.log(`\n========== 访问: ${pageConfig.name} (${pageConfig.url}) ==========`);

      // Navigate and wait for network idle
      await page.goto(`${BASE_URL}${pageConfig.url}`, { waitUntil: 'networkidle', timeout: 30000 });
      // Extra wait for dynamic content
      await page.waitForTimeout(2000);

      // Take full page screenshot
      const screenshotPath = path.join(SCREENSHOT_DIR, `${pageConfig.file}.png`);
      await page.screenshot({ path: screenshotPath, fullPage: true });
      console.log(`截图已保存: ${screenshotPath}`);

      // Also take viewport-only screenshot
      const viewportScreenshotPath = path.join(SCREENSHOT_DIR, `${pageConfig.file}_viewport.png`);
      await page.screenshot({ path: viewportScreenshotPath, fullPage: false });

      // ===== CHECK 1: Font Size =====
      console.log(`\n--- 检查文字大小 ---`);
      const fontSizeCheck = await page.evaluate(() => {
        const results = {};

        // Check .lgnewui-day-date-label-small
        const dateLabels = document.querySelectorAll('.lgnewui-day-date-label-small');
        if (dateLabels.length > 0) {
          const styles = [];
          dateLabels.forEach((el, i) => {
            if (i < 5) { // Check first 5 elements
              const cs = window.getComputedStyle(el);
              styles.push({ index: i, fontSize: cs.fontSize, element: el.textContent.trim().substring(0, 20) });
            }
          });
          results.dateLabelSmall = { found: dateLabels.length, styles };
        } else {
          results.dateLabelSmall = { found: 0, note: '未找到 .lgnewui-day-date-label-small 元素' };
        }

        // Check .lgnewui-day-timer-label
        const timerLabels = document.querySelectorAll('.lgnewui-day-timer-label');
        if (timerLabels.length > 0) {
          const styles = [];
          timerLabels.forEach((el, i) => {
            if (i < 5) {
              const cs = window.getComputedStyle(el);
              styles.push({ index: i, fontSize: cs.fontSize, element: el.textContent.trim().substring(0, 20) });
            }
          });
          results.timerLabel = { found: timerLabels.length, styles };
        } else {
          results.timerLabel = { found: 0, note: '未找到 .lgnewui-day-timer-label 元素' };
        }

        return results;
      });

      pageReport.checks.fontSize = fontSizeCheck;
      console.log('文字大小检查结果:', JSON.stringify(fontSizeCheck, null, 2));

      // ===== CHECK 2: Music Control Position =====
      console.log(`\n--- 检查音乐控件位置 ---`);
      const musicCheck = await page.evaluate(() => {
        const navMusic = document.querySelector('#nav-music');
        if (!navMusic) return { found: false, note: '未找到 #nav-music 元素' };

        const cs = window.getComputedStyle(navMusic);
        const rect = navMusic.getBoundingClientRect();
        return {
          found: true,
          bottom: cs.bottom,
          position: cs.position,
          computedBottom: cs.bottom,
          rectBottom: rect.bottom,
          rectTop: rect.top,
          rectHeight: rect.height,
          display: cs.display,
          visibility: cs.visibility,
        };
      });

      pageReport.checks.musicControl = musicCheck;
      console.log('音乐控件检查结果:', JSON.stringify(musicCheck, null, 2));

      // ===== CHECK 3: Touch Targets =====
      console.log(`\n--- 检查触摸目标尺寸 ---`);
      const touchCheck = await page.evaluate(() => {
        const results = {};

        const selectors = [
          { name: 'lgnewui-smart-card__album-link', selector: '.lgnewui-smart-card__album-link', expectedMinHeight: 44 },
          { name: 'lgnewui-smart-card__switch-btn', selector: '.lgnewui-smart-card__switch-btn', expectedSize: '44x44' },
          { name: 'lgnewui-epilogue__btn-icon', selector: '.lgnewui-epilogue__btn-icon', expectedSize: '44x44' },
          { name: 'lgnewui-link-more', selector: '.lgnewui-link-more', expectedSize: '~44' },
          { name: 'lgnewui-nav-music-btn', selector: '.lgnewui-nav-music-btn', expectedSize: '36x36' },
          { name: 'lgnewui-music-playlist-close', selector: '.lgnewui-music-playlist-close', expectedSize: '36x36' },
          { name: 'lgnewui-ios-tab', selector: '.lgnewui-ios-tab', expectedMinHeight: 44 },
        ];

        selectors.forEach(({ name, selector, expectedMinHeight, expectedSize }) => {
          const elements = document.querySelectorAll(selector);
          if (elements.length === 0) {
            results[name] = { found: 0, note: `未找到 ${selector} 元素` };
            return;
          }

          const details = [];
          elements.forEach((el, i) => {
            if (i < 3) { // Check first 3
              const cs = window.getComputedStyle(el);
              const rect = el.getBoundingClientRect();
              details.push({
                index: i,
                width: rect.width,
                height: rect.height,
                computedWidth: cs.width,
                computedHeight: cs.height,
                minHeight: cs.minHeight,
                minWidth: cs.minWidth,
              });
            }
          });

          results[name] = {
            found: elements.length,
            expectedMinHeight: expectedMinHeight || null,
            expectedSize: expectedSize || null,
            details,
          };
        });

        return results;
      });

      pageReport.checks.touchTargets = touchCheck;
      console.log('触摸目标检查结果:', JSON.stringify(touchCheck, null, 2));

      // ===== CHECK 4: Bottom Navigation =====
      console.log(`\n--- 检查底部导航 ---`);
      const bottomNavCheck = await page.evaluate(() => {
        const mobileNav = document.querySelector('#lgnewui-mobile-nav-v5');
        const navMusic = document.querySelector('#nav-music');

        const result = {};

        if (!mobileNav) {
          result.mobileNav = { found: false, note: '未找到 #lgnewui-mobile-nav-v5 元素' };
        } else {
          const cs = window.getComputedStyle(mobileNav);
          const rect = mobileNav.getBoundingClientRect();
          result.mobileNav = {
            found: true,
            visible: cs.display !== 'none' && cs.visibility !== 'hidden' && cs.opacity !== '0',
            display: cs.display,
            visibility: cs.visibility,
            opacity: cs.opacity,
            position: cs.position,
            bottom: cs.bottom,
            height: cs.height,
            rectTop: rect.top,
            rectBottom: rect.bottom,
            rectHeight: rect.height,
          };
        }

        if (!navMusic) {
          result.navMusic = { found: false, note: '未找到 #nav-music 元素' };
        } else {
          const cs = window.getComputedStyle(navMusic);
          const rect = navMusic.getBoundingClientRect();
          result.navMusic = {
            found: true,
            visible: cs.display !== 'none' && cs.visibility !== 'hidden' && cs.opacity !== '0',
            display: cs.display,
            visibility: cs.visibility,
            position: cs.position,
            bottom: cs.bottom,
            rectTop: rect.top,
            rectBottom: rect.bottom,
            rectHeight: rect.height,
          };
        }

        // Check overlap
        if (mobileNav && navMusic) {
          const navRect = mobileNav.getBoundingClientRect();
          const musicRect = navMusic.getBoundingClientRect();
          const overlap = !(navRect.bottom <= musicRect.top || musicRect.bottom <= navRect.top);
          result.overlap = {
            hasOverlap: overlap,
            mobileNavTop: navRect.top,
            mobileNavBottom: navRect.bottom,
            musicTop: musicRect.top,
            musicBottom: musicRect.bottom,
          };
        }

        return result;
      });

      pageReport.checks.bottomNav = bottomNavCheck;
      console.log('底部导航检查结果:', JSON.stringify(bottomNavCheck, null, 2));

      // ===== CHECK 5: Horizontal Overflow =====
      console.log(`\n--- 检查水平溢出 ---`);
      const overflowCheck = await page.evaluate(() => {
        const body = document.body;
        const html = document.documentElement;
        const bodyCS = window.getComputedStyle(body);

        const result = {
          bodyOverflowX: bodyCS.overflowX,
          bodyOverflowY: bodyCS.overflowY,
          bodyWidth: bodyCS.width,
          scrollWidth: body.scrollWidth,
          clientWidth: body.clientWidth,
          htmlScrollWidth: html.scrollWidth,
          htmlClientWidth: html.clientWidth,
          windowInnerWidth: window.innerWidth,
        };

        result.hasHorizontalOverflow = body.scrollWidth > body.clientWidth || html.scrollWidth > html.clientWidth;
        result.overflowAmount = Math.max(body.scrollWidth - body.clientWidth, html.scrollWidth - html.clientWidth, 0);

        // Find elements causing overflow
        const overflowElements = [];
        const allElements = document.querySelectorAll('*');
        for (let i = 0; i < allElements.length && overflowElements.length < 10; i++) {
          const el = allElements[i];
          const rect = el.getBoundingClientRect();
          if (rect.width > 0 && (rect.right > body.clientWidth + 5 || rect.left < -5)) {
            overflowElements.push({
              tag: el.tagName,
              id: el.id || '',
              class: (el.className && typeof el.className === 'string') ? el.className.substring(0, 80) : '',
              rectRight: Math.round(rect.right),
              rectLeft: Math.round(rect.left),
              rectWidth: Math.round(rect.width),
            });
          }
        }
        result.overflowElements = overflowElements;

        return result;
      });

      pageReport.checks.horizontalOverflow = overflowCheck;
      console.log('水平溢出检查结果:', JSON.stringify(overflowCheck, null, 2));

    } catch (err) {
      console.error(`页面 ${pageConfig.name} 检查出错:`, err.message);
      pageReport.error = err.message;
    }

    report.pages[pageConfig.file] = pageReport;
    await page.close();
  }

  // Save report
  const reportPath = path.join(SCREENSHOT_DIR, 'validation_report.json');
  fs.writeFileSync(reportPath, JSON.stringify(report, null, 2));
  console.log(`\n\n验证报告已保存: ${reportPath}`);

  // Print summary
  console.log('\n\n========== 验证摘要 ==========');
  for (const [pageKey, pageData] of Object.entries(report.pages)) {
    console.log(`\n--- ${pageKey} ---`);
    if (pageData.error) {
      console.log(`  错误: ${pageData.error}`);
      continue;
    }

    const checks = pageData.checks;

    // Font size summary
    if (checks.fontSize) {
      const dl = checks.fontSize.dateLabelSmall;
      const tl = checks.fontSize.timerLabel;
      if (dl && dl.found > 0 && dl.styles) {
        const smallFonts = dl.styles.filter(s => {
          const px = parseFloat(s.fontSize);
          return px < 10;
        });
        console.log(`  日期标签: 找到${dl.found}个, ${smallFonts.length > 0 ? '⚠️ 存在极小字体(' + smallFonts.map(s => s.fontSize).join(', ') + ')' : '✅ 无极小字体'}`);
      } else if (dl) {
        console.log(`  日期标签: ${dl.note || '未找到'}`);
      }
      if (tl && tl.found > 0 && tl.styles) {
        const smallFonts = tl.styles.filter(s => {
          const px = parseFloat(s.fontSize);
          return px < 10;
        });
        console.log(`  时间标签: 找到${tl.found}个, ${smallFonts.length > 0 ? '⚠️ 存在极小字体(' + smallFonts.map(s => s.fontSize).join(', ') + ')' : '✅ 无极小字体'}`);
      } else if (tl) {
        console.log(`  时间标签: ${tl.note || '未找到'}`);
      }
    }

    // Music control summary
    if (checks.musicControl) {
      if (checks.musicControl.found) {
        console.log(`  音乐控件: bottom=${checks.musicControl.bottom}, position=${checks.musicControl.position}`);
      } else {
        console.log(`  音乐控件: ${checks.musicControl.note}`);
      }
    }

    // Touch targets summary
    if (checks.touchTargets) {
      for (const [name, data] of Object.entries(checks.touchTargets)) {
        if (data.found > 0 && data.details && data.details.length > 0) {
          const d = data.details[0];
          console.log(`  ${name}: 找到${data.found}个, 尺寸=${d.width}x${d.height}, min-height=${d.minHeight}`);
        } else {
          console.log(`  ${name}: ${data.note || '未找到'}`);
        }
      }
    }

    // Bottom nav summary
    if (checks.bottomNav) {
      const mn = checks.bottomNav.mobileNav;
      const nm = checks.bottomNav.navMusic;
      if (mn && mn.found) {
        console.log(`  底部导航: 可见=${mn.visible}, 位置=top:${mn.rectTop}, bottom:${mn.rectBottom}`);
      }
      if (checks.bottomNav.overlap) {
        console.log(`  重叠检查: ${checks.bottomNav.overlap.hasOverlap ? '⚠️ 存在重叠' : '✅ 无重叠'}`);
      }
    }

    // Overflow summary
    if (checks.horizontalOverflow) {
      console.log(`  水平溢出: ${checks.horizontalOverflow.hasHorizontalOverflow ? '⚠️ 存在溢出 (' + checks.horizontalOverflow.overflowAmount + 'px)' : '✅ 无溢出'}`);
    }
  }

  await browser.close();
  console.log('\n验证完成！');
})();
