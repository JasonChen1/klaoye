<template>
    <div class="layout">
        <Layout>
            <Sider :style="{position: 'fixed', height: '100vh', left: 0}" ref="side1" hide-trigger collapsible :collapsed-width="78" v-model="isCollapsed" class="slider-wp">
                <div class="logo-con">
                    <b-navbar-item tag="router-link" :to="{ path: '/admin' }" v-lazy-container="{ selector: 'img' }" class="admin-logo">
                        <img :data-src="logoSrc" alt="klaoye">
                    </b-navbar-item>
                </div>
                <menu  width="auto" :class="menuitemClasses">
                    <li class="ivu-menu-item" 
                        @click="activeTab='dashboard'" 
                        :class="activeTab==='dashboard'?'anav-active':''">
                        <i class="ivu-icon ivu-icon-ios-book"></i>
                        <span>Dashboard</span>
                    </li> 

                    <li class="ivu-menu-item" 
                        @click="activeTab='category'" 
                        :class="activeTab==='category'?'anav-active':''">
                        <i class="ivu-icon ivu-icon-ios-book"></i>
                        <span>Categories</span>
                    </li> 

                    <li class="ivu-menu-item" 
                        @click="activeTab='product'" 
                        :class="activeTab==='product'?'anav-active':''">
                        <i class="ivu-icon ivu-icon-ios-book"></i>
                        <span>Products</span>
                    </li> 
                    
                    <li class="ivu-menu-item" 
                        @click="activeTab='testimonial'" 
                        :class="activeTab==='testimonial'?'anav-active':''">
                        <i class="ivu-icon ivu-icon-ios-book"></i>
                        <span>Testimonials</span>
                    </li> 

                    <li class="ivu-menu-item" 
                        @click="activeTab='order'" 
                        :class="activeTab==='order'?'anav-active':''">
                        <i class="ivu-icon ivu-icon-ios-book"></i>
                        <span>Orders</span>
                    </li>
                    
                    <li class="ivu-menu-item" 
                        @click="activeTab='enquiry'" 
                        :class="activeTab==='enquiry'?'anav-active':''">
                        <i class="ivu-icon ivu-icon-ios-book"></i>
                        <span>Enquiries</span>
                    </li>
                    <li class="ivu-menu-item" 
                        @click="activeTab='coupon'" 
                        :class="activeTab==='coupon'?'anav-active':''">
                        <i class="ivu-icon ivu-icon-ios-book"></i>
                        <span>Coupons</span>
                    </li>
                </menu>
            </Sider>
            <Layout :style="isCollapsed ?{marginLeft: '78px'}: {marginLeft: '200px'}">
                <Header :style="{padding: 0}" class="layout-header-bar d-flex">
                    <Icon @click.native="collapsedSider" :class="rotateIcon" :style="{margin: '0 20px'}" type="md-menu" size="24"></Icon>
                    <v-breadcrumb>
                        <BreadcrumbItem to="/admin"><span @click="activeTab=''">Home</span></BreadcrumbItem>
                        <BreadcrumbItem>{{breadcrumbList[activeTab]}}</BreadcrumbItem>
                    </v-breadcrumb>
                    <div class="navbar-end admin-nav-end">
                        <b-dropdown
                            position="is-bottom-left"
                            hoverable 
                            aria-role="list">
                            <a
                                class="navbar-item"
                                slot="trigger"
                                role="button">
                                <span>{{adminName}}</span>
                                <b-icon icon="menu-down"></b-icon>
                            </a>
                            <b-dropdown-item @click="reset" aria-role="menuitem">
                                <b-icon icon="lock-question"></b-icon>
                                Change Password
                            </b-dropdown-item>
                            <b-dropdown-item  @click="logout" aria-role="menuitem">
                                <b-icon icon="logout"></b-icon>
                                Logout
                            </b-dropdown-item>
                        </b-dropdown>
                    </div>
                </Header>
             
                <Content :style="{margin: '20px', background: '#fff', minHeight: '260px'}">
                    <div v-if="!activeTab">
                        <h1 style="display: flex;justify-content: center;padding: 1rem">KLAOYE</h1>
                    </div>
                    <div v-if="activeTab==='dashboard'">
                        <v-data-analytics-board></v-data-analytics-board>
                    </div>
                    <div v-if="activeTab==='category'">
                        <v-category-table></v-category-table>
                    </div>
                    <div v-if="activeTab==='product'">
                        <v-product-table></v-product-table>
                    </div>
                    <div v-if="activeTab==='testimonial'">
                        <v-testimonial-table></v-testimonial-table>
                    </div>
                    <div v-if="activeTab==='order'">
                        <v-order></v-order>
                    </div>
                    <div v-if="activeTab==='enquiry'">
                        <v-enquiries></v-enquiries>
                    </div>
                    <div v-if="activeTab==='coupon'">
                        <v-coupon></v-coupon>
                    </div>
                </Content>
            </Layout>
        </Layout>
    </div>
</template>

