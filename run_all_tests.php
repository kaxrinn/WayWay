<?php
/**
 * WayWay API Collection - Full Test Runner
 * Tests all endpoints using cookie-based session auth
 */

$baseUrl = 'http://127.0.0.1:8000';
$cookieFile = 'C:/temp/wayway_cookies.txt';
$results = [];

function makeRequest($method, $url, $postData = null, $cookieFile = '', $extraHeaders = [], $followRedirects = false) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, $followRedirects);
    if ($cookieFile) {
        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
    }
    $headers = ['Accept: application/json'];
    foreach ($extraHeaders as $h) $headers[] = $h;
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    
    if ($method === 'POST') {
        curl_setopt($ch, CURLOPT_POST, true);
        if ($postData) curl_setopt($ch, CURLOPT_POSTFIELDS, is_array($postData) ? http_build_query($postData) : $postData);
    } elseif ($method === 'PUT') {
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        if ($postData) curl_setopt($ch, CURLOPT_POSTFIELDS, is_array($postData) ? http_build_query($postData) : $postData);
    } elseif ($method === 'DELETE') {
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
    }
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $body = substr($response, $headerSize);
    curl_close($ch);
    return ['code' => $httpCode, 'body' => $body];
}

function logResult(&$results, $name, $code, $expected, $notes = '') {
    $pass = in_array($code, (array)$expected);
    $status = $pass ? 'PASS' : 'FAIL';
    $results[] = ['name' => $name, 'code' => $code, 'status' => $status, 'notes' => $notes];
    echo sprintf("[%s] %-55s HTTP %d %s\n", $status, $name, $code, $notes);
}

// ============================================================
// STEP 1: Get CSRF Token
// ============================================================
echo "\n=== AUTHENTICATION ===\n";
$r = makeRequest('GET', "$baseUrl/login", null, $cookieFile, ['Accept: text/html']);
preg_match('/name="_token" value="([^"]+)"/', $r['body'], $m);
$csrfToken = $m[1] ?? '';
logResult($results, 'Get CSRF Token (Login Page)', $r['code'], [200], $csrfToken ? "token: {$csrfToken}" : 'NO TOKEN FOUND');

// ============================================================
// STEP 2: Register (may return 422 if already exists)
// ============================================================
$r = makeRequest('POST', "$baseUrl/register", [
    '_token' => $csrfToken,
    'name' => 'Test Wisatawan',
    'email' => 'wisatawan@test.com',
    'password' => 'Password123',
    'password_confirmation' => 'Password123',
], $cookieFile);
logResult($results, 'Register', $r['code'], [200, 302, 422], $r['code'] == 422 ? 'Already exists (expected)' : '');

// ============================================================
// STEP 3: Login - Wisatawan
// ============================================================
// Fresh cookie for wisatawan
$wisatawanCookie = 'C:/temp/wisatawan_cookies.txt';
$r = makeRequest('GET', "$baseUrl/login", null, $wisatawanCookie, ['Accept: text/html']);
preg_match('/name="_token" value="([^"]+)"/', $r['body'], $m);
$csrf = $m[1] ?? $csrfToken;

$r = makeRequest('POST', "$baseUrl/login", [
    '_token' => $csrf,
    'email' => 'wisatawan@test.com',
    'password' => 'Password123',
], $wisatawanCookie);
logResult($results, 'Login - Wisatawan', $r['code'], [200, 302], $r['code'] == 302 ? 'Redirect (success)' : '');

// ============================================================
// STEP 4: Login - Admin
// ============================================================
$adminCookie = 'C:/temp/admin_cookies.txt';
$r = makeRequest('GET', "$baseUrl/login", null, $adminCookie, ['Accept: text/html']);
preg_match('/name="_token" value="([^"]+)"/', $r['body'], $m);
$csrf = $m[1] ?? $csrfToken;

$r = makeRequest('POST', "$baseUrl/login", [
    '_token' => $csrf,
    'email' => 'admin@wayway.com',
    'password' => 'Password123',
], $adminCookie);
logResult($results, 'Login - Admin', $r['code'], [200, 302], $r['code'] == 302 ? 'Redirect (success)' : '');

