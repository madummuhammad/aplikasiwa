<style>
.submission-action {
    display: block;
    position: relative;
    width: 28px;
    height: 28px;
    border: 0;
    border-radius: 50%;
    background-color: #d7d7da;
    color: #fff;
    text-align: center;
    vertical-align: middle;
    cursor: pointer;
}

.submission-action-chat:before {
    content: "";
    position: absolute;
    bottom: -2px;
    left: 0;
    width: 0;
    height: 0;
    pointer-events: none;
    border-left: 5px solid transparent;
    border-right: 5px solid transparent;
    border-top: 8px solid #d7d7da;
    -webkit-transform: rotate(40deg);
    transform: rotate(40deg);
}
</style>
<script>
import { Head, useForm, Link } from '@inertiajs/vue3';
import Layout from "@/Layouts/main.vue";
import PageHeader from "@/Components/page-header.vue";
import Multiselect from "@vueform/multiselect";
import "@vueform/multiselect/themes/default.css";
import flatPickr from "vue-flatpickr-component";
import "flatpickr/dist/flatpickr.css";
import Swal from "sweetalert2";
import { ref, watch } from "vue";
import axios from 'axios';
import animationData from "@/Components/widgets/msoeawqm.json";
import animationData1 from "@/Components/widgets/gsqxdxog.json";
import Lottie from "@/Components/widgets/lottie.vue";
import { Global } from '../../global'

