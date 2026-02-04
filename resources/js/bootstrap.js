import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Fa que Axios agafi les cookies de sessió del navegador
window.axios.defaults.withCredentials = true;