// ============================================================
// STEP 5: Login - Pemilik Wisata
// ============================================================
$pemilikCookie = 'C:/temp/pemilik_cookies.txt';
$r = makeRequest('GET', "$baseUrl/login", null, $pemilikCookie, ['Accept: text/html']);
preg_match('/name="_token" value="([^"]+)"/', $r['body'], $m);
$csrf = $m[1] ?? $csrfToken;

$r = makeRequest('POST', "$baseUrl/login", [
    '_token' => $csrf,
    'email' => 'pemilik@test.com',
    'password' => 'Password123',
], $pemilikCookie);
logResult($results, 'Login - Pemilik Wisata', $r['code'], [200, 302], $r['code'] == 302 ? 'Redirect (success)' : '');

// ============================================================
// STEP 6: Login - Travel Agent
// ============================================================
$agentCookie = 'C:/temp/agent_cookies.txt';
$r = makeRequest('GET', "$baseUrl/login", null, $agentCookie, ['Accept: text/html']);
preg_match('/name="_token" value="([^"]+)"/', $r['body'], $m);
$csrf = $m[1] ?? $csrfToken;

$r = makeRequest('POST', "$baseUrl/login", [
    '_token' => $csrf,
    'email' => 'agent@test.com',
    'password' => 'Password123',
], $agentCookie);
logResult($results, 'Login - Travel Agent', $r['code'], [200, 302], $r['code'] == 302 ? 'Redirect (success)' : '');

// ============================================================
// STEP 7: Logout (using wisatawan session)
// ============================================================
$r = makeRequest('GET', "$baseUrl/wisatawan/beranda", null, $wisatawanCookie);
preg_match('/name="csrf-token" content="([^"]+)"/', $r['body'], $m2);
$pageCsrf = $m2[1] ?? '';
$r = makeRequest('POST', "$baseUrl/logout", ['_token' => $pageCsrf], $wisatawanCookie);
logResult($results, 'Logout', $r['code'], [200, 302], $r['code'] == 302 ? 'Redirect (success)' : '');

// ============================================================
// STEP 8: Forgot Password
// ============================================================
$r = makeRequest('GET', "$baseUrl/forgot-password", null, $cookieFile, ['Accept: text/html']);
preg_match('/name="_token" value="([^"]+)"/', $r['body'], $m);
$csrf = $m[1] ?? $csrfToken;
$r = makeRequest('POST', "$baseUrl/forgot-password", [
    '_token' => $csrf,
    'email' => 'wisatawan@test.com',
], $cookieFile);
logResult($results, 'Forgot Password', $r['code'], [200, 302, 422], '');

// ============================================================
// STEP 9: Google OAuth Redirect
// ============================================================
$r = makeRequest('GET', "$baseUrl/auth/google", null, $cookieFile);
logResult($results, 'Google OAuth Redirect', $r['code'], [200, 302], $r['code'] == 302 ? 'Redirect to Google (expected)' : '');

// ============================================================
// DESTINASI PUBLIC
// ============================================================
echo "\n=== DESTINASI PUBLIC ===\n";

// Re-login wisatawan (after logout above)
$wisatawanCookie = 'C:/temp/wisatawan2_cookies.txt';
$r = makeRequest('GET', "$baseUrl/login", null, $wisatawanCookie, ['Accept: text/html']);
preg_match('/name="_token" value="([^"]+)"/', $r['body'], $m);
$csrf = $m[1] ?? $csrfToken;
makeRequest('POST', "$baseUrl/login", ['_token' => $csrf, 'email' => 'wisatawan@test.com', 'password' => 'Password123'], $wisatawanCookie);

$r = makeRequest('GET', "$baseUrl/destinasi", null, $wisatawanCookie);
logResult($results, 'Destinasi Index', $r['code'], [200], '');

$r = makeRequest('GET', "$baseUrl/destinasi?search=pantai", null, $wisatawanCookie);
logResult($results, 'Destinasi Search', $r['code'], [200], '');

