<?php
/**
 * Genera la imagen OG (1200x630) para previsualización en redes sociales.
 * Usa GD nativo de PHP. Sin dependencias externas.
 */

// Cache 1 dia
header('Content-Type: image/png');
header('Cache-Control: public, max-age=86400, immutable');
header('X-Content-Type-Options: nosniff');

$W = 1200;
$H = 630;

$im = imagecreatetruecolor($W, $H);
imageantialias($im, true);

// --- Fondo: gradiente diagonal morado oscuro -> azul -> rosa ---
$c1 = [38, 14, 64];     // morado oscuro arriba-izq
$c2 = [78, 24, 110];    // morado medio
$c3 = [196, 38, 110];   // magenta abajo-der
for ($y = 0; $y < $H; $y++) {
    $t = $y / $H;
    $r = (int)($c1[0] + ($c2[0] - $c1[0]) * $t);
    $g = (int)($c1[1] + ($c2[1] - $c1[1]) * $t);
    $b = (int)($c1[2] + ($c2[2] - $c1[2]) * $t);
    $line = imagecolorallocate($im, $r, $g, $b);
    imageline($im, 0, $y, $W, $y, $line);
}

// Halo radial rosa abajo-derecha
for ($i = 0; $i < 60; $i++) {
    $alpha = (int)(110 - $i * 1.7);
    if ($alpha < 0) break;
    $col = imagecolorallocatealpha($im, $c3[0], $c3[1], $c3[2], $alpha);
    $r = 320 - $i * 4;
    imagefilledellipse($im, $W - 100, $H - 80, $r, $r, $col);
}

// Halo radial dorado arriba-izq
$gold = [255, 200, 90];
for ($i = 0; $i < 50; $i++) {
    $alpha = (int)(115 - $i * 2);
    if ($alpha < 0) break;
    $col = imagecolorallocatealpha($im, $gold[0], $gold[1], $gold[2], $alpha);
    $r = 260 - $i * 4;
    imagefilledellipse($im, 120, 110, $r, $r, $col);
}

// --- Tarjeta central translucida ---
$card_x1 = 80;  $card_y1 = 110;
$card_x2 = $W - 80; $card_y2 = $H - 110;
$cardCol = imagecolorallocatealpha($im, 255, 255, 255, 110);
imagefilledrectangle($im, $card_x1, $card_y1, $card_x2, $card_y2, $cardCol);
// Borde sutil dorado
$borderCol = imagecolorallocatealpha($im, 255, 200, 90, 80);
imagerectangle($im, $card_x1, $card_y1, $card_x2, $card_y2, $borderCol);

// --- Badge superior ---
$badge_y = $card_y1 + 50;
$badgeBg = imagecolorallocatealpha($im, 255, 200, 90, 70);
imagefilledrectangle($im, $card_x1 + 60, $badge_y - 18, $card_x1 + 280, $badge_y + 18, $badgeBg);

// --- Texto ---
$white  = imagecolorallocate($im, 255, 255, 255);
$gold_s = imagecolorallocate($im, 255, 215, 130);
$soft   = imagecolorallocatealpha($im, 255, 255, 255, 35);

// GD usa fuentes built-in (1-5). Para texto grande hacemos escala manual con imagestring.
// Truco: dibujamos texto pequeno y lo escalamos a un canvas grande con imagecopyresampled.

function bigText($im, $text, $x, $y, $size, $color, $bold = false) {
    // Renderiza texto con la fuente built-in 5 (mas grande) y luego escala.
    $font = 5;
    $fw = imagefontwidth($font);
    $fh = imagefontheight($font);
    $tw = $fw * strlen($text);
    $th = $fh;

    $tmp = imagecreatetruecolor($tw, $th);
    $bg = imagecolorallocatealpha($tmp, 0, 0, 0, 127);
    imagealphablending($tmp, false);
    imagesavealpha($tmp, true);
    imagefill($tmp, 0, 0, $bg);
    imagealphablending($tmp, true);
    $cc = imagecolorallocate($tmp, 255, 255, 255);
    imagestring($tmp, $font, 0, 0, $text, $cc);
    if ($bold) {
        imagestring($tmp, $font, 1, 0, $text, $cc);
    }

    // Recolorear pixeles blancos al color deseado
    $rgb = imagecolorsforindex($im, $color);
    for ($yy = 0; $yy < $th; $yy++) {
        for ($xx = 0; $xx < $tw; $xx++) {
            $rgba = imagecolorat($tmp, $xx, $yy);
            $a = ($rgba >> 24) & 0x7F;
            if ($a < 127) {
                imagesetpixel($tmp, $xx, $yy, imagecolorallocatealpha($tmp, $rgb['red'], $rgb['green'], $rgb['blue'], $a));
            }
        }
    }

    // Escala al tamano deseado
    $newW = (int)($tw * $size);
    $newH = (int)($th * $size);
    imagecopyresampled($im, $tmp, $x, $y, 0, 0, $newW, $newH, $tw, $th);
    imagedestroy($tmp);

    return [$newW, $newH];
}

// Badge label
bigText($im, 'PERFIL FINANCIERO', $card_x1 + 80, $badge_y - 10, 1.6, $gold_s, true);

// Titulo
bigText($im, 'Consulta tu perfil', $card_x1 + 60, $card_y1 + 130, 6.0, $white, true);
bigText($im, 'financiero', $card_x1 + 60, $card_y1 + 215, 6.0, $white, true);

// Subtitulo
bigText($im, 'Calcula tu capacidad y conoce', $card_x1 + 60, $card_y1 + 340, 3.0, $white, false);
bigText($im, 'los productos disponibles.', $card_x1 + 60, $card_y1 + 385, 3.0, $white, false);

// Linea decorativa
imagefilledrectangle($im, $card_x1 + 60, $card_y1 + 305, $card_x1 + 160, $card_y1 + 309, $gold_s);

// CTA pill abajo
$pill_y = $card_y2 - 70;
$pillBg = imagecolorallocate($im, 255, 200, 90);
$pill_x1 = $card_x1 + 60;
$pill_x2 = $card_x1 + 320;
$pill_y1 = $pill_y;
$pill_y2 = $pill_y + 50;
imagefilledrectangle($im, $pill_x1, $pill_y1, $pill_x2, $pill_y2, $pillBg);
$dark = imagecolorallocate($im, 38, 14, 64);
bigText($im, 'CONSULTAR EN LINEA  >', $pill_x1 + 25, $pill_y1 + 16, 1.9, $dark, true);

imagepng($im, null, 6);
imagedestroy($im);
