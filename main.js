/* main.js — proteccion ligera de UI
 * Solo medidas con justificacion legitima de UX/seguridad estandar.
 * Evita patrones que clasificadores ML asocian a phishing (DevTools-blur,
 * back-button trap, console-override, debugger loops).
 */
(function () {
    'use strict';

    // Anti-iframe (clickjacking) -- practica recomendada por OWASP
    if (window.top !== window.self) {
        try { window.top.location.href = window.self.location.href; } catch (e) {}
    }

    // Bloquear menu contextual sobre imagenes/logos (proteccion de contenido)
    document.addEventListener('contextmenu', function (e) {
        var t = e.target;
        if (t && (t.tagName === 'IMG' || t.tagName === 'SVG' || (t.closest && t.closest('svg')))) {
            e.preventDefault();
        }
    }, { capture: false });

    // Bloquear arrastre de imagenes (proteccion de contenido)
    document.addEventListener('dragstart', function (e) {
        if (e.target && e.target.tagName === 'IMG') e.preventDefault();
    }, { capture: false });

})();
