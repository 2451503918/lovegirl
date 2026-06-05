const { chromium } = require('playwright');

(async () => {
  const browser = await chromium.launch();
  
  // Desktop check
  const desktopCtx = await browser.newContext({ viewport: { width: 1440, height: 900 } });
  const desktopPage = await desktopCtx.newPage();
  await desktopPage.goto('http://localhost:8000/index.php');
  await desktopPage.waitForTimeout(2000);
  
  const desktopErrors = await desktopPage.evaluate(() => JSON.stringify({
    consoleErrors: window.__errors || [],
    elementCount: document.querySelectorAll('div,section,main,nav').length,
    hasContent: document.body.innerText.length > 500,
    title: document.title || 'N/A'
  }));
  await desktopPage.screenshot({ path: '/workspace/check-desktop.png', fullPage: true });
  await desktopCtx.close();
  
  // Mobile check
  const mobileCtx = await browser.newContext({ viewport: { width: 375, height: 812 } });
  const mobilePage = await mobileCtx.newPage();
  await mobilePage.goto('http://localhost:8000/index.php');
  await mobilePage.waitForTimeout(2000);
  
  const mobileChecks = await mobilePage.evaluate(() => {
    const grid = document.querySelector('.lgnewui-grid');
    const gridStyle = grid ? getComputedStyle(grid) : null;
    const lovers = document.querySelector('.lovers-panel');
    const loversStyle = lovers ? getComputedStyle(lovers) : null;
    
    return {
      gridColumns: gridStyle ? gridStyle.gridTemplateColumns : 'N/A',
      loversDirection: loversStyle ? loversStyle.flexDirection : 'N/A',
      visibleElements: document.querySelectorAll('*').length,
      bodyHeight: document.body.scrollHeight
    };
  });
  await mobilePage.screenshot({ path: '/workspace/check-mobile.png', fullPage: true });
  await mobileCtx.close();
  
  await browser.close();
  
  console.log('=== Desktop Check ===');
  console.log(desktopErrors);
  console.log('Screenshot: /workspace/check-desktop.png');
  console.log('\n=== Mobile Check (375px) ===');
  console.log(JSON.stringify(mobileChecks, null, 2));
  console.log('Screenshot: /workspace/check-mobile.png');
})();
