<template>
    <div>
        <div class="feature-wp">
            <b-button type="is-info" @click.prevent="action(null,'add')">
                <b-icon icon="plus" size="is-small"></b-icon>
                <span >Add Testimonial</span>
            </b-button>
        </div>
        <b-notification :closable="false">
            <b-table
                class="testimonial-table"
                :data="tableData"
                :loading="loading"

                @page-change="onPageChange"
                :bordered="isBordered"
                :narrowed="isNarrowed"
                :hoverable="isHoverable"

                backend-sorting
                :sort-icon="sortIcon"
                :sort-icon-size="sortIconSize"
                :default-sort-direction="defaultSortOrder"
                :default-sort="[sortField, sortOrder]"
                @sort="onSort"

                backend-filtering
                @filters-change="filterColumn"

                checkable
                :checked-rows.sync="checkedRows"
                :is-row-checkable="(row) => row.id"

                :row-class="(row, index) => row.status === 0 && 'is-inactive'"
                >
                
                <b-table-column field="name" label="Name" sortable searchable v-slot="props">
                    <h2>{{ props.row.name }}</h2>
                    <span>{{ props.row.personal_des }}</span>
                </b-table-column>
                <b-table-column field="product_id" label="Product" sortable searchable v-slot="props">
                    {{ props.row.product_id }}
                </b-table-column>
                <b-table-column field="url" label="Url" width="120" sortable searchable v-slot="props">
                    {{ props.row.url }}
                </b-table-column>
                <b-table-column field="description" label="Description" width="120" sortable searchable v-slot="props">
                    {{ props.row.description }}
                </b-table-column>
                <b-table-column field="rating" label="Rating" searchable v-slot="props">
                    <b-rate
                        v-model="props.row.rating"
                        :icon-pack="'mdi'"
                        :icon="'star'"
                        :max="5"
                        :rtl="false"
                        :spaced="false"
                        :disabled="true">
                    </b-rate>
                </b-table-column>
                <b-table-column field="action" label="Action" width="110" v-slot="props">
                    <b-dropdown :triggers="['hover']" aria-role="list" class="actionDropdown">
                        <template #trigger>
                            <b-button
                            label=""
                            type="is-info"
                            icon-right="menu-down" />
                        </template>

                        <b-dropdown-item aria-role="listitem" icon-left="pencil-outline" @click.prevent.native="action(props.row,'edit')">Edit</b-dropdown-item>
                        <b-dropdown-item aria-role="listitem" icon-left="trash-can-outline" @click.prevent.native="deleteAction(props.row,props.index)">Delete</b-dropdown-item>
                    </b-dropdown>
                </b-table-column>
            </b-table>
            <c-pagination 
                :total="total"
                :currentPage="page"
                :per-page="perPage"
                @onPageChange="onPageChange"></c-pagination>
            <b-loading :is-full-page="false" :active.sync="isLoading" :can-cancel="false"></b-loading>
        </b-notification>

        <b-modal :active.sync="showModal" 
                 has-modal-card
                 trap-focus
                 :destroy-on-hide="false"
                 aria-role="dialog"
                 aria-modal
                 >
            <div class="modal-card" >
                <header class="modal-card-head">
                    <p class="modal-card-title">{{modalTitle}}</p>
                </header>
                <section class="modal-card-body">
                    <form v-if="modalType!=='delete'">
                        <div class="form-row">
                            <div class="col">
                                <b-field label="Name">
                                    <b-input type="text" class="" v-model="form.name"  @keyup="errors.name=''" placeholder="Name" required></b-input>
                                </b-field>
                                <span class="err d-flex" v-if="errors.name" >
                                    {{errors.name[0]}}
                                </span>
                            </div>
                            <div class="col">
                                <b-field label="Personal Description">
                                    <b-input type="text" class="" v-model="form.personal_des"  @keyup="errors.personal_des=''" placeholder="Persional Description" required></b-input>
                                </b-field>
                                <span class="err d-flex" v-if="errors.personal_des" >
                                    {{errors.personal_des[0]}}
                                </span>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col">
                                <b-field label="Product ID">
                                    <b-input type="text" class="" v-model="form.product_id"  @keyup="errors.product_id=''" placeholder="Product ID" required></b-input>
                                </b-field>
                                <span class="err d-flex" v-if="errors.product_id" >
                                    {{errors.product_id[0]}}
                                </span>
                            </div>
                            <div class="col">
                                <b-field label="URL">
                                    <b-input type="text" class="" v-model="form.url"  @keyup="errors.url=''" placeholder="URL" ></b-input>
                                </b-field>
                                <span class="err d-flex" v-if="errors.url" >
                                    {{errors.url[0]}}
                                </span>
                            </div>
                        </div>
                        <div class="form-input">
                            <b-field label="Rating">
                                <b-rate
                                    v-model="form.rating"
                                    :icon-pack="'mdi'"
                                    :icon="'star'"
                                    :max="5"
                                    :rtl="false"
                                    :spaced="false"
                                    :disabled="false">
                                </b-rate>                                

                                <span class="err d-flex" v-if="errors.rating" >
                                    {{errors.rating[0]}}
                                </span>                            
                            </b-field>
                        </div>
                        <div class="form-input">
                            <b-field label="Description">
                                <b-input type="textarea" class="" v-model="form.description"  @keyup="errors.description=''" placeholder="Description" ></b-input>
                            </b-field>
                            <span class="err d-flex" v-if="errors.description" >
                                {{errors.description[0]}}
                            </span>
                        </div>
                    </form>
                    <p v-else>Are you sure you want to delete this testimonial: {{row.name}}?</p>
                </section>
                <div class="modal-card-foot">
                    <b-button type="is-danger" @click="showModal=false">Cancel</b-button>
                    <b-button type="is-success" :loading="isModalLoading" @click.prevent="modalSubmit">{{modalBtn}}</b-button>
                </div>
            </div>
        </b-modal>

    </div>
