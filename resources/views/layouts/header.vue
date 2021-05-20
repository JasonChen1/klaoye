<template>
    <div id="header">
        header
        <router-link :to="{ name: 'Products' }">products</router-link>
    </div>
</template>

<script>
	export default {
		name: 'headerNav',
        props:{
            // categories:{
            //     type:Array,
            //     required:true
            // }
        },
		components: {
            // 'v-breadcrumb':Breadcrumb,
            // BreadcrumbItem,
		},
		data() {
            return {
                
            }
        },
        mounted(){
            
        },
        watch:{
            // $route(to, from) {
            //     if(to.query){
            //         this.path = to.path + '?key='+to.query.key
            //     }else{
            //         this.path = to.path
            //     }
            //     this.pathName = to.name
            // },
        },
        computed: {
           
        },
        methods: {
            
        }
    }
</script>
