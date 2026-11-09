<?php
// fetch.php - HTML wrapper so scraper-based fetchers can read JSON endpoints
header('Content-Type: text/html; charset=utf-8');

$allowed = [
    'bbb.mesh.net.nz',
    'tinypeople.mesh.net.nz',
];

$url  = $_GET['url'] ?? '';
$host = parse_url($url, PHP_URL_HOST);
$scheme = parse_url($url, PHP_URL_SCHEME);

if (!$url || $scheme !== 'https' || !in_array($host, $allowed, true)) {
    http_response_code(400);
    echo '<pre>invalid or disallowed url</pre>';
    exit;
}

$ch = curl_init($url);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT        => 20,
    CURLOPT_FOLLOWLOCATION => false,
    CURLOPT_SSL_VERIFYPEER => true,
]);
$body = curl_exec($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$err  = curl_error($ch);
curl_close($ch); //deprecated

http_response_code($code ?: 502);
echo '<pre>';
echo htmlspecialchars($body !== false ? $body : 'curl error: ' . $err, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
echo '</pre>';