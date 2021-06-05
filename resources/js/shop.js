import App from '@shop/app.vue';
import Vue from 'vue';
import router from '@router/shop'

export default new Vue({
	router,
	render: h => h(App)
});

