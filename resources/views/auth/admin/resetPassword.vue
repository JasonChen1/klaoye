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
                            <div class="form-group ">
                                <b-input 
                                    type="password" 
                                    v-model="password" 
                                    placeholder="Password*" 
                                    icon="lock"
                                    required>
                                </b-input>
                                <span class="err text-left d-flex" v-if="errors.password" >
                                    {{errors.password[0]}}
                                </span>
                            </div>
                            <div class="form-group ">
                                <b-input 
                                    type="password" 
                                    v-model="confirm" 
                                    placeholder="Confirm Password*" 
                                    icon="lock"
                                    required>
                                </b-input>
                                <span class="err text-left d-flex" v-if="errors.password" >
                                    {{errors.password[0]}}
                                </span>
                            </div>
                            <button class="btn btn-primary" @click.prevent="resetPassword">Reset</button>
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
        name: 'AdminReset',
        components: {
            'v-credential-form':CredentialForm,
        },
        data() {
            return {
                email:'',
                password:'',
                confirm:'',
                errors:[],
                isLoading:false,
                toastMessage:'',
                toastType:'',
                toastPosition:'is-top',
            }
        },
        methods:{
            resetPassword(){
                this.isLoading = true;
                axios.post('/api/admin/password/reset',{
                    token: this.$route.params.token,
                    email: this.email,
                    password: this.password,
                    password_confirmation: this.confirm
                })
                .then(res => {
                    this.$buefy.toast.open({
                        duration: 3000,
                        message: 'Reset password successfully',
                        position: this.toastPosition,
                        type: 'is-success'
                    })
                    this.$router.push('/admin/login')
                })
                .catch(err => {
                    if(err.response.status===422){
                        this.errors = err.response.data.errors
                    }else{
                        this.$buefy.toast.open({
                            duration: 3000,
                            message: 'Reset password failed',
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
