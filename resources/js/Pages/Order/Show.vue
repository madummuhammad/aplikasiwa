<script>
import { Global } from '../../global'
export default {
    props: {
        order: Object,
        checkout: Object
    },
    setup(props) {
        const global = Global();
        // let orderData=
        let success_page = props.checkout.success_page
        console.log(props.order)
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

        return {
            formatDate,
            global,
            success_page
        }
    }
}
</script>
<template>
    <Head :title="'Detail Order'" />
    <!-- <PageHeader title="Create Product" pageTitle="Product" /> -->

    <h1 class="text-center mt-3 mb-3">Detail Order #{{ order.order_number }}</h1>
    <BRow class="d-flex justify-content-center mb-5">
        <!-- End Page Checkout -->
        <BCol lg="7" xs="12">
            <div class="card pb-5">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5>
                            <span class="fw-bold">Order Number:</span> #{{ order.order_number }}
                        </h5>
                        <div class="bg-light border border-1 fw-bold p-2" v-if="order.payment_status == 'unpaid'">
                            UNPAID
                        </div>
                        <div class="bg-success text-white border border-1 fw-bold p-2"
                            v-if="order.payment_status == 'paid'">
                            PAID
                        </div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <p>
                            <span class="fw-bold">Tanggal Pemesanan:</span> {{ formatDate(order.created_at) }}
                        </p>
                        <div class="py-2 px-3 fw-bold text-uppercase" :class="{
                            'bg-info-subtle text-info': order.status == 'process',
                            'bg-success-subtle text-success': order.status == 'complete',
                            'bg-danger-subtle text-danger': order.status == 'cancel',
                            'bg-secondary-subtle text-secondary': order.status == 'refund',
                            'bg-warning-subtle text-warning': order.status == 'pending',
                        }">{{ order.status }}</div>
                        <!-- <div class="bg-light border border-1 fw-bold p-2">
                            Menunggu Konfirmasi
                        </div> -->
                        <!-- <div class="bg-success text-white border border-1 fw-bold p-2">
                            PAID
                        </div> -->
                    </div>
                    <div class="mb-3">
                        <table>
                            <tr v-for="(item, index) in order.fields">
                                <td><span class="fw-bold">{{ item.label }}:</span> {{ item.value }}</td>
                            </tr>
                        </table>
                    </div>
                    <div>
                        <div class="d-flex justify-content-between py-2" v-for="(item, index) in order.order_item">
                            <div class="d-flex">
                                {{ item.product.name }}
                                <span v-if="item.qty == 0">1</span>
                                <span v-else>{{ item.qty }}</span>
                            </div>
                            <div>
                                <span v-if="item.total_price == 0">
                                    <span v-if="item.product.price_sale_status == true">
                                        {{ global.formatNumber(item.price_sale) }}
                                    </span>
                                    <span v-else>{{ global.formatNumber(item.normal_price) }}</span>
                                </span>
                                <span v-else>{{ global.formatNumber(item.total_price) }}</span>
                            </div>
                        </div>
                        <!-- <hr> -->
                        <!-- <div class="d-flex justify-content-between">
                            <div class="d-flex">
                                Madummadum (1x)
                            </div>
                            <div>
                                asdf
                            </div>
                        </div>-->
                        <!-- <hr> -->
                        <div style="border:1px solid black"></div>
                        <div class="d-flex justify-content-between py-2">
                            <div class="d-flex fw-bold">
                                Total Biaya
                            </div>
                            <div class="fw-bold">
                                {{ global.formatNumber(order.total_price) }}
                            </div>
                        </div>
                        <div style="border:1px solid black"></div>
                    </div>
                    <template v-if="order.payment_status == 'unpaid'">
                        <p class="mt-3 text-center">Pesanan Anda sudah kami terima, silakan transfer senilai</p>
                        <h1 class="text-center text-success fw-bold">{{ global.formatNumber(order.total_price) }}</h1>
                        <p class="mt-3 text-center">Ke salah satu bank dibawah ini</p>
                        <div class="row" v-if="success_page.bodyText.payment_type == 'bank_transfer'">
                            <div class="col-12 col-lg-6" v-for="(item, index) in success_page.accountBank">
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
                            <p class="text-center" v-if="success_page.bodyText.payment_type == 'bank_transfer'">
                                Konfirmasi
                                pembayaran anda di : <a
                                    :href="global.urlCheckout + order.product.url + '/payment_confirmation/' + order.id"
                                    target="_blank" class="text-secondary">Konfirmasi
                                    Pembayaran</a></p>
                        </div>
                    </template>
                </div>
            </div>
        </BCol>
    </BRow>
</template>