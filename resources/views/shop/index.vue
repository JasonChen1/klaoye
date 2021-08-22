<template>
    <div class="container">
        <div class="carousel-wp">
            <router-link  class="router-link" to="/products" >
            	<b-carousel 
            		:indicator-inside="indicatorInside"
                    :animated="animated"
                    :has-drag="drag"
                    :autoplay="autoPlay"
                    :pause-hover="pauseHover"
                    :pause-info="pauseInfo"
                    :pause-info-type="pauseType"
                    :interval="interval"
                    :repeat="repeat"
                    :arrow="false"
                    :indicator="indicator"
                    :indicator-background="indicatorBackground"
                    :indicator-mode="indicatorMode"
                    :indicator-position="indicatorPosition"
                    :indicator-style="indicatorStyle"
            	>
            		<b-carousel-item v-for="(src, i) in carousels" :key="i">
            			<b-image :src="src"></b-image>
            		</b-carousel-item>
            		<template #indicators="props">
            			<b-image class="al image" :src="props.src" :title="props.i"></b-image>
            		</template>
            	</b-carousel>
            </router-link>
        </div>
        <div class="cate-wp">
            <h1 class="section-title">Categories</h1>
            <div :class="isMobile?'':'hc-lwp'">
                <router-link class="img-wp pr-3" v-lazy-container="{ selector: 'img' }" v-for="(img,i) in leftCate" :key="i" :to="img.url">
                    <h2>{{img.name}}</h2>
                    <img :data-src="img.src" :alt="img.name">
                </router-link>
            </div>
            <div :class="isMobile?'':'hc-rwp'">
                <router-link  class="img-wp pl-3" v-lazy-container="{ selector: 'img' }" v-for="(img,i) in rightCate" :key="i" :to="img.url">
                    <h2>{{img.name}}</h2>
                    <img :data-src="img.src" :alt="img.name">
                </router-link>
            </div>
        </div>
        <div>
            <h1 class="section-title">Testimonials</h1>
            <div class="testi-wp">
                <div class="t-wp" :class="isMobile?'mr-2':'mw-33'" v-for="(testi,i) in testimonials" :key="i">
                    <router-link :to="`/${testi.product.category_name}/${testi.product.id}`">
                        <div class="row" v-lazy-container="{ selector: 'img' }">
                            <img class="col-4 pr-0" :data-src="`/storage/thumbnail/${testi.image.image_url}`" :alt="testi.product.name">
                            <div class="col-8 pd-wp">
                                <p>{{ testi.product.name }}</p>
                                <p>${{ testi.product.price }}</p>
                            </div>
                        </div>
                    </router-link>
                    <div class="rating">
                        <b-rate
                           v-model="testi.rating"
                           :icon-pack="'mdi'"
                           :icon="'star'"
                           :max="5"
                           :rtl="false"
                           :spaced="false"
                           :disabled="true">
                        </b-rate>   
                    </div>
                    <div class="description">
                        <p>{{testi.description}}</p>
                    </div>
                    <div class="person-wp rating">
                        <div class="d-flex">
                            <div class="p-circle ">
                                <b-icon
                                    class="p-icon"
                                    icon="account"
                                    size="is-medium">
                                </b-icon>
                            </div>
                            <div class="pl-2">
                                <p>{{ testi.name }}</p>
                                <p>{{ testi.personal_des }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
	export default {
		name: 'Home',
		components: {
		},
		props:{
            isMobile:{
                type:Boolean,
                required:true
            }
        },
		data() {
			return {
				animated:'slide',
				drag: true,
                autoPlay: true,
                pauseHover: true,
                pauseInfo: false,
                repeat: true,
                indicator: true,
                indicatorBackground: false,
                indicatorInside: true,
                indicatorMode: 'hover',
                indicatorPosition: 'is-bottom',
                indicatorStyle: 'is-dots',
                pauseType: 'is-primary',
                interval: 4000,
                carousels:[
                    'https://klaoye.s3.us-west-2.amazonaws.com/carousel1.jpg',
                    'https://klaoye.s3.us-west-2.amazonaws.com/carousel2.jpg',
                    'https://klaoye.s3.us-west-2.amazonaws.com/carousel3.jpg',
                    'https://klaoye.s3.us-west-2.amazonaws.com/carousel4.jpg',
                ],
                leftCate:[
                    {
                        name:'Living-room',
                        src:'https://klaoye.s3.us-west-2.amazonaws.com/home1.jpg',
                        url:'/Living-room'
                    },
                    {
                        name:'Bedroom',
                        src:'https://klaoye.s3.us-west-2.amazonaws.com/home2.jpg',
                        url:'/Bedroom'
                    },
                ],
                rightCate:[
                    {
                        name:'Bathroom',
                        src:'https://klaoye.s3.us-west-2.amazonaws.com/home3.jpg',
                        url:'/Bathroom'
                    },
                    {
                        name:'For Pets',
                        src:'https://klaoye.s3.us-west-2.amazonaws.com/home4.jpg',
                        url:'/For Pets'
                    },
                ],
                testimonials:[

                ],
			}
		},
		mounted(){
            axios.get('/api/testimonials')
            .then(res => {
                this.testimonials = res.data
            })
        },
        computed:{
            
        },
        methods:{
        }
	}
</script>