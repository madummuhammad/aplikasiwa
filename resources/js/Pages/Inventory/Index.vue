<script>
import { Head, useForm, Link } from '@inertiajs/vue3';
import { ref, watch } from "vue";
import Layout from "@/Layouts/main.vue";
import PageHeader from "@/Components/page-header.vue";
import Slider from "@vueform/slider";
import Multiselect from "@vueform/multiselect";
import "@vueform/multiselect/themes/default.css";
import Swal from "sweetalert2";
import animationData from "@/Components/widgets/msoeawqm.json";
import Lottie from "@/Components/widgets/lottie.vue";
import { Global } from '../../global'
export default {
    props:{
        stock:Object,
        allProduct:Object,
        product:Object,
    },
    setup(props) {
        console.log('props',props)
        let stockData=ref(props.stock);

        let productData=ref(props.allProduct);

        let product=ref(props.product)

        console.log('product',product.value)

        let productOption=ref([]);

        var productSelected=ref(product.value.id);

        let variationOption=ref([]);
        var variationSelected=ref(null)
        product.value.item_product.forEach((item,index)=>{


var label=item.variation.variation_name;

if(item.variation.child_variation_name!==null){
    label=label+'-'+item.variation.child_variation_name
}
variationOption.value[index]={
    value:item.id,
    label:label
}
})
        console.log(props)

        let option=[]
        productData.value.forEach((item,index)=>{
            option[index]={
                label:item.name,
                value:item.id
            }
        })

        productOption.value=option;

        console.log('option',productOption.value)


        const formatDate = (timestamp) => {
            const date = new Date(timestamp);
            const options = {
                year: 'numeric',
                month: 'short',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                hour12: true,
            };

            return date.toLocaleString('en-US', options);
        }

        const getList=async ()=>{
            console.log(productSelected.value)
            axios.get('/api/inventory/list/'+productSelected.value+'?item_product_id='+variationSelected.value)
            .then(response => {
                console.log('Data berhasil dikirim ke backend:', response.data);

                let data=response.data;
                stockData.value=data.data;

                product.value=data.product;

                product.value.item_product.forEach((item,index)=>{

                    var label=item.variation.variation_name;

                    if(item.variation.child_variation_name!==null){
                        label=label+'-'+item.variation.child_variation_name
                    }
                    variationOption.value[index]={
                        value:item.id,
                        label:label
                    }
                })

                console.log(stockData.value)
            })
            .catch(error => {
                console.error('Error saat mengirim data ke backend:', error);
            });
        }
        watch(() => productSelected, (newFields, oldFields) => {
            getList();
        }, { deep: true });
        watch(() => variationSelected, (newFields, oldFields) => {
            getList();
        }, { deep: true });

        var addStockShow=ref(false)

        const openAddStockModal=()=>{

            addStockShow.value=true;
            console.log(addStockShow.value)
        }

        let stockIncrement=ref({
            item_product_id:null,
            stock:0,
            note:null
        })

        const addStock=async ()=>{

            if(product.value.many_variation_status==false){
                stockIncrement.value.item_product_id=product.value.item_product[0].id
            } else {
                stockIncrement.value.item_product_id=variationSelected.value
            }

            axios.post('/api/inventory/add',stockIncrement.value)
            .then(response => {
                console.log('Data berhasil dikirim ke backend:', response.data);

                let data=response.data;

                if(data.status=='success'){
                    getList();
                    addStockShow.value=false;
                }

            })
            .catch(error => {
                console.error('Error saat mengirim data ke backend:', error);
            });
        }

        var reduceStockShow=ref(false)

        const openReduceStockModal=()=>{

            reduceStockShow.value=true;
            console.log(reduceStockShow.value)
        }

        let stockDecrement=ref({
            item_product_id:null,
            stock:0,
            note:null
        })

        const reduceStock=async ()=>{

            if(product.value.many_variation_status==false){
                stockDecrement.value.item_product_id=product.value.item_product[0].id
            } else {
                stockDecrement.value.item_product_id=variationSelected.value
            }

            axios.post('/api/inventory/reduce',stockDecrement.value)
            .then(response => {
                console.log('Data berhasil dikirim ke backend:', response.data);

                let data=response.data;

                if(data.status=='success'){
                    getList();
                    reduceStockShow.value=false;
                }

            })
            .catch(error => {
                console.error('Error saat mengirim data ke backend:', error);
            });
        }



        var adjustmentStockShow=ref(false)

        const openAdjustmentStockModal=()=>{

            adjustmentStockShow.value=true;
            console.log(adjustmentStockShow.value)
        }

        let stockAdjustment=ref({
            item_product_id:null,
            stock:0,
            note:null
        })

        const adjustmentStock=async ()=>{

            if(product.value.many_variation_status==false){
                stockAdjustment.value.item_product_id=product.value.item_product[0].id
            } else {
                stockAdjustment.value.item_product_id=variationSelected.value
            }

            axios.post('/api/inventory/adjustment',stockAdjustment.value)
            .then(response => {
                console.log('Data berhasil dikirim ke backend:', response.data);

                let data=response.data;

                if(data.status=='success'){
                    getList();
                    adjustmentStockShow.value=false;
                }

            })
            .catch(error => {
                console.error('Error saat mengirim data ke backend:', error);
            });
        }

        return {
            stockData,
            formatDate,
            getList,

            product,
            productData,
            productOption,
            productSelected,

            variationOption,
            variationSelected,

            addStockShow,
            openAddStockModal,

            stockIncrement,
            addStock,

            reduceStockShow,
            openReduceStockModal,

            stockDecrement,
            reduceStock,

            adjustmentStockShow,
            openAdjustmentStockModal,

            stockAdjustment,
            adjustmentStock
        }
    },
    data(props) {
    },
    computed: {
    },
    watch: {
    },
    created() {

    },
    filters: {
    },
    beforeMount() {

    },

    methods: {
    },
    mounted() {
    },

    components: {
        Layout,
        PageHeader,
        lottie: Lottie,
        Slider,
        Multiselect,
        Head, Link
    },
};
</script>

