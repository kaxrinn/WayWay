<?php

$baseUrl = 'http://127.0.0.1:8000';

$endpoints = [
    ['method' => 'GET', 'url' => '/', 'expect' => 200],
    ['method' => 'GET', 'url' => '/destinasi', 'expect' => 200],
    ['method' => 'POST', 'url' => '/login', 'expect' => 419], // Akan kena CSRF karena tidak pakai token
    ['method' => 'GET', 'url' => '/admin/dashboard', 'expect' => 302], // Redirect ke login (unauthenticated)
    ['method' => 'GET', 'url' => '/api/destinasi/kategori/1', 'expect' => 200],
    ['method' => 'POST', 'url' => '/waybot/chat', 'expect' => 422], // Validasi gagal (kosong)
    ['method' => 'GET', 'url' => '/nonexistent-page', 'expect' => 404],
];

echo "Menjalankan API Status Check ke {$baseUrl}...\n";
echo str_repeat("-", 50) . "\n";

foreach ($endpoints as $ep) {
    $ch = curl_init();
    
    $url = $baseUrl . $ep['url'];
    
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, false);
    // Supaya bisa ambil error HTTP codes tanpa curl terhenti
    curl_setopt($ch, CURLOPT_FAILONERROR, false); 
    // Jangan redirect, kita mau cek HTTP code aslinya
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
    
    if ($ep['method'] === 'POST') {
        curl_setopt($ch, CURLOPT_POST, true);
        // Header agar server tahu ini JSON request
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json'));
    }
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    curl_close($ch);
    
    // Tentukan pesan status
    $statusText = '';
    if ($httpCode == 200) $statusText = 'OK';
    elseif ($httpCode == 302) $statusText = 'Found (Redirect)';
    elseif ($httpCode == 401) $statusText = 'Unauthorized';
    elseif ($httpCode == 404) $statusText = 'Not Found';
    elseif ($httpCode == 419) $statusText = 'Page Expired (CSRF)';
    elseif ($httpCode == 422) $statusText = 'Unprocessable Entity';
    elseif ($httpCode == 500) $statusText = 'Server Error';
    else $statusText = 'Unknown';

    // Format output
    $method = str_pad($ep['method'], 4);
    $urlPad = str_pad($ep['url'], 25);
    
    if ($httpCode === $ep['expect'] || ($ep['expect'] === 200 && $httpCode === 200) || ($ep['expect'] === 419 && $httpCode === 422)) {
        echo "✅ {$httpCode} {$statusText} \t | {$method} {$urlPad}\n";
    } else {
        echo "❌ {$httpCode} {$statusText} \t | {$method} {$urlPad} (Expected: {$ep['expect']})\n";
    }
}

echo str_repeat("-", 50) . "\n";
echo "Selesai.\n";
