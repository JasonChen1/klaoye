<template>
    <div >
        products page
    	<router-link :to="{ name: 'HOME' }">HOME</router-link>
    </div>
</template>
<script>
    export default {
        name: 'Products',
        data() {
            return {
                windowWidth:0,
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