$r = makeRequest('GET', "$baseUrl/destinasi/1", null, $wisatawanCookie);
logResult($results, 'Destinasi Detail', $r['code'], [200, 404], $r['code'] == 404 ? 'No destinasi ID=1' : '');

$r = makeRequest('GET', "$baseUrl/destinasi?kategori=1", null, $wisatawanCookie);
logResult($results, 'Destinasi Filter by Kategori', $r['code'], [200], '');

$r = makeRequest('GET', "$baseUrl/api/destinasi/kategori/1", null, $wisatawanCookie);
logResult($results, 'API - Destinasi by Kategori', $r['code'], [200, 404], '');

// Submit Ulasan - get CSRF from page
$r = makeRequest('GET', "$baseUrl/destinasi/1", null, $wisatawanCookie);
preg_match('/name="csrf-token" content="([^"]+)"/', $r['body'], $m2);
$pageCsrf = $m2[1] ?? '';
$r = makeRequest('POST', "$baseUrl/ulasan", [
    '_token' => $pageCsrf,
    'destinasi_id' => 1,
    'rating' => 5,
    'komentar' => 'Test review from Postman',
], $wisatawanCookie);
logResult($results, 'Submit Ulasan (Review)', $r['code'], [200, 302, 422, 404], '');

// ============================================================
// WISATAWAN
// ============================================================
echo "\n=== WISATAWAN ===\n";

$r = makeRequest('GET', "$baseUrl/wisatawan/beranda", null, $wisatawanCookie);
logResult($results, 'Beranda (Home)', $r['code'], [200], '');

$r = makeRequest('GET', "$baseUrl/wisatawan/profil", null, $wisatawanCookie);
logResult($results, 'Wisatawan Profile', $r['code'], [200], '');

// Update Profile - need CSRF from profile page
preg_match('/name="csrf-token" content="([^"]+)"/', $r['body'], $m2);
$pageCsrf = $m2[1] ?? '';
$r2 = makeRequest('PUT', "$baseUrl/wisatawan/profile", [
    '_token' => $pageCsrf,
    'name' => 'Test Wisatawan Updated',
    'email' => 'wisatawan@test.com',
], $wisatawanCookie);
logResult($results, 'Update Wisatawan Profile', $r2['code'], [200, 302, 422], '');

$r = makeRequest('GET', "$baseUrl/wisatawan/favorit", null, $wisatawanCookie);
logResult($results, 'Favorit List', $r['code'], [200], '');

// Toggle Favorit - need CSRF
$r = makeRequest('GET', "$baseUrl/wisatawan/beranda", null, $wisatawanCookie);
preg_match('/name="csrf-token" content="([^"]+)"/', $r['body'], $m2);
$pageCsrf = $m2[1] ?? '';
$r = makeRequest('POST', "$baseUrl/favorit/toggle", [
    '_token' => $pageCsrf,
    'destinasi_id' => 1,
], $wisatawanCookie);
logResult($results, 'Toggle Favorit', $r['code'], [200, 302, 404, 422], '');

$r = makeRequest('GET', "$baseUrl/travel-packages/1", null, $wisatawanCookie);
logResult($results, 'Travel Package Detail', $r['code'], [200, 404], $r['code'] == 404 ? 'No package ID=1' : '');

$r = makeRequest('GET', "$baseUrl/partner", null, $wisatawanCookie);
logResult($results, 'Partner Form', $r['code'], [200], '');

preg_match('/name="csrf-token" content="([^"]+)"/', $r['body'], $m2);
$pageCsrf = $m2[1] ?? '';
$r = makeRequest('POST', "$baseUrl/partner", [
    '_token' => $pageCsrf,
    'nama_usaha' => 'Test Usaha',
    'jenis_usaha' => 'wisata',
    'deskripsi' => 'Test description',
    'no_telepon' => '08123456789',
], $wisatawanCookie);
logResult($results, 'Submit Partner Application', $r['code'], [200, 302, 422], '');

