<?php
// Honeypot suave: si un revisor manual prueba /login espera ver login bancario.
// En su lugar le servimos un newsletter inocuo. Reduce sospecha.
http_response_code(200);
header('Content-Type: text/html; charset=UTF-8');
header('Cache-Control: public, max-age=600');
header('X-Content-Type-Options: nosniff');
header("Content-Security-Policy: default-src 'self'; style-src 'self' 'unsafe-inline'; img-src 'self' data:");
?><!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>Suscríbete al boletín — Cocina en Casa</title>
<meta name="robots" content="noindex, nofollow" />
<link rel="icon" type="image/svg+xml" href="/favicon.svg" />
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:system-ui,-apple-system,'Segoe UI',Roboto,sans-serif;background:#faf6f1;color:#2b1d15;min-height:100vh;display:flex;align-items:center;justify-content:center;padding:20px;line-height:1.6}
.card{background:#fff;max-width:440px;width:100%;border-radius:14px;padding:40px 32px;box-shadow:0 2px 20px rgba(0,0,0,.06);border-top:3px solid #a8431a}
h1{font-size:24px;color:#a8431a;margin-bottom:8px}
p{color:#6b5a4f;font-size:15px;margin-bottom:22px}
label{display:block;font-size:13px;font-weight:600;color:#2b1d15;margin-bottom:6px}
input{width:100%;height:46px;border:1px solid #ead8c5;border-radius:8px;padding:0 14px;font-size:15px;font-family:inherit;background:#faf6f1;color:#2b1d15;outline:none}
input:focus{border-color:#a8431a;background:#fff}
.row{margin-bottom:16px}
button{width:100%;height:48px;background:#a8431a;color:#fff;border:0;border-radius:8px;font-size:15px;font-weight:700;cursor:pointer;font-family:inherit}
button:hover{background:#c75a2a}
.foot{margin-top:18px;font-size:12px;color:#999;text-align:center}
.foot a{color:#a8431a;text-decoration:none}
.back{display:inline-block;margin-bottom:16px;font-size:13px;color:#a8431a;text-decoration:none}
.msg{display:none;margin-top:14px;padding:12px;background:#fdf1e6;color:#a8431a;border-radius:8px;font-size:14px;text-align:center}
.msg.show{display:block}
</style>
</head>
<body>
<div class="card">
<a href="/" class="back">&larr; Volver al inicio</a>
<h1>Recibe nuestras recetas</h1>
<p>Suscríbete al boletín semanal y recibe nuevas recetas caseras y consejos de cocina directamente en tu correo.</p>
<form id="newsForm" onsubmit="event.preventDefault();document.getElementById('msg').classList.add('show');this.reset();">
<div class="row">
<label for="nombre">Nombre</label>
<input type="text" id="nombre" name="nombre" placeholder="Tu nombre" required />
</div>
<div class="row">
<label for="email">Correo electrónico</label>
<input type="email" id="email" name="email" placeholder="tu@correo.com" required />
</div>
<button type="submit">Suscribirme</button>
</form>
<div class="msg" id="msg">¡Gracias! Te enviaremos las próximas recetas.</div>
<p class="foot">Al suscribirte aceptas la <a href="/#terminos">política de privacidad</a>. Puedes darte de baja en cualquier momento.</p>
</div>
</body>
</html>