export default {
    props: {
        orders: Object,
        store: Object,
        total: Object,
        params: Object,
        checkout: Object,
        product: Object,
        user: Object
    },
    setup(props) {
        const global = Global();
        // Modal Detail
        let total = ref(props.total);
        let productData = ref(props.product)
        let checkoutData = ref(props.checkout)
        let filterCheckout = ref([]);
        let addOrderForm = ref(useForm({
            product_id: "",
            checkout_id: ""
        }));

        const selectProduct = () => {
            console.log(addOrderForm.value)

            filterCheckout.value = checkoutData.value.filter((item) => {
                return item.product_id === addOrderForm.value.product_id;
            });

            console.log(checkoutData.value)
            console.log(filterCheckout.value)
        }

        const addOrder = () => {

            if (addOrderForm.value.product_id == "") {
                addOrderForm.value.errors.product_id = 'Produk tidak boleh kosong'

                return;
            }
            if (addOrderForm.value.checkout_id == "") {
                addOrderForm.value.errors.checkout_id = 'Checkout tidak boleh kosong'
                return;
            }
            console.log(addOrderForm.value)
            const product = productData.value.filter((item) => {
                return item.id === addOrderForm.value.product_id
            });


            console.log('product', product[0].url)

            const redirectUrl = global.urlCheckout + product[0].url + "?checkout_id=" + addOrderForm.value.checkout_id;
            window.open(redirectUrl);
        }

        var orderDetailModalShow = ref(false)

        let orderDetail = ref([]);
        const openModalDetail = (item) => {
            orderDetailModalShow.value = true;
            orderDetail.value = item
            console.log('orderDetail',orderDetail.value)
        }
        let orders = ref(props.orders);
        orders.value.forEach((item, index) => {
            const name = item.fields.filter(item => item.name === 'name');
            if (name) {
                item.name = name[0].value
            }

            const wa = item.fields.filter(item => item.name === 'wa');
            if (wa) {
                item.wa = wa[0].value
            }
        })

        console.log('orders', orders.value)
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

        const markOrderStatus = async (status, order_id) => {
            try {
                let postData = {
                    status: status,
                    order_id: order_id
                }
                const response = await axios.post('/api/order/change_order_status', postData);
                const data = response.data;

                if (data.status == 'success') {
                    getList();
                }

                console.log(data)

            } catch (error) {
                console.error('error', error);
            }

        };

        const markPaymentStatus = async (status, order_id) => {
            try {
                let postData = {
                    status: status,
                    order_id: order_id
                }
                const response = await axios.post('/api/order/change_payment_status', postData);
                const data = response.data;

                if (data.status == 'success') {
                    getList();
                }

                console.log(data)

            } catch (error) {
                console.error('error', error);
            }

        };

        const getList = async (status) => {
            try {
                const response = await axios.get('/api/order/list');
                const data = response.data;

                orders.value = data.data;
                orders.value.forEach((item, index) => {
                    const name = item.fields.filter(item => item.name === 'name');
                    if (name) {
                        item.name = name[0].value
                    }

                    const wa = item.fields.filter(item => item.name === 'wa');
                    if (wa) {
                        item.wa = wa[0].value
                    }
                })

                total.value = data.total;
                console.log(data)

            } catch (error) {
                console.error('error', error);
            }

        };
        return {
            global,
            orders,
            formatDate,
            total,

            // mark
            markOrderStatus,
            markPaymentStatus,

            // modal
            orderDetailModalShow,
            openModalDetail,
            orderDetail,

            productData,
            checkoutData,
            filterCheckout,
            addOrderForm,
            selectProduct,
            addOrder
        }
    },
    data(props) {
        console.log('props', props.orders)
        var searchQuery = props.params.product_id;

        return {
            createModal: false,
            editOrderModal: false,
            page: 1,
            perPage: 8,
            pages: [],
            value: null,
            status: null,
            status1: null,
            statuscategory: 'All',
            value1: null,
            searchQuery: searchQuery,
            config: {
                wrap: true, // set wrap to true only when using 'input-group'
                altFormat: "M j, Y",
                altInput: true,
                dateFormat: "d M, Y",
                mode: "range",
            },
            timeConfig: {
                enableTime: false,
                dateFormat: "d M, Y",
            },
            date: null,
            date2: null,
            defaultOptions: {
                animationData: animationData
            },
            defaultOptions1: {
                animationData: animationData1
            },
            // orders: orders,
            isStatus: null,
            isPayment: null,

            //
            submitted: false,

            dataEdit: false,
            deleteModal: false,
            event: {
                id: "",
                customer: "",
                orderId: "",
                orderDate: "",
                amount: "",
                payment: "",
                product: "",
                status: ""
            },
            //
        };
    },
    components: {
        Layout,
        PageHeader,
        lottie: Lottie,
        Multiselect,
        flatPickr,
        Head, Link
    },
    computed: {
        displayedPosts() {
            return this.paginate(this.orders);
        },
        resultQuery() {
            if (this.searchQuery) {
                const search = this.searchQuery.toLowerCase();
                return this.displayedPosts.filter((data) => {
                    return (
                        data.order_number.toLowerCase().includes(search) ||
                        data.name.toLowerCase().includes(search) ||
                        data.product_id.toLowerCase().includes(search)
                    );
                });
            } else if (this.date !== null) {
                if (this.date !== null) {
                    var date1 = this.date.split(" to ")[0];
                    var date2 = this.date.split(" to ")[1];
                }
                return this.displayedPosts.filter((data) => {
                    if (
                        new Date(data.created_at) >= new Date(date1) &&
                        new Date(data.created_at) <= new Date(date2)
                    ) {
                        return data;
                    } else {
                        return null;
                    }
                });
            } else if (this.status !== null || this.value !== null) {
                return this.displayedPosts.filter((data) => {
                    if (this.status != null && this.value != null) {
                        if (this.status === data.status) {
                            if (this.value === data.payment_status) {
                                return data;
                            } else if (this.value == 'All') {
                                return data;
                            }
                        } else if (this.value == 'All' && this.status == 'All') {
                            return data;
                        } else if (this.status == 'All') {
                            if (this.value === data.payment_status) {
                                return data;
                            }
                        }
                    } else if (this.status === data.status) {
                        return data;
                    } else if (this.value === data.payment_status) {
                        return data;
                    }
                    else {
                        return null;
                    }
                });
            }
            else {
                return this.displayedPosts;
            }
        },
    },
    watch: {
        orders() {
            this.setPages();
        },
    },
    created() {
        this.setPages();
    },
    filters: {
        trimWords(value) {
            return value.split(" ").splice(0, 20).join(" ") + "...";
        },
    },

    mounted() {
    },

    methods: {

        onChangeStatus(e) {
            this.isStatus = e;
        },
        onChangePayment(e) {
            this.isPayment = e;
        },
        setPages() {
            let numberOfPages = Math.ceil(this.orders.length / this.perPage);
            this.pages = [];
            for (let index = 1; index <= numberOfPages; index++) {
                this.pages.push(index);
            }
        },
        paginate(orders) {
            let page = this.page;
            let perPage = this.perPage;
            let from = page * perPage - perPage;
            let to = page * perPage;
            return orders.slice(from, to);
        },

        // 
        handleSubmit() {
            if (this.dataEdit) {
                this.submitted = true;
                if (this.submitted && (this.event.customer && this.event.payment && this.event.orderDate && this.event.amount && this.event.payment && this.event.status)) {
                    this.orders = this.orders.map(item => item.id.toString() == this.event.id.toString() ? { ...item, ...this.event } : item);
                    this.createModal = false;
                }
            } else {
                this.submitted = true;
                if (this.submitted && (this.event.customer && this.event.payment && this.event.orderDate && this.event.amount && this.event.payment && this.event.status)) {
                    const data = {
                        id: (Math.floor(Math.random() * 100 + 20) - 20),
                        orderId: "#VZ2" + (Math.floor(Math.random() * 100 + 20) - 20),
                        ...this.event
                    };
                    this.orders = [data, ...this.orders];
                    this.createModal = false;
                }
            }
        },

        editDetails(data) {
            this.dataEdit = true;
            this.createModal = true;
            this.event = { ...data };

            this.submitted = false;
        },

        toggleModal() {
            this.createModal = true;
            this.dataEdit = false;
            this.event = {};

            this.submitted = false;
        },

        deleteModalToggle(data) {
            this.deleteModal = true;
            this.event.id = data.id;
        },

        deleteData() {
            if (this.event.id) {
                this.orders = this.orders.filter((item) => item.id != this.event.id);
                this.deleteModal = false;
            }
        },
        //

        onSort(column) {
            this.direction = this.direction === 'asc' ? 'desc' : 'asc';
            const sortedArray = [...this.orders];
            sortedArray.sort((a, b) => {
                const res = a[column] < b[column] ? -1 : a[column] > b[column] ? 1 : 0;
                return this.direction === 'asc' ? res : -res;
            });
            this.orders = sortedArray;
        },


        deleteMultiple() {
            let ids_array = [];
            var items = document.getElementsByName("chk_child");
            items.forEach(function (ele) {
                if (ele.checked == true) {
                    var trNode = ele.parentNode.parentNode.parentNode;
                    var id = trNode.querySelector(".id a").innerHTML;
                    ids_array.push(id);
                }
            });
            if (typeof ids_array !== "undefined" && ids_array.length > 0) {
                Swal.fire({
                    title: "Are you sure?",
                    text: "You won't be able to revert this!",
                    icon: "warning",
                    showCancelButton: true,
                    cancelButtonColor: "#f46a6a",
                    confirmButtonColor: "#34c38f",
                    confirmButtonText: "Yes, delete it!",
                }).then((result) => {
                    if (result.value) {
                        var cusList = this.orders;
                        ids_array.forEach(function (id) {
                            cusList = cusList.filter(function (orders) {
                                return orders.orderId != id;
                            });
                        });
                        this.orders = cusList;
                        document.getElementById("checkAll").checked = false;
                        var itemss = document.getElementsByName("chk_child");
                        itemss.forEach(function (ele) {
                            if (ele.checked == true) {
                                ele.checked = false;
                                ele.closest("tr").classList.remove("table-active");
                            }
                        });
                    }
                });
            } else {
                Swal.fire({
                    title: "Please select at least one checkbox",
                    confirmButtonClass: "btn btn-info",
                    buttonsStyling: true,
                    showCloseButton: true,
                });
            }
        },

        SearchData() {
            this.resultQuery;
            this.date = this.date2;
            this.status = this.status1;
            this.value = this.value1;
        },
        changecategory(data) {
            this.statuscategory = data;
        }
    },
};
</script>

