<template>
    <div class="container">
        <div class="carousel-wp" >
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
                            <img class="col-4 pr-0" :data-src="testi.image.image_url" :alt="testi.product.name">
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
                                    icon="account"
                                    size="is-large"
                                    type="is-success">
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
    import carousel1 from '@images/carousel1.jpg';
    import carousel2 from '@images/carousel2.jpg';
    import carousel3 from '@images/carousel3.jpg';
    import carousel4 from '@images/carousel4.jpg';
    import home1 from '@images/home1.jpg';
    import home2 from '@images/home2.jpg';
    import home3 from '@images/home3.jpg';
    import home4 from '@images/home4.jpg';
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
                    carousel1,
                    carousel2,
                    carousel3,
                    carousel4,
                ],
                leftCate:[
                    {
                        name:'Living-room',
                        src:home1,
                        url:'/Living-room'
                    },
                    {
                        name:'Bedroom',
                        src:home2,
                        url:'/Bedroom'
                    },
                ],
                rightCate:[
                    {
                        name:'Bathroom',
                        src:home3,
                        url:'/Bathroom'
                    },
                    {
                        name:'For Pets',
                        src:home4,
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