<template>
    <div>
        dashboard page
    	<router-link :to="{ name: 'HOME' }">HOME</router-link>
    </div>
</template>
<script>
    export default {
        name: 'Dashboard',
        props:{
            // isMobile:{
            //     type:Boolean,
            //     required:true
            // }
        },
        data() {
            return {
            }
        },
        mounted(){
           
        },
        computed:{
            
        },
        methods:{

        }
    }
</script>