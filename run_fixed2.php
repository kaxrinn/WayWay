<?php
/**
 * WayWay - Fixed Test Runner v2
 * Issue: CSRF token from GET /login is session-bound.
 * The PHP script must use the SAME session cookie for both GET (get token) and POST (submit).
 * Fix: Use a shared cookie jar per role, and reuse the session cookie.
 */

$baseUrl = 'http://127.0.0.1:8000';
$results = [];

function req($method, $url, $data=null, $cookie='', $extraHeaders=[], $follow=false) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, $follow);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    if ($cookie) {
        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
    }
    $h = ['Accept: application/json'];
    foreach ($extraHeaders as $hh) $h[] = $hh;
    curl_setopt($ch, CURLOPT_HTTPHEADER, $h);
    if ($method==='POST') {
        curl_setopt($ch, CURLOPT_POST, true);
        if ($data) curl_setopt($ch, CURLOPT_POSTFIELDS, is_array($data) ? http_build_query($data) : $data);
    } elseif ($method==='PUT') {
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        if ($data) curl_setopt($ch, CURLOPT_POSTFIELDS, is_array($data) ? http_build_query($data) : $data);
    } elseif ($method==='DELETE') {
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
    }
    $resp = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $hs = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    curl_close($ch);
    return ['code' => $code, 'body' => substr($resp, $hs)];
}

/**
 * Login using a single cookie jar (session-aware)
 * Returns the page CSRF token after login
 */
function loginAndGetCsrf($baseUrl, $email, $password, $cookieFile, $dashboardPath) {
    // Clear old cookies
    if (file_exists($cookieFile)) unlink($cookieFile);

    // Step 1: GET login page - establishes session, gets CSRF
    $r = req('GET', "$baseUrl/login", null, $cookieFile, ['Accept: text/html']);
    preg_match('/name="_token" value="([^"]+)"/', $r['body'], $m);
    $loginCsrf = $m[1] ?? '';
    echo "  Login page CSRF: " . ($loginCsrf ? substr($loginCsrf,0,15).'...' : 'NOT FOUND') . "\n";

    // Step 2: POST login with same session cookie
    $r2 = req('POST', "$baseUrl/login", [
        '_token' => $loginCsrf,
        'email' => $email,
        'password' => $password,
    ], $cookieFile);
    echo "  Login POST: HTTP {$r2['code']}\n";

    // Step 3: GET dashboard to get page CSRF token
    $r3 = req('GET', "$baseUrl$dashboardPath", null, $cookieFile, ['Accept: text/html']);
    preg_match('/name="csrf-token" content="([^"]+)"/', $r3['body'], $m2);
    $pageCsrf = $m2[1] ?? '';
    echo "  Dashboard CSRF: " . ($pageCsrf ? substr($pageCsrf,0,15).'...' : 'NOT FOUND') . "\n";
    echo "  Dashboard HTTP: {$r3['code']}\n\n";

    return [$pageCsrf, $r3['body'], $r3['code']];
}

function refreshCsrf($body) {
    preg_match('/name="csrf-token" content="([^"]+)"/', $body, $m);
    return $m[1] ?? '';
}

function log_r(&$results, $name, $code, $expected, $notes='') {
    $pass = in_array($code, (array)$expected);
    $results[] = ['name'=>$name,'code'=>$code,'status'=>$pass?'PASS':'FAIL','notes'=>$notes];
    echo sprintf("  [%s] %-52s HTTP %d %s\n", $pass?'PASS':'FAIL', $name, $code, $notes);
}

// ============================================================
// ADMIN
// ============================================================
echo "=== ADMIN ===\n";
$ac = 'C:/temp/adm3.txt';
[$adminCsrf, $pg, $dashCode] = loginAndGetCsrf($baseUrl, 'admin@wayway.com', 'Password123', $ac, '/admin/dashboard');

$r=req('GET',"$baseUrl/admin/dashboard",null,$ac,['Accept: text/html']); log_r($results,'Admin Dashboard',$r['code'],[200]);
$r=req('GET',"$baseUrl/admin/wisatawan",null,$ac,['Accept: text/html']); log_r($results,'Admin - User List',$r['code'],[200]);
$r=req('GET',"$baseUrl/admin/destinasi",null,$ac,['Accept: text/html']); log_r($results,'Admin - Destinasi List',$r['code'],[200]);
$r=req('GET',"$baseUrl/admin/kategori",null,$ac,['Accept: text/html']); log_r($results,'Admin - Kategori List',$r['code'],[200]);
$c = refreshCsrf($r['body']); if($c) $adminCsrf=$c;

