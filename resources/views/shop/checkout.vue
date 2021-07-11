<template>
    <div class="container" :class="isMobile?'mt-5':''">
        <h1><strong>Checkout</strong></h1>
        <b-notification :closable="false" >
            <div class="guest-checkout-wp" :class="isMobile?'':'row'">
                <div :class="isMobile?'guest-address-wp-mb':'guest-address-wp col-md-6'">
                    <div class="address-head ">
                        <strong>Total</strong>
                        <h1 class="pb-3">$ {{total}}</h1>
                    </div>
                    <div class="prod-tb">
                        <div class="prod" v-for="(item,i) in cartData" :key="i">
                            <div class="p-img">
                                <div v-if="item.image" v-lazy-container="{ selector: 'img' }" >
                                    <img :data-src="`/storage/thumbnail/${item.image.image_url}`">
                                </div>
                                <div v-else v-lazy-container="{ selector: 'img' }" >
                                    <img :data-src="`/public/errorImage.jpg`">
                                </div>
                            </div>
                            <div class="prod-des pl-2">
                                <strong class="p-name">
                                    Name: {{item.name}}
                                </strong>
                                <div class="cart-color-wp" v-if="item.color_code">
                                    <strong >Colour:</strong>
                                    <p class="cb ml-2" :style="`background: ${item.color_code};`"></p>
                                </div>
                                <div>
                                    Qty: {{item.num}}
                                </div>
                            </div>
                            <div class="sub-wp">
                                <div class="sub-wp-inner">
                                    <p v-if="item.discount>0"> 
                                        ${{item.subtotal-item.discount_total}}<br>
                                        <s>${{ item.subtotal }}</s>
                                    </p>
                                    <p v-else> ${{ item.subtotal }}</p>
                                    <p class="unit-line">${{item.price}} each</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="total-wp ">
                        <div class="price-wp">
                            <h2>Subtotal</h2> 
                            <h2 class="price"> ${{subtotal}}</h2>
                        </div>

                        <div class="price-wp">
                            <h2>Delivery Cost</h2> 
                            <h2 class="price"> ${{delivery_total}}</h2>
                        </div>

                        <div class="price-wp">
                            <h2>Discount</h2> 
                            <h2 class="price"> ${{discount_total}}</h2>
                        </div>

                        <div class="price-wp">
                            <h2>Total</h2> 
                            <h2 class="price"> ${{total}}</h2>
                        </div>
                    </div>
                </div>
                <hr v-if="isMobile" class="hr">
                <div class="guest-checkout-method-wp col-md-6">
                    <div class="paypal-wp">
                        <div id="smart-button-container">
                            <div style="text-align: center; z-index: 1; position: relative;">
                                <div id="paypal-button-container"></div>
                            </div>
                        </div>
                    </div>

                    <div class="divider">
                        <hr>
                        <p class="divider-text Text-color--gray400">
                            Or pay with card
                        </p>
                    </div>
                    
                    <h2 class="pb-3">Shipping Information</h2>
                    
                    <div class="stripe-form">
                        <form class="address-form" @click="error=''">
                            <label class="mb-2">Email</label>
                            <div class="form-group">
                                <b-field>
                                    <b-input 
                                        type="email" 
                                        v-model="form.email" 
                                        placeholder="Email*" 
                                        icon="email"
                                        required>
                                    </b-input>
                                </b-field>
                            </div>
                            <label class="mb-2">Shipping Address</label>
                            <div class="form-row">
                                <div class="col pr-0">
                                    <b-field>
                                        <b-input 
                                            type="text" 
                                            v-model="form.name" 
                                            placeholder="Name*" 
                                            icon="account"
                                            required
                                            >
                                        </b-input>
                                    </b-field>
                                </div> 
                                <div class="col pl-0">
                                    <b-field>
                                        <b-input 
                                            type="text" 
                                            v-model="form.phone" 
                                            placeholder="Phone" 
                                            icon="phone"
                                            >
                                        </b-input>
                                    </b-field>
                                </div>
                            </div>
                            <div class="form-group mb-0">
                                <b-field>
                                    <b-input 
                                        type="text" 
                                        v-model="form.address" 
                                        placeholder="Address*" 
                                        icon="google-maps"
                                        required
                                        >
                                    </b-input>
                                </b-field>
                            </div>
                            <div class="form-row">
                                <div class="col pr-0">
                                    <b-field>
                                        <b-input 
                                            type="text" 
                                            v-model="form.city" 
                                            placeholder="City*" 
                                            icon="google-maps"
                                            required
                                            >
                                        </b-input>
                                    </b-field>
                                </div> 
                                <div class="col pl-0">
                                    <b-field>
                                        <b-input 
                                            type="text" 
                                            v-model="form.postal_code" 
                                            placeholder="Postal Code*" 
                                            icon="google-maps"
                                            required
                                            >
                                        </b-input>
                                    </b-field>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="col pr-0">
                                    <b-field>
                                        <b-input 
                                            type="text" 
                                            v-model="form.state" 
                                            placeholder="State" 
                                            icon="google-maps"
                                            >
                                        </b-input>
                                    </b-field>
                                </div> 
                                <div class="col pl-0">
                                    <b-field>
                                        <b-input 
                                            type="text" 
                                            v-model="form.country" 
                                            placeholder="Country*" 
                                            icon="google-maps"
                                            required
                                            >
                                        </b-input>
                                    </b-field>
                                </div>
                            </div>
                        </form>    
                        <h2 class="pb-3 pt-5">Payment Details</h2>
                        <div class="checkout-content-wp">
                            <label for="card-element">
                                Card Infomation
                            </label>
                            <div id="card-element" class="form-control" style='height: 2.4em; padding-top: .7em;'></div>
                            <div id="card-errors" role="alert"></div>
                        </div>
                    </div>
                    <div class="pt-3">
                        <button class="btn btn-black lg w-100" @click="checkout()">Pay ${{total}}</button>
                    </div>
                    <span class="err text-left d-flex pt-3" v-if="error" >
                        {{error}}
                    </span>
                </div>
            </div>
            <b-loading :is-full-page="false" :active.sync="isLoading" :can-cancel="false"></b-loading>
        </b-notification>

    </div>
