import Vue from 'vue'
var VueCookie = require('vue-cookie');
Vue.use(VueCookie);
const SET_LOGIN = "SET_LOGIN";
const SET_LOGOUT = "SET_LOGOUT";

export default {
	state: {
		login: Vue.cookie.get('token_u') ? true : false,
		uname: localStorage.getItem('uname') || '',
	},
	mutations: {
		[SET_LOGIN] (state, data) {
			state.login = true
			state.uname = data.user.name
		},
		[SET_LOGOUT] (state, data){
			state.login = false
			state.uname = ''
		}
	},
	actions: {
		setLoggedIn({commit},res){
			return  new Promise((resolve,reject)=>{
				Vue.cookie.set('token_u',res.data.access_token,{ expires: new Date(res.data.expires_at)})
				localStorage.setItem('uname', res.data.user.name)
				commit(SET_LOGIN,res.data)
				resolve('success')
			})
		},
		setLogout({commit},res){
			return new Promise((resolve,reject)=>{
				Vue.cookie.delete('token_u')
				localStorage.removeItem('uname') 
				commit(SET_LOGOUT)
				resolve('success')
			})
		}
	},
	getters: {
		isLoggedIn: state => state.login,
		username: state => state.uname,
	}
}