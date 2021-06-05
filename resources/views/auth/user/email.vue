<template>
    <div>
        <b-notification :closable="false" class="form-notification">
            <div class="reset-email-form-wrapper">
                <form class="reset-email-form">
                    <h1 class="mb-2">Send Reset Password Email</h1>
                    <hr>
                    <div class="form-group ">
                        <b-field>
                            <b-input 
                                type="email" 
                                v-model="email" 
                                placeholder="Email*"
                                icon="email"
                                required>
                            </b-input>
                        </b-field>
                        <span class="err text-left d-flex" v-if="errors.email" >
                            {{errors.email[0]}}
                        </span>
                    </div>
                    <button class="btn btn-primary" @click.prevent="sendResetEmail">Send</button>
                </form>
            </div>
            <b-loading :is-full-page="false" :active.sync="isLoading" :can-cancel="false"></b-loading>
        </b-notification>
    </div>
</template>

<script>
    export default {
        name: 'Email',
        components: {
        },
        data() {
            return {
                email:'',
                errors:[],
                isLoading:false,
                toastMessage:'',
                toastType:'',
                toastPosition:'is-top',
            }
        },
        methods:{
            sendResetEmail(){
                this.isLoading = true;
                axios.post('/api/user/password/email',{email: this.email})
                .then(res => {
                    this.toastMessage = 'Reset Password Email Sent Successfully'
                    this.toastType = 'is-success'
                })
                .catch(errors => {
                    this.toastMessage = 'Reset Password Email Sent Failed'
                    this.toastType = 'is-danger'
                    this.errors = errors.response.data.errors
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