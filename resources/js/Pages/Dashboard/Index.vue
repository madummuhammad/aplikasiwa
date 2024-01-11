<script>
import { Link, Head } from '@inertiajs/vue3';
import getChartColorsArray from "@/common/getChartColorsArray";

import simplebar from "simplebar-vue";
import { CountTo } from "vue3-count-to";

import { Swiper, SwiperSlide } from "swiper/vue";
import { Autoplay, Mousewheel } from "swiper/modules";
import "swiper/css";
import "swiper/css/autoplay";
import "swiper/css/mousewheel";

import flatPickr from "vue-flatpickr-component";
import "flatpickr/dist/flatpickr.css";

import Layout from "@/Layouts/main.vue";
import Revenue from "./revenue.vue";
import SalesLocation from "./sales-location.vue";


export default {
    components: {
        CountTo,
        Layout,
        Swiper,
        SwiperSlide,
        flatPickr,
        simplebar,
        Revenue,
        SalesLocation,
        Link,
        Head
    },
    props: {
        user: Object,
    },
    data() {
        return {
            date: "2022-01-01 to 2022-01-31",
            config: {
                mode: "range",
            },
            series: [44, 55, 41, 17, 15],
            chartOptions: {
                labels: ["Direct", "Social", "Email", "Other", "Referrals"],
                chart: {
                    height: 333,
                    type: "donut",
                },
                legend: {
                    position: "bottom",
                },
                stroke: {
                    show: false,
                },
                dataLabels: {
                    dropShadow: {
                        enabled: false,
                    },
                },
                colors: getChartColorsArray('["--vz-primary", "--vz-success", "--vz-secondary", "--vz-info", "--vz-warning"]'),
            },
            Autoplay, Mousewheel
        };
    },
    methods: {
        rightcolumn() {
            if (document.querySelector('.layout-rightside-col').classList.contains('d-block')) {
                document.querySelector('.layout-rightside-col').classList.remove('d-block');
                document.querySelector('.layout-rightside-col').classList.add('d-none');
            } else {
                document.querySelector('.layout-rightside-col').classList.remove('d-none');
                document.querySelector('.layout-rightside-col').classList.add('d-block');
            }
        },

        resizerightcolumn() {
            const element = document.querySelector('.layout-rightside-col');

            if (element) {
                if (window.outerWidth < 1699) {
                    element.classList.remove("d-block");
                    element.classList.add("d-none");
                } else {
                    element.classList.add("d-block");
                    element.classList.remove("d-none");
                }
            }

            if (document.documentElement.getAttribute("data-layout") === "semibox") {
                element.classList.remove("d-block");
                element.classList.add("d-none");
            }
        },

        hiderightcolumn() {
            const element = document.querySelector('.layout-rightside-col');
            if (element.classList.contains('d-block')) {
                element.classList.remove("d-block");
                element.classList.add("d-none");
            }
        }
    },
    mounted() {
        window.addEventListener("resize", this.resizerightcolumn);
    }

};
</script>

