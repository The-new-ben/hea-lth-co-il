import { spawn } from "node:child_process";
import { mkdir, rm, writeFile } from "node:fs/promises";
import { join } from "node:path";
import { tmpdir } from "node:os";

const chromePath = "C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe";
const url = process.argv[2] || "https://hea-lth.co.il/breast-augmentation-cost/";
const width = Number(process.argv[3] || 405);
const height = Number(process.argv[4] || 1400);
const outPrefix = process.argv[5] || "health-chrome-check";
const port = 9333 + Math.floor(Math.random() * 500);
const userDataDir = join(tmpdir(), `codex-health-chrome-${Date.now()}`);

await mkdir(userDataDir, { recursive: true });

const chrome = spawn(chromePath, [
  "--headless=new",
  "--disable-gpu",
  "--hide-scrollbars",
  "--no-first-run",
  "--no-default-browser-check",
  `--remote-debugging-port=${port}`,
  `--user-data-dir=${userDataDir}`,
  `--window-size=${width},${height}`,
  "about:blank",
], { stdio: "ignore" });

async function waitForJson(path) {
  const endpoint = `http://127.0.0.1:${port}${path}`;
  for (let i = 0; i < 60; i++) {
    try {
      const res = await fetch(endpoint);
      if (res.ok) return await res.json();
    } catch {}
    await new Promise((resolve) => setTimeout(resolve, 250));
  }
  throw new Error(`Chrome CDP did not become ready: ${endpoint}`);
}

function connect(wsUrl) {
  const ws = new WebSocket(wsUrl);
  let id = 0;
  const pending = new Map();
  const events = [];

  ws.addEventListener("message", (event) => {
    const msg = JSON.parse(event.data);
    if (msg.id && pending.has(msg.id)) {
      const { resolve, reject } = pending.get(msg.id);
      pending.delete(msg.id);
      if (msg.error) reject(new Error(JSON.stringify(msg.error)));
      else resolve(msg.result);
    } else if (msg.method) {
      events.push(msg);
    }
  });

  return new Promise((resolve, reject) => {
    ws.addEventListener("open", () => {
      resolve({
        ws,
        events,
        send(method, params = {}) {
          const nextId = ++id;
          ws.send(JSON.stringify({ id: nextId, method, params }));
          return new Promise((res, rej) => pending.set(nextId, { resolve: res, reject: rej }));
        },
      });
    }, { once: true });
    ws.addEventListener("error", reject, { once: true });
  });
}

async function wait(ms) {
  await new Promise((resolve) => setTimeout(resolve, ms));
}

let client;
try {
  const version = await waitForJson("/json/version");
  client = await connect(version.webSocketDebuggerUrl);
  await client.send("Target.setDiscoverTargets", { discover: true });
  const created = await client.send("Target.createTarget", { url: "about:blank" });
  const targets = await waitForJson("/json/list");
  const pageTarget = targets.find((target) => target.id === created.targetId) || targets.find((target) => target.type === "page");
  const page = await connect(pageTarget.webSocketDebuggerUrl);

  await page.send("Page.enable");
  await page.send("Runtime.enable");
  await page.send("Emulation.setDeviceMetricsOverride", {
    width,
    height,
    deviceScaleFactor: 1,
    mobile: width <= 520,
  });
  await page.send("Page.navigate", { url });
  await wait(4000);

  const expression = `(() => {
    const visible = el => !!el && getComputedStyle(el).display !== 'none' && getComputedStyle(el).visibility !== 'hidden' && el.getBoundingClientRect().width > 0 && el.getBoundingClientRect().height > 0;
    const links = sel => Array.from(document.querySelectorAll(sel)).filter(visible).map(a => a.textContent.trim());
    const before = sel => {
      const el = document.querySelector(sel);
      return el ? getComputedStyle(el, '::before').content : null;
    };
    const h1 = document.querySelector('h1.entry-title');
    const footerMenu = document.querySelector('#site-footer .site-navigation .menu');
    const footerBrand = document.querySelector('#site-footer .site-branding');
    const headerA = document.querySelector('#site-header .site-navigation .menu a');
    const bad = ['CRM','לידים','ספקים','כוונת חיפוש','עמוד כסף','מוניטיזציה'];
    return {
      url: location.href,
      viewport: { width: innerWidth, height: innerHeight },
      scrollWidth: document.documentElement.scrollWidth,
      clientWidth: document.documentElement.clientWidth,
      hasHorizontalOverflow: document.documentElement.scrollWidth > document.documentElement.clientWidth + 2,
      h1Text: h1 ? h1.textContent.trim() : null,
      h1Count: document.querySelectorAll('h1').length,
      h1Font: h1 ? getComputedStyle(h1).fontFamily : null,
      h1Weight: h1 ? getComputedStyle(h1).fontWeight : null,
      headerBrandBefore: before('#site-header .site-branding'),
      footerBrandBefore: before('#site-footer .site-branding'),
      footerNavBefore: before('#site-footer .site-navigation'),
      headerLinks: links('#site-header .site-navigation .menu a'),
      headerFont: headerA ? getComputedStyle(headerA).fontFamily : null,
      footerLinks: links('#site-footer .site-navigation .menu a'),
      footerGrid: footerMenu ? getComputedStyle(footerMenu).gridTemplateColumns : null,
      footerFont: footerMenu ? getComputedStyle(footerMenu).fontFamily : null,
      footerBrandRect: footerBrand ? {
        width: Math.round(footerBrand.getBoundingClientRect().width),
        columns: getComputedStyle(footerBrand).gridTemplateColumns,
        display: getComputedStyle(footerBrand).display,
      } : null,
      trustVisible: visible(document.querySelector('.health-trust-band')),
      oldLogoVisible: Array.from(document.querySelectorAll('#site-header img.custom-logo,#site-footer img.custom-logo')).some(visible),
      internalVisible: bad.filter(w => document.body.innerText.includes(w)),
    };
  })()`;

  const metrics = await page.send("Runtime.evaluate", { expression, returnByValue: true });

  const topShot = await page.send("Page.captureScreenshot", { format: "png", fromSurface: true });
  await writeFile(`${outPrefix}-top.png`, Buffer.from(topShot.data, "base64"));

  await page.send("Runtime.evaluate", {
    expression: "document.querySelector('#site-footer')?.scrollIntoView({block:'start'});",
    returnByValue: true,
  });
  await wait(700);
  const footerShot = await page.send("Page.captureScreenshot", { format: "png", fromSurface: true });
  await writeFile(`${outPrefix}-footer.png`, Buffer.from(footerShot.data, "base64"));

  console.log(JSON.stringify({
    url,
    width,
    height,
    metrics: metrics.result.value,
    screenshots: {
      top: `${outPrefix}-top.png`,
      footer: `${outPrefix}-footer.png`,
    },
  }, null, 2));
} finally {
  try { client?.ws?.close(); } catch {}
  chrome.kill();
  await new Promise((resolve) => {
    chrome.once("exit", resolve);
    setTimeout(resolve, 1000);
  });
  await rm(userDataDir, { recursive: true, force: true }).catch(() => {});
}