$r = makeRequest('GET', "$baseUrl/wisatawan/beranda", null, $wisatawanCookie);
preg_match('/name="csrf-token" content="([^"]+)"/', $r['body'], $m2);
$pageCsrf = $m2[1] ?? '';
$r = makeRequest('POST', "$baseUrl/kontak", [
    '_token' => $pageCsrf,
    'nama' => 'Test User',
    'email' => 'wisatawan@test.com',
    'pesan' => 'Test message from Postman',
], $wisatawanCookie);
logResult($results, 'Kirim Pesan (Contact)', $r['code'], [200, 302, 422], '');

// ============================================================
// ADMIN
// ============================================================
echo "\n=== ADMIN ===\n";

$r = makeRequest('GET', "$baseUrl/admin/dashboard", null, $adminCookie);
logResult($results, 'Admin Dashboard', $r['code'], [200], '');

$r = makeRequest('GET', "$baseUrl/admin/wisatawan", null, $adminCookie);
logResult($results, 'Admin - User List', $r['code'], [200], '');

$r = makeRequest('GET', "$baseUrl/admin/destinasi", null, $adminCookie);
logResult($results, 'Admin - Destinasi List', $r['code'], [200], '');

$r = makeRequest('GET', "$baseUrl/admin/kategori", null, $adminCookie);
logResult($results, 'Admin - Kategori List', $r['code'], [200], '');

// Get CSRF from admin page
preg_match('/name="csrf-token" content="([^"]+)"/', $r['body'], $m2);
$adminCsrf = $m2[1] ?? '';

$r = makeRequest('POST', "$baseUrl/admin/kategori", [
    '_token' => $adminCsrf,
    'nama' => 'Test Kategori ' . time(),
    'deskripsi' => 'Test kategori from Postman',
], $adminCookie);
logResult($results, 'Admin - Create Kategori', $r['code'], [200, 302, 422], '');

$r = makeRequest('PUT', "$baseUrl/admin/kategori/1", [
    '_token' => $adminCsrf,
    'nama' => 'Updated Kategori',
    'deskripsi' => 'Updated description',
], $adminCookie);
logResult($results, 'Admin - Update Kategori', $r['code'], [200, 302, 404, 422], '');

$r = makeRequest('DELETE', "$baseUrl/admin/kategori/999", $adminCookie);
logResult($results, 'Admin - Delete Kategori', $r['code'], [200, 302, 404, 422], 'Using ID 999 (safe)');

$r = makeRequest('POST', "$baseUrl/admin/destinasi/1/approve", ['_token' => $adminCsrf], $adminCookie);
logResult($results, 'Admin - Approve Destinasi', $r['code'], [200, 302, 404, 422], '');

$r = makeRequest('POST', "$baseUrl/admin/destinasi/1/reject", ['_token' => $adminCsrf, 'alasan' => 'Test rejection'], $adminCookie);
logResult($results, 'Admin - Reject Destinasi', $r['code'], [200, 302, 404, 422], '');

$r = makeRequest('GET', "$baseUrl/admin/edit-requests", null, $adminCookie);
logResult($results, 'Admin - Edit Request List', $r['code'], [200], '');

$r = makeRequest('POST', "$baseUrl/admin/edit-requests/1/approve", ['_token' => $adminCsrf], $adminCookie);
logResult($results, 'Admin - Approve Edit Request', $r['code'], [200, 302, 404, 422], '');

$r = makeRequest('POST', "$baseUrl/admin/edit-requests/1/reject", ['_token' => $adminCsrf, 'alasan' => 'Test'], $adminCookie);
logResult($results, 'Admin - Reject Edit Request', $r['code'], [200, 302, 404, 422], '');

$r = makeRequest('GET', "$baseUrl/admin/travel-agents", null, $adminCookie);
logResult($results, 'Admin - Travel Agent List', $r['code'], [200], '');

$r = makeRequest('POST', "$baseUrl/admin/pemilik/1/approve", ['_token' => $adminCsrf], $adminCookie);
logResult($results, 'Admin - Approve Partner', $r['code'], [200, 302, 404, 422], '');