<template>
    <Layout>

        <Head title="Inventory" />
        <PageHeader title="Inventory" pageTitle="Dashboard" />
        <div class="mb-3">
            <BRow class="mb-3">
                <BCol lg="4">
                    <Multiselect class="form-control cursor-pointer" v-model="productSelected"
                    :close-on-select="true" placeholder="Pilih Produk" :searchable="false"
                    :create-option="false" :options="productOption" />

                </BCol>
                <BCol lg="4" v-if="product.many_variation_status==true">
                    <Multiselect class="form-control cursor-pointer" v-model="variationSelected"
                    :close-on-select="true" placeholder="All Variation" :searchable="false"
                    :create-option="false" :options="variationOption" />

                </BCol>
            </BRow>
        </div>
        <div class="mb-3">
            <BRow class="g-4">
                <BCol sm="auto" class="d-flex">
                    <div class="me-2">
                        <Button  @click="openAdjustmentStockModal" class="btn btn-sm btn-outline-primary"><i class="bx bx-edit-alt me-1"></i> Adjust Stock</Button>
                    </div>
                    <div class="me-2">
                        <Button @click="openAddStockModal" class="btn btn-sm btn-outline-success"><i class="ri-add-line align-bottom me-1"></i> Add Stock</Button>
                    </div>
                    <div class="me-2">
                        <Button  @click="openReduceStockModal" class="btn btn-sm btn-outline-danger"><i class="bx bx-minus me-1"></i> Reduce Stock</Button>
                    </div>
                </BCol>
            </BRow>
        </div>


        <BRow>
            <BCol xl="12" lg="8">
                <div>
                    <BCard no-body>
                        <BCardBody>
                            <div class="tab-content text-muted">
                                <div class="tab-pane active" id="productnav-all" role="tabpanel">
                                    <div id="table-product-list-all" class="table-card gridjs-border-none table-responsive">
                                        <table class="table align-middle table-nowrap mb-0" id="customerTable">
                                            <thead class="table-light">
                                                <tr class="text-muted">
                                                    <th scope="col" style="width: 50px">
                                                      No
                                                    </th>
                                                    <th class="sort" data-sort="product" @click="onSort('id')">Name</th>
                                                    <th class="sort" data-sort="price" @click="onSort('price')">Description</th>
                                                    <th class="sort" data-sort="price" @click="onSort('price')">In</th>
                                                    <th class="sort" data-sort="price" @click="onSort('price')">Out
                                                    </th>
                                                    <th class="sort" data-sort="price" @click="onSort('price')">Stock
                                                    </th>
                                                    <th class="sort" data-sort="price" @click="onSort('price')">Changed By
                                                    </th>
                                                    <th class="sort" data-sort="price" @click="onSort('price')">Date
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody class="list form-check-all">
                                                <tr class="gridjs-tr" v-for="(item, index) in stockData">
                                                    <td data-column-id="productListAllCheckbox" class="gridjs-td">
                                                        {{ index+1 }}
                                                    </td>
                                                    <td data-column-id="stock" class="gridjs-td">
                                                        {{ item.item_product.product.name }}
                                                        <span v-if="item.item_product.product.many_variation_status">
                                                            <span>- {{ item.item_product.variation.variation_name }}</span>
                                                            <span v-if="item.item_product.variation.child_variation_name">- {{ item.item_product.variation.child_variation_name }}</span>
                                                        </span>
                                                    </td>
                                                    <td data-column-id="stock" class="gridjs-td">
                                                   {{ item.note }}
                                                    </td>
                                                    <td data-column-id="stock" class="gridjs-td">
                                                      <span class="text-success fw-bold" v-if="item.stock_type=='Addition'">{{ item.qty }} <i class="bx bx-up-arrow"></i></span>
                                                      <span v-else>-</span>
                                                    </td>
                                                    <td data-column-id="stock" class="gridjs-td">
                                                        <span class="text-danger fw-bold" v-if="item.stock_type=='Deduction'">{{ item.qty }} <i class="bx bx-down-arrow"></i></span>
                                                        <span v-else>-</span>

                                                    </td>
                                                    <td data-column-id="stock" class="gridjs-td">
                                                        {{item.stock}}
                                                    </td>
                                                    <td data-column-id="stock" class="gridjs-td">
                                                        {{item.by}}
                                                    </td>
                                                    <td data-column-id="action" class="gridjs-td">
                                                        {{formatDate(item.created_at) }}
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </BCardBody>
                    </BCard>
                </div>
            </BCol>
        </BRow>
        <BModal v-model="addStockShow" title="Add Stock" hide-footer>
            <div class="card-body">
                <div class="row">
                    <div class="col-6">
                        <label for="">Stock Increment</label>
                        <input type="number" v-model="stockIncrement.stock" class="form-control">
                    </div>
                    <div class="col-6">
                        <label for="">Description</label>
                        <textarea name="" v-model="stockIncrement.note" id="" class="form-control" cols="30" rows="4"></textarea>
                    </div>
                </div>
                <div class="d-flex justify-content-end mt-3">
                    <button class="btn btn-secondary" @click="addStock">SIMPAN</button>
                </div>
            </div>