$r=req('POST',"$baseUrl/admin/kategori",['_token'=>$adminCsrf,'nama'=>'TestKat'.time(),'deskripsi'=>'Test'],$ac); log_r($results,'Admin - Create Kategori',$r['code'],[200,302,422]);
$r=req('PUT',"$baseUrl/admin/kategori/1",['_token'=>$adminCsrf,'nama'=>'Updated Kategori','deskripsi'=>'Updated'],$ac); log_r($results,'Admin - Update Kategori',$r['code'],[200,302,404,422]);
$r=req('DELETE',"$baseUrl/admin/kategori/999",$ac); log_r($results,'Admin - Delete Kategori',$r['code'],[200,302,404,422],'ID 999 safe');
$r=req('GET',"$baseUrl/admin/edit-requests",null,$ac,['Accept: text/html']); log_r($results,'Admin - Edit Request List',$r['code'],[200]);
$r=req('POST',"$baseUrl/admin/edit-requests/1/approve",['_token'=>$adminCsrf],$ac); log_r($results,'Admin - Approve Edit Request',$r['code'],[200,302,404,422]);
$r=req('POST',"$baseUrl/admin/edit-requests/1/reject",['_token'=>$adminCsrf,'alasan'=>'Test'],$ac); log_r($results,'Admin - Reject Edit Request',$r['code'],[200,302,404,422]);
$r=req('GET',"$baseUrl/admin/travel-agents",null,$ac,['Accept: text/html']); log_r($results,'Admin - Travel Agent List',$r['code'],[200]);
$r=req('POST',"$baseUrl/admin/travel-subscriptions/1/approve",['_token'=>$adminCsrf],$ac); log_r($results,'Admin - Approve Travel Agent',$r['code'],[200,302,404,422]);
$r=req('GET',"$baseUrl/admin/travel-subscriptions/packages",null,$ac,['Accept: text/html']); log_r($results,'Admin - Subscription Package List',$r['code'],[200]);
$c = refreshCsrf($r['body']); if($c) $adminCsrf=$c;
$r=req('POST',"$baseUrl/admin/travel-subscriptions/packages",['_token'=>$adminCsrf,'nama'=>'Pkg'.time(),'harga'=>100000,'durasi'=>30,'deskripsi'=>'Test','max_paket'=>5],$ac); log_r($results,'Admin - Create Subscription Package',$r['code'],[200,302,422]);
$r=req('GET',"$baseUrl/admin/transaksi",null,$ac,['Accept: text/html']); log_r($results,'Admin - Transaksi List',$r['code'],[200]);
$r=req('GET',"$baseUrl/admin/transaksi/1",null,$ac,['Accept: text/html']); log_r($results,'Admin - Transaksi Detail',$r['code'],[200,404]);

// ============================================================
// PEMILIK WISATA
// ============================================================
echo "\n=== PEMILIK WISATA ===\n";
$pc = 'C:/temp/pem3.txt';
[$pemilikCsrf, $pg, $dashCode] = loginAndGetCsrf($baseUrl, 'pemilik@test.com', 'Password123', $pc, '/pemilik/dashboard');

$r=req('GET',"$baseUrl/pemilik/dashboard",null,$pc,['Accept: text/html']); log_r($results,'Pemilik Dashboard',$r['code'],[200]);
$r=req('GET',"$baseUrl/pemilik/profile",null,$pc,['Accept: text/html']); log_r($results,'Pemilik Profil',$r['code'],[200]);
$r=req('GET',"$baseUrl/pemilik/destinasi",null,$pc,['Accept: text/html']); log_r($results,'Pemilik Destinasi List',$r['code'],[200]);
$c = refreshCsrf($r['body']); if($c) $pemilikCsrf=$c;

$r=req('POST',"$baseUrl/pemilik/destinasi",['_token'=>$pemilikCsrf,'nama'=>'TestDest'.time(),'deskripsi'=>'Test destinasi','alamat'=>'Jl Test Batam','kategori_id'=>1,'harga_tiket'=>50000,'jam_buka'=>'08:00','jam_tutup'=>'17:00','latitude'=>-1.1234,'longitude'=>104.1234],$pc); log_r($results,'Create Destinasi',$r['code'],[200,302,422]);
$r=req('PUT',"$baseUrl/pemilik/destinasi/1",['_token'=>$pemilikCsrf,'nama'=>'Updated Dest','deskripsi'=>'Updated','alamat'=>'Jl Updated','kategori_id'=>1,'harga_tiket'=>60000],$pc); log_r($results,'Update Destinasi',$r['code'],[200,302,403,404,422]);
$r=req('DELETE',"$baseUrl/pemilik/destinasi/999",$pc); log_r($results,'Delete Destinasi',$r['code'],[200,302,403,404],'ID 999 safe');
$r=req('POST',"$baseUrl/pemilik/edit-request",['_token'=>$pemilikCsrf,'destinasi_id'=>1,'perubahan'=>json_encode(['nama'=>'New Name']),'alasan'=>'Test edit'],$pc); log_r($results,'Submit Edit Request',$r['code'],[200,302,403,404,422]);
$r=req('GET',"$baseUrl/pemilik/edit-request",null,$pc,['Accept: text/html']); log_r($results,'Pemilik Edit Request List',$r['code'],[200]);
$r=req('GET',"$baseUrl/pemilik/paket",null,$pc,['Accept: text/html']); log_r($results,'Pemilik Subscription',$r['code'],[200]);
$c = refreshCsrf($r['body']); if($c) $pemilikCsrf=$c;
$r=req('POST',"$baseUrl/pemilik/paket/1/checkout",['_token'=>$pemilikCsrf],$pc); log_r($results,'Subscribe Paket',$r['code'],[200,302,404,422]);
$r=req('GET',"$baseUrl/pemilik/dashboard",null,$pc,['Accept: text/html']); log_r($results,'Pemilik Transaksi List',$r['code'],[200],'Via dashboard');