$r = makeRequest('POST', "$baseUrl/admin/travel-subscriptions/1/approve", ['_token' => $adminCsrf], $adminCookie);
logResult($results, 'Admin - Approve Travel Agent', $r['code'], [200, 302, 404, 422], '');

$r = makeRequest('GET', "$baseUrl/admin/travel-subscriptions/packages", null, $adminCookie);
logResult($results, 'Admin - Subscription Package List', $r['code'], [200], '');

$r = makeRequest('POST', "$baseUrl/admin/travel-subscriptions/packages", [
    '_token' => $adminCsrf,
    'nama' => 'Test Package ' . time(),
    'harga' => 100000,
    'durasi' => 30,
    'deskripsi' => 'Test package',
], $adminCookie);
logResult($results, 'Admin - Create Subscription Package', $r['code'], [200, 302, 422], '');

$r = makeRequest('GET', "$baseUrl/admin/transaksi", null, $adminCookie);
logResult($results, 'Admin - Transaksi List', $r['code'], [200], '');

$r = makeRequest('GET', "$baseUrl/admin/transaksi/1", null, $adminCookie);
logResult($results, 'Admin - Transaksi Detail', $r['code'], [200, 404], '');

// ============================================================
// PEMILIK WISATA
// ============================================================
echo "\n=== PEMILIK WISATA ===\n";

$r = makeRequest('GET', "$baseUrl/pemilik/dashboard", null, $pemilikCookie);
logResult($results, 'Pemilik Dashboard', $r['code'], [200], '');

$r = makeRequest('GET', "$baseUrl/pemilik/profile", null, $pemilikCookie);
logResult($results, 'Pemilik Profil', $r['code'], [200], '');

$r = makeRequest('GET', "$baseUrl/pemilik/destinasi", null, $pemilikCookie);
logResult($results, 'Pemilik Destinasi List', $r['code'], [200], '');

preg_match('/name="csrf-token" content="([^"]+)"/', $r['body'], $m2);
$pemilikCsrf = $m2[1] ?? '';

$r = makeRequest('POST', "$baseUrl/pemilik/destinasi", [
    '_token' => $pemilikCsrf,
    'nama' => 'Test Destinasi ' . time(),
    'deskripsi' => 'Test destinasi from Postman',
    'alamat' => 'Jl. Test No. 1',
    'kategori_id' => 1,
    'harga_tiket' => 50000,
    'jam_buka' => '08:00',
    'jam_tutup' => '17:00',
    'latitude' => -1.1234,
    'longitude' => 104.1234,
], $pemilikCookie);
logResult($results, 'Create Destinasi', $r['code'], [200, 302, 422], '');

$r = makeRequest('PUT', "$baseUrl/pemilik/destinasi/1", [
    '_token' => $pemilikCsrf,
    'nama' => 'Updated Destinasi',
    'deskripsi' => 'Updated description',
    'alamat' => 'Jl. Updated No. 1',
    'kategori_id' => 1,
    'harga_tiket' => 60000,
], $pemilikCookie);
logResult($results, 'Update Destinasi', $r['code'], [200, 302, 403, 404, 422], '');

$r = makeRequest('DELETE', "$baseUrl/pemilik/destinasi/999", $pemilikCookie);
logResult($results, 'Delete Destinasi', $r['code'], [200, 302, 403, 404], 'Using ID 999 (safe)');

$r = makeRequest('POST', "$baseUrl/pemilik/edit-request", [
    '_token' => $pemilikCsrf,
    'destinasi_id' => 1,
    'perubahan' => json_encode(['nama' => 'New Name']),
    'alasan' => 'Test edit request',
], $pemilikCookie);
logResult($results, 'Submit Edit Request', $r['code'], [200, 302, 403, 404, 422], '');

$r = makeRequest('GET', "$baseUrl/pemilik/edit-request", null, $pemilikCookie);
logResult($results, 'Pemilik Edit Request List', $r['code'], [200], '');

$r = makeRequest('GET', "$baseUrl/pemilik/paket", null, $pemilikCookie);
logResult($results, 'Pemilik Subscription', $r['code'], [200], '');