<template>
    <Layout>

        <Head title="Dashboard" />
        <BRow>
            <BCol>
                <div class="h-100">
                    <BRow class="mb-3 pb-1">
                        <BCol cols="12">
                            <div class="d-flex align-items-lg-center flex-lg-row flex-column">
                                <div class="flex-grow-1">
                                    <h4 class="fs-16 mb-1">Selamat Datang, {{ user.name }}!</h4>
                                </div>
                                <div class="mt-3 mt-lg-0">
                                    <form action="javascript:void(0);">
                                        <BRow class="g-3 mb-0 align-items-center">
                                            <div class="col-auto">
                                                <Link href="/product/create" v-if="user.permission.product.create"
                                                    clsss="btn btn-secondary cursor-pointer">
                                                <i class="ri-add-circle-line align-middle me-1"></i>
                                                Tambah Produk
                                                </Link>
                                            </div>
                                        </BRow>
                                    </form>
                                </div>
                            </div>
                        </BCol>
                    </BRow>

                    <BRow>
                        <BCol xl="12">
                            <Revenue />
                        </BCol>
                    </BRow>
                    <BRow>
                        <BCol>
                            <BCard no-body class="card-animate">
                                <BCardBody>
                                    <div class="d-flex align-items-center">
                                        <div class="flex-grow-1 overflow-hidden">
                                            <p class="text-uppercase fw-medium text-muted text-truncate mb-0">
                                                Gross Revenue
                                            </p>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-end justify-content-between mt-4">
                                        <div>
                                            <h4 class="fs-20 fw-semibold ff-secondary mb-4">
                                                $<count-to :startVal='0' :endVal='559' :duration='5000'></count-to>k
                                            </h4>
                                        </div>
                                        <div class="avatar-sm flex-shrink-0">
                                            <span class="avatar-title bg-secondary-subtle rounded fs-3">
                                                <i class="bx bx-dollar-circle text-secondary"></i>
                                            </span>
                                        </div>
                                    </div>
                                </BCardBody>
                            </BCard>
                        </BCol>

                        <BCol>
                            <BCard no-body class="card-animate">
                                <BCardBody>
                                    <div class="d-flex align-items-center">
                                        <div class="flex-grow-1 overflow-hidden">
                                            <p class="text-uppercase fw-medium text-muted text-truncate mb-0">
                                                Shipping Cost
                                            </p>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-end justify-content-between mt-4">
                                        <div>
                                            <h4 class="fs-20 fw-semibold ff-secondary mb-4">
                                                <count-to :startVal='0' :endVal='36894' :duration='5000'></count-to>
                                            </h4>
                                        </div>
                                        <div class="avatar-sm flex-shrink-0">
                                            <span class="avatar-title bg-primary-subtle rounded fs-3">
                                                <i class="bx bx-shopping-bag text-primary"></i>
                                            </span>
                                        </div>
                                    </div>
                                </BCardBody>
                            </BCard>
                        </BCol>

                        <BCol>
                            <BCard no-body class="card-animate">
                                <BCardBody>
                                    <div class="d-flex align-items-center">
                                        <div class="flex-grow-1 overflow-hidden">
                                            <p class="text-uppercase fw-medium text-muted text-truncate mb-0">
                                                Net Revenue
                                            </p>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-end justify-content-between mt-4">
                                        <div>
                                            <h4 class="fs-20 fw-semibold ff-secondary mb-4">
                                                <count-to :startVal='0' :endVal='183' :duration='5000'></count-to>M

                                            </h4>
                                        </div>
                                        <div class="avatar-sm flex-shrink-0">
                                            <span class="avatar-title bg-success-subtle rounded fs-3">
                                                <i class="bx bx-user-circle text-success"></i>
                                            </span>
                                        </div>
                                    </div>
                                </BCardBody>
                            </BCard>
                        </BCol>

                        <BCol>
                            <BCard no-body class="card-animate">
                                <BCardBody>
                                    <div class="d-flex align-items-center">
                                        <div class="flex-grow-1 overflow-hidden">
                                            <p class="text-uppercase fw-medium text-muted text-truncate mb-0">
                                                Gross Profit
                                            </p>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-end justify-content-between mt-4">
                                        <div>
                                            <h4 class="fs-20 fw-semibold ff-secondary mb-4">
                                                $<count-to :startVal='0' :endVal='165' :duration='5000'></count-to>k
                                            </h4>
                                        </div>
                                        <div class="avatar-sm flex-shrink-0">
                                            <span class="avatar-title bg-warning-subtle rounded fs-3">
                                                <i class="bx bx-wallet text-warning"></i>
                                            </span>
                                        </div>
                                    </div>
                                </BCardBody>
                            </BCard>
                        </BCol>
                        <BCol>
                            <BCard no-body class="card-animate">
                                <BCardBody>
                                    <div class="d-flex align-items-center">
                                        <div class="flex-grow-1 overflow-hidden">
                                            <p class="text-uppercase fw-medium text-muted text-truncate mb-0">
                                                Net Profit
                                            </p>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-end justify-content-between mt-4">
                                        <div>
                                            <h4 class="fs-20 fw-semibold ff-secondary mb-4">
                                                $<count-to :startVal='0' :endVal='559' :duration='5000'></count-to>k
                                            </h4>
                                        </div>
                                        <div class="avatar-sm flex-shrink-0">
                                            <span class="avatar-title bg-secondary-subtle rounded fs-3">
                                                <i class="bx bx-dollar-circle text-secondary"></i>
                                            </span>
                                        </div>
                                    </div>
                                </BCardBody>
                            </BCard>
                        </BCol>
                    </BRow>
                    <!-- <BRow>
                        <BCol class="py-2">
                            <h3>Monitoring</h3>
                        </BCol>
                    </BRow>

                    <BRow>
                        <BCol xl="4">
                            <BCard no-body>
                                <BCardHeader class="align-items-center d-flex py-3">
                                    <BCardTitle class="mb-0 flex-grow-1"> Ticket </BCardTitle>

                                </BCardHeader>

                                <BCardBody>
                                    <div class="table-responsive table-card">
                                        <table class="table table-hover table-centered align-middle table-nowrap mb-0">
                                            <tbody>
                                                <tr>
                                                    <td>
                                                        All
                                                    </td>
                                                    <td>
                                                        <h5 class="fs-3 text-end my-1 fw-normal">0</h5>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        Open
                                                    </td>
                                                    <td>
                                                        <h5 class="fs-3 text-end my-1 fw-normal">0</h5>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        Solved
                                                    </td>
                                                    <td>
                                                        <h5 class="fs-3 text-end my-1 fw-normal">0</h5>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </BCardBody>
                            </BCard>
                        </BCol>
                        <BCol xl="4">
                            <BCard no-body>
                                <BCardHeader class="align-items-center d-flex py-3">
                                    <BCardTitle class="mb-0 flex-grow-1"> Problem </BCardTitle>

                                </BCardHeader>

                                <BCardBody>
                                    <div class="table-responsive table-card">
                                        <table class="table table-hover table-centered align-middle table-nowrap mb-0">
                                            <tbody>
                                                <tr>
                                                    <td>
                                                        Pending Claim
                                                    </td>
                                                    <td>
                                                        <h5 class="fs-3 text-end my-1 fw-normal">0</h5>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        Clamed
                                                    </td>
                                                    <td>
                                                        <h5 class="fs-3 text-end my-1 fw-normal">0</h5>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        Claim Rejected
                                                    </td>
                                                    <td>
                                                        <h5 class="fs-3 text-end my-1 fw-normal">0</h5>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </BCardBody>
                            </BCard>
                        </BCol>
                        <BCol xl="4">
                            <BCard no-body>
                                <BCardHeader class="align-items-center d-flex py-3">
                                    <BCardTitle class="mb-0 flex-grow-1"> Lost & Broken </BCardTitle>

                                </BCardHeader>

                                <BCardBody>
                                    <div class="table-responsive table-card">
                                        <table class="table table-hover table-centered align-middle table-nowrap mb-0">
                                            <tbody>
                                                <tr>
                                                    <td>
                                                        Pending Claim
                                                    </td>
                                                    <td>
                                                        <h5 class="fs-3 text-end my-1 fw-normal">0</h5>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        Clamed
                                                    </td>
                                                    <td>
                                                        <h5 class="fs-3 text-end my-1 fw-normal">0</h5>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        Claim Rejected
                                                    </td>
                                                    <td>
                                                        <h5 class="fs-3 text-end my-1 fw-normal">0</h5>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </BCardBody>
                            </BCard>
                        </BCol>

                        <BCol xl="4">
                            <BCard no-body class="card-height-100">
                                <BCardHeader class="align-items-center d-flex py-3">
                                    <BCardTitle class="mb-0 flex-grow-1">Produk Yang Menguntungkan</BCardTitle>

                                </BCardHeader>

                                <BCardBody>
                                    <div class="table-responsive table-card">
                                        <table class="table table-centered table-hover align-middle table-nowrap mb-0">
                                            <tbody>
                                                <tr>
                                                    <td colspan="4">
                                                        <div class="d-flex align-items-center">
                                                            <div class="flex-shrink-0 me-2">
                                                                <img src="@assets/images/companies/img-1.png" alt=""
                                                                    class="avatar-sm p-2" />
                                                            </div>
                                                            <div>
                                                                <h5 class="fs-14 my-1">
                                                                    <Link href="/ecommerce/seller-details"
                                                                        class="text-reset">Gantungann Kunci Uang Kuno, Mewah
                                                                    </Link>
                                                                </h5>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="flex-grow-1">
                                                                <span class="text-muted">Order</span>
                                                                <h5 class="fs-14 my-1">
                                                                    <p class="text-reset">1
                                                                    </p>
                                                                </h5>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="flex-grow-1">
                                                                <span class="text-muted">Paid</span>
                                                                <h5 class="fs-14 my-1">
                                                                    <p class="text-reset">1
                                                                    </p>
                                                                </h5>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="flex-grow-1">
                                                                <span class="text-muted">Paid Ratio</span>
                                                                <h5 class="fs-14 my-1">
                                                                    <p class="text-reset">1
                                                                    </p>
                                                                </h5>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="flex-grow-1">
                                                                <span class="text-muted">Net Revenue</span>
                                                                <h5 class="fs-14 my-1">
                                                                    <p class="text-reset">1
                                                                    </p>
                                                                </h5>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>

                                            </tbody>
                                        </table>
                                    </div>
                                </BCardBody>
                            </BCard>
                        </BCol>
                        <BCol xl="4">
                            <BCard no-body class="card-height-100">
                                <BCardHeader class="align-items-center d-flex py-3">
                                    <BCardTitle class="mb-0 flex-grow-1">Produk Permintaan Tertinggi</BCardTitle>

                                </BCardHeader>

                                <BCardBody>
                                    <div class="table-responsive table-card">
                                        <table class="table table-centered table-hover align-middle table-nowrap mb-0">
                                            <tbody>
                                                <tr>
                                                    <td colspan="4">
                                                        <div class="d-flex align-items-center">
                                                            <div class="flex-shrink-0 me-2">
                                                                <img src="@assets/images/companies/img-1.png" alt=""
                                                                    class="avatar-sm p-2" />
                                                            </div>
                                                            <div>
                                                                <h5 class="fs-14 my-1">
                                                                    <Link href="/ecommerce/seller-details"
                                                                        class="text-reset">Gantungann Kunci Uang Kuno, Mewah
                                                                    </Link>
                                                                </h5>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="flex-grow-1">
                                                                <span class="text-muted">Order</span>
                                                                <h5 class="fs-14 my-1">
                                                                    <p class="text-reset">1
                                                                    </p>
                                                                </h5>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="flex-grow-1">
                                                                <span class="text-muted">Paid</span>
                                                                <h5 class="fs-14 my-1">
                                                                    <p class="text-reset">1
                                                                    </p>
                                                                </h5>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="flex-grow-1">
                                                                <span class="text-muted">Paid Ratio</span>
                                                                <h5 class="fs-14 my-1">
                                                                    <p class="text-reset">1
                                                                    </p>
                                                                </h5>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="flex-grow-1">
                                                                <span class="text-muted">Net Revenue</span>
                                                                <h5 class="fs-14 my-1">
                                                                    <p class="text-reset">1
                                                                    </p>
                                                                </h5>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>

                                            </tbody>
                                        </table>
                                    </div>
                                </BCardBody>
                            </BCard>
                        </BCol>
                        <BCol xl="4">
                            <BCard no-body class="card-height-100">
                                <BCardHeader class="align-items-center d-flex py-3">
                                    <BCardTitle class="mb-0 flex-grow-1">Produk dengan Penjualan Tertinggi</BCardTitle>

                                </BCardHeader>

                                <BCardBody>
                                    <div class="table-responsive table-card">
                                        <table class="table table-centered table-hover align-middle table-nowrap mb-0">
                                            <tbody>
                                                <tr>
                                                    <td colspan="4">
                                                        <div class="d-flex align-items-center">
                                                            <div class="flex-shrink-0 me-2">
                                                                <img src="@assets/images/companies/img-1.png" alt=""
                                                                    class="avatar-sm p-2" />
                                                            </div>
                                                            <div>
                                                                <h5 class="fs-14 my-1">
                                                                    <Link href="/ecommerce/seller-details"
                                                                        class="text-reset">Gantungann Kunci Uang Kuno, Mewah
                                                                    </Link>
                                                                </h5>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="flex-grow-1">
                                                                <span class="text-muted">Order</span>
                                                                <h5 class="fs-14 my-1">
                                                                    <p class="text-reset">1
                                                                    </p>
                                                                </h5>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="flex-grow-1">
                                                                <span class="text-muted">Paid</span>
                                                                <h5 class="fs-14 my-1">
                                                                    <p class="text-reset">1
                                                                    </p>
                                                                </h5>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="flex-grow-1">
                                                                <span class="text-muted">Paid Ratio</span>
                                                                <h5 class="fs-14 my-1">
                                                                    <p class="text-reset">1
                                                                    </p>
                                                                </h5>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="flex-grow-1">
                                                                <span class="text-muted">Net Revenue</span>
                                                                <h5 class="fs-14 my-1">
                                                                    <p class="text-reset">1
                                                                    </p>
                                                                </h5>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>

                                            </tbody>
                                        </table>
                                    </div>
                                </BCardBody>
                            </BCard>
                        </BCol>
                    </BRow> -->
                </div>
            </BCol>
        </BRow>
    </Layout>
</template>