<template>
    <div>
    	<v-header :categories="categories" :isMobile="isMobile"></v-header>

    	<div v-if="$route.name==='HOME'" :class="isMobile?' container-mb':'container-df'">
    		<router-view/>	
    	</div>
    	<div v-else class="container" :class="isMobile?'container-mb':'container-df'">
    		<router-view/>
    	</div>
    	<v-footer :categories="categories" :isMobile="isMobile"></v-footer>
    </div>
</template>
<script>
	import Header from '@views/layouts/header'
	import Footer from '@views/layouts/footer'
	export default {
		name: 'app',
		components: {
			'v-header':Header,
			'v-footer':Footer
		},
		data() {
			return {
				categories:[],
				loadingBar:'',
				windowWidth:0,
			}
		},
		computed:{
            isMobile(){
                return this.windowWidth  < 1024 ? true:false 
            },
        },
		mounted(){
            window.addEventListener('resize', () => {
                this.windowWidth = window.innerWidth
            })
            this.windowWidth = window.innerWidth
            // this.getFontendData()
            this.loadingBar = window.loading
            this.getCategories()
        },
        methods:{
	        getFontendData(){
	        	// if(this.socialList.length<1){
	        	// 	axios.get(`/api/frontend/data`)
	        	// 	.then(res=>{
	        	// 		this.active = res.data.activeSite
	        	// 		this.socialList = res.data.socials
	        	// 		this.storeAddress = res.data.address
	        	// 	})
	        	// }
	        },
	        getCategories(){
	        	this.loadingBar.start()
	        	if(!this.$cookie.get('categories')){
	        		axios.get('/api/categories')
	        		.then(res => {
	        			this.categories = res.data
	        			Vue.cookie.set('categories',JSON.stringify(res.data),"12h")
	        		})
	        		.catch(err => {
	        		})  
	        		.finally(res=>{
	        			this.loadingBar.finish()
	        		})
	        	}else{
	        		this.categories = JSON.parse(this.$cookie.get('categories'))
	        		this.loadingBar.finish()
	        	}
	        }
        }

	}
</script>