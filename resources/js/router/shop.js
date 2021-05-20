import Vue from 'vue'
import Router from 'vue-router'
Vue.use(Router)



export default new Router({
	mode:'history',
	routes: [
		// main routes
		{ path: '/', name:'Home', component: require('@shop/index.vue').default },
		{ path: '/products', name:'Products', component: require('@shop/products.vue').default },
		// { path: '/admin/password/reset', name:'b_admin_forgot_password', component: require('@admin/pages/email.vue').default },
		// { path: '/admin/password/reset/:token', name:'b_admin_password_reset', component: require('@admin/pages/reset.vue').default },

		// error
		// { path:'/500', name:'b_server_error', component: require('@errors/500.vue').default },
		// { path:'/401', name:'b_unauthenticated', component: require('@errors/401.vue').default },
		{ path:'*', name:'NotFound', component: require('@errors/404.vue').default },
	],
	scrollBehavior (to, from, savedPosition) {
		return { x: 0, y: 0 }
	}
})