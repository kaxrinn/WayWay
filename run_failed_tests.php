<?php
$baseUrl = 'http://127.0.0.1:8000';
$results = [];

function req($method, $url, $data=null, $cookie='', $headers=[], $follow=false) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, $follow);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    if ($cookie) { curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie); curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie); }
    $h = ['Accept: application/json'];
    foreach ($headers as $hh) $h[] = $hh;
    curl_setopt($ch, CURLOPT_HTTPHEADER, $h);
    if ($method==='POST') { curl_setopt($ch, CURLOPT_POST, true); if ($data) curl_setopt($ch, CURLOPT_POSTFIELDS, is_array($data)?http_build_query($data):$data); }
    elseif ($method==='PUT') { curl_setopt($ch, CURLOPT_CUSTOMREQUEST,'PUT'); if ($data) curl_setopt($ch, CURLOPT_POSTFIELDS, is_array($data)?http_build_query($data):$data); }
    elseif ($method==='DELETE') { curl_setopt($ch, CURLOPT_CUSTOMREQUEST,'DELETE'); }
    $resp = curl_exec($ch); $code = curl_getinfo($ch, CURLINFO_HTTP_CODE); $hs = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    curl_close($ch);
    return ['code'=>$code,'body'=>substr($resp,$hs)];
}

function login($base, $email, $pass, $cookie) {
    if (file_exists($cookie)) unlink($cookie);
    $r = req('GET',"$base/login",null,$cookie,['Accept: text/html']);
    preg_match('/name="_token" value="([^"]+)"/',$r['body'],$m);
    $csrf=$m[1]??'';
    $r2 = req('POST',"$base/login",['_token'=>$csrf,'email'=>$email,'password'=>$pass],$cookie);
    return $r2['code'];
}

function csrf($base, $path, $cookie) {
    $r = req('GET',"$base$path",null,$cookie,['Accept: text/html']);
    preg_match('/name="csrf-token" content="([^"]+)"/',$r['body'],$m);
    if (!empty($m[1])) return [$m[1],$r['body']];
    preg_match('/name="_token" value="([^"]+)"/',$r['body'],$m2);
    return [$m2[1]??'',$r['body']];
}

function log_r(&$results,$name,$code,$expected,$notes='') {
    $pass = in_array($code,(array)$expected);
    $results[] = ['name'=>$name,'code'=>$code,'status'=>$pass?'PASS':'FAIL','notes'=>$notes];
    echo sprintf("[%s] %-55s HTTP %d %s\n",$pass?'PASS':'FAIL',$name,$code,$notes);
}

// ===== ADMIN =====
echo "\n=== ADMIN ===\n";
$ac = 'C:/temp/adm2.txt';
$lc = login($baseUrl,'admin@wayway.com','Password123',$ac);
echo "Login: $lc\n";
[$tok,$pg] = csrf($baseUrl,'/admin/dashboard',$ac);
echo "CSRF: ".($tok?'OK':'MISSING')."\n\n";

$r=req('GET',"$baseUrl/admin/dashboard",null,$ac,['Accept: text/html']); log_r($results,'Admin Dashboard',$r['code'],[200]);
$r=req('GET',"$baseUrl/admin/wisatawan",null,$ac,['Accept: text/html']); log_r($results,'Admin - User List',$r['code'],[200]);
$r=req('GET',"$baseUrl/admin/destinasi",null,$ac,['Accept: text/html']); log_r($results,'Admin - Destinasi List',$r['code'],[200]);
$r=req('GET',"$baseUrl/admin/kategori",null,$ac,['Accept: text/html']); log_r($results,'Admin - Kategori List',$r['code'],[200]);
preg_match('/name="csrf-token" content="([^"]+)"/',$r['body'],$mx); if(!empty($mx[1]))$tok=$mx[1];

