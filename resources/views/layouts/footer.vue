f<template>
    <div class="mt-5">
        <footer id="footer">
            <button @click="topFunction()" id="myBtn" title="Go to top"><b-icon icon="chevron-up" size="is-medium"></b-icon></button>
            <div class="container " :class="isMobile ? 'pt-2' : 'pt-5'">
                <div :class="isMobile?'':'row pb-5'">
                    <collapsible title="CONTACT"  :isMobile="isMobile">
                        <router-link  class="footer-link footer-logo" to="/">
                            <img :src="logoSrc" >
                        </router-link>
                        <ul class=" ml-0 mt-0 footer-list">
                            <li>
                                <router-link  class="footer-link" to="/contact">
                                    Contact Us
                                </router-link>
                            </li>
                        </ul>
                    </collapsible>
                    <collapsible :isMobile="isMobile" title="PRODUCTS">
                        <ul class=" ml-0 mt-0 footer-list">
                            <li v-for="(category,index) in categories" :key="index">
                                <router-link  class="footer-link" :to="`/${category.name}`" >
                                    {{ category.name }}
                                </router-link>
                            </li>
                        </ul>
                    </collapsible>
                    <collapsible :isMobile="isMobile" title="USER">
                        <ul class=" ml-0 mt-0 footer-list">
                            <li>
                                <router-link  class="footer-link" to="/dashboard">
                                    Account
                                </router-link>
                            </li>
                            <li>
                                <router-link  class="footer-link" to="/cart">
                                    Cart
                                </router-link>
                            </li>
                        </ul>
                    </collapsible>
                    <collapsible :isMobile="isMobile" title="HELP">
                        <ul class=" ml-0 mt-0 footer-list">
                            <li>
                                <a  class="footer-link" href="/sitemap.xml">
                                    Sitemap
                                </a>
                            </li>
                        </ul>
                    </collapsible>
                    <collapsible :isMobile="isMobile" title="Social Media" >
                        <ul class=" ml-0 mt-0 footer-list">
                            <li >
                                <a class="footer-link p-0 m-0" href="url" target="_blank">
                                    <span class="icon"><b-icon icon="facebook" ></b-icon></span>  
                                    <span>Facebook</span>
                                </a>
                            </li>
                            <li >
                                <a class="footer-link p-0 m-0" href="url" target="_blank">
                                    <span class="icon"><b-icon icon="instagram" ></b-icon></span>  
                                    <span>Instagram</span>
                                </a>
                            </li>
                        </ul>
                    </collapsible>
                </div>
                <hr class="footer-hr">
                <p class="mt-3">
                    © COPYRIGHT 2021 KLAOYE.
                </p>
            </div>
        </footer>
    </div>
</template>

<script>
    import Collapsible from '@components/collapsible'
    import logo from '@images/logo.png';
    export default {
        name: 'Footer',
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
            'collapsible': Collapsible,
        },
        data() {
            return {
                loadingBar:false,
                logoSrc:logo,
            }
        },
        mounted(){
            this.loadingBar = window.loading
            var mybutton = document.getElementById("myBtn");
            window.onscroll = function() {scrollFunction()};
            function scrollFunction() {
                if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
                    mybutton.style.display = "flex";
                } else {
                    mybutton.style.display = "none";
                }
            }

            if(!localStorage.getItem('cookie-law')){
                this.$buefy.snackbar.open({
                    indefinite: true,
                    message:`This website uses 'cookies' to give you the best, most relevant experience. Using this website means you're Ok with this. you can find out more about them  - by checking our <a href="/private-policy">
                            Private Policy & Cookie Policy
                        </a>.`,
                    position:'is-bottom',
                    type:'is-success',
                    actionText:'OK',
                    queue:'false',
                    onAction: () => {
                        localStorage.setItem('cookie-law',true)
                    }
                })
            }
        },
        computed:{
        },
        methods:{
            topFunction() {
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                })
            },
        }
    }
</script>
