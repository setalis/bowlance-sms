import axios from 'axios';

window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

const csrfMeta = document.querySelector('meta[name="csrf-token"]');
if (csrfMeta) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = csrfMeta.getAttribute('content');
}

window.axios.interceptors.response.use(
    (response) => response,
    (error) => {
        if (error.response?.status === 419) {
            const msg = 'Сессия истекла. Обновляю страницу…';
            if (typeof alert !== 'undefined') {
                alert(msg);
            }
            window.location.reload();
        }
        return Promise.reject(error);
    }
);
