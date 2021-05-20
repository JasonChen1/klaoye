<template>
    <div >
    	<v-header></v-header>
    	<router-view/>
    	<v-footer></v-footer>
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
			}
		}
	}
</script>