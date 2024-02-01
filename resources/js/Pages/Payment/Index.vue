<script>
import { Head, useForm, Link } from '@inertiajs/vue3';
import { ref, watch } from "vue";
import Layout from "@/Layouts/main.vue";
import PageHeader from "@/Components/page-header.vue";
import Slider from "@vueform/slider";
import Multiselect from "@vueform/multiselect";
import Swal from "sweetalert2";
import "@vueform/multiselect/themes/default.css";
import animationData from "@/Components/widgets/msoeawqm.json";
import Lottie from "@/Components/widgets/lottie.vue";
import { Global } from '../../global'
export default {
    props: {
        payment:Object
    },
    setup(props) {
        const global=Global();

        let paymentData=ref(props.payment)
        // paymentData.value.forEach((item,index)=>{
        //             item.check==true
        //         })
        const markPaymentStatus = async (status, id) => {
            try {
                let postData = {
                    status: status,
                    id: id
                }
                const response = await axios.post('/api/payment/change_payment_status', postData);
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
                const response = await axios.get('/api/payment/list');
                const data = response.data;

                // orders.value = data.data;
                paymentData.value=data.data;
                paymentData.value.forEach((item,index)=>{
                    item.check==true
                })
                console.log(paymentData.value)
                console.log(data)

            } catch (error) {
                console.error('error', error);
            }

        };

        const selectAll = ref(false)
        const toggleSelectAll = () => {
            console.log(selectAll.value)
            paymentData.value.forEach((item) => (item.check = selectAll.value))
        }

        var isAnyItemChecked = ref(false)
        const destroy = () => {
            // console.log(event)
            Swal.fire({
                title: "Yakin menghapus pembayaran?",
                text: "Data tidak akan bisa dikembalikan!",
                icon: "warning",
                showCancelButton: true,
                cancelButtonColor: "#f46a6a",
                confirmButtonColor: "#34c38f",
                confirmButtonText: "Ya, Hapus!",
            }).then((result) => {
                // console.log(result)
                console.log(paymentData.value)
                let postData={
                    payment:paymentData.value.filter(item=>item.check===true)
                }

                if (result.value) {
                    const deleteEndpoint = 'api/payment/destroy';

                    axios.post(deleteEndpoint,postData)
                        .then(response => {
                            console.log(response.data);
                            Swal.fire("Deleted!", "Your file has been deleted.", "success");
                            getList();
                        })
                        .catch(error => {
                            console.error(error);
                        });
                }
            });
            // let filteredProductData = productData.value.slice().filter((item) => !item.check);

            // console.log('filtered', filteredProductData)
            // let postData = useForm({
            //     productData: productData.value
            // });

            // console.log(postData)
            // postData.transform((data) => ({
            //     ...data,
            // }))
            //     .post(route('product.array.delete'), {
            //         onSuccess: () => {
            //             productData.value = filteredProductData;

            //         }
            //     });
        }
        return {
            global,
            markPaymentStatus,
            paymentData,

            selectAll,
            toggleSelectAll,
            isAnyItemChecked,
            destroy
        }
    },
    data(props) {
        return {
            searchQuery: null,
            defaultOptions: {
                animationData: animationData
            },
            sliderCustomzie: [1000, 3000],
            // paymentData: props.payment,
            pages: [],
            page: 1,
            perPage: 10,
            value: ["Watches", "Headset"],
        };
    },
    computed: {
        displayedPosts() {
            return this.paginate(this.paymentData);
        },
        resultQuery() {
            if (this.searchQuery) {
                const search = this.searchQuery.toLowerCase();
                return this.displayedPosts.filter((data) => {
                    return (
                        data.payment_number.toLowerCase().includes(search)
                    );
                });

            } else {
                return this.displayedPosts;
            }
        },
    },
    watch: {
        paymentData(newValue) {
            this.setPages(newValue);
        },
        resultQuery() {
            console.log('asa',this.paymentData)
            console.log(this.displayedPosts)
            if (this.searchQuery) this.setPages(this.displayedPosts);
            else this.setPages(this.paymentData);
        },
        paymentData: {
            deep: true,
            handler(newValue) {
                this.selectAll = newValue.every((item) => item.check)
                var a = this.paymentData.some((item) => item.check);
                this.isAnyItemChecked = a;
            }
        },
    },
    methods: {
        setPages(data) {
            let numberOfPages = Math.ceil(data.length / this.perPage);
            this.pages = [];
            for (let index = 1; index <= numberOfPages; index++) {
                this.pages.push(index);
            }
        },
        paginate(productData) {
            let page = this.page;
            let perPage = this.perPage;
            let from = page * perPage - perPage;
            let to = page * perPage;
            return productData.slice(from, to);
        },
    },


    components: {
        Layout,
        PageHeader,
        lottie: Lottie,
        Slider,
        Multiselect,
        Head, Link
    },
};
</script>

