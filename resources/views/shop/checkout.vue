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
                                <div v-if="item.images.length>0" v-lazy-container="{ selector: 'img' }" >
                                    <img :data-src="`/storage/thumbnail/${item.images[0].image_url}`">
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
                        paypal img - todo
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
                                            icon="google-maps"
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
                                            placeholder="Country" 
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

         <b-modal 
                :active.sync="showPaymentModal" 
                :width="350"
                :can-cancel="['escape', 'x']">
            <div class="modal-header">
                <h1 class="modal-title">Payment</h1>
                <button type="button" class="close" @click.prevent="showPaymentModal=false">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <b-notification :closable="false"  class="payment-form-wp" >
                <div class="modal-body" >
                    <div class="modal-body text-center" >
                        <div >
                            <div class="cost-wp">
                                <strong>Subtotal:</strong>
                                <div class="price-tag-wp">
                                    <strong >$ {{subtotal}}</strong>
                                </div>
                            </div>
                            <div class="cost-wp" >
                                <strong>Delivery Cost:</strong>
                                <div class="price-tag-wp">
                                    <strong>$ {{ delivery_total }}</strong>
                                </div>
                            </div>
                            <div class="cost-wp" >
                                <strong>Discount:</strong>
                                <div class="price-tag-wp">
                                    <strong>$ {{ discount_total }}</strong>
                                </div>
                            </div>
                            <div class="cost-wp">
                                <strong>Total:</strong>
                                <div class="price-tag-wp">
                                    <strong>$ {{ total }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" @click="showPaymentModal=false">Cacncel Payment</button>
                    <button type="button" class="btn btn-primary" @click.prevent="submitPayment">Pay</button>
                </div>
                <b-loading :is-full-page="false" :active.sync="isLoading" :can-cancel="false"></b-loading>
            </b-notification>
        </b-modal>
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
                }),
                directCheckout:null,
                delivery_total:0,
                discount_total:0,
                subtotal:0,
                total:0,
                // cart
                cartData:[],
                // stripe
                stripe:null,
                card:null,
                stripeRes:{},
                showPaymentModal:false,
                showPaymentLoading:false,
            }
        },
        mounted(){
            this.cartData = this.$store.getters.carts
            let totals = this.$store.getters.cartTotal
            this.total = totals.total
            this.subtotal = totals.subtotal
            this.discount_total = totals.discount_total
            this.delivery_total = totals.delivery_total

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
            checkout(){
                if(!this.form.email || !this.form.name || !this.form.address || !this.form.city || !this.form.postal_code){
                    this.error = 'Please fill in your email and shipping address.'
                    return
                }
                this.showPaymentLoading = true
                let self = this
                self.stripe.createToken(self.card).then(function(result) {
                    if (result.error) {
                        var errorElement = document.getElementById('card-errors');
                        errorElement.textContent = result.error.message;
                        self.showPaymentLoading = false
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
                            // self.showPaymentModal = true
                            self.submitPayment()
                        })
                        .catch(err=>{
                            self.disErr(err)
                        })
                        .finally(res=>{
                            self.showPaymentLoading = false
                        })
                    }
                });
            },
            submitPayment(){
                this.isLoading = true
                let self = this
                self.stripe.confirmCardPayment(self.stripeRes.client_secret, {
                    payment_method: {
                        card: self.card,
                        billing_details: {
                            name: `New Order - ${self.$store.getters.username}`
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
                    // this.$store.dispatch('clearCart')
                    this.errors=[]
                    this.$router.push(`/checkout/success`)
                })
                .catch(err=>{
                    console.log(err)
                })
            },
            setError(val){
                this.$buefy.notification.open({
                    duration: 3000,
                    message: val,
                    position: 'is-top-right',
                    type: 'is-danger',
                    hasIcon: true
                })
                this.error = val
            }
        }
    }
</script>