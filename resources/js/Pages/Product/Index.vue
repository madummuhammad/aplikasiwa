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
    setup(props) {
        let productData = ref(props.product);
        const global = Global();
        console.log('productData', productData)
        productData.value.forEach((item, index) => {
            item.check = false
        })

        console.log('productData', productData.value)
        const selectAll = ref(false)
        const toggleSelectAll = () => {
            console.log(selectAll.value)
            productData.value.forEach((item) => (item.check = selectAll.value))
        }

        var isAnyItemChecked = ref(false)
        const destroy = () => {
            let filteredProductData = productData.value.slice().filter((item) => !item.check);

            console.log('filtered', filteredProductData)
            let postData = useForm({
                productData: productData.value
            });

            console.log(postData)
            postData.transform((data) => ({
                ...data,
            }))
                .post(route('product.array.delete'), {
                    onSuccess: () => {
                        productData.value = filteredProductData;

                    }
                });
        }


        return {
            productData,
            toggleSelectAll,
            selectAll,
            isAnyItemChecked,
            destroy,
            global
        }
    },
    data(props) {
        return {
            searchQuery: null,
            defaultOptions: {
                animationData: animationData
            },
            sliderCustomzie: [1000, 3000],
            productData: props.product,
            pages: [],
            page: 1,
            perPage: 10,
            value: ["Watches", "Headset"],
        };
    },
    props: {
        product: Object,
        user: Object
    },
    computed: {
        displayedPosts() {
            return this.paginate(this.productData);
        },
        resultQuery() {
            if (this.searchQuery) {
                const search = this.searchQuery.toLowerCase();
                return this.displayedPosts.filter((data) => {
                    return (
                        data.name.toLowerCase().includes(search)
                        // data.category.toLowerCase().includes(search) ||
                        // data.stock.toString().toLowerCase().includes(search) ||
                        // data.price.toString().toLowerCase().includes(search) ||
                        // data.orders.toString().toLowerCase().includes(search) ||
                        // data.rating.toString().toLowerCase().includes(search) ||
                        // data.publishedDate[0].toLowerCase().includes(search)
                    );
                });

            } else {
                return this.displayedPosts;
            }
        },
    },
    watch: {
        productData(newValue) {
            this.setPages(newValue);
        },
        resultQuery() {
            console.log(this.displayedPosts)
            if (this.searchQuery) this.setPages(this.displayedPosts);
            else this.setPages(this.productData);
        },
        productData: {
            deep: true,
            handler(newValue) {
                this.selectAll = newValue.every((item) => item.check)
                var a = this.productData.some((item) => item.check);
                this.isAnyItemChecked = a;
            }
        },
        // selectAll(newValue) {
        //     console.log('selectAll', newValue)
        //     this.productData.forEach((item) => (item.check = newValue))
        //     console.log(this.productData)
        // }
    },
    created() {
        console.log('product', this.product)
        this.setPages(this.productData);

    },
    filters: {
        trimWords(value) {
            return value.split(" ").splice(0, 20).join(" ") + "...";
        },
    },
    beforeMount() {
        console.log('productDatass', this.productData)

    },

    methods: {
        // deleteMultiple() {
        //     let ids_array = [];
        //     var items = document.getElementsByName("chk_child");
        //     items.forEach(function (ele) {
        //         if (ele.checked == true) {
        //             var trNode = ele.parentNode.parentNode;
        //             var id = trNode.querySelector(".id a").innerHTML;
        //             ids_array.push(id);
        //         }
        //     });
        //     if (typeof ids_array !== "undefined" && ids_array.length > 0) {
        //         Swal.fire({
        //             title: "Are you sure?",
        //             text: "You won't be able to revert this!",
        //             icon: "warning",
        //             showCancelButton: true,
        //             cancelButtonColor: "#f46a6a",
        //             confirmButtonColor: "#34c38f",
        //             confirmButtonText: "Yes, delete it!",
        //         }).then((result) => {
        //             if (result.value) {
        //                 var cusList = this.productData;
        //                 ids_array.forEach(function (id) {
        //                     cusList = cusList.filter(function (orders) {
        //                         return orders.id != id;
        //                     });
        //                 });
        //                 this.productData = cusList;
        //                 document.getElementById("checkAll").checked = false;
        //                 var itemss = document.getElementsByName("chk_child");
        //                 itemss.forEach(function (ele) {
        //                     if (ele.checked == true) {
        //                         ele.checked = false;
        //                         ele.closest("tr").classList.remove("table-active");
        //                         document.getElementById('selection-element').style.display = 'none';
        //                     }
        //                 });
        //             }
        //         });
        //     } else {
        //         Swal.fire({
        //             title: "Please select at least one checkbox",
        //             confirmButtonClass: "btn btn-info",
        //             buttonsStyling: true,
        //             showCloseButton: true,
        //         });
        //     }
        // },
        onSort(column) {
            this.direction = this.direction === 'asc' ? 'desc' : 'asc';
            const sortedArray = [...this.productData];
            sortedArray.sort((a, b) => {
                const res = a[column] < b[column] ? -1 : a[column] > b[column] ? 1 : 0;
                return this.direction === 'asc' ? res : -res;
            });
            this.productData = sortedArray;
        },
        setPages(data) {
            let numberOfPages = Math.ceil(data.length / this.perPage);
            this.pages = [];
            for (let index = 1; index <= numberOfPages; index++) {
                this.pages.push(index);
            }
        },
        deletedata(event) {
            console.log(event)
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
                    const deleteEndpoint = 'api/products/destroy/' + event.id;

                    axios.delete(deleteEndpoint)
                        .then(response => {
                            console.log(response.data);
                            this.product.splice(this.product.indexOf(event), 1);
                            Swal.fire("Deleted!", "Your file has been deleted.", "success");
                        })
                        .catch(error => {
                            console.error(error);
                        });
                }
            });

        },
        paginate(productData) {
            let page = this.page;
            let perPage = this.perPage;
            let from = page * perPage - perPage;
            let to = page * perPage;
            return productData.slice(from, to);
        },
        sumStock(array) {
            var stock = 0
            array.forEach((item, index) => {
                stock = stock + parseInt(item.stock);
            })
            return stock;
        }
    },
    mounted() {
        // var total = 0;
        // var checkchild = document.querySelectorAll('.form-check-all input[type="checkbox"]');
        // checkchild.forEach(function (checkbox) {
        //     checkbox.onclick = function () {
        //         total = 0;
        //         checkchild.forEach(function (box) {
        //             if (box.checked == true) {
        //                 total++;
        //             }
        //         });
        //         if (checkbox.checked == true) {
        //             checkbox.closest("tr").classList.add("table-active");
        //             document.getElementById('selection-element').style.display = 'block';
        //             document.getElementById('select-content').innerHTML = total;
        //         } else {
        //             if (total <= 0) {
        //                 document.getElementById('selection-element').style.display = 'none';
        //             }
        //             checkbox.closest("tr").classList.remove("table-active");
        //             document.getElementById('select-content').innerHTML = total;
        //         }
        //     };
        // });
        // var checkAll = document.getElementById("checkAll");
        // if (checkAll) {
        //     checkAll.onclick = function () {
        //         var checkboxes = document.querySelectorAll(
        //             '.form-check-all input[type="checkbox"]'
        //         );
        //         if (checkAll.checked == true) {
        //             checkboxes.forEach(function (checkbox) {
        //                 checkbox.checked = true;
        //                 document.getElementById('selection-element').style.display = 'block';
        //                 document.getElementById('select-content').innerHTML = checkboxes.length;
        //                 checkbox.closest("tr").classList.add("table-active");
        //             });
        //         } else {
        //             checkboxes.forEach(function (checkbox) {
        //                 checkbox.checked = false;
        //                 document.getElementById('selection-element').style.display = 'none';
        //                 checkbox.closest("tr").classList.remove("table-active");
        //             });
        //         }
        //     };
        // }
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

        <Head title="Products" />
        <PageHeader title="Products" pageTitle="Dashboard" />
        <BRow>
            <BCol xl="12" lg="8">
                <div>
                    <BCard no-body>
                        <BCardHeader class="border-0">
                            <BRow class="g-4">
                                <BCol sm="auto" class="d-flex">
                                    <div class="me-2">
                                        <Link :href="route('product.create')" class="btn btn-secondary"
                                            v-if="user.permission.product.create"><i
                                            class="ri-add-line align-bottom me-1"></i> Tambah Produk</Link>
                                    </div>
                                    <div>
                                        <button v-if="user.permission.product.delete" class="btn btn-light border border-2"
                                            @click="destroy" :disabled="!isAnyItemChecked">Hapus
                                        </button>
                                    </div>
                                </BCol>
                                <BCol sm>
                                    <div class="d-flex justify-content-sm-end">
                                        <div class="search-box ms-2">
                                            <input type="text" class="form-control" v-model="searchQuery"
                                                placeholder="Search Products..." />
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
                                                    <th scope="col" style="width: 50px">
                                                        <input v-model="selectAll" @change="toggleSelectAll()"
                                                            class="form-check-input" type="checkbox" />
                                                    </th>
                                                    <th class="sort" data-sort="product" @click="onSort('id')">Product</th>
                                                    <th class="sort" data-sort="price" @click="onSort('price')">Harga</th>
                                                    <th class="sort" data-sort="price" @click="onSort('price')">HPP</th>
                                                    <th class="sort" data-sort="price" @click="onSort('price')">Inventaris
                                                    </th>
                                                    <th scope="col">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody class="list form-check-all">
                                                <tr class="gridjs-tr" v-for="(item, index) in resultQuery">
                                                    <td data-column-id="productListAllCheckbox" class="gridjs-td">
                                                        <input v-model="item.check" type="checkbox"
                                                            class="form-check-input" />
                                                    </td>
                                                    <td data-column-id="stock" class="gridjs-td">
                                                        <span>
                                                            <div class="d-flex align-items-center">
                                                                <div class="flex-shrink-0 me-3">
                                                                    <div
                                                                        class="avatar-sm bg-light rounded p-1 d-flex align-items-center justify-content-center">
                                                                        <img :src="item.image[0].url"
                                                                            v-if="item.image.length > 0" alt=""
                                                                            class="img-fluid d-block" />
                                                                    </div>
                                                                </div>
                                                                <div class="flex-grow-1">
                                                                    <h5 class="fs-14 mb-1">
                                                                        <Link :href="'/product/' + item.id"
                                                                            class="text-body">
                                                                        {{ item.name }}
                                                                        </Link>
                                                                    </h5>
                                                                    <!-- <p class="text-muted mb-0">
                                                                        asdf
                                                                    </p> -->
                                                                </div>
                                                            </div>
                                                        </span>
                                                    </td>
                                                    <td data-column-id="stock" class="gridjs-td">
                                                        <span v-if="item.item_product.length > 0">
                                                            {{ global.formatNumber(item.item_product[0].normal_price) }}
                                                        </span>
                                                    </td>
                                                    <td data-column-id="stock" class="gridjs-td">
                                                        <span v-if="item.item_product.length > 0">
                                                            {{ global.formatNumber(item.item_product[0].hpp) }}
                                                        </span>
                                                    </td>
                                                    <td data-column-id="stock" class="gridjs-td">

                                                        <div v-if="item.stock_status == 'tersedia'">
                                                            <!-- Manajemen Stock Active -->
                                                            <div v-if="item.manajemen_stock == true">
                                                                {{ sumStock(item.item_product) }}
                                                            </div>
                                                            <!-- Manajemen Stock Nonactive-->
                                                            <div v-if="item.manajemen_stock == false"> <i
                                                                    class="bx bx-minus"></i> </div>
                                                        </div>
                                                        <div
                                                            v-if="item.stock_status == 'habis' && item.manajemen_stock == false">
                                                            <div class="fw-bold text-danger">Stock Habis</div>
                                                        </div>
                                                        <div v-if="item.manajemen_stock == true">
                                                            <div class="fw-bold text-danger">{{ sumStock(item.item_product)
                                                            }}</div>
                                                        </div>
                                                    </td>
                                                    <!-- <td data-column-id="stock" class="gridjs-td">
                                                        asdf
                                                    </td> -->
                                                    <td data-column-id="action" class="gridjs-td">
                                                        <span>
                                                            <div class="dropdown">
                                                                <BDropdown variant="link"
                                                                    toggle-class="btn btn-soft-secondary btn-sm dropdown arrow-none"
                                                                    menu-class="dropdown-menu-end"
                                                                    :offset="{ alignmentAxis: -130, crossAxis: 0, mainAxis: 10 }">
                                                                    <template #button-content> <i class="ri-more-fill"></i>
                                                                    </template>
                                                                    <Link class="dropdown-item"
                                                                        v-if="user.permission.product.read"
                                                                        :href="'product/' + item.id">
                                                                    <i class="ri-eye-fill align-bottom me-2 text-muted"></i>
                                                                    View</Link>
                                                                    <Link class="dropdown-item"
                                                                        v-if="user.permission.product.update"
                                                                        :href="'product/edit/' + item.id"> <i
                                                                        class="ri-pencil-fill align-bottom me-2 text-muted"></i>
                                                                    Edit
                                                                    </Link>
                                                                    <li class="dropdown-divider"></li>
                                                                    <BDropdownItem @click="deletedata(item)"
                                                                        v-if="user.permission.product.delete"><i
                                                                            class="ri-delete-bin-fill align-bottom me-2 text-muted"></i>
                                                                        Delete
                                                                    </BDropdownItem>
                                                                </BDropdown>
                                                            </div>
                                                        </span>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <div class="d-flex justify-content-end m-3">
                                            <div class="pagination-wrap hstack gap-2">
                                                <BLink class="page-item pagination-prev" href="#" :disabled="page <= 1"
                                                    @click="page--">
                                                    Previous
                                                </BLink>
                                                <ul class="pagination listjs-pagination mb-0">
                                                    <li v-for="(pageNumber, index) in pages" :key="index"
                                                        @click="page = pageNumber"
                                                        :class="{ active: pageNumber == page, disabled: pageNumber == '...', }">
                                                        <BLink class="page" href="#">{{ pageNumber }}</BLink>
                                                    </li>
                                                </ul>
                                                <BLink class="page-item pagination-next" href="#"
                                                    :disabled="page >= pages.length" @click="page++">
                                                    Next
                                                </BLink>
                                            </div>
                                        </div>
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