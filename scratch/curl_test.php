<?php

$baseUrl = 'http://127.0.0.1:8000';
$cookieFile = __DIR__ . '/cookie.txt';
if (file_exists($cookieFile)) unlink($cookieFile);

// 1. Get Login Page
$ch = curl_init("{$baseUrl}/login");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
$html = curl_exec($ch);

preg_match('/name="_token" value="(.*?)"/', $html, $matches);
$token = $matches[1] ?? '';

// 2. Post Login
curl_setopt($ch, CURLOPT_URL, "{$baseUrl}/login");
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
    '_token' => $token,
    'email' => 'superadmin@test.com',
    'password' => 'password',
]));
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_exec($ch);

// 3. Test Routes
$routes = [
    '/admin/classrooms/create',
    '/admin/teachers',
    '/admin/courses',
    '/admin/attendance',
    '/admin/exams',
    '/admin/reporting/reports',
];

echo "=== REAL HTTP CURL TEST ===\n";
foreach ($routes as $route) {
    curl_setopt($ch, CURLOPT_URL, "{$baseUrl}{$route}");
    curl_setopt($ch, CURLOPT_HTTPGET, true);
    $response = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $url = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
    echo "ROUTE: {$route} => Status: {$code} (URL: {$url})\n";
    if ($code === 500) {
        if (preg_match('/<title>(.*?)<\/title>/s', $response, $m)) {
            echo "  Title: " . trim($m[1]) . "\n";
        }
    }
}

curl_close($ch);
