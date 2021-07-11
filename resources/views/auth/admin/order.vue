<template>
    <div>
        <b-notification :closable="false">
            <b-table
                class="order-table"
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
                <b-table-column field="no" label="Order No" sortable searchable v-slot="props">
                    {{ props.row.no }}
                </b-table-column>
                <b-table-column field="created_at" label="Order Date" sortable searchable v-slot="props">
                    <span class="tag is-success">
                        {{ (new Date(Date.parse(props.row.created_at))).toISOString().slice(0, 19).replace(/-/g, "/").replace("T", " ") }}
                    </span>
                </b-table-column>
                <b-table-column field="paid" label="Paid" width="120" sortable v-slot="props">
                    {{ props.row.paid?'Yes':'No' }}
                </b-table-column>
                <b-table-column field="status" label="Status" width="120" sortable v-slot="props">
                    <!-- 0 - default, 1 - 待发货, 2 - 已发货, 3 - 已签收, 4 - 退款 -->
                    {{ props.row.status==1? 'To be shipped':
                       props.row.status==2? 'Shipped':
                       props.row.status==3? 'Received': 
                       props.row.status==4? 'Refund': 'Unpaid' }}
                </b-table-column>
                <b-table-column field="payment_type" label="Payment Type" width="120" sortable v-slot="props">
                    {{ props.row.payment_type }}
                </b-table-column>
                <b-table-column field="total" label="Total" v-slot="props">
                    <strong>Subtotal:</strong> ${{props.row.subtotal}}<br>
                    <strong>Discount:</strong> -${{props.row.discount_total}}<br>
                    <strong>Delivery:</strong> +${{props.row.delivery_total}}<br>
                    <strong>Total:</strong> ${{props.row.total}}
                </b-table-column>
                <b-table-column field="action" label="Action" width="110" v-slot="props" >
                    <b-button icon-left="pencil-outline" @click.prevent.native="action(props.row,'edit')" type="is-info"></b-button>
                    <b-button icon-left="trash-can-outline" @click.prevent.native="deleteAction(props.row,props.index)" type="is-danger"></b-button>
                </b-table-column>
            </b-table>
            <c-pagination 
                :total="total"
                :currentPage="page"
                :per-page="perPage"
                @onPageChange="onPageChange"></c-pagination>
            <b-loading :is-full-page="false" :active.sync="isLoading" :can-cancel="false"></b-loading>
        </b-notification>

        <b-sidebar
            v-if="selected"
            class="order-sidebar"
            type="is-light"
            :fullheight="fullheight"
            :overlay="overlay"
            :right="right"
            :open.sync="sliderOpen"
            :can-cancel="['escape', 'outside']"
            >
            <div class="p-1">
                <div class="customer-detail">
                    <h1>Order Details</h1>
                    <hr style="border: 1px solid #dbdbdb; margin: 1rem 0 1rem 0;">
                    <p><strong>Order No:</strong> {{selected.no}}</p>
                    <div v-if="shipping">
                        <p><strong>Customer Name:</strong> {{ shipping.name}}</p>
                        <p><strong>Address:</strong></p>
                        <p>{{shipping.address}}</p>
                        <p>{{shipping.country}}, {{shipping.state}}, {{shipping.city}}</p>
                        <p><strong>Postal Code:</strong> {{shipping.postal_code}}</p>
                        <p><strong>Phone:</strong> {{shipping.phone}}</p>
                        <p><strong>Email:</strong> {{shipping.email}}</p>
                        <br>                      
                    </div>
                </div>
                <b-table 
                    class="prod-table"
                    v-if="items.length>0"
                    :data="items"
                    :narrowed="isNarrowed"
                    :hoverable="isHoverable"
                    :paginated="isPaginated"
                    :per-page="detailPerPage"
                    aria-next-label="Next page"
                    aria-previous-label="Previous page"
                    aria-page-label="Page"
                    aria-current-label="Current page"
                    :pagination-simple="isPaginationSimple"
                    >
                    <b-table-column v-slot="props" field="product.code" label="Code" >
                        {{ props.row.product.code }}
                    </b-table-column>
                    <b-table-column v-slot="props" field="product.name" label="Name">
                        <div class="tb-pd">
                            <div>
                                <img :src="`/storage/thumbnail/${props.row.product.image.image_url}`" :alt="props.row.name" class="inner-img">
                            </div>
                            <span class="pl-2" v-html="props.row.product.name"></span>
                        </div>
                    </b-table-column>
                    <b-table-column v-slot="props" field="color_code" label="Colour">
                        <div class="cart-color-wp">
                            <p class="cb ml-2" :style="`background: ${props.row.color_code};`"></p>
                        </div>
                    </b-table-column>
                    <b-table-column v-slot="props" field="product.price" label="Unit Price">
                        <div v-if="props.row.discount>0">
                            <span>${{(props.row.product.price-props.row.product.discount).toFixed(2)}}</span>
                            <s>${{ props.row.product.price }}</s>
                        </div>
                        <span v-else>${{ props.row.price }}</span>
                    </b-table-column>
                    <b-table-column v-slot="props" field="num" label="Number">
                        {{ props.row.num }}
                    </b-table-column>
                    <b-table-column v-slot="props" field="subtotal" label="Subtotal">
                        ${{ props.row.subtotal }}
                    </b-table-column>
                    
                </b-table>
                <div class="customer-detail-bt-right">
                    <p><strong>Subtotal: </strong>{{selected.subtotal }}</p>
                    <p style="color: red;">
                        <strong>Discount: </strong>-{{selected.discount_total}} 
                    </p>
                    <p><strong>Delivery Cost: </strong>+{{selected.delivery_total}}</p>
                    <p><strong>Total: </strong>{{selected.total}}</p>
                </div>
            </div>
        </b-sidebar>

        <b-modal :active.sync="showModal" 
                 has-modal-card
                 trap-focus
                 :destroy-on-hide="false"
                 aria-role="dialog"
                 aria-modal
                 @close="closeModal"
                 >
            <div class="modal-card" >
                <header class="modal-card-head">
                    <p class="modal-card-title">{{modalTitle}}</p>
                </header>
                <section class="modal-card-body">
                    <form v-if="modalType!=='delete'">
                        <div class="form-group">
                            <b-field v-if="selected">
                                <b-select v-model="form.status" required>
                                    <option value="0">Unpaids</option>
                                    <option value="1">To be shipped</option>
                                    <option value="2">Shipped</option>
                                    <option value="3">Received</option>
                                    <option value="4">Refund</option>
                                </b-select>
                            </b-field>
                            <span class="err d-flex" v-if="errors.status" >
                                {{errors.status[0]}}
                            </span>
                        </div>
                    </form>
                    <p v-else >Are you sure you want to delete this Order: <strong v-if="selected">{{selected.no}}</strong> ?</p>
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
        name: 'Order',
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
                selected:null,
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
                    status:0,                    
                }),
                errors:[],
                index:null,
                btnClicked:false,
                // slider
                shipping:null,
                items:[],
                isPaginated:true,
                detailPerPage:10,
                sliderOpen: false,
                overlay: true,
                fullheight: true,
                right: true,
                isPaginationSimple:true,
            }
        },
        created(){
            this.loadData()
        },
        watch:{
            selected(){
                this.form.status = this.selected.status
                if(!this.btnClicked){
                    this.getOrderDetails(this.selected.id)
                }
            }
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
            getOrderDetails(id){
                axios.get(`/api/admin/order/details/${id}`)
                .then(res=>{
                    this.items = res.data.items
                    this.shipping = res.data.shipping
                    this.sliderOpen=true
                })
                .catch(err=>{
                    console.log(err)
                })
            },
            loadData(){
                this.isLoading = true;
                axios.get(`/api/admin/order?page=${this.page}`, {
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
            closeModal(){
                this.btnClicked = false
            },
            action(row, type){
                this.btnClicked = true
                switch(type){
                    case "edit":
                    this.modalType = 'edit'
                    this.modalTitle = "Edit Order"
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
                this.btnClicked = true
                this.index = index
                this.modalType = 'delete'
                this.modalTitle = 'Delete Order'
                this.modalBtn = 'Delete'
                this.row = row
                this.showModal = true
            },
            modalSubmit(){
                let query =`/${this.row.id}`
                let action = this.modalType ==='delete'?'delete':'patch'

                this.isModalLoading = true
               
                this.form.submit(action,`/api/admin/order${query}`)
                .then(res=>{
                    if(this.modalType==='edit'){
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
                    this.btnClicked = false
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