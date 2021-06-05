<template>
    <div>
        <b-notification :closable="false" class="form-notification">
            <div class="reset-form-wrapper">
                <form class="reset-form">
                    <h1 class="mb-2">Password Reset</h1>
                    <hr>
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
                    </div>
                    <button class="btn btn-primary" @click.prevent="resetPassword">Reset</button>
                </form>
            </div>
            <b-loading :is-full-page="false" :active.sync="isLoading" :can-cancel="false"></b-loading>
        </b-notification>
    </div>
</template>

<script>
    export default {
        name: 'ResetPassword',
        components: {
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
                axios.post('/api/user/password/reset',{
                    token: this.$route.params.token,
                    email: this.email,
                    password: this.password,
                    password_confirmation: this.confirm
                })
                .then(res => {
                    this.toastMessage = "Password Reset Successfully"
                    this.toastType = 'is-success'
                    this.$router.push('/login')
                })
                .catch(errors => {
                    this.toastMessage = "Password Reset Failed"
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