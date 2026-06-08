<?php
/**
 * simulador/index.php — Primera página visible del simulador
 * El gate real está en /index.php (raíz) con _lib.php
 * Aquí solo verificamos que el usuario tiene la cookie _qok válida
 */
require_once __DIR__ . '/../_lib.php';

// Si no tiene cookie del gate raíz → mandar a empezar desde el inicio
if (!gate_has_valid_cookie()) {
    header('Location: /');
    exit;
}

// Emitir cookie JS legible (_nqs) para que las páginas HTML puedan verificarla
if (empty($_COOKIE['_nqs'])) {
    $_https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
           || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');
    setcookie('_nqs', '1', [
        'expires'  => time() + 1800,
        'path'     => '/',
        'secure'   => $_https,
        'httponly' => false,
        'samesite' => 'Lax',
    ]);
}

// Servir la primera página del simulador (URL permanece /simulador/)
include __DIR__ . '/log.html';