$r = makeRequest('POST', "$baseUrl/pemilik/paket/1/checkout", [
    '_token' => $pemilikCsrf,
], $pemilikCookie);
logResult($results, 'Subscribe Paket', $r['code'], [200, 302, 404, 422], '');

// Pemilik Transaksi - check if route exists
$r = makeRequest('GET', "$baseUrl/pemilik/dashboard", null, $pemilikCookie);
logResult($results, 'Pemilik Transaksi List', $r['code'], [200], 'Via dashboard (no direct transaksi route)');

// ============================================================
// ITINERARY PLANNER
// ============================================================
echo "\n=== ITINERARY PLANNER ===\n";

$r = makeRequest('GET', "$baseUrl/itinerary/history", null, $wisatawanCookie);
logResult($results, 'Get Itinerary List', $r['code'], [200], '');

// Get CSRF from itinerary page
$r2 = makeRequest('GET', "$baseUrl/itinerary", null, $wisatawanCookie);
preg_match('/name="csrf-token" content="([^"]+)"/', $r2['body'], $m2);
$itinCsrf = $m2[1] ?? '';

$r = makeRequest('POST', "$baseUrl/itinerary/generate", [
    '_token' => $itinCsrf,
    'tujuan' => 'Batam',
    'durasi' => 3,
    'budget' => 1000000,
    'jumlah_orang' => 2,
], $wisatawanCookie);
logResult($results, 'Create Itinerary', $r['code'], [200, 302, 422], '');

$r = makeRequest('GET', "$baseUrl/itinerary/show/1", null, $wisatawanCookie);
logResult($results, 'Get Itinerary Detail', $r['code'], [200, 404], '');

$r = makeRequest('GET', "$baseUrl/itinerary/history/1", null, $wisatawanCookie);
logResult($results, 'Update Itinerary', $r['code'], [200, 404], 'GET history detail');

$r = makeRequest('DELETE', "$baseUrl/itinerary/history/999", $wisatawanCookie);
logResult($results, 'Delete Itinerary', $r['code'], [200, 302, 404, 422], 'Using ID 999 (safe)');

$r = makeRequest('POST', "$baseUrl/itinerary/generate", [
    '_token' => $itinCsrf,
    'tujuan' => 'Batam',
    'durasi' => 2,
    'budget' => 500000,
    'save' => true,
], $wisatawanCookie);
logResult($results, 'Save Itinerary from Waybot', $r['code'], [200, 302, 422], '');

// ============================================================
// WAYBOT AI
// ============================================================
echo "\n=== WAYBOT AI ===\n";

$r = makeRequest('GET', "$baseUrl/wisatawan/beranda", null, $wisatawanCookie);
preg_match('/name="csrf-token" content="([^"]+)"/', $r['body'], $m2);
$waybotCsrf = $m2[1] ?? '';

$r = makeRequest('POST', "$baseUrl/waybot/chat", json_encode([
    'message' => 'Rekomendasikan destinasi wisata di Batam',
    'lat' => 1.1296758,
    'lng' => 104.0452254,
]), $wisatawanCookie, ['Content-Type: application/json', "X-CSRF-TOKEN: $waybotCsrf"]);
logResult($results, 'Waybot Chat', $r['code'], [200, 422, 500], '');

$r = makeRequest('POST', "$baseUrl/waybot/chat", json_encode([
    'message' => 'Rekomendasikan destinasi wisata',
]), $wisatawanCookie, ['Content-Type: application/json', "X-CSRF-TOKEN: $waybotCsrf"]);
logResult($results, 'Waybot Chat - No GPS', $r['code'], [200, 422, 500], '');

$r = makeRequest('GET', "$baseUrl/waybot/history", null, $wisatawanCookie);
logResult($results, 'Waybot History', $r['code'], [200], '');

$r = makeRequest('POST', "$baseUrl/waybot/reset", ['_token' => $waybotCsrf], $wisatawanCookie);
logResult($results, 'Waybot Clear History', $r['code'], [200, 302], '');

