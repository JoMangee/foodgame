<?php
// fetch.php - HTML wrapper so scraper-based fetchers can read JSON endpoints
header('Content-Type: text/html; charset=utf-8');

$allowed = [
    'bbb.mesh.net.nz',
    'tinypeople.mesh.net.nz',
];

$url    = $_GET['url'] ?? '';
$host   = parse_url($url, PHP_URL_HOST);
$scheme = parse_url($url, PHP_URL_SCHEME);

if (!$url || $scheme !== 'https' || !in_array($host, $allowed, true)) {
    http_response_code(400);
    echo '<!doctype html><html><head><title>fetch error</title></head><body><h1>fetch error</h1><p>invalid or disallowed url</p></body></html>';
    exit;
}

$ch = curl_init($url);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT        => 20,
    CURLOPT_FOLLOWLOCATION => false,
    CURLOPT_SSL_VERIFYPEER => true,
    CURLOPT_USERAGENT      => 'Mozilla/5.0 (compatible; fetch-wrapper)',
]);
$body = curl_exec($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$err  = curl_error($ch);

$payload = htmlspecialchars($body !== false ? $body : 'curl error: ' . $err, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

http_response_code($code ?: 502);
header('Content-Type: text/html; charset=utf-8');
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>fetch result</title>
</head>
<body>
<h1>fetch result</h1>
<p>Upstream status: <?php echo (int)$code; ?></p>
<p>Source: <?php echo htmlspecialchars($url, ENT_QUOTES, 'UTF-8'); ?></p>
<p><?php echo $payload; ?></p>
<pre><?php echo $payload; ?></pre>
</body>
</html>