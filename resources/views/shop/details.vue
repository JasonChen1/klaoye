<template>
    <div :class="isMobile?'':'details-wp'">
        <div :class="isMobile?'':'magnifier-wp'" v-if="product">
            <div>
                <image-magnifier :src="isMobile ? item.image.replace('204','440') : item.image.replace('204','yuan')"
                :zoom-src="isMobile ? item.image.replace('204','440') : item.image.replace('204','yuan')"
                :width="isMobile?325:450"
                :height="isMobile?325:450"
                :zoom-width="isMobile?325:450"
                :zoom-height="isMobile?325:450"
                :mask-width="150"
                :mask-height="150"
                :scale-size="200"
                :imageAlt="product.name"></image-magnifier>

                <b-carousel-list
                v-model="values"
                :data="items"
                :arrow="arrow"
                :arrow-hover="arrowHover"
                :items-to-show="perList"
                :items-to-list="increment"
                :repeat="repeat"
                :has-drag="drag"
                :has-grayscale="gray"
                :has-opacity="opacity" >
                    <template #item="list">
                        <div @click="updateImage(list)">
                            <img :src="list.image" :alt="list.alt">
                        </div>
                    </template>
                </b-carousel-list>
            </div>
            <div class="details" :class="isMobile?'mt-3':''">
                <div>
                    <h1><strong v-html="product.name"></strong></h1>
                    <div>
                        <b-tag type="is-danger" v-if="product.discount">{{ product.discount.includes('%') ? product.discount : `$ ${product.discount}` }} OFF</b-tag>
                    </div>
                    <div class="product-price mt-3">
                        <h1>
                            <strong>$
                                <span v-if="discount" class="pw-ds">{{discount}} <s>{{product.price}}</s></span>
                                <span v-else>{{product.price}}</span>
                            </strong>
                        </h1>
                    </div>
                    <div class="des-wp">
                        <div>
                            <h2><strong>Category:</strong> <span>{{product.category_name}}</span></h2>
                        </div>
                        <div>
                            <h2><strong>Size:</strong> <span>{{product.size}}</span></h2>
                        </div>
                        <div>
                            <h2>
                                <strong>Stock Available:</strong> 
                                <span v-if="(product.stock-product.occupied)>0">In Stock</span>
                                <span v-else>Out Of Stock</span>
                            </h2>
                        </div>
                        <div v-if="product.details.length>0" class="shop-color-wp">
                            <h2>
                                <strong>Colours:</strong> 
                            </h2>
                            <span v-for="(color,i) in product.details" :key="i" @click="setColor(color)" class="cb" :style="`background: ${color.color_code}`" :class="colorId==color.id?'color-active':''"></span>
                        </div>
                    </div>
                    <div class="prod-btn-wp mt-2" :class="isMobile?'mt-2':''">
                        <button class="btn add-cart-btn" @click="addToCart(product)"><b-icon icon="cart-arrow-down"></b-icon>Add To Cart</button>
                        <button class="btn buy-btn " @click="buyNow(product)"><b-icon icon="cart-arrow-right"></b-icon>Buy It Now</button>
                    </div>
                    <div v-if="product.description" class="prod-description-wp">
                        <h2><strong>Description:</strong> <span v-html=" product.description"></span></h2>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    import ImageMagnifier from '@components/magnifier'

    export default {
        name: 'Detail',
        props:{
            isMobile:{
                type:Boolean,
                required:true
            }
        },
        components: {
            'image-magnifier':ImageMagnifier
        },
        data() {
            return {
                id:null,
                product:null,
                item:{
                    title:'',
                    image:'',
                },
                colorId:null,
                color_code:null,
                previousUrl:null,
                discount:null,
                // carousel list
                arrow: false,
                arrowHover: false,
                drag: true,
                gray: false,
                opacity: false,
                values: 1,
                perList: 4,
                increment: 1,
                repeat: true,
                items: [],
                // user
                auth:null,
            }
        },
        mounted(){
            this.id = this.$route.params.product
            this.getDetails()
            this.auth = this.$store.getters.isLoggedIn
        },
        watch:{
            $route(to, from) {
                this.id = to.params.product
                this.getDetails()
            }
        },
        methods:{
            setColor(color){
                this.colorId = color.id
                this.color_code = color.color_code
            },
            updateImage(val){
                this.item.title = this.product.name
                this.item.image = val.image
            },
            getDetails(){
                axios.get(`/api/products/${this.id}`)
                .then(res=>{
                    this.product = res.data
                    if(res.data.discount){
                        this.discount = (res.data.price-res.data.discount).toFixed(2)
                    }

                    for (var i = 0; i < res.data.images.length; i++) {
                        res.data.images[i]
                        this.items.push({
                            alt: res.data.name,
                            title: res.data.name,
                            image:  `/storage/${res.data.images[i].image_url}`,
                            srcFallback:  `/storage/${res.data.images[i].image_url}`
                        })
                    }
            
                    this.item.title = res.data.name
                    this.item.image = `/storage/${res.data.images[0].image_url}`

                })
                .catch(err=>{})
                .finally(res=>{})
            },
            buyNow(prod){
                if(this.product.stock<this.product.occupied+1){
                    this.$buefy.notification.open({
                        duration: 3000,
                        message: this.$t('shop.out_of_stock'),
                        position: 'is-top-right',
                        type: 'is-danger',
                        hasIcon: true
                    })
                    return
                }
                if(!this.auth){
                    this.$router.push(`/guest/checkout?prod=${prod.product_id}&color=${this.colorId}`)
                }else{
                    this.$router.push(`/checkout?prod=${prod.product_id}&color=${this.colorId}`)
                }
            },
            addToCart(prod){
                if(!this.auth){
                    let cartArray = this.$store.getters.carts
                    let currentProd = null
                    let addFailed = false
                    if(cartArray.length>0){
                        currentProd = cartArray.find(item=> item.id === this.product.id)
                        if(currentProd && currentProd.stock <=currentProd.occupied){
                            addFailed = true
                        }
                    }
                    if(this.product.stock < this.product.occupied+1 || addFailed){
                        this.$buefy.notification.open({
                            duration: 3000,
                            message: 'Product out of stock!',
                            position: 'is-top-right',
                            type: 'is-danger',
                            hasIcon: true
                        })
                        return
                    }
                    if(this.color_code){
                        this.product['color_code'] = this.color_code
                        this.product['color_id'] = this.colorId
                    }else{
                        if(this.product.details.length>0){
                            this.product['color_code'] = this.product.details[0].color_code
                            this.product['color_id'] = this.product.details[0].id
                        }
                    }

                    this.$store.dispatch('addToCart', this.product)
                    .then(res => {
                        this.toastMessage = 'Add to cart successfully'
                        this.toastType = 'is-success'
                        this.$emit('reloadHeader')
                    })
                    .catch(err => {
                        this.toastMessage = 'Add to cart failed'
                        this.toastType = 'is-danger'
                    })
                    .finally(res=>{
                        this.$buefy.notification.open({
                            duration: 3000,
                            message: this.toastMessage,
                            position: 'is-top-right',
                            type: this.toastType,
                            hasIcon: true
                        })
                    })
                }else{
                    if(this.product.details.length>0 && !this.colorId){
                        this.colorId = this.product.details[0].id
                        this.color_code = this.product.details[0].color_code
                    }
                    axios.post(`/api/user/cart`,{
                        id:prod.id,
                        detail_id:this.colorId,
                        color_code:this.color_code
                    })
                    .then(res=>{
                        this.toastMessage = 'Add to cart successfully'
                        this.toastType = 'is-success'

                        this.$store.dispatch('userCart')
                        .then(res=>{
                            this.$emit('reloadHeader')
                        })
                    })
                    .catch(err=>{
                        if(err.response.data===1){
                            this.toastMessage = 'Out of stock'
                            this.toastType = 'is-danger'
                        }else{
                            this.toastMessage = 'Add to cart failed'
                            this.toastType = 'is-danger'
                        }
                    })
                    .finally(res=>{
                        this.$buefy.notification.open({
                            duration: 3000,
                            message: this.toastMessage,
                            position: 'is-top-right',
                            type: this.toastType,
                            hasIcon: true
                        })
                    })
                }
            }
        }
    }
</script>