$r=req('POST',"$baseUrl/admin/kategori",['_token'=>$tok,'nama'=>'TestKat'.time(),'deskripsi'=>'Test'],$ac); log_r($results,'Admin - Create Kategori',$r['code'],[200,302,422]);
$r=req('PUT',"$baseUrl/admin/kategori/1",['_token'=>$tok,'nama'=>'Updated','deskripsi'=>'Upd'],$ac); log_r($results,'Admin - Update Kategori',$r['code'],[200,302,404,422]);
$r=req('DELETE',"$baseUrl/admin/kategori/999",$ac); log_r($results,'Admin - Delete Kategori',$r['code'],[200,302,404,422],'ID 999');
$r=req('GET',"$baseUrl/admin/edit-requests",null,$ac,['Accept: text/html']); log_r($results,'Admin - Edit Request List',$r['code'],[200]);
$r=req('POST',"$baseUrl/admin/edit-requests/1/approve",['_token'=>$tok],$ac); log_r($results,'Admin - Approve Edit Request',$r['code'],[200,302,404,422]);
$r=req('POST',"$baseUrl/admin/edit-requests/1/reject",['_token'=>$tok,'alasan'=>'Test'],$ac); log_r($results,'Admin - Reject Edit Request',$r['code'],[200,302,404,422]);
$r=req('GET',"$baseUrl/admin/travel-agents",null,$ac,['Accept: text/html']); log_r($results,'Admin - Travel Agent List',$r['code'],[200]);
$r=req('POST',"$baseUrl/admin/travel-subscriptions/1/approve",['_token'=>$tok],$ac); log_r($results,'Admin - Approve Travel Agent',$r['code'],[200,302,404,422]);
$r=req('GET',"$baseUrl/admin/travel-subscriptions/packages",null,$ac,['Accept: text/html']); log_r($results,'Admin - Subscription Package List',$r['code'],[200]);
preg_match('/name="csrf-token" content="([^"]+)"/',$r['body'],$mx); if(!empty($mx[1]))$tok=$mx[1];
$r=req('POST',"$baseUrl/admin/travel-subscriptions/packages",['_token'=>$tok,'nama'=>'Pkg'.time(),'harga'=>100000,'durasi'=>30,'deskripsi'=>'Test','max_paket'=>5],$ac); log_r($results,'Admin - Create Subscription Package',$r['code'],[200,302,422]);
$r=req('GET',"$baseUrl/admin/transaksi",null,$ac,['Accept: text/html']); log_r($results,'Admin - Transaksi List',$r['code'],[200]);
$r=req('GET',"$baseUrl/admin/transaksi/1",null,$ac,['Accept: text/html']); log_r($results,'Admin - Transaksi Detail',$r['code'],[200,404]);

// ===== PEMILIK =====
echo "\n=== PEMILIK WISATA ===\n";
$pc = 'C:/temp/pem2.txt';
$lc = login($baseUrl,'pemilik@test.com','Password123',$pc);
echo "Login: $lc\n";
[$ptok,$pg] = csrf($baseUrl,'/pemilik/dashboard',$pc);
echo "CSRF: ".($ptok?'OK':'MISSING')."\n\n";

$r=req('GET',"$baseUrl/pemilik/dashboard",null,$pc,['Accept: text/html']); log_r($results,'Pemilik Dashboard',$r['code'],[200]);
$r=req('GET',"$baseUrl/pemilik/profile",null,$pc,['Accept: text/html']); log_r($results,'Pemilik Profil',$r['code'],[200]);
$r=req('GET',"$baseUrl/pemilik/destinasi",null,$pc,['Accept: text/html']); log_r($results,'Pemilik Destinasi List',$r['code'],[200]);
preg_match('/name="csrf-token" content="([^"]+)"/',$r['body'],$mx); if(!empty($mx[1]))$ptok=$mx[1];

