<template>
    <div>
        <div class="feature-wp">
            <form class="w-100">
                <div class="row">
                    <div class="col">
                        <b-field>
                            <b-input type="text" class="" v-model="form.category"  @keyup="errors.category=''" placeholder="category" required></b-input>
                        </b-field>
                        <span class="err d-flex" v-if="errors.category" >
                            {{errors.category[0]}}
                        </span>
                    </div>
                    <div class="col">
                        <b-field>
                            <b-input type="text" class="" v-model="form.subcategory"  @keyup="errors.subcategory=''" placeholder="Subcategory" ></b-input>
                        </b-field>
                        <span class="err d-flex" v-if="errors.subcategory" >
                            {{errors.subcategory[0]}}
                        </span>
                    </div>

                    <div class="col">
                        <b-button type="is-info" @click.prevent="addCategory">
                            <b-icon icon="plus" size="is-small"></b-icon>
                            <span >Add Category</span>
                        </b-button>
                    </div>
                </div>
            </form>
        </div>
        <b-notification :closable="false">
            <b-table
                class="prod-table"
                :data="tableData"
                :loading="loading"
                :selected.sync="selected"

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
                >

                <b-table-column field="name" label="Category" sortable searchable v-slot="props">
                    <b-input v-if="selected && selected.id===props.row.id" :placeholder="props.row.name" v-model="selected.name"></b-input>
                    <p v-else>{{ props.row.name }}</p>
                </b-table-column>
                <b-table-column field="name" label="Subcategory" v-slot="props">
                    <div v-for="(sub,i) in props.row.subcategories" :key="i">
                        <b-input v-if="selected && selected.id===props.row.id" :placeholder="sub.name"  v-model="selected.subcategories[i].name"></b-input>
                        <p v-else>{{ sub.name }}</p>
                    </div>
                </b-table-column>
                
                <b-table-column field="action" label="Action" width="160" v-slot="props">
                    <b-button v-if="selected && selected.id===props.row.id" type="is-info" icon-left="content-save-outline" @click.prevent.native="updateCategory"></b-button>
                    <b-button type="is-info" icon-left="cog" @click.prevent.native="action(props.row,props.index)"></b-button>
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
                    <p class="modal-card-title">Update Category</p>
                </header>
                <section class="modal-card-body" v-if="row">
                    <b-collapse class="card" animation="slide" aria-id="collapse-content">
                        <template #trigger="props">
                            <div
                                class="card-header"
                                role="button"
                                aria-controls="collapse-content">
                                <p class="card-header-title">
                                    {{row.name}}
                                </p>
                                <a class="card-header-icon">
                                    <b-icon
                                        :icon="props.open ? 'menu-down' : 'menu-up'">
                                    </b-icon>
                                </a>
                            </div>
                        </template>

                        <div class="card-content">
                            <div class="content">
                               <div v-for="(sub,i) in row.subcategories" :key="i" class="sub-delete">
                                   <p>{{sub.name}}</p>
                                   <b-button icon-left="trash-can-outline" type="is-danger" @click.prevent="deleteSub(sub.id,i)"></b-button>
                               </div>
                            </div>
                        </div>
                        <footer class="card-footer">
                            <b-button class="card-footer-item" type="is-danger" @click.prevent="deleteAll" :loading="isModalLoading">Delete All</b-button>
                        </footer>
                    </b-collapse>
                </section>
               <!--  <div class="modal-card-foot">
                    <b-button type="is-danger" @click="showModal=false">Cancel</b-button>
                    <b-button type="is-success" :loading="isModalLoading" @click.prevent="modalSubmit">{{modalBtn}}</b-button>
                </div> -->
            </div>
        </b-modal>
    </div>
</template>

<script>
    import _ from 'lodash';
    import Form from '@form/form'
    import Pagination from '@components/pagination';
    export default {
        name: 'Category',
        components: {
            'c-pagination':Pagination,
        },
        data() {
            return {
                filter:{},
                perPage:0,
                page:1,
                total:0,
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
                loading:false,
                tableData:[],
                // table
                form: new Form({
                    category:'',
                    subcategory:'',
                }),
                errors:[],
                selected:null,
                // modal
                isModalLoading:false,
                showModal:false,
                modalTitle:'',
                modalBtn:'',
                row:null,
                index:null,
                isOpen:0,
                // toast
                toastMessage:'',
                toastType:'',
            }
        },
        mounted(){
            this.loadData()
        },
        filters: {
            truncate(value, length) {
                return value.length > length
                    ? value.substr(0, length) + '...'
                    : value
            }
        },
        methods:{
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
            loadData(){
                this.isLoading = true;
                axios.get(`/api/admin/categories?page=${this.page}`, {
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
            addCategory(){
                this.form.submit('post',`/api/admin/categories`)
                .then(res=>{
                    this.loadData()
                    this.toastMessage = `Add Category Successfully`
                    this.toastType = 'is-success'
                })
                .catch(err=>{
                    this.errors = err.errors
                    this.toastMessage = `Add Category failed`
                    this.toastType = 'is-danger'
                })
                .finally(res=>{
                    this.$buefy.notification.open({
                        duration: 3000,
                        message: this.toastMessage,
                        position: 'is-top-right',
                        type: this.toastType,
                        hasIcon: true
                    })
                    this.isModalLoading = false
                })
            },
            action(row,index){
                this.showModal = true
                this.row = row
                this.index = index
            },
            deleteAll(){
                this.isModalLoading = true
                axios.delete(`/api/admin/categories/${this.row.id}`)
                .then(res=>{
                    this.tableData.splice(this.index,1)
                    this.toastMessage = 'Delete Categories Successfully' 
                    this.toastType = 'is-success'
                    this.showModal = false
                })
                .catch(err=>{
                    this.errors = err.errors
                    this.toastMessage = 'Delete Categories Failed' 
                    this.toastType = 'is-danger'
                })
                .finally(res=>{
                     this.$buefy.notification.open({
                        duration: 3000,
                        message: this.toastMessage,
                        position: 'is-top-right',
                        type: this.toastType,
                        hasIcon: true
                    })
                    this.isModalLoading = false
                })
            },
            deleteSub(id,index){
                this.isModalLoading = true
                axios.delete(`/api/admin/subcategories/${id}`)
                .then(res=>{
                    this.row.subcategories.splice(index,1)
                    this.toastMessage = 'Delete Subcategories Successfully' 
                    this.toastType = 'is-success'
                    this.showModal = false
                })
                .catch(err=>{
                    this.errors = err.errors
                    this.toastMessage = 'Delete Subcategories Failed' 
                    this.toastType = 'is-danger'
                })
                .finally(res=>{
                     this.$buefy.notification.open({
                        duration: 3000,
                        message: this.toastMessage,
                        position: 'is-top-right',
                        type: this.toastType,
                        hasIcon: true
                    })
                    this.isModalLoading = false
                })
            },
            updateCategory(){
                axios.patch(`/api/admin/categories/${this.selected.id}`,this.selected)
                .then(res=>{
                    this.selected = null
                    this.toastMessage = 'Update Categories Successfully' 
                    this.toastType = 'is-success'
                    this.showModal = false
                })
                .catch(err=>{
                    this.loadData()
                    this.errors = err.errors
                    this.toastMessage = 'Update Categories Failed' 
                    this.toastType = 'is-danger'
                })
                .finally(res=>{
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
    .sub-delete{
        display: flex;
        justify-content: space-between;
        align-items: center;
        vertical-align: middle;
        padding-bottom: 1rem;
    }
</style>