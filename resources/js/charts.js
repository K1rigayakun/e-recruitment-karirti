/**
 * Chart.js dimuat dinamis hanya jika ada elemen [data-chart] di halaman.
 * Ini memisahkan bundle Chart.js dari app.js utama.
 */
import { Chart, registerables } from 'chart.js';

Chart.register(...registerables);

/**
 * Inisialisasi semua chart di halaman berdasarkan atribut data-chart.
 * Konfigurasi dibaca dari atribut data-chart-config (JSON).
 */
export function initCharts() {
    document.querySelectorAll('[data-chart]').forEach((el) => {
        try {
            const config = JSON.parse(el.getAttribute('data-chart-config') || '{}');

            // Warna dari CSS variable agar sinkron dengan token desain
            const colors = {
                signal : getComputedStyle(document.documentElement).getPropertyValue('--color-signal').trim()  || '#4F8CFF',
                sun    : getComputedStyle(document.documentElement).getPropertyValue('--color-sun').trim()     || '#FFC933',
                mint   : getComputedStyle(document.documentElement).getPropertyValue('--color-mint').trim()    || '#2EE6A6',
                coral  : getComputedStyle(document.documentElement).getPropertyValue('--color-coral').trim()   || '#FF6B81',
                violet : getComputedStyle(document.documentElement).getPropertyValue('--color-violet').trim()  || '#A58BFF',
                mist   : getComputedStyle(document.documentElement).getPropertyValue('--color-mist').trim()    || '#8FA3CC',
            };

            // Default animasi E19
            config.options = config.options || {};
            config.options.animation = config.options.animation ?? {
                duration: 900,
                easing: 'easeOutQuart',
            };
            config.options.plugins = config.options.plugins || {};
            config.options.plugins.legend = config.options.plugins.legend ?? { labels: { color: colors.mist } };
            config.options.scales = config.options.scales ?? {};

            if (config.options.scales.x) config.options.scales.x.ticks = { color: colors.mist };
            if (config.options.scales.y) config.options.scales.y.ticks = { color: colors.mist };

            new Chart(el, config);
        } catch (e) {
            console.warn('Chart init error:', e);
        }
    });
}
