<?php
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://127.0.0.1:8000/login');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_COOKIEJAR, 'C:/temp/cookies.txt');
curl_setopt($ch, CURLOPT_COOKIEFILE, 'C:/temp/cookies.txt');
$response = curl_exec($ch);
preg_match('/name="_token" value="([^"]+)"/', $response, $m);
$token = $m[1] ?? '';
echo 'CSRF: ' . $token . PHP_EOL;
preg_match_all('/Set-Cookie: ([^\r\n]+)/', $response, $cookies);
foreach($cookies[1] as $c) echo 'Cookie: ' . $c . PHP_EOL;
curl_close($ch);
