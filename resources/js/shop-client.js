require('./bootstrap');
import Vue from 'vue';
import router from '@router/shop'
import { isMobile } from 'mobile-device-detect'
import App from '@shop/app.vue';
import store from './auth/user/store'

const token = Vue.cookie.get('token_u')
if (token) {
  axios.defaults.headers.common['Authorization'] = `Bearer ${token}`
}

window.isMobile = isMobile

router.beforeEach((to, from, next) => {
  window.loading.start();
  if (to.matched.some(record => record.meta.requiresAuth)) {
    if (!token) {
      next({
        path: '/login'
      })
    } else {
      next()
    }
  } else {
    next();
  }
});

router.afterEach(route => {
    window.loading.finish();
});

if (window.__INITIAL_STATE__) {
  // We initialize the store state with the data injected from the server
  store.replaceState(window.__INITIAL_STATE__)
}

export default new Vue({
  el: '#app',
  router,
  store,
  render: h => h(App)
});


