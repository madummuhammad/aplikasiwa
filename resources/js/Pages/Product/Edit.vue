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
import { Head, useForm, Link } from '@inertiajs/vue3';
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
import { Global } from '../../global'
export default {
    props: {
        store: Object,
        product: Object,
        team: Object
    },
    data(props) {
        return {
            debounceTimer: null,
        };
    },
    watch: {
        'form.collection': function (newVal) {
            console.log('form.collection berubah:', newVal);
        },
        'form.url': async function (newVal) {
            console.log(this.product);
            clearTimeout(this.debounceTimer);
            this.debounceTimer = setTimeout(async () => {
                console.log(newVal);
                const response = await axios.post('/api/products/check_url', { url: newVal, id: this.product.id });

                let data = response.data;
                console.log(response.data);
                if (data.status == 'error') {
                    this.form.errors.url = 'URL Sudah Dipakai';
                    console.log('form', this.form)
                }
            }, 500);
        }
    },
    setup(props) {
        const global = Global();
        // Cs Rotator
        let teamData = ref(props.team);
        let teamOption = ref([]);
        const changeRotatorRole = (role) => {
            form.value.rotatorData.role = role;
            form.value.rotatorData.team = [];
            teamOption.value = [];
            let selectOption = [];

            if (role == 'Customer Service') {
                let teamCustomer = teamData.value.filter(item => item.role === 'Customer Service');
                console.log('customer service', teamCustomer)
                teamCustomer.forEach((itemResult, indexResult) => {
                    selectOption[indexResult] = {
                        value: itemResult.id,
                        label: itemResult.user.name
                    }
                });
                console.log('selectOption', selectOption)

                teamOption.value = selectOption;
            } else {
                let teamCustomer = teamData.value.filter(item => item.role === 'Admin');
                console.log('Admin', teamCustomer)
                teamCustomer.forEach((itemResult, indexResult) => {
                    selectOption[indexResult] = {
                        value: itemResult.id,
                        label: itemResult.user.name
                    }
                });
                console.log('selectOption', selectOption)

                teamOption.value = selectOption;
            }
        }

        const changeRotatorStatus = () => {
            teamOption.value = [];
            let selectOption = [];
            if (form.value.global_cs_rotator_status == true) {
                if (!form.value.rotatorData.role) {
                    form.value.rotatorData = {
                        role: 'Customer Service',
                        team: []
                    }
                    let teamCustomer = teamData.value.filter(item => item.role === 'Customer Service');
                    console.log('customer service', teamCustomer)
                    teamCustomer.forEach((itemResult, indexResult) => {
                        selectOption[indexResult] = {
                            value: itemResult.id,
                            label: itemResult.user.name
                        }
                    });
                    console.log('selectOption', selectOption)

                    teamOption.value = selectOption;
                }
            }
        }

        onMounted(() => {
            // Cs Rotator
            if (form.value.rotatorData && form.value.rotatorData.role && form.value.rotatorData.role == 'Customer Service') {
                teamOption.value = [];
                let selectOption = [];

                let teamCustomer = teamData.value.filter(item => item.role === 'Customer Service');
                teamCustomer.forEach((itemResult, indexResult) => {
                    selectOption[indexResult] = {
                        value: itemResult.id,
                        label: itemResult.user.name
                    }
                });

                teamOption.value = selectOption;
            } else {
                teamOption.value = [];
                let selectOption = [];

                let teamCustomer = teamData.value.filter(item => item.role === 'Admin');
                teamCustomer.forEach((itemResult, indexResult) => {
                    selectOption[indexResult] = {
                        value: itemResult.id,
                        label: itemResult.user.name
                    }
                });

                teamOption.value = selectOption;
            }
        });



        onBeforeMount(() => {
            console.log(props.product)

            // Collection
            getCollection();
            form.value.collection.forEach((item, index) => {
                let option = {
                    value: item,
                    label: item.name
                }

                form.value.collection[index] = option
            });

            console.log('collec', form.value.collection)

            // Child Category
            console.log('category', form.value.category)
            if (form.value.category.child) {
                form.value.category.child.forEach((item, index) => {
                    let option = {
                        value: item,
                        label: item.name
                    };
                    optionChildCategory.value[index] = option;
                });
            }

            // Grand Child Category
            if (form.value.child_category.child) {
                form.value.child_category.child.forEach((item, index) => {
                    let option = {
                        value: item,
                        label: item.name
                    };
                    optionGrandChildCategory.value[index] = option;
                });
            }

            // Child Variation

            if (form.value.item_variations.length > 0) {
                size.value = form.value.item_variations.variations;
                color.value = form.value.item_variations.child_variation
            }
        });


        const form = ref(useForm(props.product));

        let optionChildCategory = ref([]);
        const selectCategory = () => {

            optionChildCategory.value = [];
            optionGrandChildCategory.value = [];
            form.value.child_category = [];
            form.value.grand_child_category = [];

            console.log('select category', form.value.child_category)

            if (form.value.category.child) {
                form.value.category.child.forEach((item, index) => {
                    let option = {
                        value: item,
                        label: item.name
                    };
                    optionChildCategory.value[index] = option;
                })
            }
        }

        let optionGrandChildCategory = ref([]);
        const selectChildCategory = () => {
            optionGrandChildCategory.value = []
            form.value.grand_child_category = [];

            if (form.value.child_category.child) {
                form.value.child_category.child.forEach((item, index) => {
                    let option = {
                        value: item,
                        label: item.name
                    };
                    optionGrandChildCategory.value[index] = option;
                });
                console.log('optiongrandchild', optionGrandChildCategory)
            }

        }

        let optionCollection = ref([]);

        const getCollection = async () => {
            optionCollection.value = [];
            try {
                const response = await axios.get('/api/collections');
                const data = response.data;

                if (data.status === 'success') {
                    console.log('collection:', data.data);

                    data.data.forEach((item, index) => {
                        let option = {
                            value: item,
                            label: item.name
                        }

                        optionCollection.value[index] = option
                    });
                }

            } catch (error) {
                console.error('error', error);
            }
        };

        let category = [
            {
                id: 1,
                name: 'Buku',
                child: [
                    {
                        id: 11,
                        name: 'Agama & Filsafat',
                        child: [
                            {
                                id: 111,
                                name: 'Budah'
                            },
                            {
                                id: 112,
                                name: 'Filosofi'
                            }
                        ]
                    },
                    {
                        id: 12,
                        name: 'Aristektur & Desain',
                        child: [
                            {
                                id: 121,
                                name: 'Bangunan'
                            },
                            {
                                id: 122,
                                name: 'Codes & Standars'
                            }
                        ]
                    }
                ]
            },
            {
                id: 2,
                name: 'Motor',
                child: [
                    {
                        id: 21,
                        name: 'Agama2 & Filsafat',
                        child: [
                            {
                                id: 211,
                                name: 'Budah2'
                            },
                            {
                                id: 212,
                                name: 'Filosofi2'
                            }
                        ]
                    },
                    {
                        id: 22,
                        name: 'Aristektur & Desain2',
                        child: [
                            {
                                id: 221,
                                name: 'Bangunan2'
                            },
                            {
                                id: 222,
                                name: 'Codes & Standars2'
                            }
                        ]
                    }
                ]
            }
        ]

        // Kategori
        let optionCategory = [];
        category.forEach((item) => {
            let option = {
                value: item,
                label: item.name
            };
            optionCategory.push(option);


        });

        // Product Image
        let dropzoneFile = ref("");
        const drop = (e) => {
            dropzoneFile.value = e.dataTransfer.files[0];
            form.value.image.push(dropzoneFile.value);
        };

        var tempProductImage = ref('')

        const selectedFile = async (event) => {
            try {
                const file = event.target.files[0];
                const formData = new FormData();
                formData.append('image', file);
                const response = await axios.post('/api/products/upload', formData);
                const data = response.data;

                console.log(data)

                let images = {
                    url: data.data
                }
                tempProductImage.value = images.url;
                form.value.image.push(images);

            } catch (error) {
                console.error('error', error);
            }

        };


        const deleteProductImage = (index) => {
            form.value.image.splice(index, 1);
        };

        const changePriceType = (type) => {
            form.value.price_type = type;
        }

        // Variasi harga
        var sizeValue = ref('')
        let size = ref([]);
        let variation = ref([]);

        const addSize = () => {
            console.log(size)
            size.value.push(
                {
                    name: sizeValue.value
                }
            )

            if (color.value.length > 0) {
                color.value.forEach((item, index) => {
                    form.value.item_product.push({
                        id: null,
                        hpp: 0,
                        normal_price: 0,
                        weight: 0,
                        product_code: null,
                        variation: {
                            variation_name: sizeValue.value,
                            child_variation_name: item.name
                        },
                        stock: 0
                    });
                })
            } else {
                form.value.item_product.push({
                    id: null,
                    hpp: 0,
                    normal_price: 0,
                    weight: 0,
                    product_code: null,
                    variation: {
                        variation_name: sizeValue.value,
                        // child_variation_name: item.name
                    },
                    stock: 0
                });
            }
            sizeValue.value = '';
        }

        const deleteSize = (index, item) => {
            // console.log(item)
            form.value.item_product = form.value.item_product.filter((product) => {
                return product.variation.variation_name !== item.name;
            });
            size.value.splice(index, 1);
        }


        var colorValue = ref('')
        let color = ref([]);

        const addColor = () => {
            color.value.push({ name: colorValue.value });
            if (form.value.item_product.length !== 0) {
                const newVariations = size.value.map((item) => {
                    // console.log('items', item)
                    return {
                        id: null,
                        hpp: 0,
                        normal_price: 0,
                        product_code: null,
                        weight: 0,
                        variation: {
                            variation_name: item.name,
                            child_variation_name: colorValue.value,
                        },
                        stock: 0,
                    };
                });

                form.value.item_product = [...form.value.item_product, ...newVariations];
            } else {
                size.value.forEach((item, index) => {
                    form.value.item_product.push({
                        id: null,
                        hpp: 0,
                        normal_price: 0,
                        weight: 0,
                        product_code: null,
                        variation: {
                            variation_name: item.name,
                            child_variation_name: colorValue.value
                        },
                        stock: 0
                    });
                });
            }
            colorValue.value = '';
        };



        const deleteColor = (index, item) => {
            // console.log(item);
            form.value.item_product = form.value.item_product.filter((product) => {
                return product.variation.child_variation_name !== item.name;
            });
            color.value.splice(index, 1);
        };

        const changeMultivariasi = () => {
            if (form.value.many_variation_status == true) {
                form.value.item_product = [];
                color.value.forEach((item, index) => {
                    size.value.forEach((itemSize, indexSize) => {
                        form.value.item_product.push({
                            id: null,
                            hpp: 0,
                            normal_price: 0,
                            weight: 0,
                            product_code: null,
                            variation: {
                                variation_name: itemSize.name,
                                child_variation_name: item.name
                            },
                            stock: 0
                        })
                    })
                })
            } else {
                form.value.item_product = [];
                size.value.forEach((item, index) => {
                    form.value.item_product.push({
                        id: null,
                        hpp: 0,
                        normal_price: 0,
                        weight: 0,
                        product_code: null,
                        variation: {
                            variation_name: item.name,
                            // child_variation_name: ""
                        },
                        stock: 0
                    })
                })
            }
        }

        // Bump Image
        const bumpImageChange = async (event) => {
            try {
                const file = event.target.files[0];
                const formData = new FormData();
                formData.append('image', file);
                const response = await axios.post('/api/products/upload', formData);
                const data = response.data;

                form.value.product_bump.image = data.data;

            } catch (error) {
                console.error('error', error);
            }
        }

        const submit = () => {
            console.log('form', form.value);
            // return;
            form.value.transform((data) => ({
                ...data
            }))
                .post(route('product.update', form.value.id), {
                    onSuccess: () => {
                    }
                });
        };

        // Tipe Pengiriman

        const shippingMethod = (type) => {
            form.value.shipping.shipping_type = type;
            console.log('form.shipping.shipping_type', form.value.shipping.shipping_type)
        }


        return {

            // Cs Rotator
            changeRotatorRole,
            teamData,
            teamOption,
            changeRotatorStatus,

            // Global
            global,
            form,
            submit,

            // Bump image
            bumpImageChange,

            // Kategori
            optionChildCategory,
            optionGrandChildCategory,
            selectCategory,
            selectChildCategory,

            // Koleksi
            optionCollection,
            getCollection,

            // Variasi Harga
            sizeValue,
            size,
            colorValue,
            color,
            variation,
            addSize,
            deleteSize,
            addColor,
            deleteColor,
            changeMultivariasi,

            // Kategori
            category,
            optionCategory,

            // Image
            selectedFile,
            deleteProductImage,
            tempProductImage,

            // Price Type
            changePriceType,

            // Pengiriman
            shippingMethod
        };
    },
    components: {
        ckeditor: CKEditor.component,
        DropZone,
        Layout,
        Link,
        PageHeader,
        Multiselect,
        flatPickr,
        Head
    },

    data() {
        return {
            sectionTitleShow: false,
        }
    },
    // methods: {
    //     deleteRecord(ele) {
    //         ele.target.parentElement.parentElement.parentElement.remove();
    //     },
    // },
};
</script>

