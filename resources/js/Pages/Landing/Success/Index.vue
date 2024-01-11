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
import { Global } from '../../../global'
export default {
    setup(props) {
        console.log('props success', props.success_page)
        console.log('props product', props.product)

        let productData = ref(props.product)
        let success_page = ref(props.success_page)
        let orderData = ref(props.order)
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
        const global = Global();

        const name = orderData.value.fields.filter(item => item.name === 'name');

        onMounted(() => {
            console.log('mounted', props.success_page)
            if (props.success_page.type == 'redirect_wa') {
                const redirectUrl = "https://api.whatsapp.com/send?phone=" + success_page.value.wa + "&text=Halo%2C%20saya%20sudah%20melakukan%20pemesanan%20" + productData.value.name + "%2C%20atas%20nama%20" + name[0].value + ".%20Mohon%20segera%20diproses%20ya%20%F0%9F%99%8F%F0%9F%8F%BB";
                window.open(redirectUrl);
            }
            // setTimeout(() => {
            // }, 2000);
        });
        return {
            evalExpression,
            global
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
        product: Object,
        success_page: Object,
        order: Object
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
    <Head title="Success Page" />
    <!-- <PageHeader title="Create Product" pageTitle="Product" /> -->

    <BRow class="d-flex justify-content-center mt-5 mb-5">
        <!-- Page Success -->
        <BCol lg="9" xs="12" v-if="success_page.type == 'success_page'">

            <!-- Success Page -->
            <BCard no-body class="bg-light">
                <div class="row d-flex justify-content-center mt-4 mb-2">
                    <div class="col-12" v-if="success_page.showTitle">
                        <h5 class="text-center">{{
                            success_page.title }}</h5>
                    </div>
                </div>
                <BCardBody>
                    <BCard no-body>
                        <BCardBody class="pt-0 pb-0 border-bottom border-success">
                            <div class="row">
                                <div class="col-12 p-2">
                                    <div class="mb-3">
                                        <h5 class="text-center">{{ evalExpression(success_page.headline) }}
                                        </h5>
                                    </div>
                                    <div class="d-flex justify-content-center mb-3" v-if="success_page.video !== null">
                                        <iframe :src="success_page.video" frameborder="0" allowfullscreen></iframe>
                                    </div>
                                    <div class="text-center mb-3"
                                        v-if="success_page.bodyText.payment_type == 'bank_transfer'"
                                        v-html="success_page.bodyText.bank_transfer_body"></div>
                                    <div class="text-center mb-3" v-if="success_page.bodyText.payment_type == 'cod'"
                                        v-html="success_page.bodyText.cod_body"></div>
                                    <div class="text-center mb-3" v-if="success_page.bodyText.payment_type == 'payment'"
                                        v-html="success_page.bodyText.payment_body"></div>
                                    <div class="mb-3">
                                        <h5 class="text-center text-success fw-bold fs-3">{{
                                            global.formatNumber(order.total_price) }}
                                        </h5>
                                    </div>
                                    <div class="row" v-if="success_page.bodyText.payment_type == 'bank_transfer'">
                                        <div class="col-12 col-lg-6" v-for="( item, index ) in  success_page.accountBank ">
                                            <div class="card border">
                                                <div class="card-body text-center">
                                                    <div>{{ item.bank }}</div>
                                                    <div>No. Rek: {{ item.rekening }}</div>
                                                    <div>Atas Nama: <span class="fw-bold">{{ item.atas_nama }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <p class="text-center"
                                            v-if="success_page.bodyText.payment_type == 'bank_transfer'">
                                            Konfirmasi
                                            pembayaran anda di : <a
                                                :href="global.urlCheckout + product.url + '/payment_confirmation/' + order.id"
                                                target="_blank" class="text-secondary">Konfirmasi
                                                Pembayaran</a></p>
                                        <!-- <p class="text-center text-muted">Anda akan segera redirect ke wa
                                                setelah 10 detik</p> -->

                                    </div>
                                    <!-- <div class="mb-3">
                                        <p class="text-center text-muted">Anda akan segera redirect ke wa
                                            setelah 10 detik</p>
                                    </div> -->
                                </div>
                            </div>
                        </BCardBody>
                    </BCard>
                </BCardBody>
            </BCard>
        </BCol>

        <template v-if="success_page.type == 'redirect_wa'">
            <BCol lg="9">

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
</template>