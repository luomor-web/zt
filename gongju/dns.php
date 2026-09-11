<?php
/* DNS 解析查询端点：PHP 原生 dns_get_record（无外部依赖）
 * 注意：本机 PHP 构建 exit()/die() 无效，错误分支必须用 if/else 结构 */
header('Content-Type: application/json; charset=utf-8');

$name = strtolower(trim((string)($_GET['name'] ?? '')));
$type = strtoupper(trim((string)($_GET['type'] ?? 'A')));
$map = [
    'A' => DNS_A, 'AAAA' => DNS_AAAA, 'CNAME' => DNS_CNAME, 'MX' => DNS_MX,
    'NS' => DNS_NS, 'TXT' => DNS_TXT, 'SOA' => DNS_SOA, 'CAA' => DNS_CAA,
];
$answers = [];
$error = null;

if (!isset($map[$type])) {
    $type = 'A';
}
if (!preg_match('/^([a-z0-9]([a-z0-9\-]*[a-z0-9])?\.)+[a-z]{2,}$/', $name)) {
    $error = 'invalid domain';
} else {
    $recs = @dns_get_record($name, $map[$type]);
    if ($recs === false) {
        $error = 'query failed';
    } else {
        foreach ($recs as $r) {
            $t = $r['type'] ?? $type;
            $val = '';
            if (isset($r['ip'])) $val = $r['ip'];
            elseif (isset($r['ipv6'])) $val = $r['ipv6'];
            elseif ($t === 'MX') $val = ($r['pri'] ?? 0) . ' ' . ($r['target'] ?? '');
            elseif (isset($r['target'])) $val = $r['target'];
            elseif (isset($r['txt'])) $val = $r['txt'];
            elseif ($t === 'SOA') $val = ($r['mname'] ?? '') . ' ' . ($r['rname'] ?? '');
            $answers[] = ['type' => $t, 'value' => $val, 'ttl' => $r['ttl'] ?? 0];
        }
    }
}

echo json_encode(['name' => $name, 'type' => $type, 'error' => $error, 'answers' => $answers], JSON_UNESCAPED_UNICODE);
