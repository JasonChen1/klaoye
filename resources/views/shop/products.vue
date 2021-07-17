<template>
    <div >
        <div :class="isMobile?'mb-filter-wp':'filter-wp'">
            <div class="filter">
                <div class="filter-list pr-3">
                    <div class="pr-2"><span>Sort by: </span></div>
                    <select v-model="selected">
                        <option :value="sort.key" v-for="(sort,i) in sortable" :key="i">{{ sort.value }}</option>
                    </select>
                </div>
                <div class="view-type" v-if="!isMobile">
                    <span class="cursor" @click="viewType('grid')"><b-icon icon="view-grid"></b-icon></span>
                    <span class="cursor" @click="viewType('list')"><b-icon icon="view-list"></b-icon></span>
                </div>
            </div>
            <div v-if="!isMobile">
                <b-pagination
                    :total="total"
                    :current.sync="page"
                    :range-before="rangeBefore"
                    :range-after="rangeAfter"
                    :per-page="perPage"
                    :order="order"
                    aria-next-label="Next page"
                    aria-previous-label="Previous page"
                    aria-page-label="Page"
                    aria-current-label="Current page"
                    @change="onPageChange">
                </b-pagination>
            </div>
        </div>
        <div :class="isMobile?'filter-left-mb':'row'">
            <div class="col-md-3" v-if="!isMobile">
                <div class="left-filter fixed">
                    <b-menu accordion="accordion">
                        <b-menu-list label="Categories">
                            <b-menu-item :label="`${item.name} (${item.count})`" v-for="(item,i) in categories" :key="i" tag="router-link" :to="`/${item.name}`" :active="categoryActive==item.name"></b-menu-item>
                        </b-menu-list>
                    </b-menu>
                </div>
            </div>
            <div v-else class="dropdown-filter-wp">
                <b-dropdown
                    append-to-body
                    aria-role="menu"
                    scrollable
                    max-height="200"
                    trap-focus
                >
                    <template #trigger>
                        <a
                            class="navbar-item"
                            role="button">
                            <span>Categories (Filter)</span>
                            <b-icon icon="menu-down"></b-icon>
                        </a>
                    </template>

                    <b-dropdown-item custom aria-role="listitem">
                        <b-input v-model="searchTerm" placeholder="search" expanded />
                    </b-dropdown-item>

                    <router-link class="" v-for="item of filteredData" :key="item.name" :to="`/${item.name}`"> 
                        <b-dropdown-item  aria-role="listitem">
                            {{item.name}} ({{item.count}})
                        </b-dropdown-item>
                    </router-link>
                </b-dropdown>
            </div>
            <div :class="isMobile?'':'col-md-9 pl-0'">
                <b-notification :closable="false" class="notification products-wp">
                    <ul :class="isMobile?'home-prod-wp-mb':'home-prod-wp'">
                        <li v-for="(prod,i) in products" :key="i">
                            <v-product-card :product="prod" :isMobile="isMobile" @reloadHeader="reloadHeader"></v-product-card>
                        </li>
                    </ul>
                    <div class="pagintaed-custom mt-3" :class="isMobile?'mb-pagination' : ''">
                        <div class="paginate-text">Total {{total}} Items</div>
                        <b-pagination
                            :total="total"
                            :current.sync="page"
                            :range-before="rangeBefore"
                            :range-after="rangeAfter"
                            :per-page="perPage"
                            :order="order"
                            :size="isMobile?'is-small':'default'"
                            aria-next-label="Next page"
                            aria-previous-label="Previous page"
                            aria-page-label="Page"
                            aria-current-label="Current page"
                            @change="onPageChange">
                        </b-pagination>
                        <div class="paginate-text-right">
                            Jump to
                            <input type="number" v-model="jumpPage">
                            Page
                        </div>
                    </div>
                    <b-loading :is-full-page="false" :active.sync="isLoading" :can-cancel="false"></b-loading>
                </b-notification>
            </div>
        </div>
    </div>
</template>
<script>
    import ProductCard from '@components/productCard'

    export default {
        name: 'Products',
        components: {
            'v-product-card':ProductCard,
        },
        props:{
            isMobile:{
                type:Boolean,
                required:true
            }
        },
        data() {
            return {
                products:[],
                // pagination
                total:0,
                perPage:0,
                page:1,
                rangeBefore:2,
                rangeAfter:2,
                jumpPage:1,
                order: 'is-centered',
                sortable:[
                    {
                        key:'asc',
                        value:'A to Z',
                    },
                    {
                        key:'desc',
                        value:'Z to A',
                    },
                    {
                        key:'new',
                        value:'Newest First',
                    },
                    {
                        key:'old',
                        value:'Oldest First',
                    },
                    {
                        key:'pasc',
                        value:'Price: Ascending',
                    },
                    {
                        key:'pdesc',
                        value:'Price: Descending',
                    },
                ],
                selected:null,
                isLoading:false,
                // category
                searchTerm: '',
                categories: [],
                categoryActive:'',
            }
        },
        mounted(){
            if(this.$route.params){
                this.categoryActive = this.$route.params.category
            }
            
            this.getCategory()
        },
        computed:{
            filteredData() {
                return this.categories.filter((item) => item.name.toLowerCase().indexOf(this.searchTerm.toLowerCase()) >= 0);
            }
        },
        watch:{
            $route(to, from) {
                if(to.params.category){
                    this.categoryActive = to.params.category
                }else{
                    this.categoryActive = null
                }
                this.getCategory()
            },
            jumpPage(){
                if(this.jumpPage>0){
                    this.page = this.jumpPage
                    this.getCategory()
                }
            },
            selected(){
                this.getCategory()
            },
        },
        methods:{
            reloadHeader(){
                this.$emit('reloadHeader')
            },
            onPageChange(page){
                this.page = page
                this.getCategory()
            },
            getCategory(){
                $('#myBtn').click()
                this.isLoading = true
                axios.get(`/api/products?page=${this.page}`,{
                    params:{
                        filter: this.selected,
                        category: this.categoryActive
                    }
                })
                .then(res=>{
                    this.categories = res.data.categories
                    this.products = res.data.products.data
                    this.total = res.data.products.total
                    this.perPage = res.data.products.per_page
                    this.page = res.data.products.current_page
                })
                .catch(err=>{})
                .finally(res=>{
                    this.isLoading = false
                })
            },
            viewType(type){
                if(type==='grid'){
                    $('.home-prod-wp').removeClass('list-view');
                }else if(type==='list'){
                    $('.home-prod-wp').addClass('list-view');
                }
            }
        }
    }

</script>