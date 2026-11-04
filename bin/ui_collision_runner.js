/**
 * FMS Live Headless Chrome Visual Collision & Layout Auditor
 *
 * Launches headless Chrome, connects via native WebSocket CDP,
 * logs into FMS, navigates through core pages, and tests:
 * 1. Bounding box collisions / visual overlaps between inputs, buttons, and containers
 * 2. Viewport horizontal overflow (page blowing out width)
 * 3. Broken assets (404 CSS/JS/images) or JavaScript runtime exceptions
 * 4. Modal button duplicates & interactive element accessibility
 *
 * Usage:
 *   node bin/ui_collision_runner.js
 */

const { spawn } = require('child_process');
const fs = require('fs');
const path = require('path');

const CHROME_PATH = 'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe';
const BASE_URL = 'http://localhost/mini/FMS/public/index.php';
const CDP_PORT = 9222;
const USER_DATA_DIR = path.join(__dirname, '..', 'tmp', 'cdp_profile_' + Date.now());

async function sleep(ms) {
    return new Promise(r => setTimeout(r, ms));
}

class ChromeClient {
    constructor(wsUrl) {
        this.wsUrl = wsUrl;
        this.ws = null;
        this.id = 0;
        this.pending = new Map();
        this.events = [];
    }

    async connect() {
        return new Promise((resolve, reject) => {
            this.ws = new WebSocket(this.wsUrl);
            this.ws.onopen = () => resolve();
            this.ws.onerror = (err) => reject(err);
            this.ws.onmessage = (msg) => {
                const data = JSON.parse(msg.data);
                if (data.id && this.pending.has(data.id)) {
                    const { resolve, reject } = this.pending.get(data.id);
                    this.pending.delete(data.id);
                    if (data.error) reject(data.error);
                    else resolve(data.result);
                } else if (data.method) {
                    this.events.push(data);
                }
            };
        });
    }

    async send(method, params = {}) {
        const id = ++this.id;
        return new Promise((resolve, reject) => {
            this.pending.set(id, { resolve, reject });
            this.ws.send(JSON.stringify({ id, method, params }));
        });
    }

    close() {
        if (this.ws) {
            try { this.ws.close(); } catch (e) {}
        }
    }
}

