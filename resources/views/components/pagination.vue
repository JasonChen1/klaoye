<template>
    <div class="pagintaed-custom">
        <div class="paginate-text">Total {{total}} Items</div>
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
    <div class="paginate-text-right">
        Jump
        <input type="number" v-model="jumpPage">
        Page
    </div>
</div>
</template>

<script>
    export default {
        name: 'Pagination',
        props:{
            total:{
                type:Number,
                require:true,
                default:0
            },
            currentPage:{
                type:Number,
                require:true,
                default:1
            },
            rangeBefore:{
                type:Number,
                require:true,
                default:2
            },
            rangeAfter:{
                type:Number,
                require:true,
                default:2
            },
            perPage:{
                type:Number,
                require:true,
                default:20
            },
        },
        data() {
            return {
                order: 'is-centered',
                jumpPage:1,
                page:1,
            }
        },
        mounted(){
            this.page = this.currentPage 
        },
        watch:{
            jumpPage(){
                if(this.jumpPage>0){
                    this.onPageChange(parseInt(this.jumpPage))
                }
            },
        },
        methods:{
            onPageChange(page) {
                this.$emit('onPageChange',page)
            },
        }
    }
</script>