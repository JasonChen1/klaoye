import Vue from 'vue'
var VueCookie = require('vue-cookie');
Vue.use(VueCookie);
const SET_LOGIN = "SET_LOGIN";
const SET_LOGOUT = "SET_LOGOUT";
const SET_TOKEN = "SET_TOKEN";

export default {
	state: {
		aLogin: Vue.cookie.get('token_a') ? true : false,
		aname: localStorage.getItem('aname') || '',
	},
	mutations: {
		[SET_LOGIN] (state, data) {
			state.aLogin = true
			state.aname = data.admin.name
			
		},
		[SET_LOGOUT] (state, data){
			state.aLogin = false
			state.aname = ''
		}
	},
	actions: {
		// 登录缓存
		setLoggedIn({commit},res){
			return  new Promise((resolve,reject)=>{
				Vue.cookie.set('token_a',res.data.access_token,{ expires: new Date(res.data.expires_at)})
				Vue.cookie.set('admin_lt',new Date())
				localStorage.setItem('aname', res.data.admin.name)
				commit(SET_LOGIN,res.data)
				resolve('success')
			})
		},
		// 缓存token
		setToken({commit},res){
			Vue.cookie.set('token_a',res.data.access_token,{ expires: new Date(res.data.expires_at)})
			Vue.cookie.set('admin_lt',new Date())
		},
		// 登出
		setLogout({commit},res){
			return new Promise((resolve,reject)=>{
				Vue.cookie.delete('token_a')
				Vue.cookie.delete('admin_lt')
				localStorage.removeItem('aname')
				commit(SET_LOGOUT)
				resolve('success')
			})
		},
	},
	getters: {
		isLoggedIn: state => state.aLogin,
		adminname: state => state.aname,
	}
}