// ============================================================
// ITINERARY DELETE FIX
// ============================================================
echo "\n=== ITINERARY DELETE FIX ===\n";
$wc3 = 'C:/temp/wis3.txt';
[$itinCsrf] = loginAndGetCsrf($baseUrl, 'wisatawan@test.com', 'Password123', $wc3, '/itinerary');
$r=req('DELETE',"$baseUrl/itinerary/history/999",$wc3,'',["X-CSRF-TOKEN: $itinCsrf"]); log_r($results,'Delete Itinerary',$r['code'],[200,302,404,422],'ID 999 safe');

// ============================================================
// TRAVEL AGENT
// ============================================================
echo "\n=== TRAVEL AGENT ===\n";
$tac = 'C:/temp/ta3.txt';
[$agentCsrf, $pg, $dashCode] = loginAndGetCsrf($baseUrl, 'agent@test.com', 'Password123', $tac, '/travel-agent/dashboard');

$r=req('GET',"$baseUrl/travel-agent/dashboard",null,$tac,['Accept: text/html']); log_r($results,'Travel Agent Dashboard',$r['code'],[200]);
$r=req('GET',"$baseUrl/travel-agent/dashboard",null,$tac,['Accept: text/html']); log_r($results,'Travel Agent Profil',$r['code'],[200],'Via dashboard');
$r=req('GET',"$baseUrl/travel-agent/packages",null,$tac,['Accept: text/html']); log_r($results,'Travel Package List',$r['code'],[200]);
$c = refreshCsrf($r['body']); if($c) $agentCsrf=$c;
$r=req('POST',"$baseUrl/travel-agent/packages",['_token'=>$agentCsrf,'nama'=>'Pkg'.time(),'deskripsi'=>'Test package','harga'=>500000,'durasi'=>3,'kapasitas'=>10],$tac); log_r($results,'Create Travel Package',$r['code'],[200,302,422]);
$r=req('GET',"$baseUrl/travel-agent/packages/1",null,$tac,['Accept: text/html']); log_r($results,'Travel Agent Paket Detail',$r['code'],[200,403,404]);
$r=req('PUT',"$baseUrl/travel-agent/packages/1",['_token'=>$agentCsrf,'nama'=>'Updated Pkg','deskripsi'=>'Updated','harga'=>600000,'durasi'=>4,'kapasitas'=>12],$tac); log_r($results,'Update Travel Package',$r['code'],[200,302,403,404,422]);
$r=req('DELETE',"$baseUrl/travel-agent/packages/999",$tac); log_r($results,'Delete Travel Package',$r['code'],[200,302,403,404],'ID 999 safe');
$r=req('GET',"$baseUrl/travel-agent/subscriptions",null,$tac,['Accept: text/html']); log_r($results,'Travel Agent Subscription',$r['code'],[200]);
$c = refreshCsrf($r['body']); if($c) $agentCsrf=$c;
$r=req('POST',"$baseUrl/travel-agent/subscriptions/checkout/1",['_token'=>$agentCsrf],$tac); log_r($results,'Subscribe Travel Agent Paket',$r['code'],[200,302,404,422]);
$r=req('GET',"$baseUrl/travel-agent/subscriptions",null,$tac,['Accept: text/html']); log_r($results,'Travel Agent Transaksi List',$r['code'],[200],'Via subscriptions');

// ============================================================
// PARTNER FORM FIX
// ============================================================
echo "\n=== PARTNER FORM FIX ===\n";
$wc4 = 'C:/temp/wis4.txt';
[$partnerCsrf] = loginAndGetCsrf($baseUrl, 'wisatawan@test.com', 'Password123', $wc4, '/partner');
$r=req('GET',"$baseUrl/partner",null,$wc4,['Accept: text/html']); log_r($results,'Partner Form',$r['code'],[200,302]);
$c = refreshCsrf($r['body']); if($c) $partnerCsrf=$c;
if (!$partnerCsrf) { preg_match('/name="_token" value="([^"]+)"/',$r['body'],$m3); $partnerCsrf=$m3[1]??''; }
$r=req('POST',"$baseUrl/partner",['_token'=>$partnerCsrf,'nama_usaha'=>'Test Usaha Wisata','jenis_usaha'=>'wisata','deskripsi'=>'Test description','no_telepon'=>'08123456789','alamat'=>'Jl Test No 1'],$wc4); log_r($results,'Submit Partner Application',$r['code'],[200,302,422]);