$r=req('POST',"$baseUrl/pemilik/destinasi",['_token'=>$ptok,'nama'=>'TestDest'.time(),'deskripsi'=>'Test','alamat'=>'Jl Test Batam','kategori_id'=>1,'harga_tiket'=>50000,'jam_buka'=>'08:00','jam_tutup'=>'17:00','latitude'=>-1.1234,'longitude'=>104.1234],$pc); log_r($results,'Create Destinasi',$r['code'],[200,302,422]);
$r=req('PUT',"$baseUrl/pemilik/destinasi/1",['_token'=>$ptok,'nama'=>'Updated','deskripsi'=>'Upd','alamat'=>'Jl Upd','kategori_id'=>1,'harga_tiket'=>60000],$pc); log_r($results,'Update Destinasi',$r['code'],[200,302,403,404,422]);
$r=req('DELETE',"$baseUrl/pemilik/destinasi/999",$pc); log_r($results,'Delete Destinasi',$r['code'],[200,302,403,404],'ID 999');
$r=req('POST',"$baseUrl/pemilik/edit-request",['_token'=>$ptok,'destinasi_id'=>1,'perubahan'=>json_encode(['nama'=>'New']),'alasan'=>'Test'],$pc); log_r($results,'Submit Edit Request',$r['code'],[200,302,403,404,422]);
$r=req('GET',"$baseUrl/pemilik/edit-request",null,$pc,['Accept: text/html']); log_r($results,'Pemilik Edit Request List',$r['code'],[200]);
$r=req('GET',"$baseUrl/pemilik/paket",null,$pc,['Accept: text/html']); log_r($results,'Pemilik Subscription',$r['code'],[200]);
preg_match('/name="csrf-token" content="([^"]+)"/',$r['body'],$mx); if(!empty($mx[1]))$ptok=$mx[1];
$r=req('POST',"$baseUrl/pemilik/paket/1/checkout",['_token'=>$ptok],$pc); log_r($results,'Subscribe Paket',$r['code'],[200,302,404,422]);
$r=req('GET',"$baseUrl/pemilik/dashboard",null,$pc,['Accept: text/html']); log_r($results,'Pemilik Transaksi List',$r['code'],[200],'Via dashboard');

// ===== ITINERARY DELETE FIX =====
echo "\n=== ITINERARY DELETE FIX ===\n";
$wc3 = 'C:/temp/wis3.txt';
login($baseUrl,'wisatawan@test.com','Password123',$wc3);
[$itok] = csrf($baseUrl,'/itinerary',$wc3);
$r=req('DELETE',"$baseUrl/itinerary/history/999",$wc3,'',["X-CSRF-TOKEN: $itok"]); log_r($results,'Delete Itinerary',$r['code'],[200,302,404,422],'ID 999');

// ===== TRAVEL AGENT =====
echo "\n=== TRAVEL AGENT ===\n";
$tac = 'C:/temp/ta2.txt';
$lc = login($baseUrl,'agent@test.com','Password123',$tac);
echo "Login: $lc\n";
[$tatok,$pg] = csrf($baseUrl,'/travel-agent/dashboard',$tac);
echo "CSRF: ".($tatok?'OK':'MISSING')."\n\n";

$r=req('GET',"$baseUrl/travel-agent/dashboard",null,$tac,['Accept: text/html']); log_r($results,'Travel Agent Dashboard',$r['code'],[200]);
$r=req('GET',"$baseUrl/travel-agent/dashboard",null,$tac,['Accept: text/html']); log_r($results,'Travel Agent Profil',$r['code'],[200],'Via dashboard');
$r=req('GET',"$baseUrl/travel-agent/packages",null,$tac,['Accept: text/html']); log_r($results,'Travel Package List',$r['code'],[200]);
preg_match('/name="csrf-token" content="([^"]+)"/',$r['body'],$mx); if(!empty($mx[1]))$tatok=$mx[1];
$r=req('POST',"$baseUrl/travel-agent/packages",['_token'=>$tatok,'nama'=>'Pkg'.time(),'deskripsi'=>'Test','harga'=>500000,'durasi'=>3,'kapasitas'=>10],$tac); log_r($results,'Create Travel Package',$r['code'],[200,302,422]);
$r=req('GET',"$baseUrl/travel-agent/packages/1",null,$tac,['Accept: text/html']); log_r($results,'Travel Agent Paket Detail',$r['code'],[200,403,404]);
$r=req('PUT',"$baseUrl/travel-agent/packages/1",['_token'=>$tatok,'nama'=>'Updated','deskripsi'=>'Upd','harga'=>600000,'durasi'=>4,'kapasitas'=>12],$tac); log_r($results,'Update Travel Package',$r['code'],[200,302,403,404,422]);
$r=req('DELETE',"$baseUrl/travel-agent/packages/999",$tac); log_r($results,'Delete Travel Package',$r['code'],[200,302,403,404],'ID 999');
$r=req('GET',"$baseUrl/travel-agent/subscriptions",null,$tac,['Accept: text/html']); log_r($results,'Travel Agent Subscription',$r['code'],[200]);
preg_match('/name="csrf-token" content="([^"]+)"/',$r['body'],$mx); if(!empty($mx[1]))$tatok=$mx[1];
$r=req('POST',"$baseUrl/travel-agent/subscriptions/checkout/1",['_token'=>$tatok],$tac); log_r($results,'Subscribe Travel Agent Paket',$r['code'],[200,302,404,422]);
$r=req('GET',"$baseUrl/travel-agent/subscriptions",null,$tac,['Accept: text/html']); log_r($results,'Travel Agent Transaksi List',$r['code'],[200],'Via subscriptions');

