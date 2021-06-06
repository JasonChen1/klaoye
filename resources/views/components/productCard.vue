<template>
    <div :class="isMobile?'product-block-wp-mb':'product-block-wp'">
        <div :class="isMobile?'mb-product':'product'">
            <div class="img-wp">
                <router-link class="" :to="`/${product.category_name}/${product.id}`" >
                    <figure class="m-0 figure" v-lazy-container="{ selector: 'img' }">
                        <img alt="" :data-src="`storage/${product.images[0]?product.images[0].image_url:''}`">
                    </figure>
                </router-link>
            </div>
            <div :class="isMobile?'product-text-mb':'product-text'">
                <div class="product-text-content">
                    <div class="tags tg">
                        <b-tag type="is-danger" v-if="product.discount">{{ `$ ${product.discount}` }} OFF</b-tag>
                    </div>
                    <div class="product-price">
                        <strong>$
                            <span v-if="discount" class="pw-ds">{{discount}} <s>{{product.price}}</s></span>
                            <span v-else>{{product.price}}</span>
                        </strong>
                    </div>
                </div>
                <div class="product-content-name">
                    <strong class="" v-html="product.name"></strong>
                </div>
                <div class="colors-wp" v-if="product.details.length>0">
                    <p v-for="(color,i) in product.details" :key="i" @click="setColor(color)" class="color-tag" :style="`background: ${color.color_code}`" :class="color_code==color.color_code?'color-active':''"></p>
                </div>
                <div class="prod-btn-wp" v-if="!isMobile">
                    <button class="btn add-cart-btn" @click="addToCart(product)">
                        <!-- <b-icon icon="cart-arrow-down"></b-icon> -->
                        Add To Cart
                    </button>
                    <button class="btn buy-btn" @click="buyNow(product)">
                        <!-- <b-icon icon="cart-arrow-right"></b-icon> -->
                        Buy It Now
                    </button>
                </div>

            </div>
        </div>
        <div v-if="isMobile" class="prod-overlay">
            <div class="overlay-content">
                <router-link class="" :to="`/${product.category_name}/${product.id}`" >
                    <span class="icon-white"><b-icon icon="eye" size="is-medium"></b-icon></span>
                </router-link>
                <span @click="addToCart(product)" class="icon-white"><b-icon icon="cart-arrow-down" size="is-medium"></b-icon></span>
                <span @click="buyNow(product)" class="icon-white"><b-icon icon="cart-arrow-right" size="is-medium"></b-icon></span>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: 'ProductCard',
        components: {
        },
        props:{
            product:{
                type:Object,
                required:true
            },
            isMobile:{
                type:Boolean,
                required:true
            }
        },
        data() {
            return {
                color_code:'',
                colorId:'',
                toastMessage:'',
                toastPosition:'is-top-right',
                toastType:'',
                auth:null,
            }
        },
        mounted(){
            this.auth = this.$store.getters.isLoggedIn
        },
        computed:{
            discount(){
                return (this.product.price-this.product.discount).toFixed(2)
            }
        },
        methods:{
            setColor(color){
                this.color_code = color.color_code
                this.colorId = color.id
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