<script>
    import {Breadcrumb,BreadcrumbItem,Sider,Layout,MenuItem,Icon,Header,Content} from 'view-design';
    import logo from '@images/logo.png';
    import DataAnalytics from './dataAnalytics'
    import ProductTable from './productTable'
    import Order from './order'
    import Enquiries from './enquiries'
    import Coupon from './coupon'
    import CategoryTable from './category'
    import TestimonialTable from './testimonial'

    export default {
        name: 'Dashboard',
        components: {
            'v-breadcrumb':Breadcrumb,
            BreadcrumbItem,
            Sider,
            Layout,
            MenuItem,
            Icon,
            Header,
            Content,
            'v-data-analytics-board':DataAnalytics,
            'v-category-table':CategoryTable,
            'v-product-table':ProductTable,
            'v-order':Order,
            'v-enquiries':Enquiries,            
            'v-coupon':Coupon,
            'v-testimonial-table':TestimonialTable,
        },
        data() {
            return {
                locales: window.locales,
                isCollapsed: false,
                adminName:localStorage.getItem('aname'),
                logoSrc:logo,
                activeTab:'product',//default tab
                breadcrumbList:{
                    'dashboard':'Dashboard',
                    'category':'Categories',
                    'product':'Products',
                    'testimonial':'Testimonials',
                    'order':'Orders',
                    'enquiry':'Enquiries',
                    'coupon':'Coupon',
                },
                reload:0,
                currencyList:[],
                carouselList:[],
                socialList:[],
                languages:[],
                currentLang:'English',
                isLoading:false,
                activeSite:true,
            }
        },
        mounted(){
            if(!this.$cookie.get('token_a')){
                this.$router.push('/admin/login')
            }
            this.$nextTick(() => {
                window.addEventListener('resize', this.onResize);
            })
        },
        beforeDestroy() { 
            window.removeEventListener('resize', this.onResize); 
        },
        watch:{
            activeTab(){
                // if(this.activeTab ==='dashboard'){
                //     this.getSiteActive()
                // }
                // this.reload++
            }
        },
        computed: {
            rotateIcon () {
                return [
                    'menu-icon',
                    this.isCollapsed ? 'rotate-icon' : ''
                ];
            },
            menuitemClasses () {
                return [
                    'menu-item',
                    this.isCollapsed ? 'collapsed-menu' : ''
                ]
            }
        },
        methods: {
            onResize() {
                if(window.innerWidth<=930){
                    this.isCollapsed = true
                }else {
                    this.isCollapsed = false
                }
            },
            collapsedSider () {
                this.$refs.side1.toggleCollapse();
            },
            logout(){
                axios.get('/api/admin/logout')
                .then(res => {
                    this.$store.dispatch('setLogout', res)
                    this.$router.go('/admin/login')
                })
                .catch(error => {
                })
            },
            reset(){
                this.$router.push('/admin/password/reset')
            },
        }
    }
</script>
<style lang="scss">
    .ivu-icon.ivu-icon-md-menu.menu-icon{
        display: flex;
        align-items: center;
    }
    .slider-wp{
        overflow: auto;
        position: fixed;
        height: 100vh;
    }
    .ivu-layout-header{
        background: #fff !important;
    }
    .layout{
        border: 1px solid #d7dde4;
        background: #f5f7f9;
        position: relative;
        border-radius: 4px;
        overflow: hidden;
    }
    .layout-header-bar{
        background: #fff;
        box-shadow: 0 1px 1px rgba(0,0,0,.1);
    }
    .layout-logo-left{
        width: 90%;
        height: 30px;
        background: #5b6270;
        border-radius: 3px;
        margin: 15px auto;
    }
    .menu-icon{
        transition: all .3s;
    }
    .rotate-icon{
        transform: rotate(-90deg);
    }
    .menu-item span{
        display: inline-block;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        vertical-align: bottom;
        transition: width .2s ease .2s;
    }
    .menu-item i{
        transform: translateX(0px);
        transition: font-size .2s ease, transform .2s ease;
        vertical-align: middle;
        font-size: 16px;
    }
    .collapsed-menu span{
        width: 0px;
        transition: width .2s ease;
    }
    .collapsed-menu i{
        transform: translateX(5px);
        transition: font-size .2s ease .2s, transform .2s ease .2s;
        vertical-align: middle;
        font-size: 22px;
    }
    .ivu-menu-item{
        padding: 1rem;
    }
    .ivu-layout-sider{
        background:#001529 !important;
    }
    .ivu-layout-sider-children .ivu-menu-item{
        color: #dfdfdf !important;
    }
    .ivu-layout-sider-children .ivu-menu-item:hover{
        color: #fff !important;
    }
    /*custom style*/
    .ivu-layout-content{
        min-height: 100vh !important;
    }
    .admin-nav-end .dropdown-content,
    .admin-nav-end .dropdown-content a{
        width: 100%;
    }
    .admin-nav-end .dropdown-menu{
        margin: 0;
    }
    .admin-logo{
        height: 64px;
        overflow: hidden;
    }
    .admin-logo img{
        max-height: initial;
    }
    .admin-logo.router-link-active,
    .admin-logo:hover{
        background-color: transparent !important;
    }
    .anav-active{
        color: blue !important;
        background: #000c17;
    }
    .anav-active{
        i,span{
            color: #2d8ce2;
        }
    }
</style>