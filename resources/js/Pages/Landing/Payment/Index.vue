<script>
import { Head, useForm, Link } from '@inertiajs/vue3';
import { ref, watch } from "vue";
import Multiselect from "@vueform/multiselect";
import "@vueform/multiselect/themes/default.css";
export default {
    setup(props) {
        // Inisialisasi
        let successData = ref(props.success_page);
        let productData = ref(props.product)
        let order = ref(props.order)
        // Bank Options
        let bankOptions = ref([]);

        let options = [];
        successData.value.accountBank.forEach((item, index) => {
            options[index] = {
                label: item.bank + ' - ' + item.atas_nama + ' ' + item.rekening,
                value: item
            }
        })

        bankOptions.value = options;
        console.log(props.success_page)

        // Inisialisasi Form
        let form = ref(useForm({
            atas_nama: '',
            transfer_ke: {},
            tanggal_transfer: "",
            jumlah_transfer: "",
            payment_method: "bank_transfer",
            bukti_transfer: null
        }));

        // Bukti transfer
        const handleBuktiTransfer = async (event) => {
            try {
                const file = event.target.files[0];
                const formData = new FormData();
                formData.append('image', file);
                const response = await axios.post('/api/products/upload', formData);
                const data = response.data;
                console.log(data)
                form.value.bukti_transfer = data.data;

            } catch (error) {
                console.error('error', error);
            }
        }

        const submit = () => {
            console.log(form.value);
            form.value
                .transform((data) => ({ ...data }))
                .post(route('create-confirmation', { url: productData.value.url, order_id: order.value.id }), {
                    onSuccess: () => {
                        // Handle success if needed
                    },
                });
        };

        return {
            bankOptions,
            handleBuktiTransfer,
            form,
            submit
        };
    },
    components: {
        Head,
        Multiselect,
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
                                    <form @submit.prevent="submit">
                                        <div class="mb-3">
                                            <label for="" class="fw-bold">Atas Nama Rekening <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" v-model="form.atas_nama" class="form-control"
                                                :class="{ 'is-invalid': form.errors.atas_nama }"
                                                placeholder="Masukan atas nama rekening">
                                            <div class="invalid-feedback">{{ form.errors.atas_nama }}</div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="choices-publish-status-input" class="form-label">Transfer Ke <span
                                                    class="text-danger">*</span></label>
                                            <Multiselect class="form-control mb-4" v-model="form.transfer_ke"
                                                placeholder="Pilih Bank Transfer"
                                                :class="{ 'is-invalid': form.errors.transfer_ke }" :close-on-select="true"
                                                :searchable="false" :create-option="true" :options="bankOptions" />
                                            <div class="invalid-feedback">{{ form.errors.transfer_ke }}</div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="" class="fw-bold">Tanggal Transfer<span
                                                    class="text-danger">*</span></label>
                                            <input type="date" v-model="form.tanggal_transfer" class="form-control"
                                                :class="{ 'is-invalid': form.errors.tanggal_transfer }">
                                            <div class="invalid-feedback">{{ form.errors.tanggal_transfer }}</div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="" class="fw-bold">Jumlah Transfer<span
                                                    class="text-danger">*</span></label>
                                            <input type="number" class="form-control" v-model="form.jumlah_transfer"
                                                :class="{ 'is-invalid': form.errors.jumlah_transfer }"
                                                placeholder="Contoh => 10000">
                                            <div class="invalid-feedback">{{ form.errors.jumlah_transfer }}</div>
                                        </div>
                                        <div class="mb-3" v-if="form.bukti_transfer">
                                            <img :src="form.bukti_transfer" alt="" class="img-fluid">
                                        </div>
                                        <div class="mb-3">
                                            <label for="" class="fw-bold">Bukti Transfer<span
                                                    class="text-danger">*</span></label>
                                            <input type="file" class="form-control" @change="handleBuktiTransfer"
                                                :class="{ 'is-invalid': form.errors.bukti_transfer }"
                                                placeholder="Contoh => 10000">
                                            <div class="invalid-feedback">{{ form.errors.bukti_transfer }}</div>
                                        </div>
                                        <div class="mb-3 w-100">
                                            <button class="btn btn-secondary w-100 d-block"
                                                :disabled="form.processing">KIRIM</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </BCardBody>
                    </BCard>
                </BCardBody>
            </BCard>
        </BCol>
    </BRow>
</template>