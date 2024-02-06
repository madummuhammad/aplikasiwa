<style>
.cursor-pointer {
    cursor: pointer;
}

.form-check-lg {
    width: 20px;
    height: 20px;
}

.image-product {
    width: 200px !important;
    height: 200px !important;
    object-fit: cover;
    object-position: center;
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
import { Global } from '../../../global'
export default {
    setup(props) {
        console.log('props checkout', props.checkout)
        let checkoutData = ref(useForm(props.checkout))
        const global = Global();

        console.log()
        let productData = ref(props.product);
        const submit = () => {
            console.log('checkokutData', checkoutData.value)

            const postData = useForm({
                checkoutData: { ...checkoutData.value },
            });

            postData.transform((data) => ({
                ...data
            }))
                .post(route('product.checkout.update', props.checkout.id), {
                    onSuccess: () => {
                    }
                });
        }



        // Product Image
        let dropzoneFile = ref("");
        const drop = (e) => {
            dropzoneFile.value = e.dataTransfer.files[0];
            product.value.image.push(dropzoneFile.value);
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
                product.value.image.push(images);

            } catch (error) {
                console.error('error', error);
            }

        };


        const deleteProductImage = (index) => {
            product.value.image.splice(index, 1);
        };

        // Bump Image
        const bumpImageChange = async (event) => {
            try {
                const file = event.target.files[0];
                const formData = new FormData();
                formData.append('image', file);
                const response = await axios.post('/api/products/upload', formData);
                const data = response.data;

                product.value.product_bump.image = data.data;

            } catch (error) {
                console.error('error', error);
            }
        }

        const testimonialStatus = ref(true);

        const addTestimonials = () => {
            checkoutData.value.testimonials.push(
                {
                    image: null,
                    name: '',
                    message: ''
                }
            )

            console.log(checkoutData.value.testimonials)
        }

        const deleteTestimonial = (index) => {
            checkoutData.value.testimonials.splice(index, 1);
        }

        const handleChangeTestimonial = async (event, index) => {
            try {
                const file = event.target.files[0];
                const formData = new FormData();
                formData.append('image', file);
                const response = await axios.post('/api/products/upload', formData);
                const data = response.data;
                checkoutData.value.testimonials[index].image = data.data;
                // checkoutData.value.header.image = data.data;

            } catch (error) {
                console.error('error', error);
            }
        }


        const addOlshop = () => {
            if (!checkoutData.value.olshop) {
                checkoutData.value.olshop = []
            }
            checkoutData.value.olshop.push(
                {
                    image: null,
                    name: '',
                    message: ''
                }
            )

            console.log(checkoutData.value.olshop)
        }

        const deleteOlshop = (index) => {
            checkoutData.value.olshop.splice(index, 1);
        }

        const handleChangeOlshop = async (event, index) => {
            try {
                const file = event.target.files[0];
                const formData = new FormData();
                formData.append('image', file);
                const response = await axios.post('/api/products/upload', formData);
                const data = response.data;
                checkoutData.value.olshop[index].image = data.data;
                // checkoutData.value.header.image = data.data;

            } catch (error) {
                console.error('error', error);
            }
        }

        // Pembayaran

        let paymentType = ref({
            bank_transfer: true,
            bank_transfer_description: '',
            cod_status: false,
            cod_description: '',
            epayment_status: false
        })

        // Halaman

        const page = ref('checkout');

        const pageMethod = (value) => {

            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });

            page.value = value;
        };



        // Checkout Page

        const template = ref('right-sidebar');

        const templateChange = (value) => {
            checkoutData.value.template = value;
            console.log(checkoutData)
        }

        const fieldCustomShow = ref(false)

        const headerImageChange = async (event) => {
            try {
                const file = event.target.files[0];
                const formData = new FormData();
                formData.append('image', file);
                const response = await axios.post('/api/products/upload', formData);
                const data = response.data;

                checkoutData.value.header.image = data.data;

            } catch (error) {
                console.error('error', error);
            }
        }


        const deleteHeaderImage = () => {
            checkoutData.value.header.image = null;
        }

        const checkField = (item) => {
            item.show = !item.show;

            console.log(checkoutData.value)
            checkoutData.value.fields.forEach((items, index) => {

                if (item.name == 'province') {
                    if (item.show == false) {
                        if (items.name == 'city') {
                            items.show = false
                        }
                        if (items.name == 'subdistrict') {
                            items.show = false
                        }
                    }
                }

                if (item.name == 'city') {
                    if (item.show == false) {
                        if (items.name == 'subdistrict') {
                            items.show = false
                        }
                    }
                }


                if (item.name == 'city') {
                    if (items.name == 'province') {
                        items.show = true;
                    }
                }

                if (item.name == 'subdistrict') {
                    if (items.name == 'province') {
                        items.show = true;
                    }
                    if (items.name == 'city') {
                        items.show = true;
                    }
                }
            })

            console.log(item)
        }

        let newCustomField = ref({
            name: 'Custom Field',
            field: 'Input',
            inputType: 'Text',
            required: true,
            label: 'Custom Field',
            placeholder: 'Custom Field',
            can_be_edited: true,
            can_be_deleted: true,
            show: true,
            options: [
                { text: 'Option 1' }
            ]
        })

        const addCustomField = () => {
            checkoutData.value.fields.push({ ...newCustomField.value })
            fieldCustomShow.value = false;
            console.log(checkoutData)

            newCustomField.value = {
                name: 'Custom Field',
                field: 'Input',
                inputType: 'Text',
                required: true,
                label: 'Custom Field',
                placeholder: 'Custom Field',
                can_be_edited: true,
                can_be_deleted: true,
                show: true,
                options: [
                    { text: 'Option 1' }
                ]
            }

        }

        var fieldCustomEdit = ref(false)
        const fieldCustomEditIndex = ref(0)
        let fieldCustomEditItem = ref({})

        const openModalFieldCustomEdit = (index, item) => {
            console.log(item)
            fieldCustomEdit.value = true;
            fieldCustomEditItem.value = { ...item };
            fieldCustomEditIndex.value = index;
            console.log('fieldCu', fieldCustomEditItem)
        }

        const closeModalFieldCustomEdit = () => {
            fieldCustomEdit = false;
        }

        const editFieldCustom = () => {
            fieldCustomEdit.value = false;
            checkoutData.value.fields[fieldCustomEditIndex.value] = { ...fieldCustomEditItem.value };
            console.log(checkoutData)
        }

        const removeCustomField = (index) => {
            checkoutData.value.fields.splice(index, 1);
        };

        const addOption = () => {
            var index = newCustomField.value.options.length + 1;
            let newOption = { text: 'Option ' + index }
            newCustomField.value.options.push({ ...newOption });

            console.log(newCustomField)
        }

        const removeOption = (index) => {
            newCustomField.value.options.splice(index, 1);
        };



        const removePoint = (index) => {
            checkoutData.value.points.splice(index, 1);
        };

        const addPoint = () => {
            checkoutData.value.points.push({
                poin: ''
            });
        };

        // Success Page
        const accountBankShow = ref(false);

        let listBank = [
            {
                label: 'BCA',
                value: 'BCA'
            },
            {
                label: 'BCA Syariah',
                value: 'BCA Syariah'
            },
            {
                label: 'BNI',
                value: 'BNI'
            },
        ]

        let tempBankAccount = ref(
            {
                bank: '',
                atas_nama: '',
                rekening: ''
            }
        )

        let bankAccountError = ref([])
        const addBankAccount = async () => {
            try {
                checkoutData.value.success_page.accountBank.push({ ...tempBankAccount.value });

                const response = await axios.post('/api/bank', tempBankAccount.value);
                const data = response.data;

                if (data.status === 'success') {
                    console.log('Success:', data.message);
                }

                tempBankAccount.value = {
                    bank: '',
                    atas_nama: '',
                    rekening: ''
                };

                getBank();
                accountBankShow.value = false;
            } catch (error) {
                console.error('error', error);
                bankAccountError.value = error.response.data.errors;
            }
        };

        const tempSelectedBank = ref();
        const selectBank = () => {
            if (!isBankAlreadySelected(tempSelectedBank.value)) {
                checkoutData.value.success_page.accountBank.push({ ...tempSelectedBank.value });
                console.log(checkoutData.value.success_page.accountBank);
            }
        };

        const isBankAlreadySelected = (selectedBank) => {
            return checkoutData.value.success_page.accountBank.some((bank) => {
                return bank.bank === selectedBank.bank && bank.atas_nama === selectedBank.atas_nama && bank.rekening === selectedBank.rekening;
            });
        };

        const deleteAccountBank = (index) => {
            checkoutData.value.success_page.accountBank.splice(index, 1);
        }

        const listBankAccount = ref([]);
        const getBank = async () => {
            try {
                const response = await axios.get('/api/bank');
                const data = response.data;

                if (data.status === 'success') {
                    console.log('Bank:', data);
                    listBankAccount.value = data.data;
                }

            } catch (error) {
                console.error('error', error);
            }
        }


        onBeforeMount(() => {
            console.log('product', props.product)

            if (props.product.image.length > 0) {
                tempProductImage.value = props.product.image[0].url;
            }
            getBank();
        });

        const changeBodyText = (type) => {
            checkoutData.value.success_page.bodyText.payment_type = type;
        }

        const embedYouTubeVideo = () => {
            const videoIdMatch = checkoutData.value.success_page.video.match(/(?:https?:\/\/)?(?:www\.)?(?:youtube\.com\/(?:[^\/\n\s]+\/\S+\/|(?:v|e(?:mbed)?)\/|\S*?[?&]v=)|youtu\.be\/)([a-zA-Z0-9_-]{11})/);
            if (videoIdMatch) {
                const videoId = videoIdMatch[1];
                checkoutData.value.success_page.video = `https://www.youtube.com/embed/${videoId}`;
            } else {
                checkoutData.value.success_page.video = 'Invalid YouTube link';
            }
        }

        const evalExpression = (expression) => {
            const matches = expression.match(/{{(.*?)}}/g);

            if (matches) {
                let evaluatedString = expression;

                matches.forEach(match => {
                    const expressionWithoutBrackets = match.replace(/{{(.*?)}}/, '$1').trim();

                    try {
                        const evaluatedValue = eval(expressionWithoutBrackets);
                        evaluatedString = evaluatedString.replace(match, evaluatedValue);
                    } catch (error) {
                        console.error('Error evaluating expression:', error);
                    }
                });

                return evaluatedString;
            }

            return expression;
        }


        return {
            dropzoneFile,
            drop,
            global,
            embedYouTubeVideo,
            selectedFile,
            bumpImageChange,
            tempProductImage,
            v$: useVuelidate(),
            deleteProductImage,
            paymentType,

            // Testimonial
            testimonialStatus,
            addTestimonials,
            handleChangeTestimonial,
            deleteTestimonial,

            // Olshop
            addOlshop,
            handleChangeOlshop,
            deleteOlshop,

            // Product Data
            productData,

            page,
            pageMethod,
            template,
            templateChange,
            checkoutData,
            checkField,
            addCustomField,
            removeCustomField,
            addOption,
            removeOption,
            removePoint,
            fieldCustomShow,
            newCustomField,
            fieldCustomEdit,
            openModalFieldCustomEdit,
            closeModalFieldCustomEdit,
            fieldCustomEditItem,
            editFieldCustom,
            fieldCustomEditIndex,
            addPoint,
            editor: ClassicEditor,

            changeBodyText,
            listBank,
            accountBankShow,
            tempBankAccount,
            listBankAccount,
            tempSelectedBank,
            selectBank,
            deleteAccountBank,

            evalExpression,
            addBankAccount,
            bankAccountError,

            // Header
            headerImageChange,
            deleteHeaderImage,
            submit
        };
    },
    components: {
        ckeditor: CKEditor.component,
        DropZone,
        Layout,
        PageHeader,
        Multiselect,
        flatPickr,
        Head,
        Link
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
    // methods: {
    //     deleteRecord(ele) {
    //         ele.target.parentElement.parentElement.parentElement.remove();
    //     },
    // },
};
</script>

