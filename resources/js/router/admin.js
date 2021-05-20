import Vue from 'vue'
import Router from 'vue-router'
Vue.use(Router)

export default new Router({
	mode:'history',
	routes: [
		// main routes
		{ path: '/admin', name:'AdminHome', component: require('@admin/dashboard.vue').default , meta: { requiresAuth: true }},
		{ path: '/admin/login', name:'AdminLogin', component: require('@admin/login.vue').default },
		{ path: '/admin/password/reset', name:'AdminForgotPassword', component: require('@admin/resetEmail.vue').default },
		{ path: '/admin/password/reset/:token', name:'AdminPasswordReset', component: require('@admin/resetPassword.vue').default },

		// error
		// { path:'/500', name:'b_server_error', component: require('@errors/500.vue').default },
		// { path:'/401', name:'b_unauthenticated', component: require('@errors/401.vue').default },
		{ path:'*', name:'NotFound', component: require('@errors/404.vue').default },
	],
	scrollBehavior (to, from, savedPosition) {
		return { x: 0, y: 0 }
	}
})