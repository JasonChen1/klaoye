<template>
    <div class="carousel-wp">
    	<b-carousel 
    		:indicator-inside="false"
            :animated="animated"
            :has-drag="drag"
            :autoplay="autoPlay"
            :pause-hover="pauseHover"
            :pause-info="pauseInfo"
            :pause-info-type="pauseType"
            :interval="interval"
            :repeat="repeat"
            :arrow="false"
            :indicator="false"
    	>
    		<b-carousel-item v-for="(item, i) in 6" :key="i">
    			<b-image class="image" :src="getImgUrl(i)"></b-image>
    		</b-carousel-item>
    		<template #indicators="props">
    			<b-image class="al image" :src="getImgUrl(props.i)" :title="props.i"></b-image>
    		</template>
    	</b-carousel>
    </div>
</template>
<script>
	export default {
		name: 'Home',
		components: {
		},
		data() {
			return {
				animated:'slide',
				drag: true,
                autoPlay: true,
                pauseHover: true,
                pauseInfo: false,
                repeat: true,
                pauseType: 'is-primary',
                interval: 4000,
			}
		},
		mounted(){
           
        },
        computed:{
            
        },
        methods:{
        	getImgUrl(value) {
        		return `https://picsum.photos/id/43${value}/1230/500`
        	}
        }
	}
</script>