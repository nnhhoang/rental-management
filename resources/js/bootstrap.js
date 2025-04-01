import axios from 'axios';
window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.axios.defaults.withCredentials = true;
window.axios.interceptors.request.use(config => {
    const token = localStorage.getItem('auth_token');
    if (token) {
        config.headers['Authorization'] = `Bearer ${token}`;
    }
    return config;
});
window.axios.interceptors.response.use(
    response => response,
    error => {
        if (error.response && [401, 419].includes(error.response.status)) {
            console.error('Authentication error:', error.response.status);
            // Clear the invalid token
            localStorage.removeItem('auth_token');
            // Show alert only if not on login page to avoid redirect loops
            if (!window.location.pathname.includes('/login')) {
                alert('Your session has expired. Please log in again.');
                window.location.href = '/login';
            }
        }

        if (error.response && error.response.status === 422) {
            console.error('Validation error:', error.response.data.errors);
        }
        
        return Promise.reject(error);
    }
);