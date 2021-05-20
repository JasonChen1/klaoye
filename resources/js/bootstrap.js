
try {
    window.Popper = require('popper.js').default;
    window.$ = window.jQuery = require('jquery');

    require('bootstrap');
} catch (e) {}

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

window.axios = require('axios');
window.Vue = require('vue');

import Buefy from 'buefy'
import VueLazyload from 'vue-lazyload'
import {LoadingBar} from 'view-design'
import 'view-design/dist/styles/iview.css'
import errorImg from '@images/errorImage.jpg'
var VueCookie = require('vue-cookie');

Vue.use(VueCookie);
Vue.use(VueLazyload, {
	preLoad: 1,
	error: errorImg,
	loading: errorImg,
	attempt: 2
})
Vue.use(Buefy)
window.loading = LoadingBar;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
let token = document.head.querySelector('meta[name="csrf-token"]');
if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
    console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
}