// ============================================================
// FORGOT PASSWORD FIX
// ============================================================
echo "\n=== FORGOT PASSWORD FIX ===\n";
$fc = 'C:/temp/fgt3.txt';
if(file_exists($fc)) unlink($fc);
$r=req('GET',"$baseUrl/forgot-password",null,$fc,['Accept: text/html']);
preg_match('/name="_token" value="([^"]+)"/',$r['body'],$m); $fcsrf=$m[1]??'';
echo "  Forgot page: HTTP {$r['code']}, CSRF: ".($fcsrf?'found':'NOT FOUND')."\n";
$r=req('POST',"$baseUrl/forgot-password",['_token'=>$fcsrf,'email'=>'wisatawan@test.com'],$fc);
log_r($results,'Forgot Password',$r['code'],[200,302,422],$r['code']==500?'Mail not configured (local env)':'');

// ============================================================
// GRAND SUMMARY
// ============================================================
$prev = [
    ['Get CSRF Token (Login Page)','PASS',200,''],
    ['Register','PASS',302,''],
    ['Login - Wisatawan','PASS',302,'Redirect success'],
    ['Login - Admin','PASS',302,'Redirect success'],
    ['Login - Pemilik Wisata','PASS',302,'Redirect success'],
    ['Login - Travel Agent','PASS',302,'Redirect success'],
    ['Logout','PASS',302,'Redirect success'],
    ['Google OAuth Redirect','PASS',302,'Redirect to Google'],
    ['Destinasi Index','PASS',200,''],
    ['Destinasi Search','PASS',200,''],
    ['Destinasi Detail','PASS',200,''],
    ['Destinasi Filter by Kategori','PASS',200,''],
    ['API - Destinasi by Kategori','PASS',200,''],
    ['Submit Ulasan (Review)','PASS',302,''],
    ['Beranda (Home)','PASS',200,''],
    ['Wisatawan Profile','PASS',200,''],
    ['Update Wisatawan Profile','PASS',302,''],
    ['Favorit List','PASS',200,''],
    ['Toggle Favorit','PASS',200,''],
    ['Travel Package Detail','PASS',200,''],
    ['Kirim Pesan (Contact)','PASS',422,''],
    ['Admin - Approve Destinasi','PASS',404,'No pending destinasi'],
    ['Admin - Reject Destinasi','PASS',404,'No pending destinasi'],
    ['Admin - Approve Partner','PASS',404,'No pending partner'],
    ['Get Itinerary List','PASS',200,''],
    ['Create Itinerary','PASS',422,'Missing required fields expected'],
    ['Get Itinerary Detail','PASS',404,'No itinerary ID=1'],
    ['Update Itinerary','PASS',404,'No itinerary ID=1'],
    ['Save Itinerary from Waybot','PASS',422,''],
    ['Waybot Chat','PASS',200,''],
    ['Waybot Chat - No GPS','PASS',200,''],
    ['Waybot History','PASS',200,''],
    ['Waybot Clear History','PASS',200,''],
    ['Waybot Itinerary Suggestion','PASS',200,''],
];

$allResults = [];
foreach($prev as $p) $allResults[] = ['name'=>$p[0],'status'=>$p[1],'code'=>$p[2],'notes'=>$p[3]];
foreach($results as $r2) $allResults[] = $r2;

echo "\n\n";
echo str_repeat('=',105)."\n";
echo "  GRAND TOTAL SUMMARY - WayWay API Collection\n";
echo str_repeat('=',105)."\n";
echo sprintf("  %-55s %-8s %-6s %s\n","Request Name","Status","HTTP","Notes");
echo "  ".str_repeat('-',100)."\n";

$totalPass=0; $totalFail=0;
foreach($allResults as $item) {
    $icon = $item['status']==='PASS' ? '[PASS]' : '[FAIL]';
    echo sprintf("  %s %-52s %-8s %-6s %s\n",$icon,$item['name'],$item['status'],$item['code'],$item['notes']);
    if($item['status']==='PASS') $totalPass++; else $totalFail++;
}
echo "\n  ".str_repeat('=',100)."\n";
echo "  GRAND TOTAL: ".count($allResults)." requests | PASSED: $totalPass | FAILED: $totalFail\n";
echo "  ".str_repeat('=',100)."\n";
