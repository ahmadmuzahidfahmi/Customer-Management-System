import '../css/app.css';
import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.data('searchDropdown', () => ({
    query: '',
    open: false,
    filtered: [],

    init() {
        this.filtered = window.companyList || [];
    },

    filterResults() {
        const q = this.query.toLowerCase();

        this.filtered = (window.companyList || []).filter(item =>
            item.toLowerCase().includes(q)
        );
    },

    select(item) {
        this.query = item;
        this.open = false;
    }
}));

Alpine.start();