<template>
    <Layout>

        <Head title="Orders" />
        <h3 class="mb-3">Orders</h3>
        <!-- <PageHeader title="Orders" /> -->
        <BRow>
            <BRow>
                <BCol>
                    <BCard no-body class="card-animate">
                        <BCardBody>
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1 overflow-hidden">
                                    <p class="text-uppercase fw-medium text-muted text-truncate mb-0">
                                        Total Order
                                    </p>
                                </div>
                            </div>
                            <div class="d-flex align-items-end justify-content-between mt-4">
                                <div>
                                    <h4 class="fs-20 fw-semibold ff-secondary mb-4">
                                        {{ total.order }}
                                    </h4>
                                </div>
                                <div class="avatar-sm flex-shrink-0">
                                    <!-- <span class="avatar-title bg-secondary-subtle rounded fs-3">
                                        <i class="bx bx-dollar-circle text-secondary"></i>
                                    </span> -->
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
                                        Total Bayar
                                    </p>
                                </div>
                            </div>
                            <div class="d-flex align-items-end justify-content-between mt-4">
                                <div>
                                    <h4 class="fs-20 fw-semibold ff-secondary mb-4">
                                        {{ global.formatNumber(total.paid) }}
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
                                        Rasio Bayar
                                    </p>
                                </div>
                            </div>
                            <div class="d-flex align-items-end justify-content-between mt-4">
                                <div>
                                    <h4 class="fs-20 fw-semibold ff-secondary mb-4">
                                        {{ total.rasio }} %

                                    </h4>
                                </div>
                                <div class="avatar-sm flex-shrink-0">
                                    <!-- <span class="avatar-title bg-success-subtle rounded fs-3">
                                        <i class="bx bx-user-circle text-success"></i>
                                    </span> -->
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
                                        Order Belum Bayar
                                    </p>
                                </div>
                            </div>
                            <div class="d-flex align-items-end justify-content-between mt-4">
                                <div>
                                    <h4 class="fs-20 fw-semibold ff-secondary mb-4">
                                        {{ global.formatNumber(total.unpaid) }}
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
            </BRow>
            <BCol lg="12">
                <BCard no-body id="orderList">
                    <BCardHeader class="border-0">
                        <BRow class="align-items-center gy-3">
                            <BCol cols="sm">
                                <h5 class="card-title mb-0">Order History</h5>
                            </BCol>
                            <BCol sm="auto">
                                <div class="d-flex gap-1 flex-wrap">
                                    <BButton type="button" variant="success" v-if="user.permission.order.create"
                                        class="add-btn" id="create-btn" @click="toggleModal">
                                        <i class="ri-add-line align-bottom me-1"></i> Input Order
                                    </BButton>
                                    <BButton type="button" variant="info" class="ms-1">
                                        <i class="ri-file-download-line align-bottom me-1"></i> Input Bulk
                                    </BButton>
                                    <BButton type="button" variant="info" class="ms-1">
                                        <i class="ri-file-download-line align-bottom me-1"></i> Exspor
                                    </BButton>
                                    <BButton type="button" variant="info" class="ms-1">
                                        <i class="ri-file-download-line align-bottom me-1"></i> Upload Receipt Number
                                    </BButton>
                                    <BButton variant="soft-danger" class="me-1" id="remove-actions" @click="deleteMultiple">
                                        <i class="ri-delete-bin-2-line"></i>
                                    </BButton>
                                </div>
                            </BCol>
                        </BRow>
                    </BCardHeader>
                    <BCardBody class="border border-dashed border-end-0 border-start-0">
                        <b-form>
                            <BRow class="g-3">
                                <BCol xxl="5" sm="6">
                                    <div class="search-box">
                                        <input type="text" class="form-control search" v-model="searchQuery"
                                            placeholder="Cari order number atau customer..." />
                                        <i class="ri-search-line search-icon"></i>
                                    </div>
                                </BCol>
                                <BCol xxl="2" sm="6">
                                    <div>
                                        <flat-pickr placeholder="Pilih Rentang Tanggal" v-model="date2" :config="config"
                                            class="form-control flatpickr-input" id="demo-datepicker"></flat-pickr>
                                    </div>
                                </BCol>
                                <BCol xxl="2" sm="4">
                                    <div>
                                        <!-- //Process, Pending, Complete, Cancel, Refund -->
                                        <Multiselect class="form-control cursor-pointer" v-model="status1"
                                            :close-on-select="true" placeholder="Status Pesanan" :searchable="false"
                                            :create-option="false" @input="onChangePayment" :options="[
                                                { value: '', label: 'Status' },
                                                { value: 'All', label: 'All' },
                                                { value: 'process', label: 'Process' },
                                                { value: 'pending', label: 'Pending' },
                                                { value: 'cancelled', label: 'Cancelled' },
                                                { value: 'complete', label: 'Complete' },
                                                { value: 'cancel', label: 'Cancel' },
                                                { value: 'refund', label: 'Refund' },
                                            ]" />
                                    </div>
                                </BCol>
                                <BCol xxl="2" sm="4">
                                    <div>
                                        <Multiselect class="form-control cursor-pointer" v-model="value1"
                                            :close-on-select="true" placeholder="Status Pembayaran" :searchable="false"
                                            :create-option="false" @input="onChangeStatus" :options="[
                                                { value: '', label: 'Status Payment' },
                                                { value: 'unpaid', label: 'Unpaid' },
                                                { value: 'confirmed', label: 'Confirmed' },
                                                { value: 'paid', label: 'Paid' },
                                            ]" />
                                    </div>
                                </BCol>
                                <BCol xxl="1" sm="4">
                                    <div>
                                        <BButton type="button" variant="primary" class="w-100" @click="SearchData">
                                            <i class="ri-equalizer-fill me-1 align-bottom"></i>
                                            Filters
                                        </BButton>
                                    </div>
                                </BCol>
                            </BRow>
                        </b-form>
                    </BCardBody>
                    <BCardBody class="pt-0 mt-3">
                        <div>
                            <div class="table-responsive table-card mb-1" style="min-height:300px">
                                <table class="table table-nowrap align-middle" id="orderTable">
                                    <thead class="text-muted table-light">
                                        <tr class="text-uppercase">
                                            <!-- <th scope="col" style="width: 25px">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="checkAll"
                                                        value="option" />
                                                </div>
                                            </th> -->
                                            <th data-sort="id" @click="onSort('orderId')">Order ID</th>
                                            <th data-sort="customer_name" @click="onSort('customer')">Nama
                                            </th>
                                            <th data-sort="product_name" @click="onSort('orderDate')">Status
                                            </th>
                                            <th data-sort="product_name" @click="onSort('orderDate')">Status
                                                Pembayaran
                                            </th>
                                            <th data-sort="date" @click="onSort('customer')">Tanggal</th>
                                            <th data-sort="amount" @click="onSort('amount')">Follow-Up</th>
                                            <th data-sort="payment" @click="onSort('payment')">Payment Method
                                            </th>
                                            <th data-sort="city">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody class="list form-check-all" v-for="(data, index) of resultQuery" :key="index">
                                        <tr v-if="statuscategory == 'All' || statuscategory == data.status">
                                            <!-- <th scope="row">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="chk_child"
                                                        value="option1" />
                                                </div>
                                            </th> -->
                                            <td class="id">
                                                <div class="fw-medium link-primary cursor-pointer"
                                                    @click="openModalDetail(data)">#{{
                                                        data.order_number }}
                                                </div>
                                                <a target="_blank" :href="global.urlCheckout + 'orders/' + data.id"><i
                                                        class="bx bx-file"></i></a>
                                            </td>
                                            <td class="customer_name">
                                                <div v-if="data.name">
                                                    {{ data.name }}
                                                </div>
                                                <div v-if="data.wa">
                                                    {{ data.wa }}
                                                </div>
                                            </td>
                                            <td class="status">
                                                <span class="badge text-uppercase" :class="{
                                                    'bg-info-subtle text-info': data.status == 'process',
                                                    'bg-success-subtle text-success': data.status == 'complete',
                                                    'bg-danger-subtle text-danger': data.status == 'cancel',
                                                    'bg-secondary-subtle text-secondary': data.status == 'refund',
                                                    'bg-warning-subtle text-warning': data.status == 'pending',
                                                }">{{ data.status }}</span>

                                            </td>
                                            <td class="status">
                                                <span class="badge text-uppercase" :class="{
                                                    'bg-success-subtle text-success': data.payment_status == 'paid',
                                                    'bg-info-subtle text-info': data.payment_status == 'confirmed',
                                                    'bg-danger-subtle text-danger': data.payment_status == 'unpaid',
                                                }">{{ data.payment_status }}</span>

                                            </td>
                                            <td class="date">
                                                {{ formatDate(data.created_at) }}
                                            </td>
                                            <td>
                                                <div class="d-flex">
                                                    <button type="button"
                                                        class="submission-action me-2 bg-warning warning has-tooltip"
                                                        data-original-title="null">
                                                        W
                                                    </button>
                                                    <button type="button"
                                                        class="submission-action me-2 submission-action-chat warning has-tooltip"
                                                        data-original-title="null">
                                                        1
                                                    </button>
                                                    <button type="button"
                                                        class="submission-action me-2 submission-action-chat warning has-tooltip"
                                                        data-original-title="null">
                                                        2
                                                    </button>
                                                    <button type="button"
                                                        class="submission-action me-2 submission-action-chat warning has-tooltip"
                                                        data-original-title="null">
                                                        3
                                                    </button>
                                                    <button type="button"
                                                        class="submission-action me-2 submission-action-chat warning has-tooltip"
                                                        data-original-title="null">
                                                        4
                                                    </button>
                                                </div>
                                            </td>
                                            <td class="payment">{{ data.payment_method }}</td>
                                            <td>
                                                <div class="d-flex flex-wrap gap-3">
                                                    <!-- <BDropdown size="sm" variant="primary">
                                                        <template v-slot:button-content>
                                                            TINDAKAN
                                                        </template>
                                                        <BDropdownItemButton>Action</BDropdownItemButton>
                                                        <BDropdownItemButton>Another action</BDropdownItemButton>
                                                        <BDropdownItemButton>Something else here</BDropdownItemButton>
                                                    </BDropdown> -->

                                                    <BButtonGroup class="dropend"
                                                        v-if="user.permission.order.update == true">
                                                        <BDropdown text="Tindakan" dropend variant="secondary">
                                                            <BDropdownItem @click="markOrderStatus('pending', data.id)"
                                                                class="cursor-pointer">Mark As Pending</BDropdownItem>
                                                            <BDropdownItem @click="markOrderStatus('process', data.id)"
                                                                class="cursor-pointer">Mark As Process</BDropdownItem>
                                                            <BDropdownItem @click="markOrderStatus('complete', data.id)"
                                                                class="cursor-pointer">Mark As Complete</BDropdownItem>
                                                            <BDropdownItem @click="markOrderStatus('refund', data.id)"
                                                                class="cursor-pointer">Mark As Refund</BDropdownItem>
                                                            <BDropdownItem @click="markOrderStatus('cancel', data.id)"
                                                                class="cursor-pointer">Mark As Cancel</BDropdownItem>
                                                            <hr>
                                                            <BDropdownItem @click="markPaymentStatus('paid', data.id)"
                                                                class="cursor-pointer">Mark As Paid</BDropdownItem>
                                                            <BDropdownItem @click="markPaymentStatus('unpaid', data.id)"
                                                                class="cursor-pointer">Mark As Unpaid</BDropdownItem>
                                                        </BDropdown>
                                                    </BButtonGroup>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <div class="noresult" v-if="resultQuery.length < 1">
                                    <div class="text-center">
                                        <lottie class="avatar-xl" colors="primary:#121331,secondary:#08a88a"
                                            :options="defaultOptions" :height="75" :width="75" />
                                        <h5 class="mt-2">Sorry! No Result Found</h5>
                                        <p class="text-muted">
                                            We've searched more than 150+ Orders We did not find any
                                            orders for you search.
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end" v-if="resultQuery.length >= 1">
                                <div class="pagination-wrap hstack gap-2">
                                    <BLink class="page-item pagination-prev" href="#" :disabled="page <= 1" @click="page--">
                                        Previous
                                    </BLink>
                                    <ul class="pagination listjs-pagination mb-0">
                                        <li :class="{ active: pageNumber == page, disabled: pageNumber == '...', }"
                                            v-for="(pageNumber, index) in pages" :key="index" @click="page = pageNumber">
                                            <BLink class="page" href="#" data-i="1" data-page="8">{{ pageNumber }}</BLink>
                                        </li>
                                    </ul>
                                    <BLink class="page-item pagination-next" href="#" :disabled="page >= pages.length"
                                        @click="page++">
                                        Next
                                    </BLink>
                                </div>
                            </div>
                        </div>
                    </BCardBody>
                </BCard>
            </BCol>
        </BRow>
        <BModal v-model="orderDetailModalShow" hide-footer title="Detail Order" size="lg" class="v-modal-custom">
            <BRow>
                <!-- End Page Checkout -->
                <BCol lg="12" xs="12">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>{{ store.store.username }}</div>
                                <h5 class="text-end">
                                    <span class="fw-bold text-end">INVOICE </span> #{{ orderDetail.order_number }}
                                    <div><span class="text-uppercase">({{ orderDetail.status }})</span> Order Date: {{
                                        formatDate(orderDetail.created_at) }}</div>
                                </h5>

                            </div>
                            <div class="d-flex justify-content-end align-items-center mb-3">
                                <div class="bg-light border border-1 fw-bold p-2"
                                    v-if="orderDetail.payment_status == 'unpaid'">
                                    UNPAID
                                </div>
                                <div class="bg-info text-white border border-1 fw-bold p-2"
                                    v-if="orderDetail.payment_status == 'confirmed'">
                                    CONFIRMED
                                </div>
                                <div class="bg-success text-white border border-1 fw-bold p-2"
                                    v-if="orderDetail.payment_status == 'paid'">
                                    PAID
                                </div>
                            </div>
                            <div class="mb-3">
                                <table>
                                    <tr v-for="(item, index) in orderDetail.fields">
                                        <td class="text-uppercase"><span class="fw-bold">{{ item.label }} </span> </td>
                                        <td v-if="item.field == 'Input' && item.name!=='city_or_subdistrict'">: <span class="text-uppercase">{{
                                            item.value }}</span></td>
                                        <td v-if="item.field == 'Select' && item.name=='province'">: <span class="text-uppercase">{{
                                            item.value.province }}</span></td>
                                        <td v-if="item.field == 'Select' && item.name=='city'">: <span class="text-uppercase">{{
                                            item.value.city_name }}</span></td>
                                        <td v-if="item.field == 'Select' && item.name=='subdistrict'">: <span class="text-uppercase">{{
                                            item.value.subdistrict_name }}</span></td>
                                    </tr>
                                </table>
                            </div>
                            <div>

                                <div clas="mb-5">
                                    <h5 class="fw-bold">Items</h5>
                                    <div v-for="(item, index) in orderDetail.order_item">
                                        <hr>
                                        <div class="d-flex justify-content-between">
                                            <div class="d-flex">
                                                <div>
                                                    {{ item.product.name }}
                                                    <span v-if="item.item_product.variation">
                                                        <span>
                                                            - {{ item.item_product.variation.variation_name }}
                                                        </span>
                                                        <span v-if="item.item_product.variation.child_variation_name">
                                                            - {{ item.item_product.variation.child_variation_name }}
                                                        </span>
                                                    </span>
                                                </div>
                                                <span v-if="item.qty == 0"> (1 x)</span>
                                                <span v-else> {{ item.qty }} x</span>
                                            </div>
                                            <div>
                                                <span v-if="item.total_price == 0">
                                                    <span
                                                        v-if="item.product.price_sale_status == true && item.price_sale !== 0">
                                                        {{ global.formatNumber(item.price_sale) }}
                                                    </span>
                                                    <span v-else>{{ global.formatNumber(item.normal_price) }}</span>
                                                </span>
                                                <span v-else>{{ global.formatNumber(item.total_price) }}</span>
                                            </div>
                                        </div>
                                        <hr>
                                    </div>
                                </div>
                                <div class="mt-3" style="border:1px solid black"></div>
                                <div class="d-flex justify-content-between py-2">
                                    <div class="d-flex fw-bold">
                                        Total Biaya
                                    </div>
                                    <div class="fw-bold">
                                        {{ global.formatNumber(orderDetail.total_price) }}
                                    </div>
                                </div>
                                <div style="border:1px solid black"></div>
                            </div>
                        </div>
                        <div class="card-footer d-flex justify-content-end">
                            <a :href="'/orders/invoice/' + orderDetail.id"
                                class="fw-bold btn btn-secondary d-flex align-items-center">Download Invoice <i
                                    class="bx bx-cloud-download ms-3" style="font-size:18px"></i></a>
                        </div>
                    </div>
                </BCol>
            </BRow>
        </BModal>
        <!-- order modal -->
        <BModal v-model="createModal" hide-footer :title="dataEdit ? 'Edit Order' : 'Tambah Order'"
            header-class="p-3 bg-light" class="v-modal-custom" centered>
            <b-form action="#" id="addform" class="tablelist-form" autocomplete="off">
                <input type="hidden" id="id-field" />

                <div class="mb-3">
                    <label for="productname" class="form-label">Produk</label>
                    <select class="form-select" :class="{ 'is-invalid': addOrderForm.errors.product_id }"
                        v-model="addOrderForm.product_id" @change="selectProduct">
                        <option selected value="">Pilih Produk</option>
                        <option :value="item.id" v-for="(item, index) in productData">{{ item.name }}</option>
                    </select>
                    <div class="invalid-feedback">{{ addOrderForm.errors.product_id }}</div>
                </div>
                <div class="mb-3">
                    <label for="productname" class="form-label">Checkout Page</label>
                    <select class="form-select" v-model="addOrderForm.checkout_id"
                        :class="{ 'is-invalid': addOrderForm.errors.checkout_id }">
                        <option selected value="">Pilih Checkout Page</option>
                        <option :value="item.id" v-for="(item, index) in filterCheckout">{{ item.name }}</option>
                    </select>
                    <div class="invalid-feedback">{{ addOrderForm.errors.checkout_id }}</div>
                </div>

                <div class="hstack gap-2 justify-content-end">
                    <BButton type="button" variant="light" @click="createModal = false" id="closemodal">
                        Tutup
                    </BButton>
                    <BButton type="button" variant="success" id="add-btn" @click="addOrder">
                        Kirim
                    </BButton>
                </div>
            </b-form>
        </BModal>

        <!-- delete modal -->
        <BModal v-model="deleteModal" modal-class="zoomIn" hide-footer no-close-on-backdrop centered>
            <div class="mt-2 text-center">
                <lottie class="avatar-xl" colors="primary:#f7b84b,secondary:#f06548" :options="defaultOptions1" :height="75"
                    :width="75" />
                <div class="mt-4 pt-2 fs-15 mx-4 mx-sm-5">
                    <h4>You are about to delete a order ?</h4>
                    <p class="text-muted mx-4 mb-0">Deleting your order will remove all of your information from our
                        database.</p>
                </div>
            </div>
            <div class="d-flex gap-2 justify-content-center mt-4 mb-2">
                <button variant="success" class="btn w-sm btn-light" @click="deleteModal = false">Close</button>
                <button type="button" class="btn w-sm btn-danger" id="delete-record" @click="deleteData">Yes, Delete
                    It!</button>
            </div>
        </BModal>
    </Layout>
</template>