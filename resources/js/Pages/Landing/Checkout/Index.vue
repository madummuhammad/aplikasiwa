<style>
.cursor-pointer {
    cursor: pointer;
}

.form-check-lg {
    width: 20px;
    height: 20px;
}
</style>
<script>
import { Head, useForm } from '@inertiajs/vue3';
import Layout from "@/Layouts/main.vue";
import PageHeader from "@/Components/page-header.vue";
import { ref, watch } from "vue";
import Multiselect from "@vueform/multiselect";
import "@vueform/multiselect/themes/default.css";

import CKEditor from "@ckeditor/ckeditor5-vue";
import ClassicEditor from "@ckeditor/ckeditor5-build-classic";
import DropZone from "@/Components/widgets/dropZone.vue";
import useVuelidate from "@vuelidate/core";
import flatPickr from "vue-flatpickr-component";
import "flatpickr/dist/flatpickr.css";
import { onMounted, onBeforeMount } from 'vue';
import { Global } from '../../../global'
export default {
    setup(props) {
        console.log('props checkout', props.checkout)
        console.log('props product', props.product)
        let checkoutData = ref(useForm(props.checkout))
        let productData = ref(props.product);
        const global = Global();

        const price = ref(0);

        productData.value.item_product.forEach((item, index) => {
            item.qty = 0;
        })

        console.log('productData', productData.value)
        const visibleFields = checkoutData.value.fields.filter((item) => item.show);
        checkoutData.value.fields = visibleFields;
        checkoutData.value.fields.forEach((item, index) => {
            item.value = "";
            if (item.field == 'Select') {
                if (item.options) {
                    let selectOption = [];
                    item.selectOption = [];
                    item.options.forEach((itemOption, indexOption) => {
                        selectOption[indexOption] = {
                            value: itemOption.text,
                            label: itemOption.text
                        }
                    })
                    item.selectOption.push(...selectOption);
                    console.log('selectOption', item)
                }
            }

        })

        let form = ref(useForm({
            payment_method: "bank_transfer",
            fields: checkoutData.value.fields,
            bump_status: false,
            item_product: {},
            total_price: 0,
            product: productData.value
        }));


        const getProvince = () => {
            axios.get('/api/rajaongkir/province')
                .then(response => {
                    let data = response.data;
                    console.log('Data berhasil dikirim ke backend:', data);
                    form.value.fields.forEach((item, index) => {
                        if (item.name == 'province') {
                            let selectOption = [];
                            item.selectOption = [];
                            data.rajaongkir.results.forEach((itemResult, indexResult) => {

                                selectOption[indexResult] = {
                                    label: itemResult.province,
                                    value: itemResult,
                                }
                            })
                            item.selectOption.push(...selectOption);
                        }
                    })
                })
                .catch(error => {
                    console.error('Error saat mengirim data ke backend:', error);
                });
        }

        const selectedProvince = ref("");

        watch(() => selectedProvince, (newFields, oldFields) => {
            console.log('watch form', newFields.value);
            form.value.fields.forEach((item, index) => {
                if (item.name == 'province') {
                    item.value = newFields.value;
                }
            })
            getCity();
        }, { deep: true });

        const getCity = async () => {
            selectedCity.value = "";
            selectedSubdistrict.value = "";
            axios.get('/api/rajaongkir/city?province_id=' + selectedProvince.value.province_id)
                .then(response => {
                    let data = response.data;
                    console.log('Data berhasil dikirim ke backend:', data);
                    form.value.fields.forEach((item, index) => {
                        if (item.name == 'city') {
                            let selectOption = [];
                            item.selectOption = [];
                            data.rajaongkir.results.forEach((itemResult, indexResult) => {

                                selectOption[indexResult] = {
                                    value: itemResult,
                                    label: itemResult.type + ' ' + itemResult.city_name
                                }
                            })
                            item.selectOption.push(...selectOption);
                        }
                    })
                })
                .catch(error => {
                    console.error('Error saat mengirim data ke backend:', error);
                });
        }

        const selectedCity = ref("");

        watch(() => selectedCity, (newFields, oldFields) => {
            console.log('watch form', newFields.value);
            form.value.fields.forEach((item, index) => {
                if (item.name == 'city') {
                    item.value = newFields.value;
                }
            })
            getKecamatan();
        }, { deep: true });

        const getKecamatan = () => {
            selectedSubdistrict.value = "";
            axios.get('/api/rajaongkir/kecamatan?city_id=' + selectedCity.value.city_id)
                .then(response => {
                    let data = response.data;
                    form.value.fields.forEach((item, index) => {
                        if (item.name == 'subdistrict') {
                            let selectOption = [];
                            item.selectOption = [];
                            data.rajaongkir.results.forEach((itemResult, indexResult) => {

                                selectOption[indexResult] = {
                                    value: itemResult,
                                    label: itemResult.subdistrict_name
                                }
                            })
                            item.selectOption.push(...selectOption);
                        }
                    })
                    console.log('Data berhasil dikirim ke backend:', data);
                })
                .catch(error => {
                    console.error('Error saat mengirim data ke backend:', error);
                });
        }

        const selectedSubdistrict = ref("");

        watch(() => selectedSubdistrict, (newFields, oldFields) => {
            console.log('watch form', newFields.value);
            form.value.fields.forEach((item, index) => {
                if (item.name == 'subdistrict') {
                    item.value = newFields.value;
                }
            })
            // getCity();
        }, { deep: true });

        onMounted(() => {
            getProvince();
        })

        if (productData.value.price_type == 'simple') {
            form.value.item_product = productData.value.item_product[0];
            if (productData.price_sale_status == true) {
                form.value.total_price = parseInt(productData.value.normal_price);
            } else {
                form.value.total_price = parseInt(productData.value.price_sale);
            }
        }

        watch(() => productData.value.item_product, (newFields, oldFields) => {
            form.value.item_product = [];
            if (productData.value.many_buy_status == true) {
                form.value.total_price = 0;
                productData.value.item_product.forEach((item, index) => {
                    if (parseInt(item.qty) > 0) {
                        form.value.total_price = form.value.total_price + (parseInt(item.normal_price) * item.qty)
                        item.qty = parseInt(item.qty)
                        form.value.item_product.push(item)
                    }
                })

                if (form.value.bump_status == true) {
                    form.value.total_price = form.value.total_price + parseInt(productData.value.product_bump.price)
                }

            }

            console.log('watch product data', form.value.item_product);
        }, { deep: true });

        watch(() => form.value.item_product, (newFields, oldFields) => {
            if (productData.value.many_buy_status == false) {
                form.value.total_price = parseInt(form.value.item_product.normal_price)
            }
            console.log('watch form', form.value.item_product);
        }, { deep: true });

        watch(() => form.value.bump_status, (newFields, oldFields) => {
            if (form.value.bump_status == true) {
                form.value.total_price = form.value.total_price + parseInt(productData.value.product_bump.price)
            } else {
                form.value.total_price = form.value.total_price - parseInt(productData.value.product_bump.price)
            }

            if (productData.price_type == 'simple') {
                if (form.value.bump_status == true) {
                    form.value.total_price = form.value.total_price + parseInt(productData.value.product_bump.price)
                } else {
                    form.value.total_price = form.value.total_price - parseInt(productData.value.product_bump.price)
                }
            }
            console.log('watch bump', form.value.bump_status);
        }, { deep: true });

        console.log('form', form.value);

        const submit = () => {
            // console.log(form.value)
            form.value.transform((data) => ({
                ...data
            }))
                .post(route('create-order', productData.value.url), {
                    onSuccess: () => {
                    }
                });
        }
        return {
            global,
            checkoutData,
            productData,
            form,
            price,
            submit,

            // Raja Ongkir
            getCity,
            getKecamatan,
            selectedProvince,
            selectedCity,
            selectedSubdistrict
        };
    },
    components: {
        ckeditor: CKEditor.component,
        DropZone,
        Layout,
        PageHeader,
        Multiselect,
        flatPickr,
        Head
    },
    props: {
        store: Object,
        product: Object,
        checkout: Object,
    },
    data() {
        return {
            sectionTitleShow: false,
        }
    },
};
</script>

