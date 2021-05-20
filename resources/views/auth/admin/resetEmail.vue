<template>
    <div>
        <v-credential-form>
            <template v-slot:form>
                <b-notification :closable="false" class="admin-form-notification">
                    <div class="center-contaniner">
                        <form class="admin-form">
                            <h1 class="mb-2">Reset Admin Password</h1>
                            <hr>
                            <div class="form-group ">
                                <b-input 
                                    type="email" 
                                    v-model="email" 
                                    placeholder="Email*" 
                                    icon="account"
                                    required>
                                </b-input>
                                <span class="err text-left d-flex" v-if="errors.email" >
                                    {{errors.email[0]}}
                                </span>
                            </div>
                            <button class="btn btn-primary" @click.prevent="sendResetEmail">Send Reset Email</button>
                        </form>
                    </div>
                    <b-loading :is-full-page="false" :active.sync="isLoading" :can-cancel="false"></b-loading>
                </b-notification>
            </template>
        </v-credential-form>
    </div>
   
</template>

<script>
    import CredentialForm from '@layout/admin/credentialForm.vue'
    export default {
        name: 'AdminEmail',
        components: {
            'v-credential-form':CredentialForm,
        },
        data() {
            return {
                email:'',
                errors:[],
                isLoading:false,
                toastMessage:'',
                toastType:'',
                toastPosition:'is-top',
                isLoading:false,
            }
        },
        methods:{
            sendResetEmail(){
                this.isLoading = true;
                axios.post('/api/admin/password/email',{email: this.email})
                .then(res => {
                    this.$buefy.toast.open({
                        duration: 3000,
                        message: 'Email successfully sent',
                        position: this.toastPosition,
                        type: 'is-success'
                    })
                    
                })
                .catch(err => {
                    if(err.response.status===422){
                        this.errors = err.response.data.errors
                    }else{
                        this.$buefy.toast.open({
                            duration: 3000,
                            message: 'Failed to send email',
                            position: this.toastPosition,
                            type: 'is-danger'
                        })
                    }
                })  
                .finally(res=>{
                    this.isLoading = false
                })
            }
        }
    }
</script>