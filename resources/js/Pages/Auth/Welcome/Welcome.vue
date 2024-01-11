<script>
import { Link, Head } from '@inertiajs/vue3';
import { onMounted } from 'vue';
import { Swiper, SwiperSlide } from "swiper/vue";
import { Autoplay, Navigation, Pagination } from "swiper/modules";

import "swiper/css";
import "swiper/css/autoplay";
import 'swiper/css/navigation';
import 'swiper/css/pagination';

export default {
    props: {
        store: Object,
    },
    data() {
        return {
            Autoplay: Autoplay, Pagination: Pagination, Navigation: Navigation,
            pagination: {
                clickable: true,
                el: '.swiper-pagination',
                renderBullet: function (index, className) {
                    return '<span class=' + className + '>' + (index + 1) + '</span>';
                },
            },
        }
    },
    components: {
        Swiper,
        SwiperSlide,
        Link, Head
    },
    beforeCreate() {
        console.log(this.store)
    }

};
</script>

<template>
    <Head title="Success Message" />

    <div class="auth-page-wrapper auth-bg-cover py-5 d-flex justify-content-center align-items-center min-vh-100">
        <div class="bg-overlay"></div>
        <div class="auth-page-content overflow-hidden pt-lg-5">
            <BContainer>
                <BRow>
                    <BCol lg="12">
                        <BCard no-body class="overflow-hidden">
                            <BRow class="justify-content-center g-0">
                                <BCol lg="6">
                                    <div class="p-lg-5 p-4 text-center">
                                        <div class="avatar-lg mx-auto mt-2">
                                            <div class="avatar-title bg-light text-success display-3 rounded-circle">
                                                <img src="@assets/images/store.svg" alt="">
                                            </div>
                                        </div>
                                        <div class="mt-4 pt-2">
                                        <h4>Informasi Toko</h4>
                                        <p class="text-muted mx-4">Lengkapi nama toko, kota pengiriman, kurir, serta
                                            akun bank sebagai informasi bagi pelanggan.</p>
                                        <div class="mt-4">
                                            <div v-if="!store">
                                                <Link href="/welcome/store" class="btn btn-secondary btn-lg w-100">
                                                Lengkapi
                                                Data</Link>
                                            </div>
                                            <div v-else>
                                                <button class="btn btn-secondary disabled btn-lg w-100">
                                                    Lengkapi
                                                    Data</button>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </BCol>
                                <BCol lg="6">
                                    <div class="p-lg-5 p-4 text-center">
                                        <div class="avatar-lg mx-auto mt-2">
                                            <div class="avatar-title bg-light text-success display-3 rounded-circle">
                                                <img src="@assets/images/store.svg" alt="">
                                            </div>
                                        </div>
                                        <div class="mt-4 pt-2">
                                            <h4>Gudang</h4>
                                            <p class="text-muted mx-4">Lengkapi alamat gudang untuk menjadi tempat
                                                penjemputan barang oleh pihak ekspedisi.</p>
                                            <div class="mt-4">
                                                <Link href="/welcome/warehouse" class="btn btn-secondary btn-lg w-100">
                                                Lengkapi
                                                Data</Link>
                                            </div>
                                        </div>
                                    </div>
                                </BCol>
                                <!-- <BCol lg="4">
                                                                                                                                                                        <div class="p-lg-5 p-4 text-center">
                                                                                                                                                                            <div class="avatar-lg mx-auto mt-2">
                                                                                                                                                                                <div class="avatar-title bg-light text-success display-3 rounded-circle">
                                                                                                                                                                                    <img src="@assets/images/store.svg" alt="">
                                                                                                                                                                                </div>
                                                                                                                                                                            </div>
                                                                                                                                                                            <div class="mt-4 pt-2">
                                                                                                                                                                                <h4>Pengiriman</h4>
                                                                                                                                                                                <p class="text-muted mx-4">Unggah foto KTP dan baca ketentuan untuk mulai
                                                                                                                                                                                    mengaktifkan fitur Logistics OrderOnline.id.
                                                                                                                                                                                </p>
                                                                                                                                                                                <div class="mt-4">
                                                                                                                                                                                    <Link href="/auth/signin-basic" class="btn btn-secondary btn-lg w-100">
                                                                                                                                                                                    Lengkapi
                                                                                                                                                                                    Data</Link>
                                                                                                                                                                                </div>
                                                                                                                                                                            </div>
                                                                                                                                                                        </div>
                                                                                                                                                                    </BCol> -->
                            </BRow>
                        </BCard>
                    </BCol>

                </BRow>
            </BContainer>
        </div>
    </div>
</template>