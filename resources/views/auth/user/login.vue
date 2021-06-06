<template>
    <div>
        <div class="auth-wp  section-wp">
            <b-notification :closable="false" class="form-notification">
                <div class="auth-form-wrapper">
                    <form class="login-form">
                        <h1 class="auth-head">Login</h1><br>
                        <div class="form-group ">
                            <b-field>
                                <b-input 
                                    type="email" 
                                    v-model="email" 
                                    placeholder="Email*" 
                                    icon="account"
                                    required>
                                </b-input>
                            </b-field>
                            <span class="err text-left d-flex" v-if="errors.email" >
                                {{errors.email[0]}}
                            </span>
                        </div>
                        <div class="form-group ">
                            <b-field>
                                <b-input 
                                    type="password" 
                                    v-model="password" 
                                    placeholder="Password*" 
                                    icon="lock"
                                    required>
                                </b-input>
                            </b-field>
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
                                    <router-link class="forgot-link small-text" to="/password/reset">
                                        Forgot Passowrd?
                                    </router-link>
                                </span><br>
                                <span>
                                    <router-link class="forgot-link small-text" to="/register">
                                        Register Account
                                    </router-link>
                                </span>
                            </div>
                        </div>
                    </form>
                </div>
                <b-loading :is-full-page="false" :active.sync="isLoading" :can-cancel="false"></b-loading>
            </b-notification>
        </div>
    </div>
</template>

<script>
    export default {
        name: 'auth',
        components: {
        },
        data() {
            return {
                email:'',
                password:'',
                remember:false,
                isLoading:false,
                errors:[],
            }
        },
        mounted(){
            if(this.$cookie.get('token_u')){
                this.$router.push('/dashboard')
            }
        },
        methods:{
            login(){
                this.isLoading = true
                axios.post(`/api/user/login`,{
                    email: this.email,
                    password: this.password,
                    remember_me: this.remember
                })
                .then(res=>{
                    this.$store.dispatch('userCart')
                    this.$store.dispatch('setLoggedIn', res)
                    this.$router.go('/')
                })
                .catch(err=>{
                    if(err.response.status===401){
                        this.errors = err.response.data.errors
                        this.$cookie.delete('token_u')
                        delete axios.defaults.headers.common['Authorization']
                        this.$buefy.toast.open({
                            duration: 2000,
                            message: 'Login Failed',
                            position: 'is-top',
                            type: 'is-info'
                        })
                    }else{
                        this.errors = err.response.data.errors
                    }
                })
                .finally(res=>{
                    this.isLoading = false
                })
            }
        }
    }
</script>