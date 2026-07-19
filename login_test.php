<?php
$cookieFile = 'C:/temp/cookies.txt';

// Step 1: Get CSRF token
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://127.0.0.1:8000/login');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
$response = curl_exec($ch);
preg_match('/name="_token" value="([^"]+)"/', $response, $m);
$token = $m[1] ?? '';
echo 'CSRF Token: ' . $token . PHP_EOL;
curl_close($ch);

// Step 2: Login as wisatawan
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://127.0.0.1:8000/login');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
    '_token' => $token,
    'email' => 'wisatawan@test.com',
    'password' => 'Password123',
]));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json']);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
echo 'Login Wisatawan Status: ' . $httpCode . PHP_EOL;
preg_match('/Location: ([^\r\n]+)/', $response, $loc);
echo 'Redirect: ' . ($loc[1] ?? 'none') . PHP_EOL;
curl_close($ch);

// Step 3: Test authenticated endpoint
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://127.0.0.1:8000/wisatawan/beranda');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json']);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
echo 'Wisatawan Beranda Status: ' . $httpCode . PHP_EOL;
preg_match('/Location: ([^\r\n]+)/', $response, $loc2);
echo 'Redirect: ' . ($loc2[1] ?? 'none') . PHP_EOL;
curl_close($ch);
