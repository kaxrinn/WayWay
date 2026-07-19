<?php
$cookie = 'C:/temp/test_admin.txt';
if(file_exists($cookie)) unlink($cookie);

// Step 1: GET login page
$ch = curl_init('http://127.0.0.1:8000/login');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: text/html']);
$resp = curl_exec($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$hs = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
$body = substr($resp, $hs);
curl_close($ch);
preg_match('/name="_token" value="([^"]+)"/', $body, $m);
$csrf = $m[1] ?? '';
echo "Step1 HTTP: $code\n";
echo "CSRF: $csrf\n";

// Step 2: POST login (no follow)
$ch = curl_init('http://127.0.0.1:8000/login');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(['_token'=>$csrf,'email'=>'admin@wayway.com','password'=>'Password123']));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json']);
$resp = curl_exec($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$hs = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
$headers = substr($resp, 0, $hs);
curl_close($ch);
echo "Step2 HTTP: $code\n";
preg_match('/Location: ([^\r\n]+)/', $headers, $loc);
echo "Redirect: " . ($loc[1] ?? 'none') . "\n";

// Show cookie file contents
echo "\nCookie file:\n";
echo file_get_contents($cookie);

// Step 3: GET admin dashboard
$ch = curl_init('http://127.0.0.1:8000/admin/dashboard');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: text/html']);
$resp = curl_exec($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$hs = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
$body = substr($resp, $hs);
curl_close($ch);
echo "\nStep3 HTTP: $code\n";
preg_match('/name="csrf-token" content="([^"]+)"/', $body, $m2);
echo "Page CSRF: " . ($m2[1] ?? 'NOT FOUND') . "\n";
echo "Body preview: " . substr($body, 0, 300) . "\n";