<template>
    <Layout>

        <Head title="Create Product" />
        <!-- <PageHeader title="Create Product" pageTitle="Product" /> -->

        <BRow>
            <div class="col-12 mb-5">
                <Link :href="'/product/' + form.id">
                <h2 class="d-flex align-items-center">
                    <i class="bx bx-chevron-left"></i>
                    {{ product.name }}
                </h2>
                </Link>
            </div>
            <!-- Page Product -->
            <BCol lg="6">
                <BCard no-body>
                    <BCardBody>
                        <div class="mb-3">
                            <label class="form-label" for="product-title-input">Nama Produk (akan muncul di cart &
                                invoice)</label>
                            <input type="text" @blur="urlSlug" v-model="form.name" class="form-control"
                                :class="{ 'is-invalid': form.errors.name }" id="product-title-input" />
                            <div class="invalid-feedback">{{ form.errors.name }}</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="product-price-input">URL Halaman Checkout</label>
                            <div class="input-group has-validation mb-3">
                                <span class="input-group-text" id="product-price-addon">/</span>
                                <input type="text" class="form-control" :class="{ 'is-invalid': form.errors.url }"
                                    v-model="form.url" id="product-price-input" aria-label="Price"
                                    aria-describedby="product-price-addon" required="">
                                <div class="invalid-feedback">{{ form.errors.url }}</div>
                            </div>
                            <div>URL Checkout Page: https://{{ store.store.username }}.aplikasiwa.com/{{ form.url }}
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="product-title-input">Kode Produk</label>
                            <input type="text" class="form-control" :class="{ 'is-invalid': form.errors.product_code }"
                                v-model="form.product_code" id="product-title-input" />
                            <div class="invalid-feedback">{{ form.errors.product_code }}</div>
                        </div>
                        <div class="mb-3">
                            <label for="choices-publish-status-input" class="form-label">Kategori Produk</label>
                            <Multiselect class="form-control mb-4" @change="selectCategory" v-model="form.category"
                                :close-on-select="true" :searchable="false" :create-option="true"
                                :options="optionCategory" />
                            <div class="row">
                                <div class="col-6" v-if="optionChildCategory.length !== 0">
                                    <Multiselect class="form-control" @change="selectChildCategory"
                                        v-model="form.child_category" :close-on-select="true" :searchable="false"
                                        :create-option="true" :options="optionChildCategory" />
                                </div>
                                <div class="col-6" v-if="optionGrandChildCategory.length !== 0">
                                    <Multiselect class="form-control" v-model="form.grand_child_category"
                                        :close-on-select="true" :searchable="false" :create-option="false"
                                        :options="optionGrandChildCategory" />
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="choices-publish-status-input" class="form-label">Koleksi Produki</label>
                            <Multiselect class="form-control" v-model="form.collection" :object="true" mode="tags"
                                :close-on-select="true" :searchable="true" :create-option="true"
                                :options="optionCollection" />
                        </div>
                        <div class="mb-3">
                            <label for="choices-publish-status-input" class="form-label">Status Produk</label>
                            <div class="hstack gap-2 flex-wrap">
                                <input type="radio" class="btn-check" id="product_status_1" name="product_status"
                                    v-model="form.product_status" value="archived"
                                    :checked="form.product_status == 'archived'">
                                <label class="btn btn-outline-success" for="product_status_1">Diarsipkan</label>

                                <input type="radio" class="btn-check" id="product_status_2" name="product_status"
                                    v-model="form.product_status" value="test_mode"
                                    :checked="form.product_status == 'test_mode'">
                                <label class="btn btn-outline-success" for="product_status_2">Test Mode</label>

                                <input type="radio" class="btn-check" id="product_status_3" name="product_status"
                                    v-model="form.product_status" value="live" :checked="form.product_status == 'live'">
                                <label class="btn btn-outline-success" for="product_status_3">Live</label>
                            </div>

                        </div>
                    </BCardBody>
                </BCard>

                <BCard no-body>
                    <BCardHeader>
                        <h5 class="card-title mb-0">Gambar</h5>
                    </BCardHeader>
                    <BCardBody>
                        <div class="vstack gap-2">
                            <h5 class="fs-14 mb-1">Unggah Gambar</h5>
                            <p class="text-muted">Unggah gambar produk Anda (maks. 10 gambar)</p>
                            <DropZone @change="selectedFile" />

                            <div class="border rounded" v-for="(file, index) of form.image" :key="index">
                                <div class="d-flex align-items-center p-2">
                                    <div class="flex-grow-1">
                                        <img class="img-fluid" style="width:100px" :src="file.url" />
                                    </div>
                                    <div class="flex-shrink-0 ms-3">
                                        <BButton data-dz-remove="" variant="danger" size="sm"
                                            @click="deleteProductImage(index)">
                                            Delete
                                        </BButton>
                                    </div>
                                </div>
                            </div>
                            <!-- <div class="border rounded" v-for="(file, index) of form.image" :key="index">
                                <div class="d-flex align-items-center p-2">
                                    <div class="flex-grow-1">
                                        <div class="pt-1">
                                            <h5 class="fs-14 mb-1" data-dz-name="">
                                                {{ file.name }}
                                            </h5>
                                            <p class="fs-13 text-muted mb-0" data-dz-size="">
                                                <strong>{{ file.size / 1024 }}</strong> KB
                                            </p>
                                            <strong class="error text-danger" data-dz-errormessage=""></strong>
                                        </div>
                                    </div>
                                    <div class="flex-shrink-0 ms-3">
                                        <BButton data-dz-remove="" variant="danger" size="sm"
                                            @click="deleteProductImage(index)">
                                            Delete
                                        </BButton>
                                    </div>
                                </div>
                            </div> -->
                        </div>
                    </BCardBody>
                </BCard>

                <BCard no-body>
                    <BCardHeader>
                        <h5 class="card-title mb-0">Pengaturan Harga</h5>
                    </BCardHeader>
                    <BCardBody>
                        <div class="d-flex justify-content-between mb-3">
                            <div type="button" class="text-center w-100 py-2" @click="changePriceType('simple')"
                                :class="{ 'border-bottom border-secondary': form.price_type == 'simple' }">Simple
                            </div>
                            <div type="button" class="text-center w-100 py-2" @click="changePriceType('variable')"
                                :class="{ 'border-bottom border-secondary': form.price_type == 'variable' }">
                                Variabel
                            </div>
                        </div>
                        <div v-if="form.price_type == 'simple'">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="mb-3">
                                        <label class="form-label" for="product-price-input">Harga Normal</label>
                                        <div class="input-group has-validation mb-3">
                                            <span class="input-group-text" id="product-price-addon">Rp</span>
                                            <input type="text" class="form-control" v-model="form.normal_price"
                                                id="product-price-input" placeholder="Enter price" aria-label="Price"
                                                aria-describedby="product-price-addon" required="">
                                            <div class="invalid-feedback">Please Enter a product price.</div>
                                        </div>

                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="mb-3">
                                        <div class="form-check form-switch form-switch-custom form-switch-success">
                                            <input class="form-check-input" type="checkbox" v-model="form.price_sale_status"
                                                role="switch" id="SwitchCheck11">
                                            <label class="form-check-label" for="SwitchCheck11">Harga
                                                Penjualan</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-12" v-if="form.price_sale_status == true">
                                    <div class="mb-3">
                                        <label class="form-label" for="product-price-input">Harga Penjualan</label>
                                        <div class="input-group has-validation mb-3">
                                            <span class="input-group-text" id="product-price-addon">Rp</span>
                                            <input type="text" class="form-control" v-model="form.price_sale"
                                                id="product-price-input" placeholder="Enter price" aria-label="Price"
                                                aria-describedby="product-price-addon" required="">
                                            <div class="invalid-feedback">Please Enter a product price.</div>
                                        </div>

                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="mb-3">
                                        <label class="form-label" for="product-price-input">HPP</label>
                                        <div class="input-group has-validation mb-3">
                                            <span class="input-group-text" id="product-price-addon">Rp</span>
                                            <input type="text" class="form-control" v-model="form.hpp"
                                                id="product-price-input" placeholder="Enter price" aria-label="Price"
                                                aria-describedby="product-price-addon" required="">
                                            <div class="invalid-feedback">Please Enter a product price.</div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-if="form.price_type == 'variable'">
                            <BRow>
                                <div class="col-lg-12">
                                    <div class="mb-3">
                                        <div class="form-check form-switch form-switch-custom form-switch-success">
                                            <input class="form-check-input" v-model="form.many_buy_status" type="checkbox"
                                                role="switch" id="variation">
                                            <label class="form-check-label" for="variation">Aktifkan pilihan lebih dari
                                                satu?</label>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <div class="form-check form-switch form-switch-custom form-switch-success">
                                            <input class="form-check-input" @change="changeMultivariasi"
                                                v-model="form.many_variation_status" type="checkbox" role="switch"
                                                id="variation">
                                            <label class="form-check-label" for="variation">Aktifkan multi variasi?</label>
                                        </div>
                                    </div>
                                </div>

                                <BCol lg="6">
                                    <div class="mb-3">
                                        <label class="form-label" for="meta-keywords-input">Atribut</label>
                                        <input type="text" class="form-control border-0" v-model="form.variation_name" />
                                        <input type="text" class="form-control" @keyup.enter="addSize"
                                            v-model="sizeValue" />
                                        <div class="mt-3">
                                            <div class="badge bg-success me-2" v-for="(item, index) in size">
                                                <div class="d-flex align-items-center">
                                                    {{ item.name }}
                                                    <i class="bx bx-x-circle ms-2 cursor-pointer"
                                                        @click="deleteSize(index, item)"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </BCol>
                                <BCol lg="6" v-if="form.many_variation_status == true">
                                    <div class="mb-3">
                                        <label class="form-label" for="meta-keywords-input">&nbsp;</label>
                                        <input type="text" class="form-control border-0"
                                            v-model="form.child_variation_name" />
                                        <input type="text" class="form-control" @keyup.enter="addColor"
                                            v-model="colorValue" />
                                        <div class="mt-3">
                                            <div class="badge bg-success me-2" v-for="(item, index) in color">
                                                <div class="d-flex align-items-center">
                                                    {{ item.name }}
                                                    <i class="bx bx-x-circle ms-2 cursor-pointer"
                                                        @click="deleteColor(index, item)"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </BCol>
                            </BRow>
                            <BRow>
                                <div class="col-lg-12">
                                    <div class="card-header">
                                        <h5 class="card-title">Variasi Harga</h5>
                                    </div>
                                    <div v-if="form.many_variation_status == false">
                                        <div class="card" v-for="(item, index) in form.item_product">
                                            <div class="card-body" v-if="item.variation !== null">
                                                <h5>{{ item.variation.variation_name }}</h5>
                                                <div class="row">
                                                    <div class="col-lg-6">
                                                        <div class="mb-3">
                                                            <label class="form-label"
                                                                for="product-price-input">Harga</label>
                                                            <div class="input-group has-validation mb-3">
                                                                <span class="input-group-text"
                                                                    id="product-price-addon">Rp</span>
                                                                <input type="text" class="form-control"
                                                                    v-model="item.normal_price" id="product-price-input"
                                                                    aria-label="Price"
                                                                    aria-describedby="product-price-addon" required="">
                                                                <div class="invalid-feedback">Please Enter a product
                                                                    price.
                                                                </div>
                                                            </div>

                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6">
                                                        <div class="mb-3">
                                                            <label class="form-label"
                                                                for="product-price-input">Berat</label>
                                                            <div class="input-group has-validation mb-3">
                                                                <span class="input-group-text"
                                                                    id="product-price-addon">Berat
                                                                    (gr)</span>
                                                                <input type="text" class="form-control"
                                                                    v-model="item.weight" id="product-price-input"
                                                                    aria-label="Price"
                                                                    aria-describedby="product-price-addon" required="">
                                                                <div class="invalid-feedback">Please Enter a product
                                                                    price.
                                                                </div>
                                                            </div>

                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6">
                                                        <div class="mb-3">
                                                            <label class="form-label" for="product-price-input">HPP
                                                                (Opsional)</label>
                                                            <div class="input-group has-validation mb-3">
                                                                <span class="input-group-text"
                                                                    id="product-price-addon">Rp</span>
                                                                <input type="text" class="form-control" v-model="item.hpp"
                                                                    id="product-price-input" aria-label="Price"
                                                                    aria-describedby="product-price-addon" required="">
                                                                <div class="invalid-feedback">Please Enter a product
                                                                    price.
                                                                </div>
                                                            </div>

                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6">
                                                        <div class="mb-3">
                                                            <label class="form-label" for="product-price-input">Variation
                                                                Code
                                                                (Opsional)</label>
                                                            <div class="has-validation mb-3">
                                                                <input type="text" class="form-control"
                                                                    v-model="item.product_code" id="product-price-input"
                                                                    aria-label="Price"
                                                                    aria-describedby="product-price-addon" required="">
                                                                <div class="invalid-feedback">Please Enter a product
                                                                    price.
                                                                </div>
                                                            </div>

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div v-if="form.many_variation_status == true">
                                        <div v-for="(item, index) in form.item_product">
                                            <div class="card" v-if="item.variation">
                                                <div class="card-body">
                                                    <h5>{{ item.variation.variation_name }} - {{
                                                        item.variation.child_variation_name }}</h5>
                                                    <div class="row">
                                                        <div class="col-lg-6">
                                                            <div class="mb-3">
                                                                <label class="form-label"
                                                                    for="product-price-input">Harga</label>
                                                                <div class="input-group has-validation mb-3">
                                                                    <span class="input-group-text"
                                                                        id="product-price-addon">Rp</span>
                                                                    <input type="text" class="form-control"
                                                                        v-model="item.normal_price" id="product-price-input"
                                                                        aria-label="Price"
                                                                        aria-describedby="product-price-addon" required="">
                                                                    <div class="invalid-feedback">Please Enter a product
                                                                        price.
                                                                    </div>
                                                                </div>

                                                            </div>
                                                        </div>
                                                        <div class="col-lg-6">
                                                            <div class="mb-3">
                                                                <label class="form-label"
                                                                    for="product-price-input">Berat</label>
                                                                <div class="input-group has-validation mb-3">
                                                                    <span class="input-group-text"
                                                                        id="product-price-addon">Berat
                                                                        (gr)</span>
                                                                    <input type="text" class="form-control"
                                                                        v-model="item.weight" id="product-price-input"
                                                                        aria-label="Price"
                                                                        aria-describedby="product-price-addon" required="">
                                                                    <div class="invalid-feedback">Please Enter a product
                                                                        price.
                                                                    </div>
                                                                </div>

                                                            </div>
                                                        </div>
                                                        <div class="col-lg-6">
                                                            <div class="mb-3">
                                                                <label class="form-label" for="product-price-input">HPP
                                                                    (Opsional)</label>
                                                                <div class="input-group has-validation mb-3">
                                                                    <span class="input-group-text"
                                                                        id="product-price-addon">Rp</span>
                                                                    <input type="text" class="form-control"
                                                                        v-model="item.hpp" id="product-price-input"
                                                                        aria-label="Price"
                                                                        aria-describedby="product-price-addon" required="">
                                                                    <div class="invalid-feedback">Please Enter a product
                                                                        price.
                                                                    </div>
                                                                </div>

                                                            </div>
                                                        </div>
                                                        <div class="col-lg-6">
                                                            <div class="mb-3">
                                                                <label class="form-label"
                                                                    for="product-price-input">Variation
                                                                    Code
                                                                    (Opsional)</label>
                                                                <div class="has-validation mb-3">
                                                                    <input type="text" class="form-control"
                                                                        v-model="item.product_code" id="product-price-input"
                                                                        aria-label="Price"
                                                                        aria-describedby="product-price-addon" required="">
                                                                    <div class="invalid-feedback">Please Enter a product
                                                                        price.
                                                                    </div>
                                                                </div>

                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </BRow>
                        </div>
                    </BCardBody>
                </BCard>

                <BCard no-body>
                    <BCardHeader>
                        <h5 class="card-title mb-0">Persediaan</h5>

                    </BCardHeader>
                    <BCardBody>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="mb-3">
                                    <h5>Manajemen Stock</h5>
                                    <div class="form-check form-switch form-switch-custom form-switch-success">
                                        <input class="form-check-input" v-model="form.manajemen_stock" type="checkbox"
                                            role="switch" id="variation">
                                        <label class="form-check-label" for="variation">Atur jumlah stook barang toko
                                            anda</label>
                                    </div>
                                </div>
                                <div class="mb-3" v-if="form.manajemen_stock == false">
                                    <label for="choices-publish-status-input" class="form-label">Status Stok</label>
                                    <div class="hstack gap-2 flex-wrap">
                                        <input type="radio" class="btn-check" name="stock_status" value="habis"
                                            v-model="form.stock_status" id="stock_status_1" checked>
                                        <label class="btn btn-outline-success" for="stock_status_1">Stok
                                            Habis</label>

                                        <input type="radio" class="btn-check" name="stock_status"
                                            v-model="form.stock_status" value="tersedia" id="stock_status_2">
                                        <label class="btn btn-outline-success" for="stock_status_2">Stok
                                            Tersedia</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <BRow v-if="form.manajemen_stock == true">
                            <div class="col-lg-12">
                                <div class="card-header">
                                    <h5 class="card-title">Izinkan Backorder</h5>
                                    <p>Apablia di-allow, maka pembeli masih dapat memesan barang walaupun stok sudah
                                        habis</p>
                                    <div class="mb-3">
                                        <div class="hstack gap-2 flex-wrap">
                                            <input type="radio" class="btn-check" name="backorder_status"
                                                v-model="form.backorder_status" :value="true" id="backorder_status_1"
                                                checked>
                                            <label class="btn btn-outline-success" for="backorder_status_1">Izinkan</label>

                                            <input type="radio" class="btn-check" name="backorder_status"
                                                v-model="form.backorder_status" :value="false" id="backorder_status_2">
                                            <label class="btn btn-outline-success" for="backorder_status_2">Tidak
                                                Diizinkan</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title">Kuantitas Stok</h5>
                                        <div class="row">
                                            <div class="col-lg-12"
                                                v-if="form.many_variation_status == false && form.price_type == 'variable'">
                                                <table class="table table-bordered">
                                                    <tbody>
                                                        <tr v-for="(item, index) in size">
                                                            <th class="w-50">{{ item.name }}</th>
                                                            <th class="w-50"><input class="form-control"
                                                                    v-model="item.stock" /></th>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="col-lg-12"
                                                v-if="form.many_variation_status == true && form.price_type == 'variable'">
                                                <table class="table table-bordered">
                                                    <tbody>
                                                        <template v-for="(item, index) in form.item_product">
                                                            <tr>
                                                                <th class="w-50">{{ item.variation.variation_name }} - {{
                                                                    item.variation.child_variation_name }}</th>
                                                                <th class="w-50"><input class="form-control"
                                                                        v-model="item.stock" /></th>
                                                            </tr>
                                                        </template>
                                                    </tbody>
                                                </table>
                                            </div>

                                            <div class="col-lg-12"
                                                v-if="form.many_variation_status == false, form.price_type == 'simple'">
                                                <input v-model="form.item_product[0].stock" type="text" class="form-control"
                                                    id="product-price-input" aria-label="Price"
                                                    aria-describedby="product-price-addon" required="">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </BRow>
                    </BCardBody>
                </BCard>
                <BCard no-body>
                    <BCardHeader>
                        <h5 class="card-title mb-0">Bump</h5>
                    </BCardHeader>
                    <BCardBody>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="mb-3">
                                    <div class="form-check form-switch form-switch-custom form-switch-success">
                                        <input class="form-check-input" v-model="form.bump_status" type="checkbox"
                                            role="switch" id="variation">
                                        <label class="form-check-label" for="variation">Apakah Anda ingin menampilkan
                                            Bump
                                            untuk produk ini?</label>
                                    </div>
                                </div>
                            </div>
                            <template v-if="form.bump_status == true">
                                <div class="col-lg-12">
                                    <div class="mb-3">
                                        <label class="form-label" for="product-price-input">Nama Produk Bump (akan muncul di
                                            cart & invoice)</label>
                                        <div class="has-validation mb-3">
                                            <input v-model="form.product_bump.name" type="text" class="form-control"
                                                id="product-price-input" aria-label="Price"
                                                aria-describedby="product-price-addon" required="">
                                            <div class="invalid-feedback">Please Enter a product
                                                price.
                                            </div>
                                        </div>

                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="mb-3">
                                        <label class="form-label" for="product-price-input">Harga</label>
                                        <div class="input-group has-validation mb-3">
                                            <span class="input-group-text" id="product-price-addon">Rp</span>
                                            <input v-model="form.product_bump.price" type="text" class="form-control"
                                                id="product-price-input" aria-label="Price"
                                                aria-describedby="product-price-addon" required="">
                                            <div class="invalid-feedback">Please Enter a product
                                                price.
                                            </div>
                                        </div>

                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="mb-3">
                                        <label class="form-label" for="product-price-input">Bump Weight (gr)</label>
                                        <div class="has-validation mb-3">
                                            <input v-model="form.product_bump.weight" type="text" class="form-control"
                                                id="product-price-input" aria-label="Price"
                                                aria-describedby="product-price-addon" required="">
                                            <div class="invalid-feedback">Please Enter a product
                                                price.
                                            </div>
                                        </div>

                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="mb-3">
                                        <label class="form-label" for="product-price-input">Bump Image</label>
                                        <div class="has-validation mb-3">
                                            <label for="bump-image" class="btn-primary btn">Unggah Gambar</label>
                                            <input type="file" @change="bumpImageChange" class="form-control"
                                                id="bump-image" hidden aria-label="Price"
                                                aria-describedby="product-price-addon" required="">
                                            <div class="invalid-feedback">Please Enter a product
                                                price.
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </template>
                        </div>
                    </BCardBody>
                </BCard>
                <BCard no-body>
                    <BCardHeader>
                        <h5 class="card-title mb-0">Pengiriman</h5>

                    </BCardHeader>
                    <BCardBody>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="mb-3" v-if="form.many_variation_status == false">
                                    <label class="form-label" for="product-price-input">Berat (gr)</label>
                                    <div class="has-validation mb-3">
                                        <input type="text" class="form-control" v-model="form.shipping.weight"
                                            id="product-price-input" aria-label="Price"
                                            aria-describedby="product-price-addon" required="">
                                        <div class="invalid-feedback">Please Enter a product
                                            price.
                                        </div>
                                    </div>

                                </div>
                                <div class="mb-3">
                                    <div class="form-check form-switch form-switch-custom form-switch-success">
                                        <input class="form-check-input" v-model="form.shipping.otomatic_price_shipping"
                                            type="checkbox" role="switch" id="variation">
                                        <label class="form-check-label" for="variation">Apakah Anda ingin menghitung
                                            ongkos kirim otomatis?</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <BRow v-if="form.shipping.otomatic_price_shipping == true">
                            <div class="col-lg-12">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title">Tipe Pengiriman</h5>
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="btn-group w-100 mb-3">
                                                    <button type="button" class="btn w-100"
                                                        @click="shippingMethod('otomatic')"
                                                        :class="{ 'btn-primary': form.shipping.shipping_type == 'otomatic', 'btn-light': form.shipping.shipping_type !== 'otomatic' }">Otomatis</button>
                                                    <button type="button" class="btn w-100" @click="shippingMethod('flat')"
                                                        :class="{ 'btn-primary': form.shipping.shipping_type == 'flat', 'btn-light': form.shipping.shipping_type !== 'flat' }">Flat</button>
                                                    <button type="button" class="btn w-100" @click="shippingMethod('free')"
                                                        :class="{ 'btn-primary': form.shipping.shipping_type == 'free', 'btn-light': form.shipping.shipping_type !== 'free' }">Gratis</button>
                                                </div>
                                            </div>

                                            <!-- Otomatis Tipe Pengiriman -->

                                            <div v-if="form.shipping.shipping_type == 'otomatic'">
                                                <div class="col-lg-12">
                                                    <div class="row">
                                                        <div class="col-12 col-lg-4"
                                                            v-for="(item, index) in form.shipping.otomatic">
                                                            <div class="form-check">
                                                                <input class="form-check-input" v-model="item.status"
                                                                    type="checkbox" id="formCheck2">
                                                                <label class="form-check-label" for="formCheck2">
                                                                    {{ item.name }}
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Flat Tipe Pengiriman -->
                                            <div v-if="form.shipping.shipping_type == 'flat'">
                                                <div class="col-lg-12">
                                                    <div class="row">
                                                        <div class="col-12 col-lg-12">
                                                            <div class="mb-3">
                                                                <label class="form-label" for="product-price-input">Harga
                                                                    Pengiriman
                                                                    Flat</label>
                                                                <div class="has-validation mb-3">
                                                                    <input type="text" class="form-control"
                                                                        v-model="form.shipping.flat.price"
                                                                        id="product-price-input" aria-label="Price"
                                                                        aria-describedby="product-price-addon" required="">
                                                                </div>

                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </BRow>
                    </BCardBody>
                </BCard>
                <BCard no-body>
                    <BCardHeader>
                        <h5 class="card-title mb-0">Pembayaran</h5>
                    </BCardHeader>
                    <BCardBody>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="mb-3">
                                    <div class="form-check form-switch form-switch-custom form-switch-success">
                                        <input class="form-check-input" type="checkbox" role="switch" id="variation"
                                            v-model="form.bank_transfer.status">
                                        <label class="form-check-label" for="variation">Bank Transfer</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12" v-if="form.bank_transfer.status == true">
                                <div class="mb-3">
                                    <label class="form-label" for="product-price-input">Bank Transfer -
                                        Deskripsi</label>
                                    <div class="has-validation mb-3">
                                        <input type="text" v-model="form.bank_transfer.description" class="form-control"
                                            id="product-price-input" aria-label="Price"
                                            aria-describedby="product-price-addon" required="">
                                        <div class="invalid-feedback">Please Enter a product
                                            price.
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="mb-3">
                                    <div class="form-check form-switch form-switch-custom form-switch-success">
                                        <input class="form-check-input" type="checkbox" v-model="form.cod.status"
                                            role="switch" id="variation">
                                        <label class="form-check-label" for="variation">COD</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12" v-if="form.cod.status == true">
                                <div class="mb-3">
                                    <label class="form-label" for="product-price-input">COD -
                                        Deskripsi</label>
                                    <div class="has-validation mb-3">
                                        <input v-model="form.cod.description" type="text" class="form-control"
                                            id="product-price-input" aria-label="Price"
                                            aria-describedby="product-price-addon" required="">
                                        <div class="invalid-feedback">Please Enter a product
                                            price.
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="mb-3">
                                    <div class="form-check form-switch form-switch-custom form-switch-success">
                                        <input class="form-check-input" type="checkbox" v-model="form.epayment.status"
                                            role="switch" id="variation">
                                        <label class="form-check-label" for="variation">E - Payment</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </BCardBody>
                </BCard>
                <BCard no-body>
                    <BCardHeader>
                        <h5 class="card-title mb-0">Kode Unik</h5>
                    </BCardHeader>
                    <BCardBody>
                        <h5 class="mb-3">Pengaturan Kode Unik Untuk Pembayaran</h5>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="mb-3">
                                    <div class="form-check form-switch form-switch-custom form-switch-success">
                                        <input class="form-check-input" type="checkbox" role="switch" id="variation"
                                            v-model="form.unique_code.status">
                                        <label class="form-check-label" for="variation">Aktifkan Kode Unik</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <template v-if="form.unique_code.status == true">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="btn-group w-100 mb-3">
                                        <button type="button" class="btn w-100" @click="uniqueMethod('down')"
                                            :class="{ 'btn-primary': form.unique_code.type == 'down', 'btn-light': form.unique_code.type !== 'down' }">Turunkan</button>
                                        <button type="button" class="btn w-100" @click="uniqueMethod('up')"
                                            :class="{ 'btn-primary': form.unique_code.type == 'up', 'btn-light': form.unique_code.type !== 'up' }">Naikkan</button>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-6 col-12">
                                    <div class="mb-3">
                                        <label class="form-label" for="product-price-input">Min</label>
                                        <div class="has-validation mb-3">
                                            <input type="number" v-model="form.unique_code.min" class="form-control"
                                                id="product-price-input" aria-label="Price"
                                                aria-describedby="product-price-addon" required="">
                                        </div>

                                    </div>
                                </div>
                                <div class="col-lg-6 col-12">
                                    <div class="mb-3">
                                        <label class="form-label" for="product-price-input">Max</label>
                                        <div class="has-validation mb-3">
                                            <input type="number" v-model="form.unique_code.max" class="form-control"
                                                id="product-price-input" aria-label="Price"
                                                aria-describedby="product-price-addon" required="">
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </template>
                    </BCardBody>
                </BCard>
                <BCard no-body>
                    <BCardHeader>
                        <h5 class="card-title mb-0">Reseller</h5>
                    </BCardHeader>
                    <BCardBody>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="mb-3">
                                    <div class="form-check form-switch form-switch-custom form-switch-success">
                                        <input class="form-check-input" v-model="form.reseller_status" type="checkbox"
                                            role="switch" id="variation">
                                        <label class="form-check-label" for="variation">Aktifkan Reseller untuk produk
                                            ini</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </BCardBody>
                </BCard>
                <BCard no-body>
                    <BCardHeader>
                        <h5 class="card-title mb-0">Tugaskan Produk Ke</h5>
                    </BCardHeader>
                    <BCardBody>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="mb-3">
                                    <div class="form-check form-switch form-switch-custom form-switch-success">
                                        <input v-model="form.global_cs_rotator_status" @change="changeRotatorStatus"
                                            class="form-check-input" type="checkbox" role="switch" id="variation">
                                        <label class="form-check-label" for="variation">Override Global CS
                                            Rotator</label>
                                    </div>
                                </div>
                                <div class="mb-3" v-if="form.global_cs_rotator_status">
                                    <div class="border p-3" v-if="team.length == 0">
                                        <p class="text-center">Untuk menetapkan produk ke Anggota Tim, Anda harus
                                            menambahkan Angota ke tim
                                            Anda
                                        </p>
                                        <div class="d-flex justify-content-center">
                                            <Link href="/team" class="btn btn-secondary">Tambah Anggota Tim</Link>
                                        </div>
                                    </div>
                                    <div class="border p-3" v-if="team.length !== 0">
                                        <div class="mb-3">
                                            <div class="border">
                                                <!-- <div class="d-flex justify-content-between mb-3 p-2">
                                                    <div type="button" class="text-center w-100 py-2"
                                                        @click="changeRotatorRole('Customer Service')"
                                                        :class="{ 'border-bottom border-secondary': form.rotatorData.role == 'Customer Service' }">
                                                        Customer Service
                                                    </div>
                                                    <div type="button" class="text-center w-100 py-2"
                                                        :class="{ 'border-bottom border-secondary': form.rotatorData.role == 'Admin' }"
                                                        @click="changeRotatorRole('Admin')">
                                                        Admin
                                                    </div>
                                                </div> -->
                                                <div class="p-2" v-if="form.rotatorData.role == 'Customer Service'">
                                                    <Multiselect v-model="form.rotatorData.team" class="form-control"
                                                        placeholder="Pilih Customer Service" mode="tags"
                                                        :close-on-select="false" :searchable="true" :create-option="false"
                                                        :options="teamOption" />
                                                </div>
                                                <div class="p-2" v-if="form.rotatorData.role == 'Admin'">
                                                    <Multiselect v-model="form.rotatorData.team" class="form-control"
                                                        placeholder="Pilih Admin" mode="tags" :close-on-select="false"
                                                        :searchable="true" :create-option="false" :options="teamOption" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </BCardBody>
                </BCard>
                <div class="text-end mb-3">
                    <BButton type="button" variant="primary" class="w-sm" @click="submit">Kirim
                    </BButton>
                </div>
            </BCol>
            <!-- End Page Product -->

            <!-- Checkout -->
            <BCol lg="6">

                <!-- Right Sidebar -->
                <BCard no-body class="bg-light" v-if="form.checkout.template == 'right-sidebar'"
                    style="position: sticky; top: 130px;overflow: auto; max-height: calc(100vh - 200px);">
                    <div class="row d-flex justify-content-center mt-4 mb-2">
                        <div class="col-12 col-lg-6 mb-3">
                            <img class="img-fluid w-100" :src="form.checkout.header.image" alt="">
                        </div>
                        <div class="col-12"
                            v-if="form.checkout.header.titleStatus == true || form.checkout.header.taglineStatus == true">
                            <h5 class="text-center" v-if="form.checkout.header.titleStatus == true">{{
                                form.checkout.header.title }}</h5>
                            <p class="text-center" v-if="form.checkout.header.taglineStatus == true">{{
                                form.checkout.header.tagline }}</p>
                        </div>
                    </div>
                    <BCardBody>
                        <BCard no-body>
                            <BCardBody class="pt-0 pb-0">
                                <div class="row">
                                    <div class="col-7 p-2">
                                        <div class="mb-3" v-if="form.price_type == 'variable'">
                                            <h5 class="fw-bold" v-if="form.many_buy_status == false">Pilihan Produk
                                            </h5>
                                            <template v-if="form.many_buy_status == false">
                                                <div class="row" v-if="form.many_variation_status == false">
                                                    <div class="col-6" v-for="(item, index) in form.item_product">
                                                        <div class="form-check">
                                                            <input class="form-check-input" name="product" type="radio">
                                                            <label class="form-check-label">
                                                                {{ item.variation.variation_name }}
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row" v-if="form.many_variation_status == true">
                                                    <template v-for="(item, index) in form.item_product">
                                                        <div class="col-6" v-if="item.variation !== null">
                                                            <div class="form-check">
                                                                <input class="form-check-input" name="product" type="radio">
                                                                <label class="form-check-label">
                                                                    {{ item.variation.variation_name }} - {{
                                                                        item.variation.child_variation_name }}
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </template>
                                                </div>
                                            </template>
                                            <template v-if="form.many_buy_status == true">
                                                <div class="row" v-if="form.many_variation_status == false">
                                                    <table class="table table-bordered">
                                                        <thead>
                                                            <tr>
                                                                <th>Pilihan Produk:</th>
                                                                <th>Jumlah</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr v-for="(item, index) in form.item_product">
                                                                <td class="w-75">{{ item.variation.variation_name }}</td>
                                                                <td>
                                                                    <input class="form-control" />
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div class="row" v-if="form.many_variation_status == true">
                                                    <table class="table table-bordered">
                                                        <thead>
                                                            <tr>
                                                                <th>Pilihan Produk:</th>
                                                                <th>Jumlah</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <template v-for="(item, index) in form.item_product">
                                                                <tr>
                                                                    <td class="w-75">{{ item.variation.variation_name }} -
                                                                        {{ item.variation.child_variation_name }}
                                                                    </td>
                                                                    <td>
                                                                        <input class="form-control" />
                                                                    </td>
                                                                </tr>
                                                            </template>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </template>
                                        </div>
                                        <div class="mb-3">
                                            <p class="fw-bold">{{ form.checkout.sectionTitle.form }}</p>
                                        </div>
                                        <div class="mb-3" v-for="(  item, index  ) in   form.checkout.fields  "
                                            :key="index">
                                            <div v-if="item.show == true">
                                                <input v-if="item.field == 'Input'" :type="item.inputType"
                                                    class="form-control" :placeholder="item.placeholder" />
                                                <Multiselect v-if="item.field == 'Select'" class="form-control"
                                                    v-model="value1" :close-on-select="true" :placeholder="item.placeholder"
                                                    :searchable="true" :create-option="true" :options="[
                                                        { value: '', label: item.placeholder },
                                                    ]
                                                        " />
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <p class="fw-bold">{{ form.checkout.sectionTitle.payment_method }}</p>
                                            <div class="p-4 border" v-if="form.bank_transfer.status == true">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="formCheck2">
                                                    <label class="form-check-label" for="formCheck2">
                                                        Bank Transfer
                                                    </label>
                                                </div>
                                                {{ form.bank_transfer.description }}
                                            </div>
                                            <div class="p-4 border" v-if="form.cod.status == true">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="formCheck2">
                                                    <label class="form-check-label" for="formCheck2">
                                                        COD
                                                    </label>
                                                </div>
                                                {{ form.cod.description }}
                                            </div>
                                            <div class="p-4 border" v-if="form.epayment.status == true">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="formCheck2">
                                                    <label class="form-check-label" for="formCheck2">
                                                        E - Payment
                                                    </label>
                                                </div>
                                                {{ form.bank_transfer.epayment }}
                                            </div>
                                        </div>
                                        <div class="border border-dashed border-danger border-2 p-2 mb-3"
                                            v-if="form.bump_status">
                                            <div class="d-flex align-items-center mb-1 bg-warning py-1 px-1 mb-1">
                                                <img class="me-2" src="@assets/images/bump-arrow.gif" alt="">
                                                <input type="checkbox" name="" class="form-check-input me-2" id="bump">
                                                <label class="form-check-label fw-bold" for="bump">Centang untuk
                                                    memesan juga</label>
                                            </div>
                                            <div class="fw-bold text-danger mb-1">Nama Produk Bump Anda + Harga</div>
                                            <div class="mb-1">
                                                <img class="img-fluid w-100" :src="form.product_bump.image" alt="">
                                            </div>
                                            <div class="fs-12">Penjelasan Tentang Produk Bump Anda Disini</div>
                                        </div>
                                        <button
                                            class="btn btn-warning w-100 fs-4 fw-bold d-flex align-items-center justify-content-center">{{
                                                form.checkout.buyButtonText }}
                                            <i class="bx bx-right-arrow-circle ms-2"></i></button>
                                    </div>
                                    <div class="col-5 bg-light p-2">
                                        <div class="mb-3" v-if="form.checkout.productImageStatus == true">
                                            <img class="img-fluid w-100" :src="tempProductImage" alt="">
                                        </div>
                                        <div class="mb-3">{{ form.checkout.productDescription }}</div>
                                        <p class="fw-bold">{{ form.checkout.sectionTitle.points }}</p>
                                        <div class="mb-3" v-for="(   item, index   ) in    form.checkout.points   "
                                            :key="index">
                                            <div class="d-flex align-items-center">
                                                <i class="bx bx-check text-success fs-5"></i>
                                                {{ item.poin }}
                                            </div>
                                        </div>
                                        <div class="border border-secondary p-2 mb-3"
                                            v-if="form.checkout.summaryStatus == true">
                                            <p class="fs-12">
                                                RINCIAN PESANAN
                                            </p>
                                            <table class="w-100">
                                                <tr>
                                                    <td style="vertical-align: top;">
                                                        <p class="fs-12">
                                                            {{ form.product_name }}
                                                        </p>
                                                    </td>
                                                    <td class="text-end">
                                                        <p v-if="form.price_sale_status == true"
                                                            class="fs-12 text-decoration-line-through text-danger p-0 m-0">
                                                            {{ global.formatNumber(form.normal_price) }}
                                                        </p>
                                                        <p class="fs-12" v-if="form.price_sale_status == true">
                                                            {{ global.formatNumber(form.price_sale) }}
                                                        </p>
                                                        <p class="fs-12" v-if="form.price_sale_status == false">
                                                            {{ global.formatNumber(form.normal_price) }}
                                                        </p>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                        <div class="mb-3" v-if="form.checkout.kuponStatus">
                                            <label class="form-label" for="product-price-input">GUNAKAN KODE
                                                KUPON</label>
                                            <div class="has-validation mb-3">
                                                <input type="text" class="form-control" id="product-price-input"
                                                    aria-label="Price" aria-describedby="product-price-addon" required="">
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </BCardBody>
                        </BCard>
                        <div class="container" v-if="form.checkout.testimonials.length > 0">
                            <div class="row">
                                <div class="col-12 col-lg-6" v-for="(item, index) in form.checkout.testimonials"
                                    :key="index">
                                    <div class="row align-items-center mb-3">
                                        <div class="col-auto">
                                            <img v-if="item.image" :src="item.image" alt="" style="width:100px">
                                            <div v-if="!item.image">
                                                <i class="bx bx-user " style="font-size:80px"></i>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <h5 class="mt-0 font-weight-bold">{{ item.name }}</h5>
                                            <p>{{ item.message }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </BCardBody>
                </BCard>

                <!-- Left Sidebar -->
                <BCard no-body class="bg-light" v-if="form.checkout.template == 'left-sidebar'"
                    style="position: sticky; top: 130px;overflow: auto; max-height: calc(100vh - 200px);">
                    <div class="row d-flex justify-content-center mt-4 mb-2">
                        <div class="col-12 col-lg-6 mb-3">
                            <img class="img-fluid w-100" :src="form.checkout.header.image" alt="">
                        </div>
                        <div class="col-12"
                            v-if="form.checkout.header.titleStatus == true || form.checkout.header.taglineStatus == true">
                            <h5 class="text-center" v-if="form.checkout.header.titleStatus == true">{{
                                form.checkout.header.title }}</h5>
                            <p class="text-center" v-if="form.checkout.header.taglineStatus == true">{{
                                form.checkout.header.tagline }}</p>
                        </div>
                    </div>
                    <BCardBody>
                        <BCard no-body>
                            <BCardBody class="pt-0 pb-0">
                                <div class="row">
                                    <div class="col-5 bg-light p-2">
                                        <div class="mb-3" v-if="form.checkout.productImageStatus == true">
                                            <img class="img-fluid w-100" :src="tempProductImage" alt="">
                                        </div>
                                        <div class="mb-3">{{ form.checkout.productDescription }}</div>
                                        <p class="fw-bold">{{ form.checkout.sectionTitle.points }}</p>
                                        <div class="mb-3" v-for="(   item, index   ) in    form.checkout.points   "
                                            :key="index">
                                            <div class="d-flex align-items-center">
                                                <i class="bx bx-check text-success fs-5"></i>
                                                {{ item.poin }}
                                            </div>
                                        </div>
                                        <div class="border border-secondary p-2 mb-3"
                                            v-if="form.checkout.summaryStatus == true">
                                            <p class="fs-12">
                                                RINCIAN PESANAN
                                            </p>
                                            <table class="w-100">
                                                <tr>
                                                    <td style="vertical-align: top;">
                                                        <p class="fs-12">
                                                            {{ form.product_name }}
                                                        </p>
                                                    </td>
                                                    <td class="text-end">
                                                        <p v-if="form.price_sale_status == true"
                                                            class="fs-12 text-decoration-line-through text-danger p-0 m-0">
                                                            {{ global.formatNumber(form.normal_price) }}
                                                        </p>
                                                        <p class="fs-12" v-if="form.price_sale_status == true">
                                                            {{ global.formatNumber(form.price_sale) }}
                                                        </p>
                                                        <p class="fs-12" v-if="form.price_sale_status == false">
                                                            {{ global.formatNumber(form.normal_price) }}
                                                        </p>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                        <div class="mb-3" v-if="form.checkout.kuponStatus">
                                            <label class="form-label" for="product-price-input">GUNAKAN KODE
                                                KUPON</label>
                                            <div class="has-validation mb-3">
                                                <input type="text" class="form-control" id="product-price-input"
                                                    aria-label="Price" aria-describedby="product-price-addon" required="">
                                            </div>

                                        </div>
                                    </div>
                                    <div class="col-7 p-2">
                                        <div class="mb-3" v-if="form.price_type == 'variable'">
                                            <h5 class="fw-bold" v-if="form.many_buy_status == false">Pilihan Produk
                                            </h5>
                                            <template v-if="form.many_buy_status == false">
                                                <div class="row" v-if="form.many_variation_status == false">
                                                    <div class="col-6" v-for="(item, index) in form.item_product">
                                                        <div class="form-check">
                                                            <input class="form-check-input" name="product" type="radio">
                                                            <label class="form-check-label">
                                                                {{ item.variation.variation_name }}
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row" v-if="form.many_variation_status == true">
                                                    <template v-for="(item, index) in form.item_product">
                                                        <div class="col-6">
                                                            <div class="form-check">
                                                                <input class="form-check-input" name="product" type="radio">
                                                                <label class="form-check-label">
                                                                    {{ item.variation.variation_name }} - {{
                                                                        item.variation.child_variation_name }}
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </template>
                                                </div>
                                            </template>
                                            <template v-if="form.many_buy_status == true">
                                                <div class="row" v-if="form.many_variation_status == false">
                                                    <table class="table table-bordered">
                                                        <thead>
                                                            <tr>
                                                                <th>Pilihan Produk:</th>
                                                                <th>Jumlah</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr v-for="(item, index) in form.item_product">
                                                                <td class="w-75">{{ item.variation.variation_name }}</td>
                                                                <td>
                                                                    <input class="form-control" />
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div class="row" v-if="form.many_variation_status == true">
                                                    <table class="table table-bordered">
                                                        <thead>
                                                            <tr>
                                                                <th>Pilihan Produk:</th>
                                                                <th>Jumlah</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <template v-for="(item, index) in form.item_product">
                                                                <tr>
                                                                    <td class="w-75">{{ item.variation.variation_name }} -
                                                                        {{ item.variation.child_variation_name }}
                                                                    </td>
                                                                    <td>
                                                                        <input class="form-control" />
                                                                    </td>
                                                                </tr>
                                                            </template>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </template>
                                        </div>
                                        <div class="mb-3">
                                            <p class="fw-bold">{{ form.checkout.sectionTitle.form }}</p>
                                        </div>
                                        <div class="mb-3" v-for="(  item, index  ) in   form.checkout.fields  "
                                            :key="index">
                                            <div v-if="item.show == true">
                                                <input v-if="item.field == 'Input'" :type="item.inputType"
                                                    class="form-control" :placeholder="item.placeholder" />
                                                <Multiselect v-if="item.field == 'Select'" class="form-control"
                                                    v-model="value1" :close-on-select="true" :placeholder="item.placeholder"
                                                    :searchable="true" :create-option="true" :options="[
                                                        { value: '', label: item.placeholder },
                                                    ]
                                                        " />
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <p class="fw-bold">{{ form.checkout.sectionTitle.payment_method }}</p>
                                            <div class="p-4 border" v-if="form.bank_transfer.status == true">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="formCheck2">
                                                    <label class="form-check-label" for="formCheck2">
                                                        Bank Transfer
                                                    </label>
                                                </div>
                                                {{ form.bank_transfer.description }}
                                            </div>
                                            <div class="p-4 border" v-if="form.cod.status == true">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="formCheck2">
                                                    <label class="form-check-label" for="formCheck2">
                                                        COD
                                                    </label>
                                                </div>
                                                {{ form.cod.description }}
                                            </div>
                                            <div class="p-4 border" v-if="form.epayment.status == true">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="formCheck2">
                                                    <label class="form-check-label" for="formCheck2">
                                                        E - Payment
                                                    </label>
                                                </div>
                                                {{ form.bank_transfer.epayment }}
                                            </div>
                                        </div>
                                        <div class="border border-dashed border-danger border-2 p-2 mb-3"
                                            v-if="form.bump_status == true">
                                            <div class="d-flex align-items-center mb-1 bg-warning py-1 px-1 mb-1">
                                                <img class="me-2" src="@assets/images/bump-arrow.gif" alt="">
                                                <input type="checkbox" name="" class="form-check-input me-2" id="bump">
                                                <label class="form-check-label fw-bold" for="bump">Centang untuk
                                                    memesan juga</label>
                                            </div>
                                            <div class="fw-bold text-danger mb-1">Nama Produk Bump Anda + Harga</div>
                                            <div class="mb-1" v-if="form.product_bump.image">
                                                <img class="img-fluid w-100" :src="form.product_bump.image" alt="">
                                            </div>
                                            <div class="fs-12">Penjelasan Tentang Produk Bump Anda Disini</div>
                                        </div>
                                        <button
                                            class="btn btn-warning w-100 fs-4 fw-bold d-flex align-items-center justify-content-center">{{
                                                form.checkout.buyButtonText }}
                                            <i class="bx bx-right-arrow-circle ms-2"></i></button>
                                    </div>

                                </div>
                            </BCardBody>
                        </BCard>
                        <div class="container" v-if="form.checkout.testimonials.length > 0">
                            <div class="row">
                                <div class="col-12 col-lg-6" v-for="(item, index) in form.checkout.testimonials"
                                    :key="index">
                                    <div class="row align-items-center mb-3">
                                        <div class="col-auto">
                                            <img v-if="item.image" :src="item.image" alt="" style="width:100px">
                                            <div v-if="!item.image">
                                                <i class="bx bx-user " style="font-size:80px"></i>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <h5 class="mt-0 font-weight-bold">{{ item.name }}</h5>
                                            <p>{{ item.message }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </BCardBody>
                </BCard>

                <!-- No Sidebar -->
                <BCard no-body class="bg-light" v-if="form.checkout.template == 'no-sidebar'"
                    style="position: sticky; top: 130px;overflow: auto; max-height: calc(100vh - 200px);">
                    <div class="row d-flex justify-content-center mt-4 mb-2">
                        <div class="col-12 col-lg-6 mb-3">
                            <img class="img-fluid w-100" :src="form.checkout.header.image" alt="">
                        </div>
                        <div class="col-12"
                            v-if="form.checkout.header.titleStatus == true || form.checkout.header.taglineStatus == true">
                            <h5 class="text-center" v-if="form.checkout.header.titleStatus == true">{{
                                form.checkout.header.title }}</h5>
                            <p class="text-center" v-if="form.checkout.header.taglineStatus == true">{{
                                form.checkout.header.tagline }}</p>
                        </div>
                    </div>
                    <BCardBody>
                        <BCard no-body>
                            <BCardBody class="pt-0 pb-0">
                                <div class="row">
                                    <div class="col-12 px-5 pt-5">
                                        <div class="mb-3" v-if="form.checkout.productImageStatus == true">
                                            <img class="img-fluid w-100" :src="tempProductImage" alt="">
                                        </div>
                                        <div class="mb-3">{{ form.checkout.productDescription }}</div>
                                        <p class="fw-bold">{{ form.checkout.sectionTitle.points }}</p>
                                        <div class="mb-3" v-for="(   item, index   ) in    form.checkout.points   "
                                            :key="index">
                                            <div class="d-flex align-items-center">
                                                <i class="bx bx-check text-success fs-5"></i>
                                                {{ item.poin }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 px-5 pb-5">
                                        <div class="mb-3" v-if="form.price_type == 'variable'">
                                            <h5 class="fw-bold" v-if="form.many_buy_status == false">Pilihan Produk
                                            </h5>
                                            <template v-if="form.many_buy_status == false">
                                                <div class="row" v-if="form.many_variation_status == false">
                                                    <div class="col-6" v-for="(item, index) in form.item_product">
                                                        <div class="form-check">
                                                            <input class="form-check-input" name="product" type="radio">
                                                            <label class="form-check-label">
                                                                {{ item.variation.variation_name }}
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row" v-if="form.many_variation_status == true">
                                                    <template v-for="(item, index) in form.item_product">
                                                        <div class="col-6">
                                                            <div class="form-check">
                                                                <input class="form-check-input" name="product" type="radio">
                                                                <label class="form-check-label">
                                                                    {{ item.variation.variation_name }} - {{
                                                                        item.variation.child_variation_name }}
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </template>
                                                </div>
                                            </template>
                                            <template v-if="form.many_buy_status == true">
                                                <div class="row" v-if="form.many_variation_status == false">
                                                    <table class="table table-bordered">
                                                        <thead>
                                                            <tr>
                                                                <th>Pilihan Produk:</th>
                                                                <th>Jumlah</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr v-for="(item, index) in form.item_product">
                                                                <td class="w-75">{{ item.variation.variation_name }}</td>
                                                                <td>
                                                                    <input class="form-control" />
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div class="row" v-if="form.many_variation_status == true">
                                                    <table class="table table-bordered">
                                                        <thead>
                                                            <tr>
                                                                <th>Pilihan Produk:</th>
                                                                <th>Jumlah</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <template v-for="(item, index) in form.item_product">
                                                                <tr>
                                                                    <td class="w-75">{{ item.variation.variation_name }} -
                                                                        {{ item.variation.child_variation_name }}
                                                                    </td>
                                                                    <td>
                                                                        <input class="form-control" />
                                                                    </td>
                                                                </tr>
                                                            </template>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </template>
                                        </div>
                                        <div class="mb-3">
                                            <p class="fw-bold">{{ form.checkout.sectionTitle.form }}</p>
                                        </div>
                                        <div class="mb-3" v-for="(  item, index  ) in   form.checkout.fields  "
                                            :key="index">
                                            <div v-if="item.show == true">
                                                <input v-if="item.field == 'Input'" :type="item.inputType"
                                                    class="form-control" :placeholder="item.placeholder" />
                                                <Multiselect v-if="item.field == 'Select'" class="form-control"
                                                    v-model="value1" :close-on-select="true" :placeholder="item.placeholder"
                                                    :searchable="true" :create-option="true" :options="[
                                                        { value: '', label: item.placeholder },
                                                    ]
                                                        " />
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <p class="fw-bold">{{ form.checkout.sectionTitle.payment_method }}</p>
                                            <div class="p-4 border" v-if="form.bank_transfer.status == true">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="formCheck2">
                                                    <label class="form-check-label" for="formCheck2">
                                                        Bank Transfer
                                                    </label>
                                                </div>
                                                {{ form.bank_transfer.description }}
                                            </div>
                                            <div class="p-4 border" v-if="form.cod.status == true">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="formCheck2">
                                                    <label class="form-check-label" for="formCheck2">
                                                        COD
                                                    </label>
                                                </div>
                                                {{ form.cod.description }}
                                            </div>
                                            <div class="p-4 border" v-if="form.epayment.status == true">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="formCheck2">
                                                    <label class="form-check-label" for="formCheck2">
                                                        E - Payment
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="border border-dashed border-danger border-2 p-2 mb-3"
                                            v-if="form.bump_status == true">
                                            <div class="d-flex align-items-center mb-1 bg-warning py-1 px-1 mb-1">
                                                <img class="me-2" src="@assets/images/bump-arrow.gif" alt="">
                                                <input type="checkbox" name="" class="form-check-input me-2" id="bump">
                                                <label class="form-check-label fw-bold" for="bump">Centang untuk
                                                    memesan juga</label>
                                            </div>
                                            <div class="fw-bold text-danger mb-1">Nama Produk Bump Anda + Harga</div>
                                            <div class="mb-1">
                                                <img class="img-fluid w-100" :src="form.product_bump.image" alt="">
                                            </div>
                                            <div class="fs-12">Penjelasan Tentang Produk Bump Anda Disini</div>
                                        </div>
                                        <div class="border border-secondary p-2 mb-3"
                                            v-if="form.checkout.summaryStatus == true">
                                            <p class="fs-12">
                                                RINCIAN PESANAN
                                            </p>
                                            <table class="w-100">
                                                <tr>
                                                    <td style="vertical-align: top;">
                                                        <p class="fs-12">
                                                            {{ form.product_name }}
                                                        </p>
                                                    </td>
                                                    <td class="text-end">
                                                        <p v-if="form.price_sale_status == true"
                                                            class="fs-12 text-decoration-line-through text-danger p-0 m-0">
                                                            {{ global.formatNumber(form.normal_price) }}
                                                        </p>
                                                        <p class="fs-12" v-if="form.price_sale_status == true">
                                                            {{ global.formatNumber(form.price_sale) }}
                                                        </p>
                                                        <p class="fs-12" v-if="form.price_sale_status == false">
                                                            {{ global.formatNumber(form.normal_price) }}
                                                        </p>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                        <div class="mb-3" v-if="form.checkout.kuponStatus">
                                            <label class="form-label" for="product-price-input">GUNAKAN KODE
                                                KUPON</label>
                                            <div class="has-validation mb-3">
                                                <input type="text" class="form-control" id="product-price-input"
                                                    aria-label="Price" aria-describedby="product-price-addon" required="">
                                            </div>

                                        </div>
                                        <button
                                            class="btn btn-warning w-100 fs-4 fw-bold d-flex align-items-center justify-content-center">{{
                                                form.checkout.buyButtonText }}
                                            <i class="bx bx-right-arrow-circle ms-2"></i></button>
                                    </div>

                                </div>
                            </BCardBody>
                        </BCard>
                        <div class="container" v-if="form.checkout.testimonials.length > 0">
                            <div class="row">
                                <div class="col-12 col-lg-6" v-for="(item, index) in form.checkout.testimonials"
                                    :key="index">
                                    <div class="row align-items-center mb-3">
                                        <div class="col-auto">
                                            <img v-if="item.image" :src="item.image" alt="" style="width:100px">
                                            <div v-if="!item.image">
                                                <i class="bx bx-user " style="font-size:80px"></i>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <h5 class="mt-0 font-weight-bold">{{ item.name }}</h5>
                                            <p>{{ item.message }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </BCardBody>
                </BCard>
            </BCol>
        </BRow>
    </Layout>
</template>