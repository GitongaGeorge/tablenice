// AlpineJS components for Tablenice
document.addEventListener('alpine:init', () => {
    Alpine.data('tablenic', () => ({
        search: '',
        perPage: 10,
        sortField: 'id',
        sortDirection: 'asc',

        init() {
            console.log('Tablenice initialized');
        },

        sortBy(field) {
            if (this.sortField === field) {
                this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc';
            } else {
                this.sortField = field;
                this.sortDirection = 'asc';
            }
        }
    }));
});