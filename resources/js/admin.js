require('./bootstrap');

import app from '@admin/app'
import router from '@router/admin'
import store from './auth/admin/store'

const token = Vue.cookie.get('token_a')
if (token) {
  axios.defaults.headers.common['Authorization'] = `Bearer ${token}`
}

router.beforeEach((to, from, next) => {
  window.loading.start();
  if (to.matched.some(record => record.meta.requiresAuth)) {
    if (!token) {
      next({
        path: '/admin/login'
      })
    } else {
      next()
    }
  } else {
    next() 
  }
})

router.afterEach(route => {
    window.loading.finish();
});

new Vue({
	el: '#app',
	router,
	store,
	render: h => h(app)
});
