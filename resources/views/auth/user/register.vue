<template>
    <div>
        <div class="auth-wp  section-wp">
            <b-notification :closable="false" class="form-notification">
                <div class="auth-form-wrapper">
                    <form class="register-form">
                        <h1 class="auth-head">Register</h1><br>
                        <div class="form-group ">
                            <b-field>
                                <b-input 
                                    type="text" 
                                    v-model="name" 
                                    placeholder="Name*" 
                                    icon="account"
                                    >
                                </b-input>
                            </b-field>
                            <span class="err text-left d-flex" v-if="errors.name" >
                                {{errors.name[0]}}
                            </span>
                        </div>
                        <div class="form-group">
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
                        <div class="form-group ">
                            <b-field>
                                <b-input 
                                    type="password" 
                                    v-model="password" 
                                    placeholder="password*" 
                                    icon="lock"
                                    required>
                                </b-input>
                            </b-field>
                            <span class="err text-left d-flex" v-if="errors.password" >
                                {{errors.password[0]}}
                            </span>
                        </div>
                        <div class="form-group ">
                            <b-field>
                                <b-input 
                                    type="password" 
                                    v-model="confirm" 
                                    placeholder="confirm password*" 
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
                        <button class="btn btn-primary" @click.prevent="register">Register</button>
                        <div class="form-row pt-3">

                            <div class="form-group col-md-6">
                                <b-checkbox v-model="remember" native-value="Remember Me" class="remember-check">
                                    Remember Me
                                </b-checkbox>
                            </div>
                            <div class="form-group col-md-6">
                                <span>
                                    <router-link class="forgot-link small-text" to="/password/reset">
                                        Forgot Password?
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
                name:'',
                confirm:'',
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
            register(){
                this.isLoading = true
                axios.post(`/api/user/register`,{
                    name: this.name,
                    email: this.email,
                    password: this.password,
                    remember_me: this.remember,
                    password_confirmation: this.confirm
                })
                .then(res=>{
                    this.$store.dispatch('setLoggedIn', res)
                    this.$router.go('/dashboard')
                })
                .catch(err=>{
                    this.errors = err.response.data.errors
                })
                .finally(res=>{
                    this.isLoading = false
                })
            }
        }
    }
</script>