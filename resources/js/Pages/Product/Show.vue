<script>
import { Head, Link } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue'
import Layout from "@/Layouts/main.vue";
import PageHeader from "@/Components/page-header.vue";
import Slider from "@vueform/slider";
import Multiselect from "@vueform/multiselect";
import Swal from "sweetalert2";
import "@vueform/multiselect/themes/default.css";
import animationData from "@/Components/widgets/msoeawqm.json";
import Lottie from "@/Components/widgets/lottie.vue";
import { productsData } from "@/common/data"
import { Global } from '../../global'
import CKEditor from "@ckeditor/ckeditor5-vue";
import ClassicEditor from "@ckeditor/ckeditor5-build-classic";

export default {
    props: {
        product: Object,
        user: Object
    },
    setup(props) {
        let productData = ref(props.product)
        onMounted(() => {
            console.log(props.product)
        });

        const global = Global();

        // Follow Up
        let followUp = ref(productData.value.followUp);

        const textFollowUpStatus = ref(false);

        let followUpText = ref({
            page: 'welcome',
            payment_page: 'bank_transfer'
        })

        const changeFollowUp = (page) => {
            followUpText.value.page = page
            followUpText.value.payment_page = 'bank_transfer'
        }

        const changePaymentMethod = (paymentPage, page) => {
            followUpText.value.payment_page = paymentPage
        }
        // End Follow Up
        return {
            global,

            // Editor
            editor: ClassicEditor,

            // Text FollowUp
            followUp,
            textFollowUpStatus,
            followUpText,
            changeFollowUp,
            changePaymentMethod
        }
    },
    data() {
        return {
        };
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
        ckeditor: CKEditor.component,
        Head, Link
    },
};
</script>
<style>
.arrow-right {
    position: absolute;
    top: 50%;
    right: -5px;
    transform: translateY(-50%);
    width: 40px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    background: linear-gradient(to right, #ff8c00, #ffc107);
}

.arrow-right::before {
    content: '';
    position: absolute;
    top: 50%;
    right: -100%;
    transform: translateY(-50%);
    width: 40px;
    height: 60px;
    background: #ffc107;
    clip-path: polygon(0 0, 100% 50%, 0 100%);
}
</style>
<template>
    <Layout>

        <Head title="Products" />
        <div class="d-flex justify-content-between mb-3">
            <div class="d-flex align-items-center">
                <div>
                    {{ product.name }}
                </div>
                <Link :href="'/product/edit/' + product.id" v-if="user.permission.product.update"
                    class="ms-3 btn border bg-white text-gray d-flex align-items-center">Edit <i
                    class="fs-5 ms-2 bx bx-edit-alt"></i>
                </Link>
            </div>
            <div class="d-flex align-items-center">
                <Link :href="'/orders?product_id=' + product.id"
                    class="ms-3 btn border bg-white text-gray d-flex align-items-center" v-if="user.permission.order.read">
                Lihat Order <i class="fs-5 ms-2 bx bx-show"></i>
                </Link>
                <a :href="global.urlCheckout + product.url" target="_blank"
                    class="ms-3 btn border bg-white text-gray d-flex align-items-center">View
                    Checkout Page <i class="fs-5 ms-2 bx bx-navigation"></i>
                </a>
                <button class="ms-3 btn border bg-white text-gray d-flex align-items-center"
                    v-if="user.permission.product.delete">Hapus <i class="fs-5 ms-2 bx bx-trash"></i>
                </button>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col">
                <button @click="textFollowUpStatus = !textFollowUpStatus"
                    class="btn bg-white border border-gray border-2 w-100 py-3 d-flex align-items-center justify-content-center">
                    <i class="bx bx-text fs-4"></i>
                    Teks
                    Follow-up</button>
                <BModal v-model="textFollowUpStatus" hide-footer title="Text Folow Up" size="xl" class="v-modal-custom">
                    <div class="row border">
                        <div class="col-lg-12">
                            <div class="d-flex justify-content-between mb-3">
                                <div type="button" class="text-center w-100 py-2" @click="changeFollowUp('welcome')"
                                    :class="{ 'border-bottom border-secondary': followUpText.page == 'welcome' }">
                                    Welcome
                                </div>
                                <div type="button" class="text-center w-100 py-2" @click="changeFollowUp('order_detail')"
                                    :class="{ 'border-bottom border-secondary': followUpText.page == 'order_detail' }">
                                    Order Detail
                                </div>
                                <div type="button" class="text-center w-100 py-2" @click="changeFollowUp('follow_up_1')"
                                    :class="{ 'border-bottom border-secondary': followUpText.page == 'follow_up_1' }">
                                    Follow Up 1
                                </div>
                                <div type="button" class="text-center w-100 py-2" @click="changeFollowUp('follow_up_2')"
                                    :class="{ 'border-bottom border-secondary': followUpText.page == 'follow_up_2' }">
                                    Follow Up 2
                                </div>
                                <div type="button" class="text-center w-100 py-2" @click="changeFollowUp('follow_up_3')"
                                    :class="{ 'border-bottom border-secondary': followUpText.page == 'follow_up_3' }">
                                    Follow Up 3
                                </div>
                                <div type="button" class="text-center w-100 py-2" @click="changeFollowUp('follow_up_4')"
                                    :class="{ 'border-bottom border-secondary': followUpText.page == 'follow_up_4' }">
                                    Follow Up 4
                                </div>
                                <div type="button" class="text-center w-100 py-2" @click="changeFollowUp('processing')"
                                    :class="{ 'border-bottom border-secondary': followUpText.page == 'processing' }">
                                    Processing
                                </div>
                                <div type="button" class="text-center w-100 py-2" @click="changeFollowUp('completed')"
                                    :class="{ 'border-bottom border-secondary': followUpText.page == 'completed' }">
                                    Completed
                                </div>
                                <div type="button" class="text-center w-100 py-2" @click="changeFollowUp('up_selling')"
                                    :class="{ 'border-bottom border-secondary': followUpText.page == 'up_selling' }">
                                    Up Selling
                                </div>
                                <div type="button" class="text-center w-100 py-2" @click="changeFollowUp('redirect')"
                                    :class="{ 'border-bottom border-secondary': followUpText.page == 'redirect' }">
                                    Redirect
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12 p-3" v-if="followUpText.page == 'welcome'">
                            <div>
                                <h5>Metode Pembayaran</h5>
                            </div>
                            <div class="btn-group w-100 mb-3">
                                <button @click="changePaymentMethod('bank_transfer', 'welcome')" class="btn"
                                    :class="{ 'btn-primary': followUpText.payment_page == 'bank_transfer', 'btn-outline-secondary': followUpText.payment_page !== 'bank_transfer' }">

                                    Bank Transfer</button>
                                <button @click="changePaymentMethod('cod', 'welcome')" class="btn"
                                    :class="{ 'btn-primary': followUpText.payment_page == 'cod', 'btn-outline-secondary': followUpText.payment_page !== 'cod' }">

                                    COD
                                </button>
                                <button @click="changePaymentMethod('epayment', 'welcome')" class="btn"
                                    :class="{ 'btn-primary': followUpText.payment_page == 'epayment', 'btn-outline-secondary': followUpText.payment_page !== 'epayment' }">

                                    E-Payment
                                </button>
                            </div>
                            <div>
                                <h5>Pengaturan Text</h5>
                                <div class="ckeditor-classic" v-if="followUpText.payment_page == 'bank_transfer'">
                                    <ckeditor :editor="editor" v-model="followUp.welcome.bank_transfer.text">
                                    </ckeditor>
                                </div>
                                <div class="ckeditor-classic" v-if="followUpText.payment_page == 'cod'">
                                    <ckeditor :editor="editor" v-model="followUp.welcome.cod.text">
                                    </ckeditor>
                                </div>
                                <div class="ckeditor-classic" v-if="followUpText.payment_page == 'epayment'">
                                    <ckeditor :editor="editor" v-model="followUp.welcome.epayment.text">
                                    </ckeditor>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12 p-3" v-if="followUpText.page == 'order_detail'">
                            <div>
                                <h5>Metode Pembayaran</h5>
                            </div>
                            <div class="btn-group w-100 mb-3">
                                <button @click="changePaymentMethod('bank_transfer', 'order_detail')" class="btn"
                                    :class="{ 'btn-primary': followUpText.payment_page == 'bank_transfer', 'btn-outline-secondary': followUpText.payment_page !== 'bank_transfer' }">

                                    Bank Transfer</button>
                                <button @click="changePaymentMethod('cod', 'order_detail')" class="btn"
                                    :class="{ 'btn-primary': followUpText.payment_page == 'cod', 'btn-outline-secondary': followUpText.payment_page !== 'cod' }">

                                    COD
                                </button>
                                <button @click="changePaymentMethod('epayment', 'order_detail')" class="btn"
                                    :class="{ 'btn-primary': followUpText.payment_page == 'epayment', 'btn-outline-secondary': followUpText.payment_page !== 'epayment' }">

                                    E-Payment
                                </button>
                            </div>
                            <div>
                                <h5>Pengaturan Text</h5>
                                <div class="ckeditor-classic" v-if="followUpText.payment_page == 'bank_transfer'">
                                    <ckeditor :editor="editor" v-model="followUp.order_detail.bank_transfer.text">
                                    </ckeditor>
                                </div>
                                <div class="ckeditor-classic" v-if="followUpText.payment_page == 'cod'">
                                    <ckeditor :editor="editor" v-model="followUp.order_detail.cod.text">
                                    </ckeditor>
                                </div>
                                <div class="ckeditor-classic" v-if="followUpText.payment_page == 'epayment'">
                                    <ckeditor :editor="editor" v-model="followUp.order_detail.epayment.text">
                                    </ckeditor>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12 p-3" v-if="followUpText.page == 'follow_up_1'">
                            <div>
                                <h5>Metode Pembayaran</h5>
                            </div>
                            <div class="btn-group w-100 mb-3">
                                <button @click="changePaymentMethod('bank_transfer', 'follow_up_1')" class="btn"
                                    :class="{ 'btn-primary': followUpText.payment_page == 'bank_transfer', 'btn-outline-secondary': followUpText.payment_page !== 'bank_transfer' }">

                                    Bank Transfer</button>
                                <button @click="changePaymentMethod('cod', 'follow_up_1')" class="btn"
                                    :class="{ 'btn-primary': followUpText.payment_page == 'cod', 'btn-outline-secondary': followUpText.payment_page !== 'cod' }">

                                    COD
                                </button>
                                <button @click="changePaymentMethod('epayment', 'follow_up_1')" class="btn"
                                    :class="{ 'btn-primary': followUpText.payment_page == 'epayment', 'btn-outline-secondary': followUpText.payment_page !== 'epayment' }">

                                    E-Payment
                                </button>
                            </div>
                            <div>
                                <h5>Pengaturan Text</h5>
                                <div class="ckeditor-classic" v-if="followUpText.payment_page == 'bank_transfer'">
                                    <ckeditor :editor="editor" v-model="followUp.follow_up_1.bank_transfer.text">
                                    </ckeditor>
                                </div>
                                <div class="ckeditor-classic" v-if="followUpText.payment_page == 'cod'">
                                    <ckeditor :editor="editor" v-model="followUp.follow_up_1.cod.text">
                                    </ckeditor>
                                </div>
                                <div class="ckeditor-classic" v-if="followUpText.payment_page == 'epayment'">
                                    <ckeditor :editor="editor" v-model="followUp.follow_up_1.epayment.text">
                                    </ckeditor>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12 p-3" v-if="followUpText.page == 'follow_up_2'">
                            <div>
                                <h5>Metode Pembayaran</h5>
                            </div>
                            <div class="btn-group w-100 mb-3">
                                <button @click="changePaymentMethod('bank_transfer', 'follow_up_2')" class="btn"
                                    :class="{ 'btn-primary': followUpText.payment_page == 'bank_transfer', 'btn-outline-secondary': followUpText.payment_page !== 'bank_transfer' }">

                                    Bank Transfer</button>
                                <button @click="changePaymentMethod('cod', 'follow_up_2')" class="btn"
                                    :class="{ 'btn-primary': followUpText.payment_page == 'cod', 'btn-outline-secondary': followUpText.payment_page !== 'cod' }">

                                    COD
                                </button>
                                <button @click="changePaymentMethod('epayment', 'follow_up_2')" class="btn"
                                    :class="{ 'btn-primary': followUpText.payment_page == 'epayment', 'btn-outline-secondary': followUpText.payment_page !== 'epayment' }">

                                    E-Payment
                                </button>
                            </div>
                            <div>
                                <h5>Pengaturan Text</h5>
                                <div class="ckeditor-classic" v-if="followUpText.payment_page == 'bank_transfer'">
                                    <ckeditor :editor="editor" v-model="followUp.follow_up_2.bank_transfer.text">
                                    </ckeditor>
                                </div>
                                <div class="ckeditor-classic" v-if="followUpText.payment_page == 'cod'">
                                    <ckeditor :editor="editor" v-model="followUp.follow_up_2.cod.text">
                                    </ckeditor>
                                </div>
                                <div class="ckeditor-classic" v-if="followUpText.payment_page == 'epayment'">
                                    <ckeditor :editor="editor" v-model="followUp.follow_up_2.epayment.text">
                                    </ckeditor>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12 p-3" v-if="followUpText.page == 'follow_up_3'">
                            <div>
                                <h5>Metode Pembayaran</h5>
                            </div>
                            <div class="btn-group w-100 mb-3">
                                <button @click="changePaymentMethod('bank_transfer', 'follow_up_3')" class="btn"
                                    :class="{ 'btn-primary': followUpText.payment_page == 'bank_transfer', 'btn-outline-secondary': followUpText.payment_page !== 'bank_transfer' }">

                                    Bank Transfer</button>
                                <button @click="changePaymentMethod('cod', 'follow_up_3')" class="btn"
                                    :class="{ 'btn-primary': followUpText.payment_page == 'cod', 'btn-outline-secondary': followUpText.payment_page !== 'cod' }">

                                    COD
                                </button>
                                <button @click="changePaymentMethod('epayment', 'follow_up_3')" class="btn"
                                    :class="{ 'btn-primary': followUpText.payment_page == 'epayment', 'btn-outline-secondary': followUpText.payment_page !== 'epayment' }">

                                    E-Payment
                                </button>
                            </div>
                            <div>
                                <h5>Pengaturan Text</h5>
                                <div class="ckeditor-classic" v-if="followUpText.payment_page == 'bank_transfer'">
                                    <ckeditor :editor="editor" v-model="followUp.follow_up_3.bank_transfer.text">
                                    </ckeditor>
                                </div>
                                <div class="ckeditor-classic" v-if="followUpText.payment_page == 'cod'">
                                    <ckeditor :editor="editor" v-model="followUp.follow_up_3.cod.text">
                                    </ckeditor>
                                </div>
                                <div class="ckeditor-classic" v-if="followUpText.payment_page == 'epayment'">
                                    <ckeditor :editor="editor" v-model="followUp.follow_up_3.epayment.text">
                                    </ckeditor>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12 p-3" v-if="followUpText.page == 'follow_up_4'">
                            <div>
                                <h5>Metode Pembayaran</h5>
                            </div>
                            <div class="btn-group w-100 mb-3">
                                <button @click="changePaymentMethod('bank_transfer', 'follow_up_4')" class="btn"
                                    :class="{ 'btn-primary': followUpText.payment_page == 'bank_transfer', 'btn-outline-secondary': followUpText.payment_page !== 'bank_transfer' }">

                                    Bank Transfer</button>
                                <button @click="changePaymentMethod('cod', 'follow_up_4')" class="btn"
                                    :class="{ 'btn-primary': followUpText.payment_page == 'cod', 'btn-outline-secondary': followUpText.payment_page !== 'cod' }">

                                    COD
                                </button>
                                <button @click="changePaymentMethod('epayment', 'follow_up_4')" class="btn"
                                    :class="{ 'btn-primary': followUpText.payment_page == 'epayment', 'btn-outline-secondary': followUpText.payment_page !== 'epayment' }">

                                    E-Payment
                                </button>
                            </div>
                            <div>
                                <h5>Pengaturan Text</h5>
                                <div class="ckeditor-classic" v-if="followUpText.payment_page == 'bank_transfer'">
                                    <ckeditor :editor="editor" v-model="followUp.follow_up_4.bank_transfer.text">
                                    </ckeditor>
                                </div>
                                <div class="ckeditor-classic" v-if="followUpText.payment_page == 'cod'">
                                    <ckeditor :editor="editor" v-model="followUp.follow_up_4.cod.text">
                                    </ckeditor>
                                </div>
                                <div class="ckeditor-classic" v-if="followUpText.payment_page == 'epayment'">
                                    <ckeditor :editor="editor" v-model="followUp.follow_up_4.epayment.text">
                                    </ckeditor>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12 p-3" v-if="followUpText.page == 'processing'">
                            <div>
                                <h5>Metode Pembayaran</h5>
                            </div>
                            <div class="btn-group w-100 mb-3">
                                <button @click="changePaymentMethod('bank_transfer', 'processing')" class="btn"
                                    :class="{ 'btn-primary': followUpText.payment_page == 'bank_transfer', 'btn-outline-secondary': followUpText.payment_page !== 'bank_transfer' }">

                                    Bank Transfer</button>
                                <button @click="changePaymentMethod('cod', 'processing')" class="btn"
                                    :class="{ 'btn-primary': followUpText.payment_page == 'cod', 'btn-outline-secondary': followUpText.payment_page !== 'cod' }">

                                    COD
                                </button>
                                <button @click="changePaymentMethod('epayment', 'processing')" class="btn"
                                    :class="{ 'btn-primary': followUpText.payment_page == 'epayment', 'btn-outline-secondary': followUpText.payment_page !== 'epayment' }">

                                    E-Payment
                                </button>
                            </div>
                            <div>
                                <h5>Pengaturan Text</h5>
                                <div class="ckeditor-classic" v-if="followUpText.payment_page == 'bank_transfer'">
                                    <ckeditor :editor="editor" v-model="followUp.processing.bank_transfer.text">
                                    </ckeditor>
                                </div>
                                <div class="ckeditor-classic" v-if="followUpText.payment_page == 'cod'">
                                    <ckeditor :editor="editor" v-model="followUp.processing.cod.text">
                                    </ckeditor>
                                </div>
                                <div class="ckeditor-classic" v-if="followUpText.payment_page == 'epayment'">
                                    <ckeditor :editor="editor" v-model="followUp.processing.epayment.text">
                                    </ckeditor>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12 p-3" v-if="followUpText.page == 'completed'">
                            <div>
                                <h5>Metode Pembayaran</h5>
                            </div>
                            <div class="btn-group w-100 mb-3">
                                <button @click="changePaymentMethod('bank_transfer', 'completed')" class="btn"
                                    :class="{ 'btn-primary': followUpText.payment_page == 'bank_transfer', 'btn-outline-secondary': followUpText.payment_page !== 'bank_transfer' }">

                                    Bank Transfer</button>
                                <button @click="changePaymentMethod('cod', 'completed')" class="btn"
                                    :class="{ 'btn-primary': followUpText.payment_page == 'cod', 'btn-outline-secondary': followUpText.payment_page !== 'cod' }">

                                    COD
                                </button>
                                <button @click="changePaymentMethod('epayment', 'completed')" class="btn"
                                    :class="{ 'btn-primary': followUpText.payment_page == 'epayment', 'btn-outline-secondary': followUpText.payment_page !== 'epayment' }">

                                    E-Payment
                                </button>
                            </div>
                            <div>
                                <h5>Pengaturan Text</h5>
                                <div class="ckeditor-classic" v-if="followUpText.payment_page == 'bank_transfer'">
                                    <ckeditor :editor="editor" v-model="followUp.completed.bank_transfer.text">
                                    </ckeditor>
                                </div>
                                <div class="ckeditor-classic" v-if="followUpText.payment_page == 'cod'">
                                    <ckeditor :editor="editor" v-model="followUp.completed.cod.text">
                                    </ckeditor>
                                </div>
                                <div class="ckeditor-classic" v-if="followUpText.payment_page == 'epayment'">
                                    <ckeditor :editor="editor" v-model="followUp.completed.epayment.text">
                                    </ckeditor>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12 p-3" v-if="followUpText.page == 'up_selling'">
                            <div>
                                <h5>Metode Pembayaran</h5>
                            </div>
                            <div class="btn-group w-100 mb-3">
                                <button @click="changePaymentMethod('bank_transfer', 'up_selling')" class="btn"
                                    :class="{ 'btn-primary': followUpText.payment_page == 'bank_transfer', 'btn-outline-secondary': followUpText.payment_page !== 'bank_transfer' }">

                                    Bank Transfer</button>
                                <button @click="changePaymentMethod('cod', 'up_selling')" class="btn"
                                    :class="{ 'btn-primary': followUpText.payment_page == 'cod', 'btn-outline-secondary': followUpText.payment_page !== 'cod' }">

                                    COD
                                </button>
                                <button @click="changePaymentMethod('epayment', 'up_selling')" class="btn"
                                    :class="{ 'btn-primary': followUpText.payment_page == 'epayment', 'btn-outline-secondary': followUpText.payment_page !== 'epayment' }">

                                    E-Payment
                                </button>
                            </div>
                            <div>
                                <h5>Pengaturan Text</h5>
                                <div class="ckeditor-classic" v-if="followUpText.payment_page == 'bank_transfer'">
                                    <ckeditor :editor="editor" v-model="followUp.up_selling.bank_transfer.text">
                                    </ckeditor>
                                </div>
                                <div class="ckeditor-classic" v-if="followUpText.payment_page == 'cod'">
                                    <ckeditor :editor="editor" v-model="followUp.up_selling.cod.text">
                                    </ckeditor>
                                </div>
                                <div class="ckeditor-classic" v-if="followUpText.payment_page == 'epayment'">
                                    <ckeditor :editor="editor" v-model="followUp.up_selling.epayment.text">
                                    </ckeditor>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12 p-3" v-if="followUpText.page == 'redirect'">
                            <div>
                                <h5>Metode Pembayaran</h5>
                            </div>
                            <div class="btn-group w-100 mb-3">
                                <button @click="changePaymentMethod('bank_transfer', 'redirect')" class="btn"
                                    :class="{ 'btn-primary': followUpText.payment_page == 'bank_transfer', 'btn-outline-secondary': followUpText.payment_page !== 'bank_transfer' }">

                                    Bank Transfer</button>
                                <button @click="changePaymentMethod('cod', 'redirect')" class="btn"
                                    :class="{ 'btn-primary': followUpText.payment_page == 'cod', 'btn-outline-secondary': followUpText.payment_page !== 'cod' }">

                                    COD
                                </button>
                                <button @click="changePaymentMethod('epayment', 'redirect')" class="btn"
                                    :class="{ 'btn-primary': followUpText.payment_page == 'epayment', 'btn-outline-secondary': followUpText.payment_page !== 'epayment' }">

                                    E-Payment
                                </button>
                            </div>
                            <div>
                                <h5>Pengaturan Text</h5>
                                <div class="ckeditor-classic" v-if="followUpText.payment_page == 'bank_transfer'">
                                    <ckeditor :editor="editor" v-model="followUp.redirect.bank_transfer.text">
                                    </ckeditor>
                                </div>
                                <div class="ckeditor-classic" v-if="followUpText.payment_page == 'cod'">
                                    <ckeditor :editor="editor" v-model="followUp.redirect.cod.text">
                                    </ckeditor>
                                </div>
                                <div class="ckeditor-classic" v-if="followUpText.payment_page == 'epayment'">
                                    <ckeditor :editor="editor" v-model="followUp.redirect.epayment.text">
                                    </ckeditor>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer v-modal-footer">
                        <BButton type="button" variant="primary" @click="textFollowUpStatus = false">SIMPAN
                        </BButton>
                    </div>
                </BModal>
            </div>
            <div class="col">
                <button
                    class="btn bg-white border border-gray border-2 w-100 py-3 d-flex align-items-center justify-content-center"><i
                        class="bx bx-user-circle fs-4"></i> Autoresponder</button>
            </div>
            <div class="col">
                <button
                    class="btn bg-white border border-gray border-2 w-100 py-3 d-flex align-items-center justify-content-center">
                    <i class="bx bx-envelope fs-4"></i>
                    Custom
                    Audience</button>
            </div>
            <div class="col">
                <button
                    class="btn bg-white border border-gray border-2 w-100 py-3 d-flex align-items-center justify-content-center">
                    <i class="bx bx-message-dots fs-4"></i>
                    Chat
                    Generate</button>
            </div>
            <div class="col">
                <button
                    class="btn bg-white border border-gray border-2 w-100 py-3 d-flex align-items-center justify-content-center">
                    <i class="bx bx-bell fs-4"></i>
                    Notification</button>
            </div>
            <div class="col">
                <button
                    class="btn bg-white border border-gray border-2 w-100 py-3 d-flex align-items-center justify-content-center">
                    <i class="bx bx-user fs-4"></i>
                    Assignation</button>
            </div>
        </div>
        <div class="bg-white py-4 shadow rounded mb-5 container">
            <div class="row">
                <div class="col-lg-3 col-12 px-2 text-center border-end border-secondary  position-relative">
                    <i class="bx bx-store text-warning" style="font-size: 60px;"></i>
                    <p>Sales Page View</p>
                    <h3 class="text-secondary">0</h3>
                    <div class="arrow-right fw-bold">0%</div>
                </div>
                <div class="col-lg-3 col-12 px-2 text-center border-end border-secondary">
                    <i class="bx bx-shopping-bag text-warning" style="font-size: 60px;"></i>
                    <p>Checkout Page View</p>
                    <h3 class="text-secondary">0</h3>
                    <div class="arrow-right fw-bold">0%</div>
                </div>
                <div class="col-lg-3 col-12 px-2 text-center border-end border-secondary">
                    <i class="bx bx-notepad text-warning" style="font-size: 60px;"></i>
                    <p>Order</p>
                    <h3 class="text-secondary">0</h3>
                    <div class="arrow-right fw-bold">0%</div>
                </div>
                <div class="col-lg-3 col-12 px-2 text-center">
                    <i class="bx bx-dollar text-warning" style="font-size: 60px;"></i>
                    <p>Paid</p>
                    <h3 class="text-secondary">0</h3>
                </div>
            </div>
        </div>
        <BRow>
            <BCol xl="12" lg="8">
                <div>
                    <BCard no-body>
                        <BCardHeader class="border-0">
                            <BRow class="g-4">
                                <BCol sm="auto">
                                    <div>
                                        <Link :href="route('product.checkout.create', product.id)"
                                            v-if="user.permission.checkout.create" class="btn btn-secondary">
                                        <i class="ri-add-line align-bottom me-1"></i> Create Checkout Page</Link>
                                    </div>
                                </BCol>
                            </BRow>
                        </BCardHeader>
                        <BCardBody>
                            <div class="tab-content text-muted">
                                <div class="tab-pane active" id="productnav-all" role="tabpanel">
                                    <div id="table-product-list-all" class="table-card gridjs-border-none table-responsive">
                                        <table class="table align-middle table-nowrap mb-0" id="customerTable">
                                            <thead class="table-light">
                                                <tr class="text-muted">
                                                    <th class="sort" data-sort="product">Checkout Page</th>
                                                    <th class="sort" data-sort="price">View</th>
                                                    <th class="sort" data-sort="price">Order</th>
                                                    <th class="sort" data-sort="price">Paid
                                                    </th>
                                                    <th class="sort" data-sort="price">Paid Ratio
                                                    </th>
                                                    <th class="sort" data-sort="price">Net Revenue
                                                    </th>
                                                    <th scope="col">Traffic Distribution</th>
                                                </tr>
                                            </thead>
                                            <tbody class="list form-check-all">
                                                <tr v-for="(item, index) in product.checkout">
                                                    <td>
                                                        {{ item.name }}<br>
                                                        <Link :href="'/product/checkout/edit/' + item.id"
                                                            v-if="user.permission.checkout.update">Edit</Link>
                                                    </td>
                                                    <td>{{ item.view }}</td>
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
    </Layout>
</template>