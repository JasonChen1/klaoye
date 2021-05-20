<template>
    <div>
        <v-credential-form>
            <template v-slot:form>
                <b-notification :closable="false" class="admin-form-notification">
                    <div class="center-contaniner">
                        <form class="admin-form">
                            <h1 class="mb-2">Admin Login</h1>
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
                                <span class="err text-left d-flex" v-if="errors.message" >
                                    {{errors.message}}
                                </span>
                            </div>
                            <button class="btn btn-primary" @click.prevent="login">Login</button>
                            <div class="form-row pt-3">
                                <div class="form-group col-md-6">
                                    <b-checkbox v-model="remember" native-value="Remember Me" class="remember-check">
                                        Remember Me
                                    </b-checkbox>
                                </div>
                                <div class="form-group col-md-6">
                                    <span> 
                                        <router-link class="forgot-link small-text" to="/admin/password/reset">
                                            Forgot Passowrd?
                                        </router-link>
                                    </span>
                                </div>
                            </div>
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
        name: 'AdminAuth',
        components: {
            'v-credential-form':CredentialForm,
        },
        data() {
            return {
                errors:[],
                email:'',
                password:'',
                remember:false,
                isLoading:false,
            }
        },
        mounted(){
            if(this.$cookie.get('token_a')){
                this.$router.push('/admin')
            }
        },
        methods:{
            login(){
                this.isLoading = true
                axios.post('/api/admin/login',{
                    email: this.email,
                    password: this.password,
                    remember_me: this.remember,
                    returnSecureToken: true      
                })
                .then(res => {
                    this.$store.dispatch('setLoggedIn', res)
                    this.$router.go('/admin')
                })
                .catch(err => {
                    if(err.response.status===401){
                        this.$cookie.delete('token_a')
                        delete axios.defaults.headers.common['Authorization']
                        this.$buefy.toast.open({
                            duration: 2000,
                            message: 'Credential incorrect',
                            position: 'is-top',
                            type: 'is-danger'
                        })
                    }else if(err.response.status===404){
                        this.$buefy.toast.open({
                            duration: 2000,
                            message: 'Credential not found',
                            position: 'is-top',
                            type: 'is-danger'
                        })
                    }else{
                        this.errors = err.response.data.errors
                    }
                })
                .finally(res=>{
                    this.isLoading = false
                })
            },
        }
    }
</script>