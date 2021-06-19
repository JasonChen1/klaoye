<template>
    <div class="container" :class="isMobile?'mt-3':''">
        <div class="tb-head">
            <span>
                <strong>Total: $ {{total.toFixed(2)}}</strong>
                <p v-if="discount.amount>0" style="color: red; font-weight: bold;">
                    Discount: {{discount.discount}} OFF
                </p>
            </span>
            <strong class="cursor color-info" @click="clearAll">
                CLEAR ALL
            </strong>
        </div>
        <div class="cart-wp  section-wp">
            <b-table
                class="cart-table"
                :data="data"
                :bordered="isBordered"
                :striped="isStriped"
                :narrowed="isNarrowed"
                :hoverable="isHoverable"
                :loading="isLoading"
                :focusable="isFocusable"
                paginated
                :per-page="perPage"
                :current-page.sync="page"
                :pagination-position="paginationPosition"
                :default-sort-direction="defaultSortDirection"
                aria-next-label="Next page"
                aria-previous-label="Previous page"
                aria-page-label="Page"
                aria-current-label="Current page"
                :mobile-cards="hasMobileCards">

                <b-table-column v-slot="props" field="image" label="Image" width="100" centered > 
                    <div v-if="props.row.images.length>0" v-lazy-container="{ selector: 'img' }" @click="imageActive(props.row.images[0].image_url)" class="cart-product-img">
                        <img :data-src="`/storage/thumbnail/${props.row.images[0].image_url}`">
                    </div>
                    <div v-else v-lazy-container="{ selector: 'img' }" class="cart-product-img">
                        <img :data-src="`/public/errorImage.jpg`">
                    </div>
                </b-table-column>
                <b-table-column v-slot="props" field="name" label="Details" sortable :class="isMobile?'cart-mb-name':''">
                    <strong class="p-name">
                        Product Name:
                        <router-link class="" :to="`/cart/${isLogin ? props.row.product_id:props.row.id}`"  v-html="props.row.name"></router-link>
                    </strong>
                    <div class="cart-color-wp" v-if="props.row.color_code">
                        <strong >Colour:</strong>
                        <p class="cb ml-2" :style="`background: ${props.row.color_code};`"></p>
                    </div>
                </b-table-column>
                <b-table-column v-slot="props" field="price" label="Unit Price" sortable centered>
                    <span v-if="props.row.discount>0"> 
                        $ {{props.row.discounted}}<br>
                        <s>$ {{ props.row.price }}</s>
                    </span>
                    <span v-else>
                        $ {{ props.row.price }}</span>
                    </span>
                </b-table-column>
                <b-table-column v-slot="props" field="num" label="Quantity" sortable centered width="180">
                    <div class="b-numberinput field has-addons">
                        <p class="control">
                            <button type="button" class="button btn-custom-cl is-primary is-rounded" @click="changeQuantity(props.index,props.row.num-1,props.row.id)">
                                <span class="icon">
                                    <i class="mdi mdi-minus mdi-24px"></i>
                                </span>
                            </button>
                        </p> 
                        <div class="control is-clearfix">
                            <input type="number" class="input" :value="props.row.num" @change="changeQuantity(props.index,parseInt($event.target.value),props.row.id)" >
                        </div> 
                        <p class="control">
                            <button type="button" class="button btn-custom-cl is-primary is-rounded" @click="changeQuantity(props.index,props.row.num+1,props.row.id)">
                                <span class="icon"><i class="mdi mdi-plus mdi-24px"></i></span>
                            </button>
                        </p>
                    </div>
                </b-table-column>
                <b-table-column v-slot="props" field="subtotal" label="Subtotal" sortable centered>
                    <span v-if="props.row.discount>0"> 
                        $ {{props.row.subtotal-props.row.discount_total}}<br>
                        <s>$ {{ props.row.subtotal }}</s>
                    </span>
                    <span v-else>
                        $ {{ props.row.subtotal }}</span>
                    </span>
                </b-table-column>
                <b-table-column v-slot="props" field="action" label="Action" centered>
                    <span class="cursor" @click="deleleFromCart(props.index,props.row.id)">
                        <b-icon  icon="trash-can" ></b-icon>
                    </span>
                </b-table-column>
            </b-table>
            <div class="cart-bottom-wp pt-2 row">
                <div class="col-md-4" :class="isMobile?'mb-3':''">
                    <router-link class="" to="/products">
                        <button class="btn btn-black">Continue Shopping</button>
                    </router-link>
                </div>
                <div class="col-md-4">
                    <div class="cart-section">
                        <div class="cart-section-content">
                            <div class="total-right-wp pb-3">
                                <span>Note: Discounts/coupons will only apply to product's base cost</span>
                                <div class="cost-wp">
                                    <strong>Subtotal:</strong>
                                    <div class="price-tag-wp">
                                        <strong>$ {{ subtotal.toFixed(2) }}</strong>
                                    </div>
                                </div>
                                <div class="cost-wp">
                                    <strong>Discount:</strong>
                                    <div class="price-tag-wp">
                                        <strong>$ {{ discount_total.toFixed(2) }}</strong>
                                    </div>
                                </div>
                                <div class="cost-wp">
                                    <strong>Delivery Cost:</strong>
                                    <div class="price-tag-wp">
                                        <strong>$ {{ delivery_total.toFixed(2) }}</strong>
                                    </div>
                                </div>
                                <div class="cost-wp">
                                    <strong>Total:</strong>
                                    <div class="price-tag-wp">
                                        <strong>$ {{total.toFixed(2)}}</strong>
                                    </div>
                                </div>
                            </div>
                            <div class="btn-right-wp">
                                <button class="btn btn-black lg" @click="hanldeCheckout">Checkout</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <b-modal :active.sync="imageModalActive" class="display-modal">
                <div class="default-image">
                    <img :src="imageUrl" v-if="imageUrl">
                </div>
            </b-modal> 
        </div>
    </div>
