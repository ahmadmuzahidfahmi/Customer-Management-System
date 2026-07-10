import '../css/app.css';
import Alpine from 'alpinejs';

window.Alpine = Alpine;

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

Alpine.start();