<template>
    <Layout>

        <Head title="Edit Checkout Page" />
        <!-- <PageHeader title="Create Product" pageTitle="Product" /> -->

        <BRow>
            <div class="col-12 mb-5 mt-5">
                <Link :href="'/product/' + product.id">
                <h2 class="d-flex align-items-center">
                    <i class="bx bx-chevron-left"></i>
                    {{ product.name }}
                </h2>
                </Link>
            </div>
            <div class="col-12">
                <div class="btn-group w-100 mb-3">
                    <button @click="pageMethod('checkout')" class="btn"
                        :class="{ 'btn-primary': page == 'checkout', 'btn-outline-secondary': page !== 'checkout' }">
                        <i
                            class="ri-truck-line fs-16 avatar-xs d-inline-flex align-items-center justify-content-center bg-primary-subtle text-primary rounded-circle align-middle me-2"></i>
                        Halaman Checkout
                    </button>
                    <button @click="pageMethod('success')" class="btn"
                        :class="{ 'btn-primary': page == 'success', 'btn-outline-secondary': page !== 'success' }">
                        <i
                            class="ri-bank-card-line fs-16 avatar-xs d-inline-flex align-items-center justify-content-center bg-primary-subtle text-primary rounded-circle align-middle me-2"></i>
                        Halaman Sukses
                    </button>
                </div>
            </div>

            <!-- Page Checkout -->
            <BCol lg="6" v-if="page == 'checkout'">
                <BCard no-body>
                    <BCardHeader>
                        <h5 class="card-title mb-0">Nama Halaman Checkout</h5>
                    </BCardHeader>
                    <BCardBody>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="mb-3">
                                    <input type="text" v-model="checkoutData.name" class="form-control"
                                        id="product-title-input" />
                                </div>
                            </div>
                        </div>
                    </BCardBody>
                </BCard>

                <!-- Pengaturan Template Posisi-->
                <BCard no-body>
                    <BCardHeader>
                        <h5 class="card-title mb-0">Template</h5>
                    </BCardHeader>
                    <BCardBody>
                        <div class="row">
                            <div class="col">
                                <p class="text-center">Right Sidebar</p>
                                <div class="border border-2 p-2"
                                    :class="{ 'border-secondary': checkoutData.template == 'right-sidebar', 'border-light': checkoutData.template !== 'right-sidebar' }"
                                    @click="templateChange('right-sidebar')" style="height:150px;cursor:pointer">
                                    <div class="container-fluid px-1">
                                        <div class="row">
                                            <div class="col-8 bg-light">
                                                <div style="height:130px"></div>
                                            </div>
                                            <div class="col-3 ms-2 bg-light">
                                                <div style="height:130px"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col">
                                <p class="text-center">Left Sidebar</p>
                                <div class="border border-2 p-2"
                                    :class="{ 'border-secondary': checkoutData.template == 'left-sidebar', 'border-light': checkoutData.template !== 'left-sidebar' }"
                                    @click="templateChange('left-sidebar')" style="height:150px;cursor:pointer">
                                    <div class="container-fluid px-1">
                                        <div class="row">
                                            <div class="col-3 bg-light">
                                                <div style="height:130px"></div>
                                            </div>
                                            <div class="col-8 ms-2 bg-light">
                                                <div style="height:130px"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col">
                                <p class="text-center">No Sidebar</p>
                                <div class="border border-2 p-2"
                                    :class="{ 'border-secondary': checkoutData.template == 'no-sidebar', 'border-light': checkoutData.template !== 'no-sidebar' }"
                                    @click="templateChange('no-sidebar')" style="height:150px;cursor:pointer">
                                    <div class="container-fluid px-1">
                                        <div class="row d-flex justify-content-center">
                                            <div class="col-8 bg-light">
                                                <div style="height:130px"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12 d-flex justify-content-end">
                                <button class="btn border border-dark mt-3 d-flex align-items-center fs-5"
                                    @click="sectionTitleShow = !sectionTitleShow">Judul Section <i
                                        class="bx bx-pencil fs-5"></i>
                                </button>
                                <BModal v-model="sectionTitleShow" hide-footer title="Section Titles" size="lg"
                                    class="v-modal-custom">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label class="form-label" for="product-title-input">Form:</label>
                                                <input type="text" class="form-control"
                                                    v-model="checkoutData.sectionTitle.form" />
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label class="form-label" for="product-title-input">Variasi:</label>
                                                <input type="text" class="form-control"
                                                    v-model="checkoutData.sectionTitle.variation" />
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label class="form-label" for="product-title-input">Metode
                                                    Pembayaran:</label>
                                                <input type="text" class="form-control"
                                                    v-model="checkoutData.sectionTitle.payment_method" />
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label class="form-label" for="product-title-input">Poin Poin:</label>
                                                <input type="text" class="form-control"
                                                    v-model="checkoutData.sectionTitle.points" />
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label class="form-label" for="product-title-input">Pengiriman:</label>
                                                <input type="text" class="form-control"
                                                    v-model="checkoutData.sectionTitle.delivery" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer v-modal-footer">
                                        <BButton type="button" variant="primary" @click="sectionTitleShow = false">OK
                                        </BButton>
                                    </div>
                                </BModal>
                            </div>
                        </div>
                    </BCardBody>
                </BCard>
                <BCard no-body>
                    <BCardHeader>
                        <h5 class="card-title mb-0">Header</h5>
                    </BCardHeader>
                    <BCardBody>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="mb-3">
                                    <h5>Unggah Gambar Di Header</h5>
                                    <p>(Ini biasanya logo produk atau perusahaan Anda)</p>
                                </div>
                                <div class="d-flex align-items-center mb-3">
                                    <div>
                                        <label class="btn btn-secondary mb-0" for="header-image"><i
                                                class="bx bx-image-alt"></i>
                                            Pilih Gambar</label>
                                        <input type="file" @change="headerImageChange" hidden id="header-image">
                                    </div>
                                    <div class="ms-2">
                                        <button @click="deleteHeaderImage" class="btn btn-light"><i class="bx bx-trash"></i>
                                            Hapus</button>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <div class="form-check form-switch form-switch-custom form-switch-success">
                                        <input class="form-check-input" type="checkbox" role="switch" id="headerTitle"
                                            v-model="checkoutData.header.titleStatus">
                                        <label class="form-check-label" for="headerTitle">Apakah Anda Ingin menampilkan
                                            judul
                                            di atas keranjang Anda?</label>
                                    </div>
                                </div>
                                <div class="mb-3" v-if="checkoutData.header.titleStatus == true">
                                    <input class="form-control" type="text" placeholder="Title"
                                        v-model="checkoutData.header.title">
                                </div>
                                <div class="mb-3">
                                    <div class="form-check form-switch form-switch-custom form-switch-success">
                                        <input class="form-check-input" type="checkbox" role="switch" id="headerTagline"
                                            v-model="checkoutData.header.taglineStatus">
                                        <label class="form-check-label" for="headerTagline">Apakah Anda Ingin menampilkan
                                            judul
                                            di atas keranjang Anda?</label>
                                    </div>
                                </div>
                                <div class="mb-3" v-if="checkoutData.header.taglineStatus == true">
                                    <input class="form-control" type="text" placeholder="Tagline"
                                        v-model="checkoutData.header.tagline">
                                </div>
                            </div>
                        </div>
                    </BCardBody>
                </BCard>
                <BCard no-body>
                    <BCardHeader>
                        <h5 class="card-title mb-0">Gambar</h5>
                    </BCardHeader>
                    <BCardBody>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="mb-3">
                                    <div class="form-check form-switch form-switch-custom form-switch-success">
                                        <input class="form-check-input" type="checkbox" role="switch" id="checkImageProduct"
                                            v-model="checkoutData.productImageStatus">
                                        <label class="form-check-label" for="checkImageProduct">Tampilkan gambar produk di
                                            halaman checkout?</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </BCardBody>
                </BCard>
                <!-- Pengaturan Bidang yang Diminta -->
                <BCard no-body>
                    <BCardHeader>
                        <h5 class="card-title mb-0">Bidang yang Diminta</h5>
                    </BCardHeader>
                    <BCardBody>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="mb-3 d-flex align-items-center" v-for="(item, index) in  checkoutData.fields ">
                                    <div v-if="item.name != 'wa'"
                                        class="form-check me-3 form-check-outline form-check-primary ">
                                        <input class="form-check-input" type="checkbox" :id="'formCheck' + index"
                                            @click="checkField(item)" :checked="item.show ? true : false">
                                    </div>
                                    <div v-else class="form-check me-3 form-check-outline form-check-primary ">

                                    </div>
                                    <div class="input-group has-validation ">
                                        <input type="text" class="form-control rounded-0 border-end-0"
                                            :id="'product-price-input' + index" :placeholder="item.label" aria-label="Price"
                                            aria-describedby="product-price-addon" required="" readonly>

                                        <button v-if="item.can_be_deleted == true" @click="removeCustomField(index)"
                                            class="input-group-text rounded-0 bg-white" id="product-price-addon"><i
                                                class="bx bx-trash"></i>
                                        </button>
                                        <button class="input-group-text rounded-0 bg-white"
                                            @click="openModalFieldCustomEdit(index, item)" id="product-price-addon"><i
                                                class="bx bx-cog"></i>
                                        </button>
                                    </div>

                                </div>
                                <div class="d-flex justify-content-end">
                                    <button class="btn btn-secondary" @click="fieldCustomShow = !fieldCustomShow">Tambah
                                        Field Custom</button>
                                    <BModal v-model="fieldCustomShow" hide-footer title="Custom Field" size="lg"
                                        class="v-modal-custom">
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <div class="mb-3">
                                                    <label class="form-label" for="product-title-input">Field:</label>
                                                    <select v-model="newCustomField.field" class="form-select">
                                                        <option value="Input">Input</option>
                                                        <option value="Textarea">Textarea</option>
                                                        <option value="Select">Select</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-lg-6" v-if="newCustomField.field == 'Input'">
                                                <div class="mb-3">
                                                    <label class="form-label" for="product-title-input">Input Type:</label>
                                                    <select v-model="newCustomField.inputType" class="form-select">
                                                        <option value="Text">Text</option>
                                                        <option value="Date">Date</option>
                                                        <option value="Number">Number</option>
                                                        <option value="Email">Email</option>
                                                        <option value="Url">Url</option>
                                                        <option value="File">File</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-lg-6">
                                                <div class="mb-3">
                                                    <label class="form-label" for="product-title-input">Label</label>
                                                    <input type="text" v-model="newCustomField.label"
                                                        class="form-control" />
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="mb-3">
                                                    <label class="form-label" for="product-title-input">Placeholder</label>
                                                    <input type="text" v-model="newCustomField.placeholder"
                                                        class="form-control" />
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="form-check form-switch form-switch-custom form-switch-success">
                                                    <input v-model="newCustomField.required" class="form-check-input"
                                                        type="checkbox" role="switch" id="required">
                                                    <label class="form-check-label" for="required">Required</label>
                                                </div>
                                            </div>
                                            <div class="col-lg-12 mt-3" v-if="newCustomField.field == 'Select'">
                                                <h5>Options</h5>
                                                <div class="input-group has-validation mb-3"
                                                    v-for="(item, index) in newCustomField.options" :key="index">
                                                    <input type="text" v-model="item.text"
                                                        class="form-control rounded-0 border-end-0" aria-label="Price"
                                                        aria-describedby="product-price-addon">

                                                    <button @click="removeOption(index)" class="input-group-text"
                                                        id="product-price-addon"><i class="bx bx-trash"></i>
                                                    </button>
                                                </div>
                                                <div class="d-flex justify-content-end">
                                                    <button class="btn btn-secondary" @click="addOption">Add Option</button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer v-modal-footer">
                                            <BButton type="button" variant="secondary" @click="addCustomField">Simpan
                                            </BButton>
                                        </div>
                                    </BModal>
                                </div>
                                <div class="alert alert-warning mt-2">Untuk menggunakan fitur perhitungan ongkir otomatis,
                                    harap aktifkan field Kecamatan</div>
                                <BModal v-model="fieldCustomEdit" hide-footer title="Custom Field" size="lg"
                                    class="v-modal-custom">
                                    <div class="row">
                                        <div class="col-lg-6" v-if="fieldCustomEditItem.name == 'Custom Field'">
                                            <div class="mb-3">
                                                <label class="form-label" for="product-title-input">Field:</label>
                                                <select v-model="fieldCustomEditItem.field" class="form-select">
                                                    <option value="Input">Input</option>
                                                    <option value="Textarea">Textarea</option>
                                                    <option value="Select">Select</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-6"
                                            v-if="fieldCustomEditItem.field == 'Input' && fieldCustomEditItem.name == 'Custom Field'">
                                            <div class="mb-3">
                                                <label class="form-label" for="product-title-input">Input Type:</label>
                                                <select v-model="fieldCustomEditItem.inputType" class="form-select">
                                                    <option value="Text">Text</option>
                                                    <option value="Date">Date</option>
                                                    <option value="Number">Number</option>
                                                    <option value="Email">Email</option>
                                                    <option value="Url">Url</option>
                                                    <option value="File">File</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label class="form-label" for="product-title-input">Label</label>
                                                <input type="text" v-model="fieldCustomEditItem.label"
                                                    class="form-control" />
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label class="form-label" for="product-title-input">Placeholder</label>
                                                <input type="text" v-model="fieldCustomEditItem.placeholder"
                                                    class="form-control" />
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-check form-switch form-switch-custom form-switch-success">
                                                <input v-model="fieldCustomEditItem.required" class="form-check-input"
                                                    type="checkbox" role="switch" id="required">
                                                <label class="form-check-label" for="required">Required</label>
                                            </div>
                                        </div>
                                        <div class="col-lg-12 mt-3"
                                            v-if="fieldCustomEditItem.field == 'Select' && fieldCustomEditItem.name == 'Custom Field'">
                                            <h5>Options</h5>
                                            <div class="input-group has-validation mb-3"
                                                v-for="(item, index) in fieldCustomEditItem.options" :key="index">
                                                <input type="text" v-model="item.text"
                                                    class="form-control rounded-0 border-end-0" aria-label="Price"
                                                    aria-describedby="product-price-addon">

                                                <button @click="removeOption(index)" class="input-group-text"
                                                    id="product-price-addon"><i class="bx bx-trash"></i>
                                                </button>
                                            </div>
                                            <div class="d-flex justify-content-end">
                                                <button class="btn btn-secondary" @click="addOption">Add Option</button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer v-modal-footer">
                                        <BButton type="button" variant="secondary" @click="editFieldCustom">Simpan
                                        </BButton>
                                    </div>
                                </BModal>
                            </div>
                        </div>
                    </BCardBody>
                </BCard>
                <BCard no-body>
                    <BCardHeader>
                        <h5 class="card-title mb-0">Dropship</h5>
                    </BCardHeader>
                    <BCardBody>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="mb-3">
                                    <div class="form-check form-switch form-switch-custom form-switch-success">
                                        <input class="form-check-input" type="checkbox" role="switch" id="dropship"
                                            v-model="checkoutData.dropship_status">
                                        <label class="form-check-label" for="dropship">Apakah Anda ingin
                                            mengaktifkan dropship di keranjang Anda?</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </BCardBody>
                </BCard>
                <BCard no-body>
                    <BCardHeader>
                        <h5 class="card-title mb-0">Tombol Beli</h5>
                    </BCardHeader>
                    <BCardBody>
                        <p>Sesuaikan template tombol pembelian Anda di bawah ini</p>
                        <div class="row">
                            <div class="col-lg-12 mb-3">
                                <button
                                    :style="'background-color:' + checkoutData.buyButtonBackground + ';color:' + checkoutData.buyButtonTextColor"
                                    class="btn w-100 fs-4 fw-bold d-flex align-items-center justify-content-center">{{
                                        checkoutData.buyButtonText }}
                                    <i class="bx bx-right-arrow-circle ms-2"></i></button>
                            </div>
                            <div class="col-lg-12 mb-3">
                                <p class="fw-bold m-0 p-0">Masukan teks untuk tombol pembelian Anda.</p>
                                <p class="m-0 p-0">(Saran: 'Beli Sekarang!' atau 'Selesaikan Pesanan')</p>
                            </div>
                            <div class="col-lg-12">
                                <input class="form-control" type="text" v-model="checkoutData.buyButtonText">
                            </div>
                            <div class="col-lg-12 mt-3">
                                <div class="form-group">
                                    <label class="form-label">Warna Background</label><br>
                                    <input type="color" v-model="checkoutData.buyButtonBackground">
                                </div>
                            </div>
                            <div class="col-lg-12 mt-3">
                                <div class="form-group">
                                    <label class="form-label">Warna Teks</label><br>
                                    <input type="color" v-model="checkoutData.buyButtonTextColor">
                                </div>
                            </div>
                        </div>
                    </BCardBody>
                </BCard>
                <BCard no-body>
                    <BCardHeader>
                        <h5 class="card-title mb-0">Konten</h5>
                    </BCardHeader>
                    <BCardBody>
                        <div class="row">
                            <div class="col-lg-12">
                                <p>Masukan deskripsi produk untuk menjelaskan lebih lanjtu tentang produk Anda</p>
                                <div class="mb-3">
                                    <textarea name="" v-model="checkoutData.productDescription" class="form-control" id=""
                                        cols="30" rows="5"></textarea>
                                </div>
                            </div>
                        </div>
                    </BCardBody>
                </BCard>
                <BCard no-body>
                    <BCardHeader>
                        <h5 class="card-title mb-0">Kolom Kupon</h5>
                    </BCardHeader>
                    <BCardBody>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="mb-3">
                                    <div class="form-check form-switch form-switch-custom form-switch-success">
                                        <input class="form-check-input" type="checkbox" role="switch" id="checkImageProduct"
                                            v-model="checkoutData.kuponStatus">
                                        <label class="form-check-label" for="checkImageProduct">Apakan Anda ingin menunjukan
                                            bidang kupon di keranjang Anda?</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </BCardBody>
                </BCard>
                <BCard no-body>
                    <BCardHeader>
                        <h5 class="card-title mb-0">Poin Poin</h5>
                    </BCardHeader>
                    <BCardBody>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="mb-3">
                                    <p>Tambahkan poin-poin untuk menampilkan manfaat dan fitur produk anda.</p>
                                </div>
                                <div class="mb-3" v-for="( item, index ) in  checkoutData.points " :key="index">
                                    <div class="input-group">
                                        <input type="text" v-model="item.poin" class="form-control">
                                        <span class="input-group-text" style="cursor:pointer" @click="removePoint(index)">
                                            <i class="bx bx-trash"></i> Hapus
                                        </span>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-end">
                                    <button class="btn btn-secondary" @click="addPoint">
                                        Tambahkan poin-poin
                                    </button>
                                </div>
                            </div>
                        </div>
                    </BCardBody>
                </BCard>
                <BCard no-body>
                    <BCardHeader>
                        <h5 class="card-title mb-0">Ringkasan Pesanan</h5>
                    </BCardHeader>
                    <BCardBody>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="mb-3">
                                    <div class="form-check form-switch form-switch-custom form-switch-success">
                                        <input class="form-check-input" type="checkbox" role="switch" id="summaryStatus"
                                            v-model="checkoutData.summaryStatus">
                                        <label class="form-check-label" for="summaryStatus">Apakah Anda ingin
                                            menampilkan ringkasan pesanan di keranjang Anda?</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </BCardBody>
                </BCard>
                <BCard no-body>
                    <BCardHeader>
                        <h5 class="card-title mb-0">Testimonials</h5>
                    </BCardHeader>
                    <BCardBody>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="mb-3">
                                    <p>Tambahkan testimoni untuk meningkatkan bukti sosial untuk menarik lebih banyak
                                        pelanggan.</p>
                                </div>
                                <div class="mb-3" v-for="( item, index ) in    checkoutData.testimonials   " :key="index">
                                    <div class="row">
                                        <div class="col-4 d-flex align-items-center justify-content-center">
                                            <div v-if="item.image">
                                                <label :for="'testimonials' + index" class="img-thumbnail cursor-pointer"
                                                    style="width: 150px;">
                                                    <img class="img-fluid w-100" :src="item.image" alt="">
                                                </label>
                                            </div>
                                            <input type="file" hidden :id="'testimonials' + index"
                                                @change="handleChangeTestimonial($event, index)">
                                            <div v-if="!item.image">
                                                <label :for="'testimonials' + index"
                                                    class="img-thumbnail d-flex align-items-center justify-content-center text-dark cursor-pointer"
                                                    style="height:150px;width: 150px;">
                                                    <i class="bx bx-user " style="font-size:80px"></i>
                                                </label>

                                            </div>
                                        </div>
                                        <div class="col-8">
                                            <input type="text" class="form-control mb-3" v-model="item.name">
                                            <textarea name="" id="" cols="30" rows="5" class="form-control"
                                                v-model="item.message"></textarea>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-end mt-2">
                                        <button class="btn btn-danger" @click="deleteTestimonial(index)">Hapus</button>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-end">
                                    <button class="btn btn-secondary" @click="addTestimonials">
                                        Tambahkan Testimonials
                                    </button>
                                </div>
                            </div>
                        </div>
                    </BCardBody>
                </BCard>
                <BCard no-body>
                    <BCardHeader>
                        <h5 class="card-title mb-0">Other Shop</h5>
                    </BCardHeader>
                    <BCardBody>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="mb-3">
                                    <p>Tambahkan olshop lain untuk ditampilkan pada halaman checkout produk.</p>
                                </div>
                                <div class="mb-3" v-for="( item, index ) in    checkoutData.olshop" :key="index">
                                    <div class="row">
                                        <div class="col-4 d-flex align-items-center justify-content-center">
                                            <div v-if="item.image">
                                                <label :for="'olshop' + index" class="img-thumbnail cursor-pointer"
                                                    style="width: 150px;">
                                                    <img class="img-fluid w-100" :src="item.image" alt="">
                                                </label>
                                            </div>
                                            <input type="file" hidden :id="'olshop' + index"
                                                @change="handleChangeOlshop($event, index)">
                                            <div v-if="!item.image">
                                                <label :for="'olshop' + index"
                                                    class="img-thumbnail d-flex align-items-center justify-content-center text-dark cursor-pointer"
                                                    style="height:150px;width: 150px;">
                                                    <i class="bx bx-image " style="font-size:80px"></i>
                                                </label>

                                            </div>
                                        </div>
                                        <div class="col-8">
                                            <input type="text" class="form-control mb-3" v-model="item.name">
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-end mt-2">
                                        <button class="btn btn-danger" @click="deleteOlshop(index)">Hapus</button>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-end">
                                    <button class="btn btn-secondary" @click="addOlshop">
                                        Tambahkan Toko Lain
                                    </button>
                                </div>
                            </div>
                        </div>
                    </BCardBody>
                </BCard>
                <div class="text-end mb-3">
                    <BButton type="button" variant="primary" class="w-sm" @click="pageMethod('success')">Selanjutnya
                    </BButton>
                </div>
            </BCol>
            <!-- End Page Checkout -->
            <BCol lg="6" v-if="page !== 'success'">

                <!-- Right Sidebar -->
                <BCard no-body class="bg-light" v-if="checkoutData.template == 'right-sidebar'"
                    style="position: sticky; top: 130px;overflow: auto; max-height: calc(100vh - 200px);">
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
                                                            <input class="form-check-input" name="product" type="radio">
                                                            <label class="form-check-label">
                                                                {{ item.variation.variation_name }}
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row" v-if="product.many_variation_status == true">
                                                    <template v-for="(item, index) in product.item_product">
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
                                                                    <input class="form-control" />
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
                                            <p class="fw-bold">{{ checkoutData.sectionTitle.form }}</p>
                                        </div>
                                        <div class="mb-3" v-for="(  item, index  ) in   checkoutData.fields  " :key="index">
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
                                            <p class="fw-bold">{{ checkoutData.sectionTitle.payment_method }}</p>
                                            <div class="p-4 border" v-if="product.bank_transfer.status == true">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="formCheck2">
                                                    <label class="form-check-label" for="formCheck2">
                                                        Bank Transfer
                                                    </label>
                                                </div>
                                                {{ product.bank_transfer.description }}
                                            </div>
                                            <div class="p-4 border" v-if="product.cod.status == true">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="formCheck2">
                                                    <label class="form-check-label" for="formCheck2">
                                                        COD
                                                    </label>
                                                </div>
                                                {{ product.cod.description }}
                                            </div>
                                            <div class="p-4 border" v-if="product.epayment.status == true">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="formCheck2">
                                                    <label class="form-check-label" for="formCheck2">
                                                        E - Payment
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="border border-dashed border-danger border-2 p-2 mb-3"
                                            v-if="product.bump_status">
                                            <div class="d-flex align-items-center mb-1 bg-warning py-1 px-1 mb-1">
                                                <img class="me-2" src="@assets/images/bump-arrow.gif" alt="">
                                                <input type="checkbox" name="" class="form-check-input me-2" id="bump">
                                                <label class="form-check-label fw-bold" for="bump">Centang untuk
                                                    memesan juga</label>
                                            </div>
                                            <div class="fw-bold text-danger mb-1">Nama Produk Bump Anda + Harga</div>
                                            <div class="mb-1">
                                                <img class="img-fluid w-100" :src="product.product_bump.image" alt="">
                                            </div>
                                            <div class="fs-12">Penjelasan Tentang Produk Bump Anda Disini</div>
                                        </div>
                                        <button
                                            :style="'background-color:' + checkoutData.buyButtonBackground + ';color:' + checkoutData.buyButtonTextColor"
                                            class="btn w-100 fs-4 fw-bold d-flex align-items-center justify-content-center">{{
                                                checkoutData.buyButtonText }}
                                            <i class="bx bx-right-arrow-circle ms-2"></i></button>
                                    </div>
                                    <div class="col-5 bg-light p-2">
                                        <div class="mb-3  d-flex justify-content-center"
                                            v-if="checkoutData.productImageStatus == true">
                                            <img class="img-fluid w-100 image-product" :src="tempProductImage" alt="">
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
                                                    <td class="text-end">
                                                        <p v-if="product.price_sale_status == true"
                                                            class="fs-12 text-decoration-line-through text-danger p-0 m-0">
                                                            {{ global.formatNumber(product.normal_price) }}
                                                        </p>
                                                        <p class="fs-12" v-if="product.price_sale_status == true">
                                                            {{ global.formatNumber(product.price_sale) }}
                                                        </p>
                                                        <p class="fs-12" v-if="product.price_sale_status == false">
                                                            {{ global.formatNumber(product.normal_price) }}
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
                                                    aria-label="Price" aria-describedby="product-price-addon" required="">
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
                        <div class="container p-3" v-if="checkoutData.olshop && checkoutData.olshop.length > 0">
                            <div class="row">
                                <div class="col-12 col-lg-6" v-for="(item, index) in checkoutData.olshop" :key="index">
                                    <div class="row align-items-center mb-3 p-2">
                                        <div class="col-auto">
                                            <div style="background:white">
                                                <img v-if="item.image" :src="item.image" alt="" style="width:100px">
                                                <div v-if="!item.image">
                                                    <i class="bx bx-image " style="font-size:80px"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <h5 class="mt-0 font-weight-bold">{{ item.name }}</h5>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </BCardBody>
                </BCard>

                <!-- Left Sidebar -->
                <BCard no-body class="bg-light" v-if="checkoutData.template == 'left-sidebar'"
                    style="position: sticky; top: 130px;overflow: auto; max-height: calc(100vh - 200px);">
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
                                                    <td class="text-end">
                                                        <p v-if="product.price_sale_status == true"
                                                            class="fs-12 text-decoration-line-through text-danger p-0 m-0">
                                                            {{ global.formatNumber(product.normal_price) }}
                                                        </p>
                                                        <p class="fs-12" v-if="product.price_sale_status == true">
                                                            {{ global.formatNumber(product.price_sale) }}
                                                        </p>
                                                        <p class="fs-12" v-if="product.price_sale_status == false">
                                                            {{ global.formatNumber(product.normal_price) }}
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
                                                    aria-label="Price" aria-describedby="product-price-addon" required="">
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
                                                            <input class="form-check-input" name="product" type="radio">
                                                            <label class="form-check-label">
                                                                {{ item.variation.variation_name }}
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row" v-if="product.many_variation_status == true">
                                                    <template v-for="(item, index) in product.item_product">
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
                                                                    <input class="form-control" />
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
                                            <p class="fw-bold">{{ checkoutData.sectionTitle.form }}</p>
                                        </div>
                                        <div class="mb-3" v-for="(  item, index  ) in   checkoutData.fields  " :key="index">
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
                                            <p class="fw-bold">{{ checkoutData.sectionTitle.payment_method }}</p>
                                            <div class="p-4 border" v-if="product.bank_transfer.status == true">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="formCheck2">
                                                    <label class="form-check-label" for="formCheck2">
                                                        Bank Transfer
                                                    </label>
                                                </div>
                                                {{ product.bank_transfer.description }}
                                            </div>
                                            <div class="p-4 border" v-if="product.cod.status == true">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="formCheck2">
                                                    <label class="form-check-label" for="formCheck2">
                                                        COD
                                                    </label>
                                                </div>
                                                {{ product.cod.description }}
                                            </div>
                                            <div class="p-4 border" v-if="product.epayment.status == true">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="formCheck2">
                                                    <label class="form-check-label" for="formCheck2">
                                                        E - Payment
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="border border-dashed border-danger border-2 p-2 mb-3"
                                            v-if="product.bump_status == true">
                                            <div class="d-flex align-items-center mb-1 bg-warning py-1 px-1 mb-1">
                                                <img class="me-2" src="@assets/images/bump-arrow.gif" alt="">
                                                <input type="checkbox" name="" class="form-check-input me-2" id="bump">
                                                <label class="form-check-label fw-bold" for="bump">Centang untuk
                                                    memesan juga</label>
                                            </div>
                                            <div class="fw-bold text-danger mb-1">Nama Produk Bump Anda + Harga</div>
                                            <div class="mb-1" v-if="product.product_bump.image">
                                                <img class="img-fluid w-100" :src="product.product_bump.image" alt="">
                                            </div>
                                            <div class="fs-12">Penjelasan Tentang Produk Bump Anda Disini</div>
                                        </div>
                                        <button
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
                        <div class="container p-3" v-if="checkoutData.olshop && checkoutData.olshop.length > 0">
                            <div class="row">
                                <div class="col-12 col-lg-6" v-for="(item, index) in checkoutData.olshop" :key="index">
                                    <div class="row align-items-center mb-3 p-2">
                                        <div class="col-auto">
                                            <div style="background:white">
                                                <img v-if="item.image" :src="item.image" alt="" style="width:100px">
                                                <div v-if="!item.image">
                                                    <i class="bx bx-image " style="font-size:80px"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <h5 class="mt-0 font-weight-bold">{{ item.name }}</h5>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </BCardBody>
                </BCard>

                <!-- No Sidebar -->
                <BCard no-body class="bg-light" v-if="checkoutData.template == 'no-sidebar'"
                    style="position: sticky; top: 130px;overflow: auto; max-height: calc(100vh - 200px);">
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
                                                            <input class="form-check-input" name="product" type="radio">
                                                            <label class="form-check-label">
                                                                {{ item.variation.variation_name }}
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row" v-if="product.many_variation_status == true">
                                                    <template v-for="(item, index) in product.item_product">
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
                                                                    <input class="form-control" />
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
                                            <p class="fw-bold">{{ checkoutData.sectionTitle.form }}</p>
                                        </div>
                                        <div class="mb-3" v-for="(  item, index  ) in   checkoutData.fields  " :key="index">
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
                                            <p class="fw-bold">{{ checkoutData.sectionTitle.payment_method }}</p>
                                            <div class="p-4 border" v-if="product.bank_transfer.status == true">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="formCheck2">
                                                    <label class="form-check-label" for="formCheck2">
                                                        Bank Transfer
                                                    </label>
                                                </div>
                                                {{ product.bank_transfer.description }}
                                            </div>
                                            <div class="p-4 border" v-if="product.cod.status == true">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="formCheck2">
                                                    <label class="form-check-label" for="formCheck2">
                                                        COD
                                                    </label>
                                                </div>
                                                {{ product.cod.description }}
                                            </div>
                                            <div class="p-4 border" v-if="product.epayment.status == true">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="formCheck2">
                                                    <label class="form-check-label" for="formCheck2">
                                                        E - Payment
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="border border-dashed border-danger border-2 p-2 mb-3"
                                            v-if="product.bump_status == true">
                                            <div class="d-flex align-items-center mb-1 bg-warning py-1 px-1 mb-1">
                                                <img class="me-2" src="@assets/images/bump-arrow.gif" alt="">
                                                <input type="checkbox" name="" class="form-check-input me-2" id="bump">
                                                <label class="form-check-label fw-bold" for="bump">Centang untuk
                                                    memesan juga</label>
                                            </div>
                                            <div class="fw-bold text-danger mb-1">Nama Produk Bump Anda + Harga</div>
                                            <div class="mb-1">
                                                <img class="img-fluid w-100" :src="product.product_bump.image" alt="">
                                            </div>
                                            <div class="fs-12">Penjelasan Tentang Produk Bump Anda Disini</div>
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
                                                    <td class="text-end">
                                                        <p v-if="product.price_sale_status == true"
                                                            class="fs-12 text-decoration-line-through text-danger p-0 m-0">
                                                            {{ global.formatNumber(product.normal_price) }}
                                                        </p>
                                                        <p class="fs-12" v-if="product.price_sale_status == true">
                                                            {{ global.formatNumber(product.price_sale) }}
                                                        </p>
                                                        <p class="fs-12" v-if="product.price_sale_status == false">
                                                            {{ global.formatNumber(product.normal_price) }}
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
                                                    aria-label="Price" aria-describedby="product-price-addon" required="">
                                            </div>

                                        </div>
                                        <button
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
                        <div class="container p-3" v-if="checkoutData.olshop && checkoutData.olshop.length > 0">
                            <div class="row">
                                <div class="col-12 col-lg-6" v-for="(item, index) in checkoutData.olshop" :key="index">
                                    <div class="row align-items-center mb-3 p-2">
                                        <div class="col-auto">
                                            <div style="background:white">
                                                <img v-if="item.image" :src="item.image" alt="" style="width:100px">
                                                <div v-if="!item.image">
                                                    <i class="bx bx-image " style="font-size:80px"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <h5 class="mt-0 font-weight-bold">{{ item.name }}</h5>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </BCardBody>
                </BCard>
            </BCol>

            <!-- Page Success -->
            <BCol lg="6" v-if="page == 'success'">
                <BCard no-body>
                    <BCardHeader>
                        <h5 class="card-title mb-0">Saat Pembelian Selesai, Arahkan Menuju</h5>
                    </BCardHeader>
                    <BCardBody>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="mb-3">
                                    <label class="form-check-label fw-bold mb-3" for="headline_success">Masukan
                                        Headline</label>
                                    <select class="form-select" v-model="checkoutData.success_page.type">
                                        <option value="success_page">
                                            Success Page
                                        </option>
                                        <option value="redirect_wa">
                                            Redirect to WA
                                        </option>
                                    </select>
                                </div>
                                <div class="mb-3" v-if="checkoutData.success_page.type == 'redirect_wa'">
                                    <label class="form-check-label fw-bold mb-3" for="headline_success">Masukan no WA
                                    </label>
                                    <input class="form-control" v-model="checkoutData.success_page.wa" type="text" required>
                                </div>
                            </div>
                        </div>
                    </BCardBody>
                </BCard>
                <template v-if="checkoutData.success_page.type == 'success_page'">
                    <BCard no-body>
                        <BCardHeader>
                            <h5 class="card-title mb-0">Headline Text</h5>
                        </BCardHeader>
                        <BCardBody>
                            <div class="row">
                                <div class="col-lg-12">
                                    <p>Masukan Judul Success Page's Anda di bawah ini</p>
                                    <div class="mb-3">
                                        <div class="form-check form-switch form-switch-custom form-switch-success">
                                            <input class="form-check-input" v-model="checkoutData.success_page.showTitle"
                                                type="checkbox" role="switch" id="title_success">
                                            <label class="form-check-label" for="title_success">Tampilkan judul di bagian
                                                atas
                                                success page?</label>
                                        </div>
                                    </div>
                                    <div class="mb-3" v-if="checkoutData.success_page.showTitle">
                                        <label class="form-label fw-bold mb-3">Success Title</label>
                                        <input class="form-control" v-model="checkoutData.success_page.title"
                                            placeholder="Judul" type="text">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-check-label fw-bold mb-3" for="headline_success">Masukan
                                            Headline</label>
                                        <input class="form-control" v-model="checkoutData.success_page.headline" type="text"
                                            id="headline_success">
                                    </div>
                                </div>
                            </div>
                        </BCardBody>
                    </BCard>
                    <BCard no-body>
                        <BCardHeader>
                            <h5 class="card-title mb-0">Video</h5>
                        </BCardHeader>
                        <BCardBody>
                            <div class="row">
                                <div class="col-lg-12">
                                    <p>Tambahkan video ke success page Anda di bawah ini</p>
                                    <div class="mb-3">
                                        <label class="form-check-label mb-3" for="headline_success">Masukan
                                            URL Video YouTube</label>
                                        <input class="form-control" v-model="checkoutData.success_page.video"
                                            @keyup="embedYouTubeVideo" type="text" id="headline_success">
                                    </div>
                                </div>
                            </div>
                        </BCardBody>
                    </BCard>
                    <BCard no-body>
                        <BCardHeader>
                            <h5 class="card-title mb-0">Akun Bank</h5>
                        </BCardHeader>
                        <BCardBody>
                            <div class="row">
                                <div class="col-lg-12">
                                    <p>Choose bank account for your success page</p>
                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between">
                                            <label for="choices-publish-status-input" class="form-label">Akun Bank</label>
                                            <Link href="/profile?page=payment"
                                                class="cursor-pointer text-secondary fw-bold">+ Tambah No. Rekening</Link>
                                            <BModal v-model="accountBankShow" hide-footer title="Informasi  Bank" size="lg"
                                                class="v-modal-custom">
                                                <div class="row">
                                                    <div class="col-lg-12">
                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold"
                                                                for="product-title-input">Bank</label>
                                                            <Multiselect class="form-control"
                                                                :class="{ 'is-invalid': bankAccountError.bank }"
                                                                placeholder="Select option" v-model="tempBankAccount.bank"
                                                                :close-on-select="true" :searchable="true"
                                                                :create-option="true" :options="listBank" />
                                                            <div class="text-danger" v-if="bankAccountError.bank">
                                                                {{ bankAccountError.bank[0] }}
                                                            </div>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold">Atas Nama</label>
                                                            <input v-model='tempBankAccount.atas_nama' class="form-control"
                                                                :class="{ 'is-invalid': bankAccountError.atas_nama }"
                                                                type="text">
                                                            <div class="text-danger" v-if="bankAccountError.atas_nama">
                                                                {{ bankAccountError.atas_nama[0] }}
                                                            </div>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold">Nomor Rekening</label>
                                                            <input v-model="tempBankAccount.rekening" class="form-control"
                                                                :class="{ 'is-invalid': bankAccountError.rekening }"
                                                                type="number">
                                                            <div class="text-danger" v-if="bankAccountError.rekening">
                                                                {{ bankAccountError.rekening[0] }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer v-modal-footer">
                                                    <BButton type="button" variant="primary" @click="addBankAccount">
                                                        Tambah Akun Bank
                                                    </BButton>
                                                </div>
                                            </BModal>
                                        </div>
                                        <select class="form-select" v-model="tempSelectedBank" @change="selectBank">
                                            <option value="">Pilih akun bank</option>
                                            <option :value="item" v-for="(item, index) in listBankAccount">{{ item.bank }}
                                                - {{ item.atas_nama }} - {{ item.rekening }}
                                            </option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <div v-for="(item, index) in checkoutData.success_page.accountBank"
                                            class="border py-4 px-2 d-flex justify-content-between align-items-center">
                                            <div>
                                                {{ index + 1 }}. {{ item.bank }} - {{ item.rekening }} - {{ item.atas_nama
                                                }}
                                            </div>
                                            <div @click="deleteAccountBank(index)">
                                                <i class="bx bx-trash fs-2 cursor-pointer"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </BCardBody>
                    </BCard>
                    <BCard no-body>
                        <BCardHeader>
                            <h5 class="card-title mb-0">Body Text</h5>
                        </BCardHeader>
                        <BCardBody>
                            <div class="row">
                                <div class="col-lg-12">
                                    <p class="text-muted">Edit Konten success page dan tautan Anda di bawah</p>
                                    <div class="mb-3">
                                        <label class="form-check-label mb-3" for="headline_success">Masukan beberapa teks
                                            untuk
                                            ditampilkan di Success Page</label>
                                        <div class="d-flex justify-content-between mb-3">
                                            <div type="button" class="text-center w-100 py-2"
                                                @click="changeBodyText('bank_transfer')"
                                                :class="{ 'border-bottom border-secondary': checkoutData.success_page.bodyText.payment_type == 'bank_transfer' }">
                                                Bank Transfer
                                            </div>
                                            <div type="button" class="text-center w-100 py-2" @click="changeBodyText('cod')"
                                                :class="{ 'border-bottom border-secondary': checkoutData.success_page.bodyText.payment_type == 'cod' }">
                                                COD
                                            </div>
                                            <div type="button" class="text-center w-100 py-2"
                                                @click="changeBodyText('payment')"
                                                :class="{ 'border-bottom border-secondary': checkoutData.success_page.bodyText.payment_type == 'payment' }">
                                                Payment
                                            </div>
                                        </div>
                                        <div nav-class="row mb-3">
                                            <div class="col-12" title="Bank Transfer"
                                                v-if="checkoutData.success_page.bodyText.payment_type == 'bank_transfer'">
                                                <div class="ckeditor-classic">
                                                    <ckeditor
                                                        v-model="checkoutData.success_page.bodyText.bank_transfer_body"
                                                        :editor="editor"></ckeditor>
                                                </div>
                                            </div>
                                            <div class="col-12" title="COD"
                                                v-if="checkoutData.success_page.bodyText.payment_type == 'cod'">
                                                <div class="ckeditor-classic">
                                                    <ckeditor v-model="checkoutData.success_page.bodyText.cod_body"
                                                        :editor="editor">
                                                    </ckeditor>
                                                </div>
                                            </div>
                                            <div class="col-12" title="Payment"
                                                v-if="checkoutData.success_page.bodyText.payment_type == 'payment'">
                                                <div class="ckeditor-classic">
                                                    <ckeditor v-model="checkoutData.success_page.bodyText.payment_body"
                                                        :editor="editor">
                                                    </ckeditor>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </BCardBody>
                    </BCard>
                </template>
                <div class="text-end mb-3">
                    <BButton type="button" variant="primary" class="w-sm" @click="submit">SIMPAN
                    </BButton>
                </div>
            </BCol>
            <template v-if="checkoutData.success_page.type == 'success_page'">
                <BCol lg="6" v-if="page == 'success'">

                    <!-- Success Page -->
                    <BCard no-body class="bg-light"
                        style="position: sticky; top: 130px;overflow: auto; max-height: calc(100vh - 200px);">
                        <div class="row d-flex justify-content-center mt-4 mb-2">
                            <div class="col-12" v-if="checkoutData.success_page.showTitle">
                                <h5 class="text-center">{{
                                    checkoutData.success_page.title }}</h5>
                            </div>
                        </div>
                        <BCardBody>
                            <BCard no-body>
                                <BCardBody class="pt-0 pb-0 border-bottom border-success">
                                    <div class="row">
                                        <div class="col-12 p-2">
                                            <div class="mb-3">
                                                <h5 class="text-center">{{
                                                    evalExpression(checkoutData.success_page.headline) }}
                                                </h5>
                                            </div>
                                            <div class="d-flex justify-content-center"
                                                v-if="checkoutData.success_page.video != ''">
                                                <iframe :src="checkoutData.success_page.video" frameborder="0"
                                                    allowfullscreen></iframe>
                                            </div>
                                            <div class="text-center"
                                                v-if="checkoutData.success_page.bodyText.payment_type == 'bank_transfer'"
                                                v-html="checkoutData.success_page.bodyText.bank_transfer_body"></div>
                                            <div class="text-center"
                                                v-if="checkoutData.success_page.bodyText.payment_type == 'cod'"
                                                v-html="checkoutData.success_page.bodyText.cod_body"></div>
                                            <div class="text-center"
                                                v-if="checkoutData.success_page.bodyText.payment_type == 'payment'"
                                                v-html="checkoutData.success_page.bodyText.payment_body"></div>
                                            <div class="mb-3">
                                                <h5 class="text-center text-success fw-bold fs-3">{{
                                                    global.formatNumber(product.normal_price)
                                                }}
                                                </h5>
                                            </div>
                                            <div class="row"
                                                v-if="checkoutData.success_page.bodyText.payment_type == 'bank_transfer'">
                                                <div class="col-12 col-lg-6"
                                                    v-for="(item, index) in checkoutData.success_page.accountBank">
                                                    <div class="card border">
                                                        <div class="card-body text-center">
                                                            <div>{{ item.bank }}</div>
                                                            <div>No. Rek: {{ item.rekening }}</div>
                                                            <div>Atas Nama: <span class="fw-bold">{{ item.atas_nama
                                                            }}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <p class="text-center"
                                                    v-if="checkoutData.success_page.bodyText.payment_type == 'bank_transfer'">
                                                    Konfirmasi
                                                    pembayaran anda di : <span class="text-secondary">Konfirmasi
                                                        Pembayaran</span></p>
                                                <!-- <p class="text-center text-muted">Anda akan segera redirect ke wa
                                                    setelah 10 detik</p> -->

                                            </div>
                                        </div>
                                    </div>
                                </BCardBody>
                            </BCard>
                        </BCardBody>
                    </BCard>
                </BCol>
            </template>
            <template v-if="checkoutData.success_page.type == 'redirect_wa'">
                <BCol lg="6" v-if="page == 'success'">

                    <!-- Success Page -->
                    <BCard no-body class="bg-light"
                        style="position: sticky; top: 130px;overflow: auto; max-height: calc(100vh - 200px);">
                        <BCardBody>
                            <BCard no-body>
                                <BCardBody class="border-bottom border-success">
                                    <div class="d-flex justify-content-around align-items-center px-5">
                                        <img style="height:60px;" src="@assets/images/logo-white.png">
                                        <img style="height:40px" src="@assets/images/icon-arrow-right.png">
                                        <img style="height:100px" src="@assets/images/icon-www.png">
                                    </div>
                                    <div class="text-center">Anda akan redirect ke WA Kami</div>
                                </BCardBody>
                            </BCard>
                        </BCardBody>
                    </BCard>
                </BCol>
            </template>
            <!-- End page success -->
        </BRow>
    </Layout>
</template>