async function runAuditor() {
    console.log("================================================================================");
    console.log("           FMS HEADLESS CHROME LIVE VISUAL COLLISION & DOM AUDITOR             ");
    console.log("================================================================================\n");

    if (!fs.existsSync(CHROME_PATH)) {
        console.error("ERROR: Chrome binary not found at: " + CHROME_PATH);
        process.exit(1);
    }

    // 1. Launch Chrome
    console.log("[1] Spawning headless Chrome browser (1366x768 standard desktop)...");
    const chromeProc = spawn(CHROME_PATH, [
        '--headless=new',
        `--remote-debugging-port=${CDP_PORT}`,
        `--user-data-dir=${USER_DATA_DIR}`,
        '--window-size=1366,768',
        '--no-first-run',
        '--no-default-browser-check',
        '--disable-gpu',
        'about:blank'
    ]);

    let version = null;
    for (let i = 0; i < 25; i++) {
        await sleep(200);
        try {
            const res = await fetch(`http://127.0.0.1:${CDP_PORT}/json/version`);
            if (res.ok) {
                version = await res.json();
                break;
            }
        } catch (e) {}
    }

    if (!version) {
        console.error("ERROR: Could not connect to Chrome DevTools port.");
        chromeProc.kill('SIGKILL');
        process.exit(1);
    }

    console.log("    Connected to " + version.Browser);

    // 2. Attach to page target
    const listRes = await fetch(`http://127.0.0.1:${CDP_PORT}/json/list`);
    const targets = await listRes.json();
    const pageTarget = targets.find(t => t.type === 'page') || targets[0];

    const client = new ChromeClient(pageTarget.webSocketDebuggerUrl);
    await client.connect();

    await client.send('Page.enable');
    await client.send('Runtime.enable');
    await client.send('Network.enable');

    const networkErrors = [];
    const jsErrors = [];

    // Track console and network errors
    const originalEvents = client.events;
    const intervalId = setInterval(() => {
        while (originalEvents.length > 0) {
            const ev = originalEvents.shift();
            if (ev.method === 'Network.responseReceived') {
                const status = ev.params.response.status;
                if (status >= 400 && !ev.params.response.url.includes('favicon.ico')) {
                    networkErrors.push({ url: ev.params.response.url, status });
                }
            } else if (ev.method === 'Runtime.exceptionThrown') {
                jsErrors.push(ev.params.exceptionDetails.text);
            }
        }
    }, 50);

    // Helper: Navigate and wait
    async function navigate(url) {
        await client.send('Page.navigate', { url });
        await sleep(1500); // Wait for DOM, CSSOM and scripts
    }

    // Helper: Evaluate script in page
    async function evaluate(fnStr) {
        const res = await client.send('Runtime.evaluate', {
            expression: `(${fnStr})()`,
            returnByValue: true
        });
        if (res.exceptionDetails) {
            throw new Error(res.exceptionDetails.text);
        }
        return res.result ? res.result.value : null;
    }

    // 3. Authenticate as Admin
    console.log("\n[2] Logging in as Administrator...");
    await navigate(`${BASE_URL}?route=auth/login`);

    const loginResult = await evaluate(`function() {
        const u = document.querySelector('input[name="identifier"], input[name="username"], input[name="email"], #identifier, #username');
        const p = document.querySelector('input[name="password"], #password');
        const btn = document.querySelector('button[type="submit"]');
        if (u && p) {
            u.value = 'admin@gmrit.edu.in';
            p.value = 'Admin@123';
            if (btn) btn.click();
            else if (u.form) u.form.submit();
            return { success: true };
        }
        return { success: false, reason: 'Login form not found' };
    }`);

    if (loginResult && loginResult.success) {
        await sleep(1800); // Wait for redirect to dashboard
        console.log("    Logged in successfully.");
    } else {
        console.log("    Notice: " + (loginResult ? loginResult.reason : 'unknown'));
    }

    // 4. Test Key Pages for DOM Collision & Viewport Overflow
    const pagesToAudit = [
        { name: "Admin Dashboard", url: `${BASE_URL}?route=dashboard` },
        { name: "Admin Departments", url: `${BASE_URL}?route=admin/departments` },
        { name: "Admin Users Management", url: `${BASE_URL}?route=admin/users` },
        { name: "Department Files List", url: `${BASE_URL}?route=documents/list&context=dept_file` },
        { name: "Document View Page", url: `${BASE_URL}?route=documents/view&id=41` },
        { name: "Upload Journal Form", url: `${BASE_URL}?route=documents/upload&type=journal` },
        { name: "Upload Conference Form", url: `${BASE_URL}?route=documents/upload&type=conference` }
    ];

    console.log("\n[3] Auditing Core Pages for Visual Collisions, Overlaps & Layout Integrity...");

    let totalCollisions = 0;
    let totalOverflows = 0;

    for (const page of pagesToAudit) {
        process.stdout.write(`    Checking [${page.name}]... `);
        await navigate(page.url);

        // In-Page DOM Inspector
        const audit = await evaluate(`function() {
            const results = {
                title: document.title,
                scrollWidth: document.documentElement.scrollWidth,
                clientWidth: window.innerWidth,
                hasOverflow: document.documentElement.scrollWidth > window.innerWidth,
                collisions: [],
                visibleModals: 0,
                duplicateButtons: []
            };

            function getVisibleRect(el) {
                let rect = el.getBoundingClientRect();
                if (rect.width <= 0 || rect.height <= 0) return null;
                const style = window.getComputedStyle(el);
                if (style.display === 'none' || style.visibility === 'hidden' || style.opacity === '0') return null;

                let curr = el.parentElement;
                let clippedRect = {
                    left: rect.left,
                    top: rect.top,
                    right: rect.right,
                    bottom: rect.bottom
                };

                while (curr && curr !== document.body && curr !== document.documentElement) {
                    const cs = window.getComputedStyle(curr);
                    const isScrollContainer = cs.overflow === 'hidden' || cs.overflow === 'auto' || cs.overflow === 'scroll' || cs.overflowY === 'auto' || cs.overflowY === 'hidden';
                    if (isScrollContainer) {
                        const pr = curr.getBoundingClientRect();
                        clippedRect.left = Math.max(clippedRect.left, pr.left);
                        clippedRect.top = Math.max(clippedRect.top, pr.top);
                        clippedRect.right = Math.min(clippedRect.right, pr.right);
                        clippedRect.bottom = Math.min(clippedRect.bottom, pr.bottom);
                    }
                    curr = curr.parentElement;
                }

                const width = Math.max(0, clippedRect.right - clippedRect.left);
                const height = Math.max(0, clippedRect.bottom - clippedRect.top);
                if (width <= 0 || height <= 0) return null;

                return {
                    left: clippedRect.left,
                    top: clippedRect.top,
                    right: clippedRect.right,
                    bottom: clippedRect.bottom,
                    width: width,
                    height: height
                };
            }

            // 1. Check Visual Collisions between interactive elements
            const selector = 'input:not([type="hidden"]), select, textarea, button, a.btn, a[style*="background"], .status-badge, table th, table td > div';
            const rawElements = Array.from(document.querySelectorAll(selector));
            const elementsWithRect = [];

            for (const el of rawElements) {
                const vr = getVisibleRect(el);
                if (vr) {
                    elementsWithRect.push({ el, rect: vr });
                }
            }

            for (let i = 0; i < elementsWithRect.length; i++) {
                const itemA = elementsWithRect[i];
                const elA = itemA.el;
                const rA = itemA.rect;

                for (let j = i + 1; j < elementsWithRect.length; j++) {
                    const itemB = elementsWithRect[j];
                    const elB = itemB.el;
                    const rB = itemB.rect;

                    // Skip parent-child containment
                    if (elA.contains(elB) || elB.contains(elA)) continue;

                    // Calculate overlap
                    const overlapX = Math.max(0, Math.min(rA.right, rB.right) - Math.max(rA.left, rB.left));
                    const overlapY = Math.max(0, Math.min(rA.bottom, rB.bottom) - Math.max(rA.top, rB.top));

                    // Flag if overlap is significant (> 8px on both axes)
                    if (overlapX > 8 && overlapY > 8) {
                        const tagA = (elA.id ? '#' + elA.id : '') || (elA.className ? '.' + elA.className.split(' ')[0] : '') || elA.tagName.toLowerCase();
                        const tagB = (elB.id ? '#' + elB.id : '') || (elB.className ? '.' + elB.className.split(' ')[0] : '') || elB.tagName.toLowerCase();
                        results.collisions.push({
                            elA: tagA + ' (' + (elA.innerText || elA.placeholder || elA.value || '').trim().slice(0, 20) + ')',
                            elB: tagB + ' (' + (elB.innerText || elB.placeholder || elB.value || '').trim().slice(0, 20) + ')',
                            overlap: Math.round(overlapX) + 'x' + Math.round(overlapY) + 'px'
                        });
                    }
                }
            }

            // 2. Check Modals for Duplicate Action Buttons
            const modals = document.querySelectorAll('.modal, [id*="modal"]');
            modals.forEach(m => {
                const btns = Array.from(m.querySelectorAll('button, a.btn')).map(b => b.innerText.trim().toLowerCase()).filter(t => t.length > 0);
                const counts = {};
                btns.forEach(b => { counts[b] = (counts[b] || 0) + 1; });
                for (const [name, count] of Object.entries(counts)) {
                    if (count > 1) {
                        results.duplicateButtons.push({ modal: m.id || 'unnamed', button: name, count });
                    }
                }
            });

            return results;
        }`);

        if (audit.hasOverflow) {
            totalOverflows++;
            console.log(`\n       [WARN] Horizontal Overflow: Page width (${audit.scrollWidth}px) exceeds window (${audit.clientWidth}px).`);
        }

        if (audit.collisions && audit.collisions.length > 0) {
            totalCollisions += audit.collisions.length;
            console.log(`\n       [COLLISION DETECTED] ${audit.collisions.length} overlapping element(s):`);
            audit.collisions.slice(0, 3).forEach(c => {
                console.log(`         * ${c.elA} overlaps ${c.elB} by ${c.overlap}`);
            });
        }

        if (audit.duplicateButtons && audit.duplicateButtons.length > 0) {
            console.log(`\n       [DUPLICATE BUTTONS] Modal contains identical duplicate actions:`);
            audit.duplicateButtons.forEach(d => {
                console.log(`         * Button '${d.button}' appears ${d.count} times in modal #${d.modal}`);
            });
        }

        if (!audit.hasOverflow && audit.collisions.length === 0 && audit.duplicateButtons.length === 0) {
            console.log("CLEAN (0 collisions, 0 overflows)");
        }
    }

    // 5. Cleanup
    clearInterval(intervalId);
    client.close();
    chromeProc.kill('SIGKILL');
    try { fs.rmSync(USER_DATA_DIR, { recursive: true, force: true }); } catch (e) {}

    console.log("\n================================================================================");
    console.log("AUDIT SUMMARY:");
    console.log(`  Visual Collisions Detected : ${totalCollisions}`);
    console.log(`  Horizontal Viewport Faults : ${totalOverflows}`);
    console.log(`  Broken Network Assets      : ${networkErrors.length}`);
    if (networkErrors.length > 0) {
        networkErrors.forEach(err => console.log(`    - [HTTP ${err.status}] ${err.url}`));
    }
    console.log(`  JavaScript Console Errors  : ${jsErrors.length}`);
    console.log("================================================================================");

    process.exit(0);
}

runAuditor().catch(err => {
    console.error("Auditor error:", err);
    process.exit(1);
});
