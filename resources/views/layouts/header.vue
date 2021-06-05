<template>
    <div id="header" :class="!isMobile?'header-df':'header-mb'">
        <b-navbar class="nav-bar-main" :class="isMobile?'navbar-mb':''" fixed-top>
            <template slot="brand">
                <b-navbar-item tag="router-link" :to="{ path: '/' }" v-lazy-container="{ selector: 'img' }">
                    <img :data-src="logoSrc" alt="KLAOYE">
                </b-navbar-item>
            </template>
            <template slot="burger">
                <a role="button" aria-label="menu" class="navbar-burger burger" @click="burgerActive" >
                    <span aria-hidden="true"></span> 
                    <span aria-hidden="true"></span> 
                    <span aria-hidden="true"></span>
                </a>
                <div>
                    <b-sidebar
                        type="is-light"
                        :fullheight="true"
                        :fullwidth="false"
                        :overlay="true"
                        :right="true"
                        :open.sync="sidebarOpen"
                        >
                        <div class="p-3">
                            <router-link class="" to="/" v-lazy-container="{ selector: 'img' }">
                                <img data-src="/images/LOGO.png" alt="KLAOYE">
                            </router-link>
                            <b-menu class="pt-2">
                                <b-menu-list label="MENU">
                                    <b-navbar-item tag="div" class="p-0">
                                        <b-field label="" >
                                            <b-autocomplete
                                                class="search-auto"
                                                v-model="searchText"
                                                :data="filteredDataArray"
                                                placeholder="Search for products"
                                                clearable
                                                icon="magnify"
                                                :loading="isFetching"
                                                @click.native="getProductList"
                                                @select="option => selected = option">
                                                <template slot-scope="props">
                                                    <div class="media">
                                                        <div class="media-left" v-lazy-container="{ selector: 'img' }">
                                                            <img width="32" :data-src="`https://ximiphoto.oss-cn-hangzhou.aliyuncs.com/thumb/${props.option.code}.jpg?x-oss-process=style/45`">
                                                        </div>
                                                        <div class="media-content" v-html="props.option.name"></div>
                                                    </div>
                                                </template>
                                            </b-autocomplete>
                                        </b-field>
                                    </b-navbar-item>
                                    <b-menu-list>
                                        <div @click="sidebarOpen=false">
                                            <b-menu-item icon="cart" label="VIEW CART" tag="router-link" to="/cart"></b-menu-item>
                                        </div>
                                    </b-menu-list>
                                </b-menu-list>
                                <b-menu-list label="ACCOUNT">
                                    <div @click="sidebarOpen=false" v-if="!isLogin">
                                        <b-menu-item icon="account-plus" label="REGISTER" tag="router-link" to="/register"></b-menu-item>
                                    </div>
                                    <div @click="sidebarOpen=false">
                                        <b-menu-item icon="account-key" label="LOGIN" tag="router-link" to="/login" v-if="!isLogin"></b-menu-item>
                                        <b-menu-item icon="account-key" label="MY ACOUNT" tag="router-link" to="/dashboard" v-else></b-menu-item>
                                    </div>
                                    <div @click="sidebarOpen=false" v-if="isLogin">
                                        <b-menu-item icon="account-lock" label="LOGOUT"  @click="logout"></b-menu-item>
                                    </div>
                                </b-menu-list>
                                <b-menu-list label="PRODUCTS" >
                                    <div @click="sidebarOpen=false" >
                                        <b-menu-item :label="category.name" v-for="(category,index) in categories" :key="index" tag="router-link" :to="`/${category.name}`"></b-menu-item>
                                    </div>
                                </b-menu-list>
                            </b-menu>
                        </div>
                    </b-sidebar>
                </div>
            </template>
            <template slot="start" >
                <!-- empty -->
            </template>
            <template slot="end">
                <b-navbar-item tag="div">
                    <b-field label="">
                        <b-autocomplete
                            v-model="searchText"
                            :data="filteredDataArray"
                            placeholder="Search for products"
                            clearable
                            icon="magnify"
                            :loading="isFetching"
                            @click.native="getProductList"
                            @select="option => selected = option">
                            <template slot-scope="props">
                                <div class="media">
                                    <div class="media-left" v-lazy-container="{ selector: 'img' }">
                                        <img width="32" :data-src="`https://ximiphoto.oss-cn-hangzhou.aliyuncs.com/thumb/${props.option.code}.jpg?x-oss-process=style/45`">
                                    </div>
                                    <div class="media-content" v-html="props.option.name"></div>
                                </div>
                            </template>
                        </b-autocomplete>
                    </b-field>
                </b-navbar-item>
                <b-navbar-item tag="div">
                    <router-link class="custom-cart" to="/cart" icon="cart">
                        <b-icon icon="cart" size="is-medium"></b-icon>
                        VIEW CART
                        <span class="dot" v-if="!isLogin">1</span>
                        <span class="dot" v-else> 1</span>
                    </router-link>
                </b-navbar-item>
                <b-navbar-item tag="router-link" :to="{ path: '/register' }" v-if="!isLogin">
                    REGISTER
                </b-navbar-item>
                <b-navbar-item tag="router-link" :to="{ path: '/login' }" v-if="!isLogin">
                    LOGIN
                </b-navbar-item>
                <b-navbar-item tag="router-link" :to="{ path: '/dashboard' }" v-else>
                    MY ACCOUNT
                </b-navbar-item>
                <b-navbar-item  @click="logout" v-if="isLogin">
                    LOGOUT
                </b-navbar-item>
            </template>
        </b-navbar>

        <div class="categories-container" v-if="!isMobile">
            <b-navbar-item tag="router-link" :to="{ path: '/' }">
                HOME
            </b-navbar-item>
            <b-dropdown :triggers="['hover']" aria-role="list" class="header-prod-dropdown">
                <template #trigger>
                    <b-navbar-item tag="router-link" :to="{ path: '/products' }" >
                        PRODUCTS
                        <b-icon icon="menu-down"></b-icon>
                    </b-navbar-item>
                </template>

                <b-dropdown-item aria-role="listitem" v-for="(category,i) in categories" :key="i" @click="redirectToCategory(category.name)">
                    {{category.name}}
                </b-dropdown-item>
            </b-dropdown>
            
            <b-navbar-item tag="router-link" :to="{ path: '/contact' }" >
                CONTACT US
            </b-navbar-item>
           
        </div>
        <div class="top-offset container" :class="isMobile?'container-mb':'container-df'" v-if="$route.name!=='HOME'">
            <v-breadcrumb class="p-3"  >
                <BreadcrumbItem to="/">HOME</BreadcrumbItem>
                <BreadcrumbItem :to="path" style="text-transform: uppercase;">{{pathName!=='HOME'?pathName:''}}</BreadcrumbItem>
            </v-breadcrumb>
        </div>
    </div>