</template>

<script>
    import Form from '@form/form'
    export default {
        name: 'GuestCheckout',
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
                isLoading:false,
                error:'',
                // toast
                toastMessage:'',
                toastPosition:'is-top',
                toastType:'',
                // payment form
                form: new Form({
                    stripeToken:'',
                    prod:null,
                    order_no:null,
                    color:null,
                    cartData:null,
                    // address
                    email:'',
                    phone:'',
                    name:'',
                    address:'',
                    city:'',
                    state:'',
                    country:'',
                    postal_code:'',
                    // paypal
                    pStatus:'',
                }),
                directCheckout:null,
                delivery_total:0,
                discount_total:0,
                subtotal:0,
                total:0,
                // cart
                cartData:[],
                singleProd:false,
                // stripe
                stripe:null,
                card:null,
                stripeRes:{},
            }
        },
        mounted(){ 
            this.initPayPalButton()
            if(this.$route.query.prod){
                this.singleProd = true
                this.getProdcut(this.$route.query)
            }else{
                this.cartData = this.$store.getters.carts
                let totals = this.$store.getters.cartTotal
                this.total = totals.total
                this.subtotal = totals.subtotal
                this.discount_total = totals.discount_total
                this.delivery_total = totals.delivery_total
            }
            this.stripe = Stripe('pk_test_51J2d97FkeXjw08GhyB6jSpj8ZHkwIYxtomInAJsLNjleturgCUrPpvSyzq2wq5MADrFQAEChtDFri2Pbch4eRzpn00DduYcMvV')

            var elements = this.stripe.elements();
            var style = {
                base: {
                    color: '#32325D',
                    fontWeight: 500,
                    fontFamily: 'Source Code Pro, Consolas, Menlo, monospace',
                    fontSize: '16px',
                    fontSmoothing: 'antialiased',
                    '::placeholder': {
                        color: '#CFD7DF',
                    },
                    ':-webkit-autofill': {
                        color: '#e39f48',
                    },
                },
                invalid: {
                    color: '#E25950',
                    '::placeholder': {
                        color: '#FFCCA5',
                    },
                },
            };
            this.card = elements.create('card');
            this.card.mount('#card-element');
        },
        methods:{
            getProdcut(query){
                axios.get(`/api/products/${query.prod}`)
                .then(res=>{
                    this.cartData = [res.data]
                    this.cartData[0]['color_code'] = query.color_code?'#'+query.color_code:''
                    this.cartData[0]['color_id'] = query.color_id
                    this.cartData[0]['image'] = res.data.images[0]
                    this.cartData[0]['num'] = 1
                    this.total = parseFloat(res.data.price)-parseFloat(res.data.discount)+parseFloat(res.data.delivery)
                    this.subtotal = this.cartData[0]['subtotal'] = parseFloat(res.data.price)
                    this.discount_total = parseFloat(res.data.discount)
                    this.delivery_total = parseFloat(res.data.delivery)
                })
                .catch(err=>{
                    console.log(err)
                })
            },
            initPayPalButton() {
                let self = this
                paypal.Buttons({
                    style: {
                        shape: 'pill',
                        color: 'blue',
                        layout: 'horizontal',
                        label: 'checkout',
                    },
                    createOrder: function(data, actions) {
                        return actions.order.create({
                            purchase_units: [
                            {
                                "description":"Klaoye - Checkout",
                                "amount":{
                                    "currency_code":"USD",
                                    "value":self.total
                                }
                            }
                            ]
                        });
                    },
                    onApprove: function(data, actions) {
                        return actions.order.capture().then(function(details) {
                            // paypal checkout 
                            self.form.prod = self.singleProd
                            self.form.color = self.colorId
                            self.form.order_no = self.order_no
                            self.form.cartData = self.cartData
                            self.form.delivery = self.selectedDelivery
                            // address
                            self.form.email = details.payer.email_address
                            self.form.name = details.purchase_units[0].shipping.name.full_name
                            self.form.address = details.purchase_units[0].shipping.address.address_line_1
                            self.form.state = details.purchase_units[0].shipping.address.admin_area_1
                            self.form.city = details.purchase_units[0].shipping.address.admin_area_2
                            self.form.country = details.purchase_units[0].shipping.address.country_code
                            self.form.postal_code = details.purchase_units[0].shipping.address.postal_code
                            self.form.pStatus = details.status
                            self.form.submit('post','/api/paypal/checkout')
                            .then(res=>{
                                if(!self.singleProd){
                                    self.$store.dispatch('clearCart')
                                }
                                self.$router.push(`/checkout/success`)
                            })
                            .catch(err=>{
                                self.disErr(err)
                            })
                        });
                    },
                    onError: function(err) {
                        self.disErr(err)
                    }
                }).render('#paypal-button-container');
            },
            checkout(){
                if(!this.form.email || !this.form.name || !this.form.address || !this.form.city || !this.form.postal_code || !this.form.country){
                    this.error = 'Please fill in your email and shipping address.'
                    return
                }
                this.isLoading = true
                let self = this
                self.stripe.createToken(self.card).then(function(result) {
                    if (result.error) {
                        var errorElement = document.getElementById('card-errors');
                        errorElement.textContent = result.error.message;
                        self.isLoading = false
                    } else {
                        self.form.stripeToken = result.token.id
                        self.form.prod = self.directCheckout
                        self.form.color = self.colorId
                        self.form.order_no = self.order_no
                        self.form.cartData = self.cartData
                        self.form.delivery = self.selectedDelivery
                        self.form.submit('post','/api/checkout')
                        .then(res=>{
                            self.stripeRes = res
                            self.submitPayment()
                        })
                        .catch(err=>{
                            self.error = 'Please fill in your email and shipping address.'
                            self.disErr(err)
                            self.isLoading = false
                        })
                    }
                });
            },
            submitPayment(){
                let self = this
                self.stripe.confirmCardPayment(self.stripeRes.client_secret, {
                    payment_method: {
                        card: self.card,
                        billing_details: {
                            name: `New Order`
                        }
                    }
                }).then(function(result) {
                    if (result.error) {
                        this.$buefy.notification.open({
                            duration: 3000,
                            message: result.error.message,
                            position: 'is-top-right',
                            type: 'is-danger',
                            hasIcon: true
                        })
                    } else {
                        if (result.paymentIntent.status === 'succeeded') {
                            self.paymentCallback()
                        }
                    }
                });
            },
            paymentCallback(){
                axios.post(`/api/stripe/callback`,this.stripeRes)
                .then(res=>{
                    this.isLoading = false
                    if(!this.singleProd){
                        this.$store.dispatch('clearCart')
                    }
                    this.error=[]
                    this.$router.push(`/checkout/success`)
                })
                .catch(err=>{
                    console.log(err)
                })
            },
            disErr(err){
                this.$buefy.notification.open({
                    duration: 3000,
                    message: err,
                    position: 'is-top-right',
                    type: 'is-danger',
                    hasIcon: true
                })
            },
        }
    }
</script>