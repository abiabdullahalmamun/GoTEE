// resources/js/plugins/axios.js
import axios from 'axios';

// Read CSRF token from meta tag set by Laravel in Blade
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

const instance = axios.create({
    baseURL: '/api/web', // Adjust if needed
    withCredentials: true, // Important: allows sending session cookies
    headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': csrfToken,
        'Accept': 'application/json'
    }
});

export default instance;