</template>

<script>
    import _ from 'lodash';
    import Form from '@form/form'
    import Pagination from '@components/pagination';

    export default {
        name: 'TestimonialTable',
        components: {
            'c-pagination':Pagination,
        },
        data() {
            return {
                // table
                filter:{},
                tableData:[],
                total: 0,
                loading: false,
                sortField: 'id',
                sortOrder: 'desc',
                defaultSortOrder: 'desc',
                order: 'is-centered',
                sortIcon: 'menu-up',
                sortIconSize: 'is-small',
                page: 1,
                perPage: 20,
                isBordered:false,
                isNarrowed:false,
                isHoverable:true,
                isLoading: false,
                checkedRows:[],
                // toast
                toastMessage:'',
                toastPosition:'is-top',
                toastType:'',
                // modal
                modalType:'',
                modalTitle:'',
                modalBtn:'',
                row:null,
                showModal:false,
                isModalLoading:false,                
                mReload:0,
                // form
                form: new Form({
                    product_id:'',
                    name:'',
                    url:'',
                    rating:0,
                    personal_des:'',
                    description:'',                    
                }),
                errors:[],
                index:null,
            }
        },
        created(){
            this.loadData()
        },
        watch:{
        },
        filters: {
            truncate(value, length) {
                return value.length > length
                    ? value.substr(0, length) + '...'
                    : value
            }
        },
        computed: {
        },
        methods:{
            loadData(){
                this.isLoading = true;
                axios.get(`/api/admin/testimonials?page=${this.page}`, {
                    params: {
                        filter: this.filter
                    }
                })
                .then(res => {
                    this.perPage = res.data.per_page
                    this.page = res.data.current_page
                    this.total = res.data.total
                    this.tableData = res.data.data
                })
                .catch(errors => {
                    this.$buefy.notification.open({
                        duration: 3000,
                        message: errors.response,
                        position: 'is-top-right',
                        type: 'is-danger',
                        hasIcon: true
                    })
                })  
                .finally(res=>{
                    this.isLoading = false
                })
            },
            onPageChange(page) {
                this.page = page
                this.filter.sortField = this.sortField
                this.filter.sortOrder = this.sortOrder
                this.loadData()
            },
            onSort(field, order) {
                this.filter.sortField = field
                this.filter.sortOrder = order
                this.loadData()
            },
            filterColumn: _.debounce(function(evt){
                this.filter = evt
                this.filter.sortField = this.sortField
                this.filter.sortOrder = this.sortOrder
                this.loadData()
            },600),
            toggle(row) {
                this.$refs.table.toggleDetails(row)
            },
            action(row, type){
                switch(type){
                    case "add":
                    this.modalType = 'add'
                    this.modalTitle = "Add Testimonial"
                    this.modalBtn = 'Add'
                    this.showModal = true
                    this.form.images = []
                    this.uploads = []
                    break
                    case "edit":
                    this.modalType = 'edit'
                    this.modalTitle = "Edit Testimonial"
                    this.modalBtn = 'Edit'
                    this.row = row
                    Object.keys(row).map(field=>{
                        this.form[field] = row[field]
                    })
                    this.showModal = true
                    break
                }
            },
            deleteAction(row,index){
                this.index = index
                this.modalType = 'delete'
                this.modalTitle = 'Delete Testimonial'
                this.modalBtn = 'Delete'
                this.row = row
                this.showModal = true
            },
            modalSubmit(){
                let query = this.modalType==='add'?'':`/${this.row.id}`
                let action = this.modalType ==='delete'?'delete':'post'

                this.isModalLoading = true
               
                this.form.submit(action,`/api/admin/testimonials${query}`)
                .then(res=>{
                    if(this.modalType==='add'){
                        this.tableData.unshift(res)
                    }else if(this.modalType==='edit'){
                        Object.keys(this.row).map(field=>{
                            this.row[field] = res[field]
                        })
                    }else{
                        this.tableData.splice(this.index,1)
                    }
                    this.showModal = false
                    this.toastMessage = `${this.modalTitle} Successfully`
                    this.toastType = 'is-success'
                })
                .catch(err=>{
                    this.errors = err.errors
                    this.toastMessage = `${this.modalTitle} failed, please check if product exists.`
                    this.toastType = 'is-danger'
                })
                .finally(res=>{
                    this.isModalLoading = false
                    this.$buefy.notification.open({
                        duration: 3000,
                        message: this.toastMessage,
                        position: 'is-top-right',
                        type: this.toastType,
                        hasIcon: true
                    })
                })
            },
        }
    }
</script>
<style lang="scss" scoped>
    
</style>