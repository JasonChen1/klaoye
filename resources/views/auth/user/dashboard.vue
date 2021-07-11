<template>
    <div>
        <div :class="isMobile?'filter-left-mb':'row'" class="pt-5">
            <div class="col-md-2" v-if="!isMobile">
                <div class="left-filter fixed" >
                    <b-menu accordion="accordion">
                        <b-menu-list label="Menu">
                            <b-menu-item :label="item.name" v-for="(item,i) in tabs" :key="i" :active="activeDefault==item.name" @click.native="currentTab(item.name)"></b-menu-item>
                        </b-menu-list>
                    </b-menu>
                </div>
            </div>
            
            <div v-else class="dropdown-filter-wp">
                <section>
                    <b-collapse
                        class="card"
                        animation="slide"
                        v-for="(tab, index) of tabs"
                        :key="index"
                        :open="isOpen == index"
                        @open="isOpen = index">
                        <template #trigger="props">
                            <div class="card-header" role="button">
                                <p class="card-header-title">
                                    {{ tab.name }}
                                </p>
                                <a class="card-header-icon">
                                    <b-icon :icon="props.open ? 'menu-down' : 'menu-up'"></b-icon>
                                </a>
                            </div>
                        </template>
                        <div class="card-content">
                            <div class="content">
                                <div v-if="tab.name=='Profile'">
                                    <form class="mb-dform">  
                                        <div class="form-row-mb">
                                            <div class="fr-label">
                                                Name
                                            </div> 
                                            <div class="">
                                                <b-field v-if="action">
                                                    <b-input 
                                                        type="text" 
                                                        v-model="profileForm.name" 
                                                        placeholder="Name" 
                                                        icon="account"
                                                        required
                                                        >
                                                    </b-input>
                                                </b-field>
                                                <span v-else>{{profileForm.name}}</span>
                                            </div>
                                        </div>
                                        <div class="form-row-mb">
                                            <div class="fr-label">
                                                Email
                                            </div> 
                                            <div class="">
                                                <b-field v-if="action">
                                                    <b-input 
                                                        type="text" 
                                                        v-model="profileForm.email" 
                                                        placeholder="Email" 
                                                        icon="email"
                                                        required
                                                        >
                                                    </b-input>
                                                </b-field>
                                                <span v-else>{{profileForm.email}}</span>
                                            </div>
                                        </div>
                                    </form>
                                </div>

                                <div v-if="tab.name=='Address'">
                                    <form>  
                                        <div class="form-row-mb">
                                            <div class="fr-label">
                                                Phone
                                            </div> 
                                            <div class="">
                                                <b-field v-if="action">
                                                    <b-input 
                                                        type="text" 
                                                        v-model="addressForm.phone" 
                                                        placeholder="Phone" 
                                                        icon="phone"
                                                        >
                                                    </b-input>
                                                </b-field>
                                                <span v-else>{{addressForm.phone}}</span>
                                            </div>
                                        </div>
                                        <div class="form-row-mb">
                                            <div class="fr-label">
                                                Address
                                            </div> 
                                            <div class="">
                                                <b-field v-if="action">
                                                    <b-input 
                                                        type="text" 
                                                        v-model="addressForm.address" 
                                                        placeholder="Address" 
                                                        icon="google-maps"
                                                        required
                                                        >
                                                    </b-input>
                                                </b-field>
                                                <span v-else>{{addressForm.address}}</span>
                                            </div>
                                        </div>
                                        <div class="form-row-mb">
                                            <div class="fr-label">
                                                City
                                            </div> 
                                            <div class="">
                                                <b-field v-if="action">
                                                    <b-input 
                                                        type="text" 
                                                        v-model="addressForm.city" 
                                                        placeholder="City" 
                                                        icon="city"
                                                        required
                                                        >
                                                    </b-input>
                                                </b-field>
                                                <span v-else>{{addressForm.city}}</span>
                                            </div>
                                        </div>
                                        <div class="form-row-mb">
                                            <div class="fr-label">
                                                State
                                            </div> 
                                            <div class="">
                                                <b-field v-if="action">
                                                    <b-input 
                                                        type="text" 
                                                        v-model="addressForm.state" 
                                                        placeholder="State" 
                                                        icon="google-maps"
                                                        >
                                                    </b-input>
                                                </b-field>
                                                <span v-else>{{addressForm.state}}</span>
                                            </div>
                                        </div>
                                        <div class="form-row-mb">
                                            <div class="fr-label">
                                                Country
                                            </div> 
                                            <div class="">
                                                <b-field v-if="action">
                                                    <b-input 
                                                        type="text" 
                                                        v-model="addressForm.country" 
                                                        placeholder="Country" 
                                                        icon="google-maps"
                                                        required
                                                        >
                                                    </b-input>
                                                </b-field>
                                                <span v-else>{{addressForm.country}}</span>
                                            </div>
                                        </div>
                                        <div class="form-row-mb">
                                            <div class="fr-label">
                                                Postal Code
                                            </div> 
                                            <div class="">
                                                <b-field v-if="action">
                                                    <b-input 
                                                        type="text" 
                                                        v-model="addressForm.postal_code" 
                                                        placeholder="Postal Code" 
                                                        icon="google-maps"
                                                        required
                                                        >
                                                    </b-input>
                                                </b-field>
                                                <span v-else>{{addressForm.postal_code}}</span>
                                            </div>
                                        </div>
                                    </form>
                                </div>

                                <div class="dst-mb pt-3" v-if="tab.name!='Orders'">
                                    <b-button class="btn action-btn" @click="changeAction(tab.name)" :loading="isLoading">{{action?'Save':'Edit'}}</b-button>
                                </div>
                            </div>
                        </div>
                        <div v-if="tab.name=='Orders'">
                            <v-order></v-order>
                        </div>
                    </b-collapse>
                </section>
            </div>

            <div class="col-md-10" v-if="!isMobile">
                <div class="dst" >
                    <h1>{{current}}</h1>
                    <div class="btn-dwp" v-if="current!='Orders'">
                        <b-button class="btn action-btn" @click="changeAction(current)" :loading="isLoading">{{action?'Save':'Edit'}}</b-button>
                    </div>
                </div>
                <div v-if="current=='Profile'">
                    <form class="pt-3 ud-form">  
                        <div class="form-row">
                            <div class="col-md-4 pr-0">
                                Name
                            </div> 
                            <div class="col-md-8 pl-0">
                                <b-field v-if="action">
                                    <b-input 
                                        type="text" 
                                        v-model="profileForm.name" 
                                        placeholder="Name" 
                                        icon="account"
                                        required
                                        >
                                    </b-input>
                                </b-field>
                                <span v-else>{{profileForm.name}}</span>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-md-4 pr-0">
                                Email
                            </div> 
                            <div class="col-md-8 pl-0">
                                <b-field v-if="action">
                                    <b-input 
                                        type="text" 
                                        v-model="profileForm.email" 
                                        placeholder="Email" 
                                        icon="email"
                                        required
                                        >
                                    </b-input>
                                </b-field>
                                <span v-else>{{profileForm.email}}</span>
                            </div>
                        </div>
                    </form>
                </div>
                <div v-if="current=='Address'">
                    <form class="pt-3 ud-form">  
                        <div class="form-row">
                            <div class="col-md-4 pr-0">
                                Phone
                            </div> 
                            <div class="col-md-8 pl-0">
                                <b-field v-if="action">
                                    <b-input 
                                        type="text" 
                                        v-model="addressForm.phone" 
                                        placeholder="Phone" 
                                        icon="phone"
                                        >
                                    </b-input>
                                </b-field>
                                <span v-else>{{addressForm.phone}}</span>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-md-4 pr-0">
                                Address
                            </div> 
                            <div class="col-md-8 pl-0">
                                <b-field v-if="action">
                                    <b-input 
                                        type="text" 
                                        v-model="addressForm.address" 
                                        placeholder="Address" 
                                        icon="google-maps"
                                        required
                                        >
                                    </b-input>
                                </b-field>
                                <span v-else>{{addressForm.address}}</span>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-md-4 pr-0">
                                City
                            </div> 
                            <div class="col-md-8 pl-0">
                                <b-field v-if="action">
                                    <b-input 
                                        type="text" 
                                        v-model="addressForm.city" 
                                        placeholder="City" 
                                        icon="city"
                                        required
                                        >
                                    </b-input>
                                </b-field>
                                <span v-else>{{addressForm.city}}</span>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-md-4 pr-0">
                                State
                            </div> 
                            <div class="col-md-8 pl-0">
                                <b-field v-if="action">
                                    <b-input 
                                        type="text" 
                                        v-model="addressForm.state" 
                                        placeholder="State" 
                                        icon="google-maps"
                                        >
                                    </b-input>
                                </b-field>
                                <span v-else>{{addressForm.state}}</span>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-md-4 pr-0">
                                Country
                            </div> 
                            <div class="col-md-8 pl-0">
                                <b-field v-if="action">
                                    <b-input 
                                        type="text" 
                                        v-model="addressForm.country" 
                                        placeholder="Country" 
                                        icon="google-maps"
                                        required
                                        >
                                    </b-input>
                                </b-field>
                                <span v-else>{{addressForm.country}}</span>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-md-4 pr-0">
                                Postal Code
                            </div> 
                            <div class="col-md-8 pl-0">
                                <b-field v-if="action">
                                    <b-input 
                                        type="text" 
                                        v-model="addressForm.postal_code" 
                                        placeholder="Postal Code" 
                                        icon="google-maps"
                                        required
                                        >
                                    </b-input>
                                </b-field>
                                <span v-else>{{addressForm.postal_code}}</span>
                            </div>
                        </div>
                    </form>
                </div>
                <div v-if="current=='Orders'">
                    <v-order></v-order>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    import order from '@user/order'
    export default {
        name: 'Dashboard',
        props:{
            isMobile:{
                type:Boolean,
                required:true
            }
        },
        components: {
            'v-order':order,
        },
        data() {
            return {
                isLoading:false,
                action:false,
                activeDefault:'Profile',
                isOpen:0,
                tabs:[
                    {
                        'name':'Profile'
                    },
                    {
                        'name':'Address'
                    },
                    {
                        'name':'Orders'
                    },
                ],
                current:'Profile',
                // form
                profileForm: {
                    name:'',
                    email:''
                },
                addressForm: {
                    phone:'',
                    address:'',
                    city:'',
                    state:'',
                    country:'',
                    postal_code:'',
                },
            }
        },
        mounted(){
           this.getUserData()
        },
        computed:{
            
        },
        methods:{
            changeAction(type){
                if(this.action){
                    switch(type) {
                        case 'Profile':
                        this.updateDetail()
                        break;
                        case 'Address':
                        this.updateAddress()
                        break;
                        case 'Orders':
                        break;

                        default:
                        this.action = !this.action
                        this.isLoading = false  
                    }
                }else{
                    this.action = !this.action
                }
            },
            updateAddress(){
                this.isLoading = true
                axios.patch('/api/user/address',this.addressForm)
                .then(res=>{
                    console.log(res)
                    for (const field in this.addressForm) {
                        this.addressForm[field] = res.data[field]
                    }
                    this.disSuccess('Update user address successfully')
                })
                .catch(err=>{
                    this.disErr('Update user address failed')
                })
                .finally(res=>{
                    this.action = !this.action
                    this.isLoading = false    
                })
            },
            updateDetail(){
                this.isLoading = true
                axios.patch('/api/user/details',this.profileForm)
                .then(res=>{
                    this.profileForm.name = res.data.name
                    this.profileForm.email= res.data.email
                    this.disSuccess('Update user details successfully')
                })
                .catch(err=>{
                    this.disErr('Update user details failed')
                })
                .finally(res=>{
                    this.action = !this.action
                    this.isLoading = false    
                })
            },
            currentTab(tab){
                this.current = tab
            },
            getUserData(){
                axios.get('/api/user/details')
                .then(res=>{
                    this.profileForm.name = res.data.name
                    this.profileForm.email= res.data.email

                    for (const field in this.addressForm) {
                        this.addressForm[field] = res.data.address[field]
                    }
                })
                .catch(err=>{
                    console.log(err)
                })
            },
            disErr(msg){
                this.$buefy.notification.open({
                    duration: 3000,
                    message: msg,
                    position: 'is-top-right',
                    type: 'is-danger',
                    hasIcon: true
                })
            },
            disSuccess(msg){
                this.$buefy.notification.open({
                    duration: 3000,
                    message: msg,
                    position: 'is-top-right',
                    type: 'is-success',
                    hasIcon: true
                })
            }
        }
    }
</script>