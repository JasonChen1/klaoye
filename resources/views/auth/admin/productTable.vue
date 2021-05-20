<template>
    <div>
        <div class="feature-wp">
            <b-field class="file is-info mr-1" :class="{'has-name': !!file}">
                <b-upload v-model="file" class="file-label">
                    <span class="file-cta">
                        <b-icon class="file-icon" icon="upload"></b-icon>
                        <span class="file-label">Import Products</span>
                    </span>
                </b-upload>
            </b-field>
            <b-button type="is-info" @click="addProduct">
                <b-icon icon="plus" size="is-small"></b-icon>
                <span >Add Product</span>
            </b-button>
        </div>
        <div class="progress-wp" v-if="progressVal>0">
            <b-progress type="is-success" :value="progressVal" show-value format="percent" ></b-progress>
        </div>
        <b-notification :closable="false">
            <b-table
                class="prod-table"
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
                >

                <b-table-column field="code" label="Code" sortable searchable v-slot="props">
                    {{ props.row.code }}
                </b-table-column>
                <b-table-column field="name" label="Name" sortable searchable v-slot="props">
                    <div class="d-flex">
                        <div>
                            <span class="cursor" @click="activeImageModal(props.row.image)">
                                <img :src="props.row.image" :alt="props.row.name" >
                            </span>
                        </div>
                        <span class="pl-2" v-html="props.row.name"></span>
                    </div>
                </b-table-column>
                <b-table-column field="price" label="Price" width="120" sortable searchable v-slot="props">
                    <p><strong>Price:</strong>${{ props.row.price }}</p>
                </b-table-column>
                <b-table-column field="stock" label="Stock" width="120" sortable searchable v-slot="props">
                    {{ props.row.stock }}
                </b-table-column>
                <b-table-column field="description" label="Description" width="120" sortable searchable v-slot="props">
                    <span v-html="props.row.description"></span>
                </b-table-column>
                <b-table-column field="details" label="Details" searchable v-slot="props">
                    <div class="color-block-wp">
                        <Poptip placement="top" width="200" word-wrap v-for="(color,i) in props.row.colours" :key="i" v-if="props.row.colours.length>0">
                            <div class="color-block" :style="`background: ${color.color_code}`">
                                <span class="cross">x</span>
                            </div>
                            <div class="api" slot="content">
                                <p><i class="ivu-icon ivu-icon-ios-help-circle"></i> Are you sure you want to remove this colour?</p>
                                <div class="poptip-footer">
                                    <b-button size="is-small" @click.prevent="closePoptip">Cancel</b-button>
                                    <b-button size="is-small" type="is-info" class="ml-2" @click.prevent="deleteColor(props,color.id,i)">Remove</b-button>
                                </div>
                            </div>
                        </Poptip>
                        <div class="color-block" style="background: #fff; border: 1px solid #000;">
                            <span class="cross" style="color: #000 !important;" @click.prevent="addColourModal(props)">+</span>
                        </div>
                    </div>
                </b-table-column>
                <b-table-column field="action" label="Action" width="110" v-slot="props">
                    <b-switch 
                        v-model="props.row.status==1?true:false" 
                        type="is-info"
                        @click.prevent.native="activeProduct(props.row,props.row.status==1?0:1)">
                        {{props.row.status==1? 'Active':'Inactive'}}
                    </b-switch>
                    <b-button type="is-info" icon-left="pencil-outline" @click.prevent="editProduct(props.row)">Edit</b-button>
                </b-table-column>
            </b-table>
            <c-pagination 
                :total="total"
                :currentPage="page"
                :per-page="perPage"
                @onPageChange="onPageChange"></c-pagination>
            <b-loading :is-full-page="false" :active.sync="isLoading" :can-cancel="false"></b-loading>
        </b-notification>
        <b-modal :active.sync="showColorModal" has-modal-card>
            <div class="modal-card color-card">
                <b-notification :closable="false">
                    <header class="modal-card-head">
                        <p class="modal-card-title">Add Colour</p>
                    </header>
                    <section class="modal-card-body color-card-body">
                        <form>
                            <div class="form-input color-wp">
                                <b-field>
                                    <v-swatches
                                    v-model="colorForm.color_code"
                                    show-fallback
                                    shapes="circles"
                                    fallback-input-type="color"
                                    popover-x="right"></v-swatches>  
                                </b-field>
                                <span class="err d-flex" v-if="colorErrors.code" >
                                    {{colorErrors.code[0]}}
                                </span>
                            </div>
                        </form>
                    </section>
                    <div class="modal-card-foot">
                        <b-button type="is-danger" @click="showColorModal=false">Cancel</b-button>
                        <b-button type="is-success" :loading="isModalLoading" @click.prevent="addColourSubmit">Add</b-button>
                    </div>
                    <b-loading :is-full-page="false" :active.sync="isModalLoading" :can-cancel="false"></b-loading>
                </b-notification>
            </div>
        </b-modal>

        <b-modal :active.sync="showImageModal"  v-if="imageUrl">
            <p class="image is-4by3">
                <img :src="imageUrl">
            </p>
        </b-modal>

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
                    <form>
                        <div class="form-input">
                            <b-field label="Code">
                                <b-input type="text" class="" v-model="form.code"  @keyup="errors.code=''" placeholder="Code" required></b-input>
                            </b-field>
                            <span class="err d-flex" v-if="errors.code" >
                                {{errors.code[0]}}
                            </span>
                        </div>
                        <div class="form-input">
                            <b-field label="Name">
                                <b-input type="text" class="" v-model="form.name"  @keyup="errors.name=''" placeholder="Name" required></b-input>
                            </b-field>
                            <span class="err d-flex" v-if="errors.name" >
                                {{errors.name[0]}}
                            </span>
                        </div>
                        <div class="form-input">
                            <b-field label="Price">
                                <b-input type="text" class="" v-model="form.price"  @keyup="errors.price=''" placeholder="Price" required></b-input>
                            </b-field>
                            <span class="err d-flex" v-if="errors.price" >
                                {{errors.price[0]}}
                            </span>
                        </div>
                        <div class="form-input">
                            <b-field label="Discount">
                                <b-input type="text" class="" v-model="form.discount"  @keyup="errors.discount=''" placeholder="Discount" required></b-input>
                            </b-field>
                            <span class="err d-flex" v-if="errors.discount" >
                                {{errors.discount[0]}}
                            </span>
                        </div>
                        <div class="form-input">
                            <b-field label="size">
                                <b-input type="text" class="" v-model="form.size"  @keyup="errors.size=''" placeholder="size"></b-input>
                            </b-field>
                            <span class="err d-flex" v-if="errors.size" >
                                {{errors.size[0]}}
                            </span>
                        </div>
                        <div class="form-input">
                            <b-field label="Stock">
                                <b-input type="text" class="" v-model="form.stock"  @keyup="errors.stock=''" placeholder="Stock" required></b-input>
                            </b-field>
                            <span class="err d-flex" v-if="errors.stock" >
                                {{errors.stock[0]}}
                            </span>
                        </div>
                        <div class="form-input">
                            <b-field label="Description">
                                <b-input type="textarea" class="" v-model="form.description"  @keyup="errors.description=''" placeholder="Description" ></b-input>
                            </b-field>
                            <span class="err d-flex" v-if="errors.description" >
                                {{errors.description[0]}}
                            </span>
                        </div>
                        <div class="form-input">
                            <b-field label="Image">
                                <b-upload v-model="form.images"
                                    multiple
                                    drag-drop>
                                    <section class="section">
                                        <div class="content has-text-centered">
                                            <p>
                                                <b-icon
                                                    icon="upload"
                                                    size="is-large">
                                                </b-icon>
                                            </p>
                                            <p>Drop your files here or click to upload</p>
                                        </div>
                                    </section>
                                </b-upload>
                            </b-field>
                            <div class="preview-container" >
                                <div class="preview-wrapper" v-for="(img,i) in form.images" :key="i" v-if="form.images.length>0">
                                    <div class="overlay">
                                        <div class="center">
                                            <span @click.prevent="removeImg(i)">
                                                <b-icon class="icon-white" icon="trash-can-outline" size="is-medium" ></b-icon>
                                            </span>
                                        </div>
                                    </div>
                                    
                                    <img :src="previewImg(img)" class="preview-img">
                                </div>
                            </div>
                        </div>
                    </form>
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
    import {Poptip} from 'view-design';
    import VSwatches from 'vue-swatches'
    import "vue-swatches/dist/vue-swatches.css"
    import Pagination from '@components/pagination';

    export default {
        name: 'ProductTable',
        components: {
            Poptip,
            'c-pagination':Pagination,
            VSwatches
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
                showImageModal:false,
                showColorModal:false,
                showBulkModal:false,
                imageUrl:null,
                mReload:0,
                // form
                form: new Form({
                    code:'',
                    name:'',
                    price:'',
                    discount:'',
                    stock:'',
                    description:'',
                    size:'',
                    status:1,
                    images:[],
                }),
                errors:[],
                colorForm: new Form({
                    color_code:'#178da0',
                    id:'',
                }),
                colorErrors:[],
                // import
                file:null,
                productDescription:null,
                // sync btn 
                syncBtn:false,
                btnLoading:false,
                progressVal:0,
                // bulk edit
                radio:null,
            }
        },
        created(){
            this.loadData()
        },
        watch:{
            // file(){
            //     if(this.file){
            //         this.importProducts()
            //     }
            // },
        },
        filters: {
            truncate(value, length) {
                return value.length > length
                    ? value.substr(0, length) + '...'
                    : value
            }
        },
        methods:{
            previewImg(file){
                return URL.createObjectURL(file)
            },
            removeImg(index){
                this.form.images.splice(index,1)
            },
            exportStock(){
                axios({
                    url: '/api/admin/export/stocks',
                    method: 'GET',
                    responseType: 'blob'
                }).then((res) => {
                    var fileURL = window.URL.createObjectURL(new Blob([res.data]));
                    var fileLink = document.createElement('a');
                    fileLink.href = fileURL;
                    fileLink.setAttribute('download', 'products.xlsx');
                    document.body.appendChild(fileLink);
                    fileLink.click();
                })
                .catch(err=>{
                    console.log(err)
                })
            },
            deleteColor(row,id,i){
                axios.delete(`/api/colour/${id}`)
                .then(res=>{
                    row.colours.splice(i,1)
                    this.closePoptip()
                })
                .catch(err=>{})
            },
            addColourModal(row){
                this.row = row
                this.colorForm.id = row.id
                this.showColorModal = true
            },
            addColourSubmit(){
                this.isModalLoading = true
                this.colorForm.submit('post',`/api/colour`)
                .then(res=>{
                    this.row.colours.push(res)
                    this.showColorModal = false
                    this.toastMessage = this.$t('admin.add_colours_success')
                    this.toastType = 'is-success'
                })
                .catch(err=>{
                    this.colorErrors = err
                    this.toastMessage = this.$t('admin.add_colours_failed')
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
            activeImageModal(code){
                this.showImageModal = true
                this.imageUrl =`https://ximiphoto.oss-cn-hangzhou.aliyuncs.com/thumb/${code}.jpg?x-oss-process=style/yuan`
            },
            loadData(){
                this.isLoading = true;
                axios.get(`/api/admin/products?page=${this.page}`, {
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
            addProduct(){
                this.modalTitle = "Add Product"
                this.modalBtn = 'Add'
                this.modalType = 'add'
                this.showModal = true
            },
            editProduct(row){
                this.modalTitle = "Edit Product"
                this.modalBtn = 'Edit'
                this.modalType = 'edit'
                this.row = row
                Object.keys(row).map(field=>{
                    this.form[field] = row[field]
                })
                this.showModal = true
            },
            activeProduct(row,status){
                let pid = row.product_id
                axios.post(`/api/admin/product/set`,{
                    id:row.id,
                    status:status
                })
                .then(res=>{
                    row.status=res.data
                    this.toastMessage = 'Update Product Successfully'
                    this.toastType = 'is-success'
                })
                .catch(err=>{
                    this.toastMessage = 'Update Product Failed'
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
            modalSubmit(){
                let action = this.modalType==='add'?'post':
                             this.modalType==='edit'?'patch':
                             this.modalType==='delete'?'delete':''
                let query = this.modalType==='add'?'':`/${this.row.id}`
                this.isModalLoading = true
                this.form.submit(action,`/api/admin/products${query}`,true)
                .then(res=>{
                    Object.keys(this.row).map(field=>{
                        this.row[field] = res[field]
                    })
                    this.showModal = false
                    this.toastMessage = `${this.modalTitle} Successfully`
                    this.toastType = 'is-success'
                })
                .catch(err=>{
                    this.errors = err.errors
                    this.toastMessage = `${this.modalTitle} failed`
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
            importProducts(){
                var formData = new FormData()
                var file = this.file
                formData.append("file", file);
                this.isLoading = true
                axios.post(`/api/import/products`,formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                })
                .then(res=>{
                    if(res.data.current_page<res.data.last_page){
                        this.continueImportProducts(2,res.data.fileData)
                        this.progressVal = (100/res.data.last_page)*1
                    }else{
                        this.file = null
                        this.loadData()
                        this.$buefy.notification.open({
                            duration: 3000,
                            message: this.$t('admin.import_product_success'),
                            position: 'is-top-right',
                            type: 'is-success',
                            hasIcon: true
                        })
                        this.isLoading = false
                    }
                })
                .catch(err=>{
                    this.$buefy.notification.open({
                        duration: 3000,
                        message: this.$t('admin.import_product_failed'),
                        position: 'is-top-right',
                        type: 'is-danger',
                        hasIcon: true
                    })
                    this.errors = err.response.data
                    this.isLoading = false
                })
            },
            continueImportProducts(next,data){
                axios.post(`/api/import/products?page=${next}`,{
                    fileData:data
                })
                .then(res=>{
                    let n =parseInt(res.data.current_page)+1
                    if(n <=res.data.last_page){
                        this.progressVal = (100/res.data.last_page)*n
                        this.continueImportProducts(n,res.data.fileData)
                    }else{
                        this.file = null
                        this.loadData()
                        this.$buefy.notification.open({
                            duration: 3000,
                            message: this.$t('admin.import_product_success'),
                            position: 'is-top-right',
                            type: 'is-success',
                            hasIcon: true
                        })
                        this.isLoading = false
                        this.progressVal = 0
                    }
                })
                .catch(err=>{
                    this.$buefy.notification.open({
                        duration: 3000,
                        message: this.$t('admin.import_product_failed'),
                        position: 'is-top-right',
                        type: 'is-danger',
                        hasIcon: true
                    })
                    this.errors = err.response.data
                    this.isLoading = false
                })
            },
            closePoptip(){
                $('.ivu-poptip').click();
            },
        }
    }
</script>
<style lang="scss" scoped>
    .radio-wp{
        display: flex;
        flex-wrap: wrap;
    }
    .progress-wp{
        padding: 1rem;
    }
    .add-color-block-btn{
        border: 1px solid #000;
        width: 25px;
        height: 25px;
        border-radius: 50%;
        display: flex;
        justify-content: center;
        align-items: center;
        &:hover{
            background-color:rgba(192,192,192,0.3);
        }
    }
    .color-block-wp{
        position: relative;
        display: flex;
        flex-wrap: wrap;
        .color-block{
            margin: 0.2rem 0.2rem 0 0;
            border-radius: 50%;
            width: 25px;
            height: 25px;
            .cross{
                border-radius: 50%;
                position: relative;
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100%;
                color: #fff;
                cursor:pointer;
                &:hover{
                    background-color:rgba(192,192,192,0.3);
                }
            }
        }
    }
    .color-card-body{
        min-height: 400px;
    }
    .feature-wp{
        display: flex;
        padding: 1rem;
    }
    .form-input{
        margin-bottom: 1rem;
    }
    .preview-wrapper, .preview-wrapper > *{
        width: 90px;
        height: 90px;
        border-radius: 5px;
    }
    .preview-container{
        display: flex;
        flex-wrap: wrap;
    }
    .preview-wrapper{
        margin: 1rem 1rem 1rem 0;
        position: relative;
        .overlay{
            position: absolute;
            display: none;
        }
        &:hover{
            .overlay{
                display: flex;
                justify-content: center;
                background: rgba(0, 0, 0, 0.5);
                .center{
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    .icon-white{
                        color: #fff;
                        cursor: pointer;
                    }
                }
            }
        }
    }
</style>