<template>
    <Layout>

        <Head title="Konfirmasi Pembayaran" />
        <PageHeader title="Konfirmasi Pembayaran" pageTitle="Dashboard" />
        <BRow>
            <BCol xl="12" lg="8">
                <div>
                    <BCard no-body>
                        <BCardHeader class="border-0">
                            <BRow class="g-4">
                                <BCol sm="auto" class="d-flex">
                                    <div>
                                        <button class="btn btn-light btn-sm border border-2"
                                            @click="destroy" v-if="isAnyItemChecked">Hapus
                                        </button>
                                    </div>
                                </BCol>
                                <BCol sm>
                                    <div class="d-flex justify-content-sm-end">
                                        <div class="search-box ms-2">
                                            <input type="text" class="form-control" v-model="searchQuery"
                                                placeholder="Search Payment..." />
                                            <i class="ri-search-line search-icon"></i>
                                        </div>
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
                                                    <th>
                                                        <input v-model="selectAll" @change="toggleSelectAll()"
                                                            class="form-check-input" type="checkbox" />
                                                    </th>
                                                    <th  data-sort="product" @click="onSort('id')">Order ID</th>
                                                    <th  data-sort="price" @click="onSort('price')">Nama Akun</th>
                                                    <th  data-sort="price" @click="onSort('price')">Transfer Ke</th>
                                                    <th  data-sort="price" @click="onSort('price')">Jumlah Transfer
                                                    </th>
                                                    <th  data-sort="price" @click="onSort('price')">Bukti Transfer
                                                    </th>
                                                    <th  data-sort="price" @click="onSort('price')">Diproses Oleh
                                                    </th>
                                                    <th  data-sort="price" @click="onSort('price')">Status
                                                    </th>
                                                    <th scope="col">Tindakan</th>
                                                </tr>
                                            </thead>
                                            <tbody class="list form-check-all">
                                                <tr class="gridjs-tr" v-for="(item,index) in resultQuery">
                                                    <td data-column-id="stock" class="gridjs-td">
                                                        <input v-model="item.check" type="checkbox"
                                                            class="form-check-input" />
                                                    </td>
                                                    <td data-column-id="stock" class="gridjs-td">
                                                     #{{item.payment_number}}
                                                    </td>
                                                    <td data-column-id="stock" class="gridjs-td">
                                                        {{item.atas_nama}}
                                                     
                                                    </td>
                                                    <td data-column-id="stock" class="gridjs-td">
                                                     {{item.transfer_ke.bank}} - {{item.transfer_ke.atas_nama}} - {{item.transfer_ke.rekening}}
                                                    </td>
                                                    <td data-column-id="stock" class="gridjs-td">
                                                     {{global.formatNumber(item.jumlah_transfer)}}
                                                    </td>
                                                    <td data-column-id="stock" class="gridjs-td">
                                                        <a :href="item.bukti_transfer" target="_blank" rel="noopener noreferrer">
                                                            <img style="width: 100px; height: 100px;" :src="item.bukti_transfer" alt="">
                                                        </a>
                                                    </td>
                                                    <td data-column-id="stock" class="gridjs-td">
                                                     <span v-if="item.processed_by && item.status=='paid'">{{ item.processed_by }}</span>
                                                    </td>
                                                    <td class="status">
                                                        <span v-if="item.processed_by && item.status=='paid'">{{ item.status }}</span>
                                                        <span class="badge text-uppercase" :class="{
                                                            'bg-success-subtle text-success': item.status== 'paid',
                                                            'bg-info-subtle text-info': item.status== 'confirmed',
                                                            'bg-danger-subtle text-danger': item.status== 'unpaid',
                                                            }">{{ item.status}}</span>
                                                    </td>
                                                    <td>
                                                        <BButtonGroup class="dropend"
                                                           >
                                                            <BDropdown text="Tindakan" dropend variant="secondary">
                                                                <BDropdownItem @click="markPaymentStatus('paid',item.id)"
                                                                    class="cursor-pointer">Mark As Paid</BDropdownItem>
                                                                <BDropdownItem @click="markPaymentStatus('unpaid',item.id)"
                                                                    class="cursor-pointer">Mark As Unpaid</BDropdownItem>
                                                            </BDropdown>
                                                        </BButtonGroup>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="tab-pane" id="productnav-published" role="tabpanel">
                                    <div id="table-product-list-published" class="table-card gridjs-border-none"></div>
                                </div>

                                <div class="tab-pane" id="productnav-draft" role="tabpanel">
                                    <div class="py-4 text-center">
                                        <div>
                                            <lottie class="avatar-xl" colors="primary:#121331,secondary:#08a88a"
                                                :options="defaultOptions" :height="75" :width="75" />
                                        </div>

                                        <div class="mt-4">
                                            <h5>Sorry! No Result Found</h5>
                                        </div>
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