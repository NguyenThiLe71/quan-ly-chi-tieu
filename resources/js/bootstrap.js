import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * Thêm dòng này để kích hoạt Bootstrap 5 qua Vite
 */
import * as bootstrap from 'bootstrap';
window.bootstrap = bootstrap;