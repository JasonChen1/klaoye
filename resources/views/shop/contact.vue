<template>
    <div class="row contact-wp">
        <div class="col-md-6 contact-lt">
            <h1 class="mb-2">Contact Us</h1>
            <p>
            “KALOYE” is committed to pursuing a high-quality, petty-bourgeois lifestyle, and is committed to providing consumers with quality small appliances and furniture, so that consumers can have a higher and more comfortable quality of life at an economic cost.
            </p>
            <br>
            <p>
            We also offer detailed pre-sale and after-sale service to build a more friendly relationship with you.
            </p>

            <p>If you have any questions, please contact</p><br>

            <p>
                Email: 
                <a href = "mailto: klaoyeservice@gmail.com">klaoyeservice@gmail.com</a>
            </p>
            <p>
                Facebook: 
                <a href="https://www.facebook.com/Klaoyelife-103192698672359">Klaoye</a>
            </p>

        </div>
        <div class="col-md-6">
            <b-notification :closable="false" class="form-notification">
                <div class="contact-form-wp">
                    <form class="contact-form">
                        <h1 class="mb-2">Enquiries</h1>
                        <div class="form-row">
                            <div class="form-group text-left col-md-6">
                                <label>Name</label>
                                <b-field>
                                    <b-input type="text" class="" placeholder="Name" v-model="form.name"  @focus="errors.name=''" required></b-input>
                                </b-field>
                                <span class="err d-flex" v-if="errors.name" >
                                    {{errors.name[0]}}
                                </span>
                            </div>
                            <div class="form-group text-left col-md-6">
                                <label>Email</label>
                                <b-field>
                                    <b-input type="email" class="" placeholder="Email" v-model="form.email"  @focus="errors.email=''" required></b-input>
                                </b-field>
                                <span class="err d-flex" v-if="errors.email" >
                                    {{errors.email[0]}}
                                </span>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group text-left col-md-6">
                                <label>Phone</label>
                                <b-field>
                                    <b-input type="numeric" class="" v-model="form.phone"  @focus="errors.phone=''" placeholder="Phone"></b-input>
                                </b-field>
                                <span class="err d-flex" v-if="errors.phone" >
                                    {{errors.phone[0]}}
                                </span>
                            </div>
                            <div class="form-group text-left col-md-6">
                                <label>Product</label>
                                <b-field>
                                    <b-input type="text" class="" placeholder="Product name/code" v-model="form.product"  @focus="errors.product=''" required></b-input>
                                </b-field>
                                <span class="err d-flex" v-if="errors.product" >
                                    {{errors.product[0]}}
                                </span>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group text-left col-md-12">
                                <label>How may we help you?</label>
                                <b-field>
                                    <b-input maxlength="800" type="textarea" v-model="form.message"  @focus="errors.message=''" placeholder="Message" required></b-input>
                                </b-field>
                                <span class="err d-flex" v-if="errors.message" >
                                    {{errors.message[0]}}
                                </span>
                            </div>
                        </div>
                        <div class="form-foot">
                            <button  class="btn btn-black lg w-100" @click.prevent="formSubmit">Submit</button>
                        </div>
                    </form>
                </div>
                <b-loading :is-full-page="false" :active.sync="isLoading" :can-cancel="false"></b-loading>
            </b-notification>
        </div>
    </div>
</template>

<script>
    import Form from '@form/form'
    export default {
        name: 'Contact',
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
                form: new Form({
                    name:'',
                    email:'',
                    product:'',
                    phone:'',
                    message:'',
                }),
                errors:[],
                isLoading:false,
                toastMessage:'',
                toastType:'',
                toastPosition:'is-top',
            }
        },
        methods:{
            formSubmit(){
                this.isLoading = true;
                this.form.submit('post',`/api/enquiry`)
                .then(res=>{
                    this.toastMessage = "Message submit successfully"
                    this.toastType = 'is-success'
                })
                .catch(err=>{
                   this.toastMessage = "Message submit failed"
                    this.toastType = 'is-danger'
                    this.errors = err
                })
                .finally(res=>{
                   this.$buefy.toast.open({
                        duration: 3000,
                        message: this.toastMessage,
                        position: this.toastPosition,
                        type: this.toastType
                    })
                    this.isLoading = false
                })
            }
        }
    }
</script>