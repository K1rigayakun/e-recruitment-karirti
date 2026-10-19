/**
 * E01 — Hero Pipeline Animation
 * SVG git graph menggambar dirinya sendiri, node muncul berurutan,
 * node akhir berdenyut selama hero terlihat.
 */
import { animate, createTimeline, stagger, svg, utils } from 'animejs';
import { isReduced } from './prefs.js';
import { dur } from './tokens.js';

let pulseAnim = null;

export function initHeroPipeline() {
    const svgEl = document.getElementById('hero-pipeline-svg');
    if (!svgEl) return;

    const tracks = [...svgEl.querySelectorAll('.pipeline-track')];
    const nodes  = [...svgEl.querySelectorAll('.pipeline-node')];
    const labels = [...svgEl.querySelectorAll('.pipeline-label')];
    const final  = svgEl.querySelector('#pipeline-final');
    const pulse  = svgEl.querySelector('#pipeline-pulse');

    if (isReduced()) {
        // Langsung tampil tanpa animasi
        utils.set(tracks, { opacity: 1 });
        utils.set(nodes, { opacity: 1 });
        utils.set(labels, { opacity: 1 });
        return;
    }

    // Sembunyikan semua dulu
    utils.set(tracks, { opacity: 0 });
    utils.set(nodes, { opacity: 0, scale: 0 });
    utils.set(labels, { opacity: 0 });

    // Buat drawable untuk setiap track
    const drawables = tracks.map((t) => svg.createDrawable(t));

    const tl = createTimeline({ defaults: { ease: 'inOutQuad' } });

    // 1. Gambar garis berurutan (stagger 180ms)
    tl.add(drawables.map((d, i) => ({
        targets: d,
        draw: ['0 0', '0 1'],
        duration: 1400,
        delay: i * 180,
        ease: 'inOutQuad',
    })), 0);

    // 2. Node muncul dengan spring, stagger 120ms
    tl.add(nodes, {
        opacity: [0, 1],
        scale: [0, 1],
        duration: 320,
        delay: stagger(120),
        ease: 'spring(1, 80, 10, 0)',
    }, 400);

    // 3. Label fade-in
    tl.add(labels, {
        opacity: [0, 1],
        duration: 300,
        delay: stagger(80),
    }, 900);

    // 4. Pulse node akhir saat hero masih terlihat
    tl.add(pulse, { opacity: [0, 1] }, 1800);
    tl.then(() => {
        if (!pulse || !final || isReduced()) return;

        // Scale pulse ring berulang
        pulseAnim = animate(pulse, {
            scale: [1, 2.2],
            opacity: [0.6, 0],
            duration: 2000,
            ease: 'outExpo',
            loop: true,
        });

        // Pause/resume saat hero tidak terlihat
        const io = new IntersectionObserver((entries) => {
            for (const e of entries) {
                e.isIntersecting ? pulseAnim?.play() : pulseAnim?.pause();
            }
        });
        io.observe(svgEl);
    });
}
