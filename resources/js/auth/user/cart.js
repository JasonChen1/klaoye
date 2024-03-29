import Vue from 'vue'
var VueCookie = require('vue-cookie');
Vue.use(VueCookie);
const ADD_CART = "ADD_CART";
const CLEAR_CART = "CLEAR_CART";
const DLETE_FROM_CART = "DLETE_FROM_CART";
const CHANGE_QUANTITY = "CHANGE_QUANTITY";
const USER_CART = "USER_CART";
const CART_TOTAL = "CART_TOTAL";

export default {
	state: {
		carts: [],
		userCart: 0,
		totals:[],
	},
	mutations: {
		[ADD_CART] (state, data) {
			let discount = 0
			let discountTotal = 0
			let subTotal = 0
			let exists = false
			for (var i = 0; i < state.carts.length; i++) {
				if(state.carts[i].id===data.id){
					exists = true
					let occupied = state.carts[i].occupied+1
					if(occupied <= data.stock){
						state.carts[i].occupied = occupied
						state.carts[i]['num'] +=1
						subTotal = state.carts[i]['num']*state.carts[i].price
						if(state.carts[i].discount){
							state.carts[i]['discounted'] = (state.carts[i].price - state.carts[i].discount).toFixed(2)
						}
						if(data.color_code){
							state.carts[i]['color_code'] = data.color_code
							state.carts[i]['color_id'] = data.color_id
						}
						// each products subtotal and discounted total
						state.carts[i]['discount_total'] = (state.carts[i].discount*state.carts[i]['num']).toFixed(2)
						state.carts[i]['delivery_total'] = (state.carts[i].delivery*state.carts[i]['num']).toFixed(2)
						state.carts[i]['subtotal'] = subTotal.toFixed(2)
					}
				}
			}
			if(!exists){
				data.occupied += 1
				data['num'] = 1
				data['subtotal'] = data.price
				data['discount_total'] = 0
				data['delivery_total'] = data.delivery
				if(data.discount){
					data['discount_total'] = data.discount
					data['discounted'] = (data.price - data.discount).toFixed(2)
				}
						
				state.carts.push(data)
			}
		},
		[CLEAR_CART] (state){
			state.carts = []
			state.totals = []
			state.userCart = 0
		},
		[DLETE_FROM_CART] (state,id){
			let itemIndex = state.carts.findIndex(item=> item.id === id)
			state.carts.splice(itemIndex,1)
		},
		[CHANGE_QUANTITY](state,data){
			let discount = 0
			let discountTotal = 0
			let percent= 0
			let subTotal = 0
			let diff = data.data.quantity - state.carts[data.index]['num']
		
			if(diff > 0){
				state.carts[data.index]['occupied'] += diff
			}else{
				state.carts[data.index]['occupied'] -= Math.abs(diff)
			}

			state.carts[data.index]['num'] = data.data.quantity
			subTotal = state.carts[data.index]['num']*state.carts[data.index]['price']
			if(state.carts[data.index]['discount']){
				discountTotal = state.carts[data.index]['discount']*state.carts[data.index]['num']
				state.carts[data.index]['discount_total'] = discountTotal.toFixed(2)

				if(!state.carts[data.index]['discounted']){
					state.carts[data.index]['discounted'] = (state.carts[data.index]['price']-discount).toFixed(2)
				}
			}
			if(state.carts[data.index]['delivery']){
				discountTotal = state.carts[data.index]['delivery']*state.carts[data.index]['num']
				state.carts[data.index]['delivery_total'] = discountTotal.toFixed(2)
			}
			if(state.carts[data.index]['color_code']){
				state.carts[data.index]['color_code'] = state.carts[data.index]['color_code']
				state.carts[data.index]['color_id'] = state.carts[data.index]['color_id']
			}
			state.carts[data.index]['subtotal'] = subTotal.toFixed(2)
		},
		[USER_CART](state,num){
			state.userCart = num
		},
		[CART_TOTAL](state,data){
			state.totals = data
		}
	},
	actions: {
		addToCart({commit,getters},data){
			return new Promise((resolve,reject)=>{
				commit(ADD_CART,data)
				resolve('success')
			})
		},
		clearCart({commit}){
			return new Promise((resolve,reject)=>{
				commit(CLEAR_CART)
				resolve('success')
			})
		},
		deleteFromCart({commit},id){
			return new Promise((resolve,reject)=>{
				commit(DLETE_FROM_CART,id)
				resolve('success')
			})
		},
		changeQuantity({commit,getters},data){
			return new Promise((resolve,reject)=>{
				let currentIndex = getters.carts.findIndex(item=> item.id === data.id)
				let currentVal = getters.carts[currentIndex]
				if(currentVal['stock']<data.quantity){
					reject(1)
					return;
				}
				if(data.quantity<1){
					reject('product quantity can not be less than 1')
					return;
				}

				commit(CHANGE_QUANTITY,{index:currentIndex,data:data})
				resolve('success')
			})
		},
		userCart({commit,getters}){
			return new Promise((resolve,reject)=>{
				axios.get(`/api/user/cart/count`)
				.then(res=>{
					commit(USER_CART,res.data)
					resolve('success')
				})
				.catch(err=>{
					reject(err)
				})
			})
		},
		addCartTotal({commit},data){
			commit(CART_TOTAL,data)
		}
	},
	getters: {
		carts: state => state.carts,
		userCarts: state => state.userCart,
		cartTotal: state => state.totals
	}
}