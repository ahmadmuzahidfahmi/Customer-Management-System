
import '../css/app.css';
import Alpine from 'alpinejs';
import Chart from 'chart.js/auto';


window.Alpine = Alpine;

document.addEventListener('alpine:init', () => {
    Alpine.store('search', { query: '' });
});

window.searchDropdown = function (items = []) {
    return {
        query: '',
        open: false,
        filtered: [],

        init() {
            this.filtered = items;
        },

        filterResults() {
            const q = this.query.toLowerCase();

            this.filtered = items.filter(item =>
                item.toLowerCase().includes(q)
            );
        },

        select(item) {
            this.query = item;
            this.open = false;
        }
    };
};

document.addEventListener('DOMContentLoaded', () => {
    const canvas = document.getElementById('customerGrowthChart');

    if (canvas) {
        const labels = JSON.parse(canvas.dataset.labels);
        const values = JSON.parse(canvas.dataset.values);

        new Chart(canvas, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Total Customers',
                    data: values,
                    borderColor: 'rgb(70, 192, 189)',
                    backgroundColor: 'rgba(70, 192, 189, 0.1)',
                    tension: 0.3,
                    fill: true,
                    pointRadius: 3,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
            }
        });
    }
});
Alpine.start();