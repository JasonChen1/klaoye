<template>
    <div class="container" :class="isMobile?'mt-5':''">
        <h1><strong>Fill In Checkout Details</strong></h1>
        <b-notification :closable="false" >
            <div class="guest-checkout-wp" :class="isMobile?'':'row'">
                <div :class="isMobile?'guest-address-wp-mb':'guest-address-wp col-md-6'">
                    <div class="address-head ">
                        <h2 class="pb-3">Shipping Address</h2>
                    </div>
                    <div class="address-wp">
                        <form class="address-form" @click="error=''">
                            <div class="form-group">
                                <b-field>
                                    <b-input 
                                        type="text" 
                                        v-model="form.firstname" 
                                        placeholder="First Name*" 
                                        icon="account"
                                        required
                                        >
                                    </b-input>
                                </b-field>
                            </div>
                            <div class="form-group ">
                                <b-field>
                                    <b-input 
                                        type="text" 
                                        v-model="form.lastname" 
                                        placeholder="Last Name" 
                                        icon="account"
                                        >
                                    </b-input>
                                </b-field>
                            </div>
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
                            <div class="form-group">
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
                            <div class="form-group">
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
                            <div class="form-group">
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
                            <div class="form-group">
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
                            <div class="form-group">
                                <b-field>
                                    <b-input 
                                        type="text" 
                                        v-model="form.country" 
                                        placeholder="Country" 
                                        icon="google-maps"
                                        >
                                    </b-input>
                                </b-field>
                            </div>
                            <div class="form-group">
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
                        </form>
                    </div>
                </div>
                <hr v-if="isMobile" class="hr">
                <div class="guest-checkout-method-wp col-md-6">
                    <div class="checkout-head ">
                        <h2 class="pb-3">Payment</h2>
                    </div>
                    <div>
                        <p><strong>Subtotal</strong> $</p>
                        <p><strong>Delivery Cost</strong> $</p>
                        <p><strong>Discount</strong> $</p>
                        <p><strong>Total</strong> $</p>
                    </div>
                    <div class="row">
                        <div :class="`col-md-6`">
                            <div class="checkout-content-wp">
                                <label for="card-element">
                                    Credit/Debit Card
                                </label>
                                <div id="card-element" class="form-control" style='height: 2.4em; padding-top: .7em;'></div>
                                <div id="card-errors" role="alert"></div>
                            </div>
                        </div>
                    </div>
                    <div class="pt-3">
                        <button class="btn btn-black lg" @click="checkout()">Submit</button>
                    </div>
                    <span class="err text-left d-flex pl-3 pt-3" v-if="error" >
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
        name: 'Guest Checkout',
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
                isMobile:window.isMobile,
                isLoading:false,
                // address
                form: {
                    email:'',
                    firstname:'',
                    lastname:'',
                    address1:'',
                    city:'',
                    state:'',
                    country:'',
                    phone:'',
                    postal_code:'',
                },
                error:'',
                // toast
                toastMessage:'',
                toastPosition:'is-top',
                toastType:'',
                // payment form
                paymentForm: new Form({
                    stripeToken:'',
                    prod:null,
                    order_no:null,
                    shipping_address_id:null,
                    shippingAddress:null,
                    color:null,
                    cartData:null,
                    delivery:0,
                }),
                directCheckout:null,
            }
        },
        methods:{
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