import Alpine from 'alpinejs';
import anchor from '@alpinejs/anchor';
import collapse from '@alpinejs/collapse';
import focus from '@alpinejs/focus';
import intersect from '@alpinejs/intersect';
import morph from '@alpinejs/morph';
import persist from '@alpinejs/persist';

// Self-hosted Inter font (replaces Google Fonts CDN)
import '@fontsource/inter/300.css';
import '@fontsource/inter/400.css';
import '@fontsource/inter/500.css';
import '@fontsource/inter/600.css';
import '@fontsource/inter/700.css';
import '@fontsource/inter/800.css';

// Import Font Awesome CSS
import '@fortawesome/fontawesome-free/css/all.min.css';

// Import Chart.js (for dashboard charts)
import { Chart } from 'chart.js';
import 'chartjs-adapter-moment';

// Import flatpickr (for date pickers)
import flatpickr from 'flatpickr';

// SweetAlert2 + Marked (replace jsdelivr CDN)
import Swal from 'sweetalert2';
import { marked } from 'marked';
window.Swal = Swal;
window.marked = marked;

// Register Alpine plugins
Alpine.plugin(anchor);
Alpine.plugin(collapse);
Alpine.plugin(focus);
Alpine.plugin(intersect);
Alpine.plugin(morph);
Alpine.plugin(persist);

// Alpine start
window.Alpine = Alpine;
Alpine.start();

// Define Chart.js default settings
Chart.defaults.font.family = '"Inter", sans-serif';
Chart.defaults.font.weight = 500;
Chart.defaults.plugins.tooltip.borderWidth = 1;
Chart.defaults.plugins.tooltip.displayColors = false;
Chart.defaults.plugins.tooltip.mode = 'nearest';
Chart.defaults.plugins.tooltip.intersect = false;
Chart.defaults.plugins.tooltip.position = 'nearest';
Chart.defaults.plugins.tooltip.caretSize = 0;
Chart.defaults.plugins.tooltip.caretPadding = 20;
Chart.defaults.plugins.tooltip.cornerRadius = 8;
Chart.defaults.plugins.tooltip.padding = 8;

// Function that generates a gradient for line charts
export const chartAreaGradient = (ctx, chartArea, colorStops) => {
    if (!ctx || !chartArea || !colorStops || colorStops.length === 0) {
        return 'transparent';
    }
    const gradient = ctx.createLinearGradient(0, chartArea.bottom, 0, chartArea.top);
    colorStops.forEach(({ stop, color }) => {
        gradient.addColorStop(stop, color);
    });
    return gradient;
};

// Register Chart.js plugin to add a bg option for chart area
Chart.register({
    id: 'chartAreaPlugin',
    beforeDraw: (chart) => {
        if (chart.config.options.chartArea && chart.config.options.chartArea.backgroundColor) {
            const ctx = chart.canvas.getContext('2d');
            const { chartArea } = chart;
            ctx.save();
            ctx.fillStyle = chart.config.options.chartArea.backgroundColor;
            ctx.fillRect(chartArea.left, chartArea.top, chartArea.right - chartArea.left, chartArea.bottom - chartArea.top);
            ctx.restore();
        }
    },
});

// Initialize on DOM Ready
document.addEventListener('DOMContentLoaded', () => {
    // Flatpickr initialization
    const datepickers = document.querySelectorAll('.datepicker');
    if (datepickers.length > 0) {
        datepickers.forEach((picker) => {
            flatpickr(picker, {
                mode: 'single',
                static: true,
                monthSelectorType: 'static',
                dateFormat: 'Y-m-d',
                prevArrow: '<svg class="fill-current" width="7" height="11" viewBox="0 0 7 11"><path d="M5.4 10.8l1.4-1.4-4-4 4-4L5.4 0 0 5.4z" /></svg>',
                nextArrow: '<svg class="fill-current" width="7" height="11" viewBox="0 0 7 11"><path d="M1.4 10.8L0 9.4l4-4-4-4L1.4 0l5.4 5.4z" /></svg>',
            });
        });
    }

    // Initialize dashboard charts if they exist
    const initDashboardCharts = () => {
        try {
            import('./components/dashboard-chart.js').then(module => {
                if (module.default) module.default();
            });
        } catch (e) {
            // Dashboard chart not required
        }
    };

    initDashboardCharts();
});

// Simple utility functions
window.formatNumber = (num) => {
    return new Intl.NumberFormat('id-ID').format(num);
};

window.formatDate = (date) => {
    return new Date(date).toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
};

window.formatCurrency = (amount) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(amount);
};
