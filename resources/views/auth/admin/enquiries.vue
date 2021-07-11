<template>
    <div>
        <b-notification :closable="false">
            <b-table
                class="order-table"
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
                >
                <b-table-column field="name" label="Name" sortable searchable v-slot="props">
                    {{ props.row.name }}
                </b-table-column>
                <b-table-column field="created_at" label="Order Date" sortable searchable v-slot="props">
                    <span class="tag is-success">
                        {{ (new Date(Date.parse(props.row.created_at))).toISOString().slice(0, 19).replace(/-/g, "/").replace("T", " ") }}
                    </span>
                </b-table-column>
                <b-table-column field="email" label="Email" width="120" sortable searchable v-slot="props">
                    {{ props.row.email }}
                </b-table-column>
                <b-table-column field="phone" label="Phone" width="120" sortable searchable v-slot="props">
                    {{ props.row.phone }}
                </b-table-column>
                <b-table-column field="product" label="Product" width="120" sortable searchable v-slot="props">
                    {{ props.row.product }}
                </b-table-column>
                <b-table-column field="message" label="Message" sortable searchable v-slot="props">
                    <p v-html="props.row.message"></p>
                </b-table-column>
            </b-table>
            <c-pagination 
                :total="total"
                :currentPage="page"
                :per-page="perPage"
                @onPageChange="onPageChange"></c-pagination>
            <b-loading :is-full-page="false" :active.sync="isLoading" :can-cancel="false"></b-loading>
        </b-notification>

    </div>
</template>

<script>
    import _ from 'lodash';
    import Pagination from '@components/pagination';

    export default {
        name: 'Enquiries',
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
                // toast
                toastMessage:'',
                toastPosition:'is-top',
                toastType:'',
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
                axios.get(`/api/admin/enquiry?page=${this.page}`, {
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
        }
    }
</script>
<style lang="scss" scoped>
    
</style>