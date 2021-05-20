<template>
	<div>
		<router-view/>
	</div>
</template>

<script>
	export default {
		name: 'app',
		components: {
		},
		data(){
			return {
				timeLeft:7200000,//2hour
				loginTime:this.$cookie.get('admin_lt')
			}
		},
		mounted(){
			let self = this
			$(document).ready(function() {
				$('body').bind('mousedown keydown', function(event) {
					if(self.loginTime){
						let now = new Date()
						let diff = now - new Date(self.loginTime)
						if(diff<self.timeLeft && diff>6000000){
							self.refreshToken()
						}
					}
				});
			});	
		},
		methods:{
			refreshToken(){
				axios.get('/api/admin/refresh/token')
				.then(res=>{
					this.loginTime = this.$cookie.get('admin_lt')
					this.$store.dispatch('setToken', res)
				})
			}
		}
	}
</script>