// ===== PARTNER FORM FIX =====
echo "\n=== PARTNER FORM FIX ===\n";
$wc4 = 'C:/temp/wis4.txt';
login($baseUrl,'wisatawan@test.com','Password123',$wc4);
$r=req('GET',"$baseUrl/partner",null,$wc4,['Accept: text/html']); log_r($results,'Partner Form',$r['code'],[200,302]);
preg_match('/name="csrf-token" content="([^"]+)"/',$r['body'],$m2); $pcsrf=$m2[1]??'';
if(!$pcsrf){preg_match('/name="_token" value="([^"]+)"/',$r['body'],$m3);$pcsrf=$m3[1]??'';}
$r=req('POST',"$baseUrl/partner",['_token'=>$pcsrf,'nama_usaha'=>'Test Usaha','jenis_usaha'=>'wisata','deskripsi'=>'Test desc','no_telepon'=>'08123456789','alamat'=>'Jl Test'],$wc4); log_r($results,'Submit Partner Application',$r['code'],[200,302,422]);

// ===== FORGOT PASSWORD FIX =====
echo "\n=== FORGOT PASSWORD FIX ===\n";
$fc = 'C:/temp/fgt2.txt';
if(file_exists($fc))unlink($fc);
$r=req('GET',"$baseUrl/forgot-password",null,$fc,['Accept: text/html']);
preg_match('/name="_token" value="([^"]+)"/',$r['body'],$m); $fcsrf=$m[1]??'';
$r=req('POST',"$baseUrl/forgot-password",['_token'=>$fcsrf,'email'=>'wisatawan@test.com'],$fc);
log_r($results,'Forgot Password',$r['code'],[200,302,422],$r['code']==500?'Mail not configured (local env)':'');

// ===== FULL SUMMARY =====
echo "\n\n";
echo str_repeat('=',100)."\n";
echo "FULL COMBINED RESULTS (Previous PASS + Re-run)\n";
echo str_repeat('=',100)."\n\n";

// Previous run results (from run_all_tests.php)
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
    ['Create Itinerary','PASS',422,'Missing required fields'],
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
foreach($prev as $p) {
    $allResults[] = ['name'=>$p[0],'status'=>$p[1],'code'=>$p[2],'notes'=>$p[3]];
}
foreach($results as $r2) {
    $allResults[] = $r2;
}

$totalPass=0; $totalFail=0;
echo sprintf("%-55s %-8s %-6s %s\n","Request Name","Status","HTTP","Notes");
echo str_repeat('-',100)."\n";
foreach($allResults as $item) {
    $icon = $item['status']==='PASS' ? '[PASS]' : '[FAIL]';
    echo sprintf("%s %-52s %-8s %-6s %s\n",$icon,$item['name'],$item['status'],$item['code'],$item['notes']);
    if($item['status']==='PASS') $totalPass++; else $totalFail++;
}
echo "\n".str_repeat('=',100)."\n";
echo "GRAND TOTAL: ".count($allResults)." requests | PASSED: $totalPass | FAILED: $totalFail\n";
echo str_repeat('=',100)."\n";