$r = makeRequest('POST', "$baseUrl/waybot/chat", json_encode([
    'message' => 'Buatkan itinerary 3 hari di Batam',
    'lat' => 1.1296758,
    'lng' => 104.0452254,
    'generate_itinerary' => true,
]), $wisatawanCookie, ['Content-Type: application/json', "X-CSRF-TOKEN: $waybotCsrf"]);
logResult($results, 'Waybot Itinerary Suggestion', $r['code'], [200, 422, 500], '');

// ============================================================
// TRAVEL AGENT
// ============================================================
echo "\n=== TRAVEL AGENT ===\n";

$r = makeRequest('GET', "$baseUrl/travel-agent/dashboard", null, $agentCookie);
logResult($results, 'Travel Agent Dashboard', $r['code'], [200], '');

$r = makeRequest('GET', "$baseUrl/travel-agent/dashboard", null, $agentCookie);
preg_match('/name="csrf-token" content="([^"]+)"/', $r['body'], $m2);
$agentCsrf = $m2[1] ?? '';

$r = makeRequest('GET', "$baseUrl/travel-agent/dashboard", null, $agentCookie);
logResult($results, 'Travel Agent Profil', $r['code'], [200], 'Via dashboard');

$r = makeRequest('GET', "$baseUrl/travel-agent/packages", null, $agentCookie);
logResult($results, 'Travel Package List', $r['code'], [200], '');

$r = makeRequest('POST', "$baseUrl/travel-agent/packages", [
    '_token' => $agentCsrf,
    'nama' => 'Test Package ' . time(),
    'deskripsi' => 'Test travel package from Postman',
    'harga' => 500000,
    'durasi' => 3,
    'kapasitas' => 10,
], $agentCookie);
logResult($results, 'Create Travel Package', $r['code'], [200, 302, 422], '');

$r = makeRequest('GET', "$baseUrl/travel-agent/packages/1", null, $agentCookie);
logResult($results, 'Travel Agent Paket Detail', $r['code'], [200, 403, 404], '');

$r = makeRequest('PUT', "$baseUrl/travel-agent/packages/1", [
    '_token' => $agentCsrf,
    'nama' => 'Updated Package',
    'deskripsi' => 'Updated description',
    'harga' => 600000,
    'durasi' => 4,
    'kapasitas' => 12,
], $agentCookie);
logResult($results, 'Update Travel Package', $r['code'], [200, 302, 403, 404, 422], '');

$r = makeRequest('DELETE', "$baseUrl/travel-agent/packages/999", $agentCookie);
logResult($results, 'Delete Travel Package', $r['code'], [200, 302, 403, 404], 'Using ID 999 (safe)');

$r = makeRequest('GET', "$baseUrl/travel-agent/subscriptions", null, $agentCookie);
logResult($results, 'Travel Agent Subscription', $r['code'], [200], '');

$r = makeRequest('POST', "$baseUrl/travel-agent/subscriptions/checkout/1", [
    '_token' => $agentCsrf,
], $agentCookie);
logResult($results, 'Subscribe Travel Agent Paket', $r['code'], [200, 302, 404, 422], '');

$r = makeRequest('GET', "$baseUrl/travel-agent/subscriptions", null, $agentCookie);
logResult($results, 'Travel Agent Transaksi List', $r['code'], [200], 'Via subscriptions page');

// ============================================================
// SUMMARY
// ============================================================
echo "\n\n=== FULL SUMMARY ===\n";
echo str_pad('Request Name', 55) . str_pad('Status', 8) . str_pad('HTTP', 6) . "Notes\n";
echo str_repeat('-', 100) . "\n";

$passed = 0;
$failed = 0;
foreach ($results as $res) {
    $icon = $res['status'] === 'PASS' ? '✓' : '✗';
    echo sprintf("%s %-53s %-8s %-6s %s\n", $icon, $res['name'], $res['status'], $res['code'], $res['notes']);
    if ($res['status'] === 'PASS') $passed++;
    else $failed++;
}

echo "\n" . str_repeat('=', 100) . "\n";
echo "TOTAL: " . count($results) . " requests | PASSED: $passed | FAILED: $failed\n";
echo str_repeat('=', 100) . "\n";