<template>
    <Head :title="product.name" />
    <!-- <PageHeader title="Create Product" pageTitle="Product" /> -->

    <BRow class="d-flex justify-content-center mt-5 mb-5">
        <!-- End Page Checkout -->
        <BCol lg="9" xs="12">

            <!-- Right Sidebar -->
            <form @submit.prevent="submit">
                <BCard no-body class="bg-light" v-if="checkoutData.template == 'right-sidebar'">
                    <div class="row d-flex justify-content-center mt-4 mb-2">
                        <div class="col-12 col-lg-6 mb-3">
                            <img class="img-fluid w-100" :src="checkoutData.header.image" alt="">
                        </div>
                        <div class="col-12"
                            v-if="checkoutData.header.titleStatus == true || checkoutData.header.taglineStatus == true">
                            <h5 class="text-center" v-if="checkoutData.header.titleStatus == true">{{
                                checkoutData.header.title }}</h5>
                            <p class="text-center" v-if="checkoutData.header.taglineStatus == true">{{
                                checkoutData.header.tagline }}</p>
                        </div>
                    </div>
                    <BCardBody>
                        <BCard no-body>
                            <BCardBody class="pt-0 pb-0">
                                <div class="row">
                                    <div class="col-7 p-2">
                                        <div class="mb-3" v-if="product.price_type == 'variable'">
                                            <h5 class="fw-bold" v-if="product.many_buy_status == false">Pilihan Produk
                                            </h5>
                                            <template v-if="product.many_buy_status == false">
                                                <div class="row" v-if="product.many_variation_status == false">
                                                    <div class="col-6" v-for="(item, index) in product.item_product">
                                                        <div class="form-check">
                                                            <input class="form-check-input" :id="'product' + index"
                                                                v-model="form.item_product" :value="item" name="product"
                                                                type="radio" required>
                                                            <label class="form-check-label" :for="'product' + index">
                                                                {{ item.variation.variation_name }}
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row" v-if="product.many_variation_status == true">
                                                    <template v-for="(item, index) in product.item_product">
                                                        <div class="col-6" v-if="item.variation !== null">
                                                            <div class="form-check">
                                                                <input class="form-check-input" :id="'product' + index"
                                                                    v-model="form.item_product" :value="item" name="product"
                                                                    type="radio" required>
                                                                <label class="form-check-label" :for="'product' + index">
                                                                    {{ item.variation.variation_name }} - {{
                                                                        item.variation.child_variation_name }}
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </template>
                                                </div>
                                            </template>
                                            <template v-if="product.many_buy_status == true">
                                                <div class="row" v-if="product.many_variation_status == false">
                                                    <table class="table table-bordered">
                                                        <thead>
                                                            <tr>
                                                                <th>Pilihan Produk:</th>
                                                                <th>Jumlah</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr v-for="(item, index) in product.item_product">
                                                                <td class="w-75">{{ item.variation.variation_name }}</td>
                                                                <td>
                                                                    <input class="form-control" type="number"
                                                                        v-model="item.qty" />
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div class="row" v-if="product.many_variation_status == true">
                                                    <table class="table table-bordered">
                                                        <thead>
                                                            <tr>
                                                                <th>Pilihan Produk:</th>
                                                                <th>Jumlah</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <template v-for="(item, index) in product.item_product">
                                                                <tr>
                                                                    <td class="w-75">{{ item.variation.variation_name }} -
                                                                        {{ item.variation.child_variation_name }}
                                                                    </td>
                                                                    <td>
                                                                        <input class="form-control" type="number"
                                                                            v-model="item.qty" />
                                                                    </td>
                                                                </tr>
                                                            </template>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </template>
                                        </div>
                                        <div class="mb-3">
                                            <p class="fw-bold">{{ checkoutData.sectionTitle.form }}</p>
                                        </div>
                                        <div class="mb-3" v-for="(  item, index  ) in   form.fields  " :key="index">
                                            <div v-if="item.show == true">
                                                <input v-if="item.field == 'Input'" v-model="item.value"
                                                    :type="item.inputType" class="form-control"
                                                    :placeholder="item.placeholder" :required="item.required" />
                                                <Multiselect v-if="item.field == 'Select' && item.name == 'province'"
                                                    v-model="selectedProvince" class="form-select" :close-on-select="true"
                                                    :placeholder="item.placeholder" :searchable="true"
                                                    :create-option="false" :options="item.selectOption"
                                                    :required="item.required" />
                                                <Multiselect v-if="item.field == 'Select' && item.name == 'city'"
                                                    class="form-select" v-model="selectedCity" :close-on-select="true"
                                                    :placeholder="item.placeholder" :searchable="true"
                                                    :create-option="false" :options="item.selectOption"
                                                    :required="item.required" />
                                                <Multiselect v-if="item.field == 'Select' && item.name == 'subdistrict'"
                                                    class="form-select" v-model="selectedSubdistrict"
                                                    :close-on-select="true" :placeholder="item.placeholder"
                                                    :searchable="true" :create-option="false" :options="item.selectOption"
                                                    :required="item.required" />
                                                <Multiselect
                                                    v-if="item.field == 'Select' && item.name !== 'subdistrict' && item.name !== 'city' && item.name !== 'province'"
                                                    class="form-select" v-model="item.value" :close-on-select="true"
                                                    :placeholder="item.placeholder" :searchable="true"
                                                    :create-option="false" :options="item.selectOption"
                                                    :required="item.required" />
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <p class="fw-bold">{{ checkoutData.sectionTitle.payment_method }}</p>

                                            <div class="p-4 border" v-if="product.bank_transfer.status == true">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" value="bank_transfer"
                                                        v-model="form.payment_method" id="bankTransferCheckbox">
                                                    <label class="form-check-label" for="bankTransferCheckbox">
                                                        Bank Transfer
                                                    </label>
                                                </div>
                                            </div>

                                            <div class="p-4 border" v-if="product.cod.status == true">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" value="cod"
                                                        v-model="form.payment_method" id="codCheckbox">
                                                    <label class="form-check-label" for="codCheckbox">
                                                        COD
                                                    </label>
                                                </div>
                                            </div>

                                            <div class="p-4 border" v-if="product.epayment.status == true">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" value="epayment"
                                                        v-model="form.payment_method" id="epaymentCheckbox">
                                                    <label class="form-check-label" for="epaymentCheckbox">
                                                        E - Payment
                                                    </label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="border border-dashed border-danger border-2 p-2 mb-3"
                                            v-if="product.bump_status">
                                            <div class="d-flex align-items-center mb-1 bg-warning py-1 px-1 mb-1">
                                                <img class="me-2" src="@assets/images/bump-arrow.gif" alt="">
                                                <input type="checkbox" name="" class="form-check-input me-2"
                                                    v-model="form.bump_status" id="bump">
                                                <label class="form-check-label fw-bold" for="bump">Centang untuk
                                                    memesan juga</label>
                                            </div>
                                            <div class="fw-bold text-danger mb-1">{{ product.product_bump.name }} +
                                                {{ product.product_bump.price }}</div>
                                            <div class="mb-1">
                                                <img class="img-fluid w-100" :src="product.product_bump.image" alt="">
                                            </div>
                                            <!-- <div class="fs-12">Penjelasan Tentang Produk Bump Anda Disini</div> -->
                                        </div>
                                        <button @click="submit"
                                            :style="'background-color:' + checkoutData.buyButtonBackground + ';color:' + checkoutData.buyButtonTextColor"
                                            class="btn w-100 fs-4 fw-bold d-flex align-items-center justify-content-center">{{
                                                checkoutData.buyButtonText }}
                                            <i class="bx bx-right-arrow-circle ms-2"></i></button>
                                    </div>
                                    <div class="col-5 bg-light p-2">
                                        <div class="mb-3" v-if="checkoutData.productImageStatus == true">
                                            <img class="img-fluid w-100" :src="tempProductImage" alt="">
                                        </div>
                                        <div class="mb-3">{{ checkoutData.productDescription }}</div>
                                        <p class="fw-bold">{{ checkoutData.sectionTitle.points }}</p>
                                        <div class="mb-3" v-for="(   item, index   ) in    checkoutData.points   "
                                            :key="index">
                                            <div class="d-flex align-items-center">
                                                <i class="bx bx-check text-success fs-5"></i>
                                                {{ item.poin }}
                                            </div>
                                        </div>
                                        <div class="border border-secondary p-2 mb-3"
                                            v-if="checkoutData.summaryStatus == true">
                                            <p class="fs-12">
                                                RINCIAN PESANAN
                                            </p>
                                            <table class="w-100">
                                                <tr>
                                                    <td style="vertical-align: top;">
                                                        <p class="fs-12">
                                                            {{ product.name }}
                                                        </p>
                                                    </td>
                                                    <td class="text-end" v-if="product.price_type == 'simple'">
                                                        <p v-if="product.price_sale_status == true"
                                                            class="fs-12 text-decoration-line-through text-danger p-0 m-0">
                                                            {{ global.formatNumber(product.normal_price) }}
                                                        </p>
                                                        <p class="fs-12" v-if="product.price_sale_status == true">
                                                            {{ global.formatNumber(form.total_price) }}
                                                        </p>
                                                        <p class="fs-12" v-if="product.price_sale_status == false">
                                                            {{ global.formatNumber(form.total_price) }}
                                                        </p>
                                                    </td>
                                                    <td class="text-end" v-if="product.price_type == 'variable'">
                                                        <p class="fs-12">
                                                            {{ global.formatNumber(form.total_price) }}
                                                        </p>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                        <div class="mb-3" v-if="checkoutData.kuponStatus">
                                            <label class="form-label" for="product-price-input">GUNAKAN KODE
                                                KUPON</label>
                                            <div class="has-validation mb-3">
                                                <input type="text" class="form-control" id="product-price-input"
                                                    aria-label="Price" aria-describedby="product-price-addon">
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </BCardBody>
                        </BCard>
                        <div class="container p-3" style="background:#00579e" v-if="checkoutData.testimonials.length > 0">
                            <div class="row">
                                <div class="col-12 col-lg-6" v-for="(item, index) in checkoutData.testimonials"
                                    :key="index">
                                    <div class="row align-items-center mb-3 p-2">
                                        <div class="col-auto">
                                            <div style="background:white">
                                                <img v-if="item.image" :src="item.image" alt="" style="width:100px">
                                                <div v-if="!item.image">
                                                    <i class="bx bx-user " style="font-size:80px"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <h5 class="mt-0 font-weight-bold text-white">{{ item.name }}</h5>
                                            <p class="text-white">{{ item.message }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </BCardBody>
                </BCard>


                <!-- Left Sidebar -->
                <BCard no-body class="bg-light" v-if="checkoutData.template == 'left-sidebar'">
                    <div class="row d-flex justify-content-center mt-4 mb-2">
                        <div class="col-12 col-lg-6 mb-3">
                            <img class="img-fluid w-100" :src="checkoutData.header.image" alt="">
                        </div>
                        <div class="col-12"
                            v-if="checkoutData.header.titleStatus == true || checkoutData.header.taglineStatus == true">
                            <h5 class="text-center" v-if="checkoutData.header.titleStatus == true">{{
                                checkoutData.header.title }}</h5>
                            <p class="text-center" v-if="checkoutData.header.taglineStatus == true">{{
                                checkoutData.header.tagline }}</p>
                        </div>
                    </div>
                    <BCardBody>
                        <BCard no-body>
                            <BCardBody class="pt-0 pb-0">
                                <div class="row">
                                    <div class="col-5 bg-light p-2">
                                        <div class="mb-3" v-if="checkoutData.productImageStatus == true">
                                            <img class="img-fluid w-100" :src="tempProductImage" alt="">
                                        </div>
                                        <div class="mb-3">{{ checkoutData.productDescription }}</div>
                                        <p class="fw-bold">{{ checkoutData.sectionTitle.points }}</p>
                                        <div class="mb-3" v-for="(   item, index   ) in    checkoutData.points   "
                                            :key="index">
                                            <div class="d-flex align-items-center">
                                                <i class="bx bx-check text-success fs-5"></i>
                                                {{ item.poin }}
                                            </div>
                                        </div>
                                        <div class="border border-secondary p-2 mb-3"
                                            v-if="checkoutData.summaryStatus == true">
                                            <p class="fs-12">
                                                RINCIAN PESANAN
                                            </p>
                                            <table class="w-100">
                                                <tr>
                                                    <td style="vertical-align: top;">
                                                        <p class="fs-12">
                                                            {{ product.name }}
                                                        </p>
                                                    </td>
                                                    <td class="text-end" v-if="product.price_type == 'simple'">
                                                        <p v-if="product.price_sale_status == true"
                                                            class="fs-12 text-decoration-line-through text-danger p-0 m-0">
                                                            {{ global.formatNumber(product.normal_price) }}
                                                        </p>
                                                        <p class="fs-12" v-if="product.price_sale_status == true">
                                                            {{ global.formatNumber(form.total_price) }}
                                                        </p>
                                                        <p class="fs-12" v-if="product.price_sale_status == false">
                                                            {{ global.formatNumber(form.total_price) }}
                                                        </p>
                                                    </td>
                                                    <td class="text-end" v-if="product.price_type == 'variable'">
                                                        <p class="fs-12">
                                                            {{ global.formatNumber(form.total_price) }}
                                                        </p>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                        <div class="mb-3" v-if="checkoutData.kuponStatus">
                                            <label class="form-label" for="product-price-input">GUNAKAN KODE
                                                KUPON</label>
                                            <div class="has-validation mb-3">
                                                <input type="text" class="form-control" id="product-price-input"
                                                    aria-label="Price" aria-describedby="product-price-addon">
                                            </div>

                                        </div>
                                    </div>
                                    <div class="col-7 p-2">
                                        <div class="mb-3" v-if="product.price_type == 'variable'">
                                            <h5 class="fw-bold" v-if="product.many_buy_status == false">Pilihan Produk
                                            </h5>
                                            <template v-if="product.many_buy_status == false">
                                                <div class="row" v-if="product.many_variation_status == false">
                                                    <div class="col-6" v-for="(item, index) in product.item_product">
                                                        <div class="form-check">
                                                            <input class="form-check-input" :id="'product' + index"
                                                                v-model="form.item_product" :value="item" name="product"
                                                                type="radio" required>
                                                            <label class="form-check-label" :for="'product' + index">
                                                                {{ item.variation.variation_name }}
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row" v-if="product.many_variation_status == true">
                                                    <template v-for="(item, index) in product.item_product">
                                                        <div class="col-6" v-if="item.variation !== null">
                                                            <div class="form-check">
                                                                <input class="form-check-input" :id="'product' + index"
                                                                    v-model="form.item_product" :value="item" name="product"
                                                                    type="radio" required>
                                                                <label class="form-check-label" :for="'product' + index">
                                                                    {{ item.variation.variation_name }} - {{
                                                                        item.variation.child_variation_name }}
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </template>
                                                </div>
                                            </template>
                                            <template v-if="product.many_buy_status == true">
                                                <div class="row" v-if="product.many_variation_status == false">
                                                    <table class="table table-bordered">
                                                        <thead>
                                                            <tr>
                                                                <th>Pilihan Produk:</th>
                                                                <th>Jumlah</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr v-for="(item, index) in product.item_product">
                                                                <td class="w-75">{{ item.variation.variation_name }}</td>
                                                                <td>
                                                                    <input class="form-control" type="number"
                                                                        v-model="item.qty" />
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div class="row" v-if="product.many_variation_status == true">
                                                    <table class="table table-bordered">
                                                        <thead>
                                                            <tr>
                                                                <th>Pilihan Produk:</th>
                                                                <th>Jumlah</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <template v-for="(item, index) in product.item_product">
                                                                <tr>
                                                                    <td class="w-75">{{ item.variation.variation_name }} -
                                                                        {{ item.variation.child_variation_name }}
                                                                    </td>
                                                                    <td>
                                                                        <input class="form-control" type="number"
                                                                            v-model="item.qty" />
                                                                    </td>
                                                                </tr>
                                                            </template>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </template>
                                        </div>
                                        <div class="mb-3">
                                            <p class="fw-bold">{{ checkoutData.sectionTitle.form }}</p>
                                        </div>
                                        <div class="mb-3" v-for="(  item, index  ) in   form.fields  " :key="index">
                                            <div v-if="item.show == true">
                                                <input v-if="item.field == 'Input'" v-model="item.value"
                                                    :type="item.inputType" class="form-control"
                                                    :placeholder="item.placeholder" :required="item.required" />
                                                <Multiselect v-if="item.field == 'Select' && item.name == 'province'"
                                                    v-model="selectedProvince" class="form-select" :close-on-select="true"
                                                    :placeholder="item.placeholder" :searchable="true"
                                                    :create-option="false" :options="item.selectOption"
                                                    :required="item.required" />
                                                <Multiselect v-if="item.field == 'Select' && item.name == 'city'"
                                                    class="form-select" v-model="selectedCity" :close-on-select="true"
                                                    :placeholder="item.placeholder" :searchable="true"
                                                    :create-option="false" :options="item.selectOption"
                                                    :required="item.required" />
                                                <Multiselect v-if="item.field == 'Select' && item.name == 'subdistrict'"
                                                    class="form-select" v-model="selectedSubdistrict"
                                                    :close-on-select="true" :placeholder="item.placeholder"
                                                    :searchable="true" :create-option="false" :options="item.selectOption"
                                                    :required="item.required" />
                                                <Multiselect
                                                    v-if="item.field == 'Select' && item.name !== 'subdistrict' && item.name !== 'city' && item.name !== 'province'"
                                                    class="form-select" v-model="item.value" :close-on-select="true"
                                                    :placeholder="item.placeholder" :searchable="true"
                                                    :create-option="false" :options="item.selectOption"
                                                    :required="item.required" />
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <p class="fw-bold">{{ checkoutData.sectionTitle.payment_method }}</p>

                                            <div class="p-4 border" v-if="product.bank_transfer.status == true">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" value="bank_transfer"
                                                        v-model="form.payment_method" id="bankTransferCheckbox">
                                                    <label class="form-check-label" for="bankTransferCheckbox">
                                                        Bank Transfer
                                                    </label>
                                                </div>
                                            </div>

                                            <div class="p-4 border" v-if="product.cod.status == true">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" value="cod"
                                                        v-model="form.payment_method" id="codCheckbox">
                                                    <label class="form-check-label" for="codCheckbox">
                                                        COD
                                                    </label>
                                                </div>
                                            </div>

                                            <div class="p-4 border" v-if="product.epayment.status == true">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" value="epayment"
                                                        v-model="form.payment_method" id="epaymentCheckbox">
                                                    <label class="form-check-label" for="epaymentCheckbox">
                                                        E - Payment
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="border border-dashed border-danger border-2 p-2 mb-3"
                                            v-if="product.bump_status">
                                            <div class="d-flex align-items-center mb-1 bg-warning py-1 px-1 mb-1">
                                                <img class="me-2" src="@assets/images/bump-arrow.gif" alt="">
                                                <input type="checkbox" name="" class="form-check-input me-2"
                                                    v-model="form.bump_status" id="bump">
                                                <label class="form-check-label fw-bold" for="bump">Centang untuk
                                                    memesan juga</label>
                                            </div>
                                            <div class="fw-bold text-danger mb-1">{{ product.product_bump.name }} +
                                                {{ product.product_bump.price }}
                                            </div>
                                            <div class="mb-1">
                                                <img class="img-fluid w-100" :src="product.product_bump.image" alt="">
                                            </div>
                                            <!-- <div class="fs-12">Penjelasan Tentang Produk Bump Anda Disini</div> -->
                                        </div>
                                        <button @click="submit"
                                            :style="'background-color:' + checkoutData.buyButtonBackground + ';color:' + checkoutData.buyButtonTextColor"
                                            class="btn w-100 fs-4 fw-bold d-flex align-items-center justify-content-center">{{
                                                checkoutData.buyButtonText }}
                                            <i class="bx bx-right-arrow-circle ms-2"></i></button>
                                    </div>

                                </div>
                            </BCardBody>
                        </BCard>
                        <div class="container p-3" style="background:#00579e" v-if="checkoutData.testimonials.length > 0">
                            <div class="row">
                                <div class="col-12 col-lg-6" v-for="(item, index) in checkoutData.testimonials"
                                    :key="index">
                                    <div class="row align-items-center mb-3 p-2">
                                        <div class="col-auto">
                                            <div style="background:white">
                                                <img v-if="item.image" :src="item.image" alt="" style="width:100px">
                                                <div v-if="!item.image">
                                                    <i class="bx bx-user " style="font-size:80px"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <h5 class="mt-0 font-weight-bold text-white">{{ item.name }}</h5>
                                            <p class="text-white">{{ item.message }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </BCardBody>
                </BCard>

                <!-- No Sidebar -->
                <BCard no-body class="bg-light" v-if="checkoutData.template == 'no-sidebar'">
                    <div class="row d-flex justify-content-center mt-4 mb-2">
                        <div class="col-12 col-lg-6 mb-3">
                            <img class="img-fluid w-100" :src="checkoutData.header.image" alt="">
                        </div>
                        <div class="col-12"
                            v-if="checkoutData.header.titleStatus == true || checkoutData.header.taglineStatus == true">
                            <h5 class="text-center" v-if="checkoutData.header.titleStatus == true">{{
                                checkoutData.header.title }}</h5>
                            <p class="text-center" v-if="checkoutData.header.taglineStatus == true">{{
                                checkoutData.header.tagline }}</p>
                        </div>
                    </div>
                    <BCardBody>
                        <BCard no-body>
                            <BCardBody class="pt-0 pb-0">
                                <div class="row">
                                    <div class="col-12 px-5 pt-5">
                                        <div class="mb-3" v-if="checkoutData.productImageStatus == true">
                                            <img class="img-fluid w-100" :src="tempProductImage" alt="">
                                        </div>
                                        <div class="mb-3">{{ checkoutData.productDescription }}</div>
                                        <p class="fw-bold">{{ checkoutData.sectionTitle.points }}</p>
                                        <div class="mb-3" v-for="(   item, index   ) in    checkoutData.points   "
                                            :key="index">
                                            <div class="d-flex align-items-center">
                                                <i class="bx bx-check text-success fs-5"></i>
                                                {{ item.poin }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 px-5 pb-5">
                                        <div class="mb-3" v-if="product.price_type == 'variable'">
                                            <h5 class="fw-bold" v-if="product.many_buy_status == false">Pilihan Produk
                                            </h5>
                                            <template v-if="product.many_buy_status == false">
                                                <div class="row" v-if="product.many_variation_status == false">
                                                    <div class="col-6" v-for="(item, index) in product.item_product">
                                                        <div class="form-check">
                                                            <input class="form-check-input" :id="'product' + index"
                                                                v-model="form.item_product" :value="item" name="product"
                                                                type="radio" required>
                                                            <label class="form-check-label" :for="'product' + index">
                                                                {{ item.variation.variation_name }}
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row" v-if="product.many_variation_status == true">
                                                    <template v-for="(item, index) in product.item_product">
                                                        <div class="col-6" v-if="item.variation !== null">
                                                            <div class="form-check">
                                                                <input class="form-check-input" :id="'product' + index"
                                                                    v-model="form.item_product" :value="item" name="product"
                                                                    type="radio" required>
                                                                <label class="form-check-label" :for="'product' + index">
                                                                    {{ item.variation.variation_name }} - {{
                                                                        item.variation.child_variation_name }}
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </template>
                                                </div>
                                            </template>
                                            <template v-if="product.many_buy_status == true">
                                                <div class="row" v-if="product.many_variation_status == false">
                                                    <table class="table table-bordered">
                                                        <thead>
                                                            <tr>
                                                                <th>Pilihan Produk:</th>
                                                                <th>Jumlah</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr v-for="(item, index) in product.item_product">
                                                                <td class="w-75">{{ item.variation.variation_name }}</td>
                                                                <td>
                                                                    <input class="form-control" type="number"
                                                                        v-model="item.qty" />
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div class="row" v-if="product.many_variation_status == true">
                                                    <table class="table table-bordered">
                                                        <thead>
                                                            <tr>
                                                                <th>Pilihan Produk:</th>
                                                                <th>Jumlah</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <template v-for="(item, index) in product.item_product">
                                                                <tr>
                                                                    <td class="w-75">{{ item.variation.variation_name }} -
                                                                        {{ item.variation.child_variation_name }}
                                                                    </td>
                                                                    <td>
                                                                        <input class="form-control" type="number"
                                                                            v-model="item.qty" />
                                                                    </td>
                                                                </tr>
                                                            </template>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </template>
                                        </div>
                                        <div class="mb-3">
                                            <p class="fw-bold">{{ checkoutData.sectionTitle.form }}</p>
                                        </div>
                                        <div class="mb-3" v-for="(  item, index  ) in   form.fields  " :key="index">
                                            <div v-if="item.show == true">
                                                <input v-if="item.field == 'Input'" v-model="item.value"
                                                    :type="item.inputType" class="form-control"
                                                    :placeholder="item.placeholder" :required="item.required" />
                                                <Multiselect v-if="item.field == 'Select' && item.name == 'province'"
                                                    v-model="selectedProvince" class="form-select" :close-on-select="true"
                                                    :placeholder="item.placeholder" :searchable="true"
                                                    :create-option="false" :options="item.selectOption"
                                                    :required="item.required" />
                                                <Multiselect v-if="item.field == 'Select' && item.name == 'city'"
                                                    class="form-select" v-model="selectedCity" :close-on-select="true"
                                                    :placeholder="item.placeholder" :searchable="true"
                                                    :create-option="false" :options="item.selectOption"
                                                    :required="item.required" />
                                                <Multiselect v-if="item.field == 'Select' && item.name == 'subdistrict'"
                                                    class="form-select" v-model="selectedSubdistrict"
                                                    :close-on-select="true" :placeholder="item.placeholder"
                                                    :searchable="true" :create-option="false" :options="item.selectOption"
                                                    :required="item.required" />
                                                <Multiselect
                                                    v-if="item.field == 'Select' && item.name !== 'subdistrict' && item.name !== 'city' && item.name !== 'province'"
                                                    class="form-select" v-model="item.value" :close-on-select="true"
                                                    :placeholder="item.placeholder" :searchable="true"
                                                    :create-option="false" :options="item.selectOption"
                                                    :required="item.required" />
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <p class="fw-bold">{{ checkoutData.sectionTitle.payment_method }}</p>

                                            <div class="p-4 border" v-if="product.bank_transfer.status == true">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" value="bank_transfer"
                                                        v-model="form.payment_method" id="bankTransferCheckbox">
                                                    <label class="form-check-label" for="bankTransferCheckbox">
                                                        Bank Transfer
                                                    </label>
                                                </div>
                                            </div>

                                            <div class="p-4 border" v-if="product.cod.status == true">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" value="cod"
                                                        v-model="form.payment_method" id="codCheckbox">
                                                    <label class="form-check-label" for="codCheckbox">
                                                        COD
                                                    </label>
                                                </div>
                                            </div>

                                            <div class="p-4 border" v-if="product.epayment.status == true">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" value="epayment"
                                                        v-model="form.payment_method" id="epaymentCheckbox">
                                                    <label class="form-check-label" for="epaymentCheckbox">
                                                        E - Payment
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="border border-dashed border-danger border-2 p-2 mb-3"
                                            v-if="product.bump_status">
                                            <div class="d-flex align-items-center mb-1 bg-warning py-1 px-1 mb-1">
                                                <img class="me-2" src="@assets/images/bump-arrow.gif" alt="">
                                                <input type="checkbox" name="" class="form-check-input me-2"
                                                    v-model="form.bump_status" id="bump">
                                                <label class="form-check-label fw-bold" for="bump">Centang untuk
                                                    memesan juga</label>
                                            </div>
                                            <div class="fw-bold text-danger mb-1">{{ product.product_bump.name }} +
                                                {{ product.product_bump.price }}</div>
                                            <div class="mb-1">
                                                <img class="img-fluid w-100" :src="product.product_bump.image" alt="">
                                            </div>
                                            <!-- <div class="fs-12">Penjelasan Tentang Produk Bump Anda Disini</div> -->
                                        </div>
                                        <div class="border border-secondary p-2 mb-3"
                                            v-if="checkoutData.summaryStatus == true">
                                            <p class="fs-12">
                                                RINCIAN PESANAN
                                            </p>
                                            <table class="w-100">
                                                <tr>
                                                    <td style="vertical-align: top;">
                                                        <p class="fs-12">
                                                            {{ product.name }}
                                                        </p>
                                                    </td>
                                                    <td class="text-end" v-if="product.price_type == 'simple'">
                                                        <p v-if="product.price_sale_status == true"
                                                            class="fs-12 text-decoration-line-through text-danger p-0 m-0">
                                                            {{ global.formatNumber(product.normal_price) }}
                                                        </p>
                                                        <p class="fs-12" v-if="product.price_sale_status == true">
                                                            {{ global.formatNumber(form.total_price) }}
                                                        </p>
                                                        <p class="fs-12" v-if="product.price_sale_status == false">
                                                            {{ global.formatNumber(form.total_price) }}
                                                        </p>
                                                    </td>
                                                    <td class="text-end" v-if="product.price_type == 'variable'">
                                                        <p class="fs-12">
                                                            {{ global.formatNumber(form.total_price) }}
                                                        </p>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                        <div class="mb-3" v-if="checkoutData.kuponStatus">
                                            <label class="form-label" for="product-price-input">GUNAKAN KODE
                                                KUPON</label>
                                            <div class="has-validation mb-3">
                                                <input type="text" class="form-control" id="product-price-input"
                                                    aria-label="Price" aria-describedby="product-price-addon">
                                            </div>

                                        </div>
                                        <button @click="submit"
                                            :style="'background-color:' + checkoutData.buyButtonBackground + ';color:' + checkoutData.buyButtonTextColor"
                                            class="btn w-100 fs-4 fw-bold d-flex align-items-center justify-content-center">{{
                                                checkoutData.buyButtonText }}
                                            <i class="bx bx-right-arrow-circle ms-2"></i></button>
                                    </div>

                                </div>
                            </BCardBody>
                        </BCard>
                        <div class="container p-3" style="background:#00579e" v-if="checkoutData.testimonials.length > 0">
                            <div class="row">
                                <div class="col-12 col-lg-6" v-for="(item, index) in checkoutData.testimonials"
                                    :key="index">
                                    <div class="row align-items-center mb-3 p-2">
                                        <div class="col-auto">
                                            <div style="background:white">
                                                <img v-if="item.image" :src="item.image" alt="" style="width:100px">
                                                <div v-if="!item.image">
                                                    <i class="bx bx-user " style="font-size:80px"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <h5 class="mt-0 font-weight-bold text-white">{{ item.name }}</h5>
                                            <p class="text-white">{{ item.message }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </BCardBody>
                </BCard>
            </form>
        </BCol>
    </BRow>
</template>