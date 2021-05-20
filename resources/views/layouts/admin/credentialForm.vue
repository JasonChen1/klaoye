<template>
	<div class="admin-login-container" :style="`background-image: url(${bgImg});`">
        <slot name="form"></slot>
    </div>
</template>
<script>
	import bgImg from '@images/credential-bg.jpg'
	export default {
        name: 'credentialForm',
        components: {
        },
        data() {
            return {
                bgImg:bgImg,
            }
        },
        mounted(){
        },
        methods:{
        }
    }
</script>