</template>

<script>
    import {Breadcrumb,BreadcrumbItem} from 'view-design';
    import logo from '@images/logo.png';
	export default {
		name: 'headerNav',
        props:{
            categories:{
                type:Array,
                required:true
            },
            isMobile:{
                type:Boolean,
                required:true
            }
        },
		components: {
            'v-breadcrumb':Breadcrumb,
            BreadcrumbItem,
		},
		data() {
            return {
                searchText:'',
                loadingBar:false,
                sidebarOpen:false,
                pathName:'home',
                path:'/',
                logoSrc:logo,
                // autocomplete - search
                isFetching: false,
                searchableProducts:[],
                selected:'',
                searchMultipleText:'', 
                isLogin:false,
            }
        },
        mounted(){
            this.pathName = this.$route.name
            this.loadingBar = window.loading
            this.isLogin = this.$store.getters.isLoggedIn
        },
        watch:{
            $route(to, from) {
                if(to.query){
                    this.path = to.path + '?key='+to.query.key
                }else{
                    this.path = to.path
                }
               
                if(to.params.category){
                    this.pathName = to.params.category
                }else{
                    this.pathName = to.name
                }
            },
            selected(){
                let uri = `/search/${this.selected.id}`
                this.$router.push(uri)
            },
            searchText(){
                if(this.searchText){
                    this.getProductList()
                }
            }
        },
        computed: {
            filteredDataArray() {
                if(this.searchText){
                    return this.searchableProducts.filter((item)=>{
                        return item.name.toString().toLowerCase().indexOf(this.searchText.toLowerCase()) >=0
                    })
                }else{
                    return this.searchableProducts
                }
            }
        },
        methods: {
            redirectToCategory(name){
                this.$router.push(`/${name}`)
            },
            getCarts(){
                this.$store.dispatch('userCart')
                .then(res => {}, err => {
                    if(err.response.status=='401'){
                        this.$store.dispatch('setLogout')
                    }
                })
            },
            searchMultiple(){
                if(this.searchMultipleText){
                    if(this.$route.query.text!==this.searchMultipleText){
                        this.$router.push(`/search?text=${this.searchMultipleText}`)
                    }
                }
            },
            getProductList(){
                this.isFetching = true
                if(this.searchableProducts.length<1){
                    axios.get('/api/search')
                    .then(res=>{
                        this.searchableProducts = res.data
                    })
                    .catch(err=>{})
                    .finally(()=>{
                        this.isFetching = false
                    })
                }else{
                    this.isFetching = false
                }
            },
            searchKeyWord(){
                if(this.searchText){
                    if(this.$route.path.split('/').pop()!==this.searchText){
                        this.$router.push(`/search/${this.searchText}`)
                    }
                }
            },
            logout(){
                axios.get('/api/user/logout')
                .then(res => {
                    this.$store.dispatch('setLogout', res)
                    this.$router.go('/login')
                })
                .catch(error => {
                })
            },
            burgerActive(){
                this.sidebarOpen = true
            },
        }
    }
</script>
