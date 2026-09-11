<?php
/* .ics 日历代理：服务端拉取远程 iCalendar 文件（规避浏览器 CORS），带 SSRF 防护
 * 注意：本机 PHP 构建 exit()/die() 无效，错误分支必须用 if/else 结构（与 zixun.php 同款写法） */
header('Content-Type: text/plain; charset=utf-8');

$error = null;
$body = false;
$url = trim((string)($_GET['url'] ?? ''));
$parts = parse_url($url);

if (strlen($url) > 2000) {
    $error = [400, 'url too long'];
} elseif (!$parts || !in_array($parts['scheme'] ?? '', ['http', 'https'], true) || empty($parts['host'])) {
    $error = [400, 'invalid url'];
} else {
    // SSRF 防护：解析主机 IP，拒绝内网/保留地址
    $ip = gethostbyname($parts['host']);
    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false) {
        $error = [403, 'blocked host'];
    } elseif (function_exists('curl_init')) {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => 3,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_USERAGENT => 'luomor-ics/1.0',
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
        ]);
        $body = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($body === false || $code >= 400) $error = [502, 'fetch failed'];
    } else {
        $ctx = stream_context_create([
            'http' => ['timeout' => 10, 'max_redirects' => 3, 'user_agent' => 'luomor-ics/1.0', 'follow_location' => 1],
            'ssl' => ['verify_peer' => true, 'verify_peer_name' => true],
        ]);
        $body = @file_get_contents($url, false, $ctx, 0, 2 * 1024 * 1024);
        if ($body === false) $error = [502, 'fetch failed'];
    }
}

if ($error) {
    http_response_code($error[0]);
    echo $error[1];
} else {
    echo substr($body, 0, 2 * 1024 * 1024);
}