</template>
<script>
    export default {
        name: 'Cart',
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
                data:[],
                isBordered: true,
                isStriped: false,
                isNarrowed: false,
                isHoverable: true,
                isFocusable: false,
                isLoading: false,
                hasMobileCards: true,
                imageModalActive:false,
                paginationPosition: 'bottom',
                defaultSortDirection: 'asc',
                page: 1,
                perPage: 5,
                imageUrl:'',
                // toast
                toastMessage:'',
                toastPosition:'is-top',
                toastType:'',
                total:0,
                isLogin:false,
                //discount
                discount:0,
                subtotal:0,
                discount_total:0,
                delivery_total:0,
            }
        },
        mounted(){
            this.isLogin = this.$store.getters.isLoggedIn

            if(this.isLogin){
                this.getCart()
            }else{
                this.data = this.$store.getters.carts
                this.calTotal()
            }
        },
        methods:{
            hanldeCheckout(){
                let url = this.isLogin?'/checkout':'/guest/checkout'
                if(this.data.length>0){
                    this.addItem(this.data,url)
                }else{
                    this.$router.push(url)
                }
            },
            addItem(data,url){
                this.$store.dispatch('addCartTotal', { 
                    subtotal:this.subtotal.toFixed(2),
                    total:this.total.toFixed(2),
                    delivery_total: this.delivery_total.toFixed(2),
                    discount_total: this.discount_total.toFixed(2),
                })
                this.$router.push(url)
            },
            getCart(){
                this.isLoading = true
                axios.get(`/api/user/cart`)
                .then(res=>{
                    this.data = res.data.items
                    this.total = parseFloat(res.data.total.total)
                    this.subtotal = parseFloat(res.data.total.subtotal)
                    this.discount_total = parseFloat(res.data.total.discount_total)
                    this.delivery_total = parseFloat(res.data.total.delivery_total)
                })
                .catch(err=>{})
                .finally(res=>{
                    this.isLoading = false
                })
            },
            // 统计原价总金额
            calTotal(){
                this.total = this.subtotal = this.discount_total = this.delivery_total = 0
                for (var i = 0; i < this.data.length; i++) {
                    if(this.data[i].discount_total>0){
                        this.total+=parseFloat(this.data[i].subtotal - this.data[i].discount_total)

                        this.discount_total += parseFloat(this.data[i].discount_total)
                    }else{
                        this.total+=parseFloat(this.data[i].subtotal)
                    }
                    if(this.data[i].delivery_total>0){
                        this.delivery_total += parseFloat(this.data[i].delivery_total)
                    }
                    this.subtotal += parseFloat(this.data[i].subtotal)
                }

                this.total = this.total + this.delivery_total
            },
            imageActive(url){
                this.imageModalActive=true
                this.imageUrl = `/storage/${url}`
            },
            // 清除购物车
            clearAll(){
                this.isLoading = true
                if(this.isLogin){
                    axios.get('/api/user/cart/empty')
                    .then(res=>{
                        this.total = this.subtotal = this.discount_total = 0
                        this.data = []
                        this.toastMessage='Empty cart successfully'
                        this.toastType='is-success'
                        this.$emit('reloadHeader')
                    })
                    .catch(err=>{
                        this.toastMessage='Empty cart failed'
                        this.toastType='is-danger'
                    })
                    .finally(res=>{
                        this.dispalyNotification()
                        this.isLoading = false
                    })
                }else{
                    this.$store.dispatch('clearCart')
                    .then(res=>{
                        this.data = []
                        this.toastMessage='Clear Cart Successfully'
                        this.toastType='is-success'
                    })
                    .catch(err=>{
                        this.toastMessage='Clear Cart failed'
                        this.toastType='is-danger'
                    })
                    .finally(res=>{
                        this.dispalyNotification()
                        this.isLoading = false
                    })
                }
                this.total = 0
            },
            // 删除单个产品
            deleleFromCart(index,id){
                this.isLoading = true
                if(this.isLogin){
                    axios.delete(`/api/user/cart/${id}`)
                    .then(res=>{
                        this.data.splice(index,1)
                        this.total = parseFloat(res.data.total)
                        this.subtotal = parseFloat(res.data.subtotal)
                        this.discount_total = parseFloat(res.data.discount_total)
                        this.$emit('reloadHeader')
                        this.toastMessage='Delete from cart successfully'
                        this.toastType='is-success'
                    })
                    .catch(err=>{
                        this.toastMessage='Delete from cart failed'
                        this.toastType='is-danger'
                    })
                    .finally(res=>{
                        this.dispalyNotification()
                        this.isLoading = false
                    })
                }else{
                    this.$store.dispatch('deleteFromCart',id)
                    .then(res=>{
                        this.toastMessage='Delete from cart successfully'
                        this.toastType='is-success'
                    })
                    .catch(err=>{
                        this.toastMessage='Delete from cart failed'
                        this.toastType='is-danger'
                    })
                    .finally(res=>{
                        this.dispalyNotification()
                        this.isLoading = false
                    })
                }
                this.calTotal()
            },
            // 更新产品数量
            changeQuantity(index,quantity,id){
                this.isLoading = true
                if(this.isLogin){
                    axios.patch(`/api/user/cart/${id}`,{
                        'num':quantity,
                    })
                    .then(res=>{
                        Object.keys(res.data.item).map(field=>{
                            this.data[index][field] = res.data.item[field]
                        })
                        this.total = parseFloat(res.data.cart.total)
                        this.subtotal = parseFloat(res.data.cart.subtotal)
                        this.discount_total = parseFloat(res.data.cart.discount_total)
                        this.toastMessage='Update cart successfully'
                        this.toastType='is-success'
                    })
                    .catch(err=>{
                        if (err.response.data===1) {
                            this.toastMessage='Out of stock'
                        }else{
                            this.toastMessage=err.response.data
                        }
                        this.toastType='is-danger'
                    })
                    .finally(res=>{
                        this.dispalyNotification()
                        this.isLoading = false
                    })
                }else{
                    this.$store.dispatch('changeQuantity',{
                        'quantity':quantity,
                        'id':id,
                    })
                    .then(res=>{
                        this.calTotal()
                        this.toastMessage='Update cart successfully'
                        this.toastType='is-success'
                    })
                    .catch(err=>{
                        if(err===1){
                            this.toastMessage='Out of stock'
                        }else{
                            this.toastMessage='Update cart failed'
                        }
                        this.toastType='is-danger'
                    })
                    .finally(res=>{
                        this.dispalyNotification()
                        this.isLoading = false
                    })
                }
            },
            dispalyNotification(){
                this.$buefy.notification.open({
                    duration: 3000,
                    message: this.toastMessage,
                    position: 'is-top-right',
                    type: this.toastType,
                    hasIcon: true
                })
            }
        }
    }
</script>