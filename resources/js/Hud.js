// Home hero effects in the style of a game-intel HUD: text that decodes itself, and a crosshair
// that tracks the mouse with live coordinates. Skipped when the visitor prefers reduced motion.

const GLYPHS = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789#/_<>';

const prefersReducedMotion = () => window.matchMedia('(prefers-reduced-motion: reduce)').matches;

/** Scramble an element's text through random glyphs, then settle it left to right. */
export function scramble(el, duration = 900) {
    el.dataset.scrambleText ??= el.textContent;
    const text = el.dataset.scrambleText;
    const start = performance.now();

    cancelAnimationFrame(el.scrambleFrame);

    const frame = (now) => {
        const progress = Math.min((now - start) / duration, 1);
        const settled = Math.floor(progress * text.length);

        el.textContent = [...text]
            .map((char, i) => (i < settled || char === ' ' ? char : GLYPHS[Math.floor(Math.random() * GLYPHS.length)]))
            .join('');

        if (progress < 1) {
            el.scrambleFrame = requestAnimationFrame(frame);
        }
    };

    el.scrambleFrame = requestAnimationFrame(frame);
}

/**
 * Elements with data-scramble decode once on load (after data-scramble-delay ms) and again
 * whenever they're hovered.
 */
export function initScramble(root = document) {
    if (prefersReducedMotion()) return;

    root.querySelectorAll('[data-scramble]').forEach((el) => {
        setTimeout(() => scramble(el), Number(el.dataset.scrambleDelay ?? 0));
        el.addEventListener('mouseenter', () => scramble(el, 500));
    });
}

/**
 * A crosshair that eases after the mouse inside [data-crosshair-area]: a horizontal and a vertical
 * hairline, a square at their crossing, and the position printed into [data-hud-coords].
 */
export function initCrosshair() {
    const area = document.querySelector('[data-crosshair-area]');

    if (!area || prefersReducedMotion() || window.matchMedia('(pointer: coarse)').matches) return;

    const lineX = area.querySelector('[data-crosshair-x]');
    const lineY = area.querySelector('[data-crosshair-y]');
    const box = area.querySelector('[data-crosshair-box]');
    const coords = area.querySelector('[data-hud-coords]');
    const pad = (n) => String(Math.max(0, Math.round(n))).padStart(4, '0');

    let targetX = 0;
    let targetY = 0;
    let x = 0;
    let y = 0;
    let frame = null;

    const animate = () => {
        x += (targetX - x) * 0.18;
        y += (targetY - y) * 0.18;

        lineX.style.transform = `translateY(${y}px)`;
        lineY.style.transform = `translateX(${x}px)`;
        box.style.transform = `translate(${x}px, ${y}px)`;

        if (coords) {
            coords.textContent = `X ${pad(x)} · Y ${pad(y)}`;
        }

        // Keep going until the crosshair has caught up with the mouse.
        frame = Math.abs(targetX - x) + Math.abs(targetY - y) > 0.5 ? requestAnimationFrame(animate) : null;
    };

    area.addEventListener('mousemove', (e) => {
        const rect = area.getBoundingClientRect();
        targetX = e.clientX - rect.left;
        targetY = e.clientY - rect.top;

        if (!area.classList.contains('is-tracking')) {
            // Jump straight to the pointer when it enters, rather than sliding in from a corner.
            x = targetX;
            y = targetY;
            area.classList.add('is-tracking');
        }

        frame ??= requestAnimationFrame(animate);
    });

    area.addEventListener('mouseleave', () => area.classList.remove('is-tracking'));
}
