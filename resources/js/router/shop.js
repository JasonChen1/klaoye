import Vue from 'vue'
import Router from 'vue-router'
Vue.use(Router)

export default new Router({
	mode:'history',
	routes: [
		// main routes
		{ path: '/', name:'HOME', component: require('@shop/index.vue').default },
		{ path: '/products', name:'PRODUCTS', component: require('@shop/products.vue').default },
		{ path: '/private-policy', name:'PRIVATE POLICY', component: require('@shop/privatePolicy.vue').default },
		{ path: '/faq-shipping', name:'FAQ SHIPPING', component: require('@shop/faqshipping.vue').default },
		{ path: '/dashboard', name:'DASHBOARD', component: require('@user/dashboard.vue').default , meta: { requiresAuth: true }},
		{ path: '/contact', name:'CONTACT US', component: require('@shop/contact.vue').default },
		{ path: '/cart', name:'CART', component: require('@shop/cart.vue').default },
		{ path: '/login', name:'LOGIN', component: require('@user/login.vue').default },
		{ path: '/register', name:'REGISTER', component: require('@user/register.vue').default },
		{ path: '/verified', name:'VERIFIED SUCCESS', component: require('@user/verified.vue').default },
		{ path: '/password/reset', name:'FORGOT PASSWORD', component: require('@user/email.vue').default },
		{ path: '/password/reset/:token', name:'PASSWORD RESET', component: require('@user/reset.vue').default },

		// { path: '/order', name:'b_order', component: require('@user/order.vue').default , meta: { requiresAuth: true }},
		// { path: '/address', name:'b_address', component: require('@user/address.vue').default , meta: { requiresAuth: true }},
		// { path: '/reset/password', name:'b_update_password', component: require('@user/resetPassword.vue').default , meta: { requiresAuth: true }},
		// { path: '/checkout', name:'b_checkout', component: require('@user/checkout.vue').default , meta: { requiresAuth: true }},
		// { path: '/guest/checkout', name:'b_guest_checkout', component: require('@shop/guestCheckout.vue').default },
		// { path: '/checkout/success', name:'b_checkout_success', component: require('@shop/checkoutSuccess.vue').default },

		{ path: '/:category', name:'CATEGORY', component: require('@shop/products.vue').default },
		{ path: '/:category/:product', name:'PRODUCT DETAILS', component: require('@shop/details.vue').default },
		// error
		// { path:'/500', name:'b_server_error', component: require('@errors/500.vue').default },
		// { path:'/401', name:'b_unauthenticated', component: require('@errors/401.vue').default },
		{ path:'*', name:'NotFound', component: require('@errors/404.vue').default },
	],
	scrollBehavior (to, from, savedPosition) {
		return { x: 0, y: 0 }
	}
})