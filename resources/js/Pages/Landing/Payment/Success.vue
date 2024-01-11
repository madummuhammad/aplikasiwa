<script>
import { Head, useForm, Link } from '@inertiajs/vue3';
import { ref, watch } from "vue";
import Multiselect from "@vueform/multiselect";
import "@vueform/multiselect/themes/default.css";
export default {
    setup(props) {
        return {
        };
    },
    components: {
        Head,
        Multiselect,
        Link
    },
    props: {
    },
    data() {
        return {
        }
    },
};
</script>

<template>
    <Head title="Konfirmasi Pembayaran" />
    <!-- <PageHeader title="Create Product" pageTitle="Product" /> -->

    <BRow class="d-flex justify-content-center mt-5 mb-5">
        <!-- Page Success -->
        <BCol lg="6" xs="12">
            <!-- Success Page -->
            <BCard no-body class="bg-light">
                <div class="row d-flex justify-content-center mt-4 mb-2">
                    <div class="col-12">
                        <h2 class="text-center fw-bold">Konfirmasi Pembayaran</h2>
                    </div>
                </div>
                <BCardBody>
                    <BCard class="shadow">
                        <BCardBody>
                            <div class="row">
                                <div class="col-12">
                                    <h4 class="text-center">Terima kasih telah melakukan konfirmasi pembayaran,
                                        Order anda akan segera kami proses.</h4>
                                </div>
                            </div>
                        </BCardBody>
                    </BCard>
                </BCardBody>
            </BCard>
        </BCol>
    </BRow>
</template>