</BModal>

<BModal v-model="reduceStockShow" title="Reduce Stock" hide-footer>
            <div class="card-body">
                <div class="row">
                    <div class="col-6">
                        <label for="">Stock Decrement</label>
                        <input type="number" v-model="stockDecrement.stock" class="form-control">
                    </div>
                    <div class="col-6">
                        <label for="">Description</label>
                        <textarea name="" v-model="stockDecrement.note" id="" class="form-control" cols="30" rows="4"></textarea>
                    </div>
                </div>
                <div class="d-flex justify-content-end mt-3">
                    <button class="btn btn-secondary" @click="reduceStock">SIMPAN</button>
                </div>
            </div>
</BModal>

<BModal v-model="adjustmentStockShow" title="Adjustment Stock" hide-footer>
            <div class="card-body">
                <div class="row">
                    <div class="col-6">
                        <label for="">Stock Adjustment</label>
                        <input type="number" v-model="stockAdjustment.stock" class="form-control">
                    </div>
                    <div class="col-6">
                        <label for="">Description</label>
                        <textarea name="" v-model="stockAdjustment.note" id="" class="form-control" cols="30" rows="4"></textarea>
                    </div>
                </div>
                <div class="d-flex justify-content-end mt-3">
                    <button class="btn btn-secondary" @click="adjustmentStock">SIMPAN</button>
                </div>
            </div>
</BModal>
    </Layout>
</template>