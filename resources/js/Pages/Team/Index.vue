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
        console.log('props', props)
        const global = Global();
        let teamData = ref(props.team);

        teamData.value.forEach((item, index) => {
            item.check = false
        })

        const selectAll = ref(false)

        const toggleSelectAll = () => {
            console.log(selectAll.value)
            teamData.value.forEach((item) => (item.check = selectAll.value))
        }

        var isAnyItemChecked = ref(false)
        const deleteCheckedData = () => {
            console.log(teamData.value)
            let filteredData = teamData.value.slice().filter((item) => !item.check);

            console.log('filtered', filteredData)
            let postData = useForm({
                teamData: teamData.value
            });

            console.log(postData)
            postData.transform((data) => ({
                ...data,
            }))
                .post(route('team.destroy'), {
                    onSuccess: () => {
                        teamData.value = filteredData;

                    }
                });
        }

        const deleteData = (item) => {
            item.check = true;
            let filteredData = teamData.value.slice().filter((item) => !item.check);
            console.log('item', item)
            console.log('filtered', filteredData)
            let postData = useForm({
                teamData: { item }
            });

            console.log('postData', postData)

            console.log(postData)
            postData.transform((data) => ({
                ...data,
            }))
                .post(route('team.destroy'), {
                    onSuccess: () => {
                        teamData.value = filteredData;

                    }
                });
        }
        return {
            teamData,
            global,

            toggleSelectAll,
            selectAll,
            deleteCheckedData,
            deleteData,
            isAnyItemChecked
        }
    },
    watch: {
        // productData(newValue) {
        //     this.setPages(newValue);
        // },
    },
    data(props) {
        return {
            searchQuery: null,
            defaultOptions: {
                animationData: animationData
            },
            sliderCustomzie: [1000, 3000],
            teamData: props.team,
            pages: [],
            page: 1,
            perPage: 10,
            value: ["Watches", "Headset"],
        };
    },
    props: {
        team: Object,
        user: Object
    },
    computed: {
        displayedPosts() {
            return this.paginate(this.teamData);
        },
        resultQuery() {
            if (this.searchQuery) {
                const search = this.searchQuery.toLowerCase();
                return this.displayedPosts.filter((data) => {
                    return (
                        data.user.name.toLowerCase().includes(search)
                    );
                });

            } else {
                return this.displayedPosts;
            }
        },
    },
    watch: {
        teamData(newValue) {
            this.setPages(newValue);
        },
        resultQuery() {
            console.log(this.displayedPosts)
            if (this.searchQuery) this.setPages(this.displayedPosts);
            else this.setPages(this.teamData);
        },
        teamData: {
            deep: true,
            handler(newValue) {
                this.selectAll = newValue.every((item) => item.check)
                var a = this.teamData.some((item) => item.check);
                this.isAnyItemChecked = a;
            }
        },
        // selectAll(newValue) {
        //     console.log('selectAll', newValue)
        //     this.teamData.forEach((item) => (item.check = newValue))
        //     console.log(this.teamData)
        // }
    },
    created() {
        console.log('team', this.team)
        this.setPages(this.teamData);

    },
    filters: {
        trimWords(value) {
            return value.split(" ").splice(0, 20).join(" ") + "...";
        },
    },
    beforeMount() {
        console.log('teamDatass', this.teamData)

    },

    methods: {
        onSort(column) {
            this.direction = this.direction === 'asc' ? 'desc' : 'asc';
            const sortedArray = [...this.teamData];
            sortedArray.sort((a, b) => {
                const res = a[column] < b[column] ? -1 : a[column] > b[column] ? 1 : 0;
                return this.direction === 'asc' ? res : -res;
            });
            this.teamData = sortedArray;
        },
        setPages(data) {
            let numberOfPages = Math.ceil(data.length / this.perPage);
            this.pages = [];
            for (let index = 1; index <= numberOfPages; index++) {
                this.pages.push(index);
            }
        },
        paginate(teamData) {
            let page = this.page;
            let perPage = this.perPage;
            let from = page * perPage - perPage;
            let to = page * perPage;
            return teamData.slice(from, to);
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

        <Head title="Team Management" />
        <PageHeader title="Team Management" pageTitle="Dashboard" />
        <BRow>
            <BCol xl="12" lg="8">
                <div>
                    <BCard no-body>
                        <BCardHeader class="border-0">
                            <BRow class="g-4">
                                <BCol sm="auto" class="d-flex">
                                    <div class="me-2">
                                        <Link v-if="user.permission.team.create == true" :href="route('team.create')"
                                            class="btn btn-secondary"><i class="ri-add-line align-bottom me-1"></i> Tambah
                                        Anggota Team</Link>
                                    </div>
                                    <div>
                                        <button v-if="user.permission.team.delete == true"
                                            class="btn btn-light border border-2" @click="deleteCheckedData"
                                            :disabled="!isAnyItemChecked">Hapus
                                        </button>
                                    </div>
                                </BCol>
                                <BCol sm>
                                    <div class="d-flex justify-content-sm-end">
                                        <div class="search-box ms-2">
                                            <input type="text" class="form-control" v-model="searchQuery"
                                                placeholder="Search Anggota Team..." />
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
                                                    <th class="sort" data-sort="product" @click="onSort('id')">Nama</th>
                                                    <th class="sort" data-sort="price" @click="onSort('price')">Email</th>
                                                    <th class="sort" data-sort="price" @click="onSort('price')">Telepon</th>
                                                    <th class="sort" data-sort="price" @click="onSort('price')">Role
                                                    </th>
                                                    <th class="sort" data-sort="price" @click="onSort('price')">Tanggal
                                                        Pembuatan
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
                                                        {{ item.user.name }}
                                                    </td>
                                                    <td data-column-id="stock" class="gridjs-td">
                                                        {{ item.user.email }}
                                                    </td>
                                                    <td data-column-id="stock" class="gridjs-td">
                                                        {{ item.user.wa }}
                                                    </td>
                                                    <td data-column-id="stock" class="gridjs-td">
                                                        {{ item.role }}
                                                    </td>
                                                    <td data-column-id="stock" class="gridjs-td">
                                                        {{ item.created_at.split('T')[0] }}
                                                    </td>
                                                    <!-- <td data-column-id="stock" class="gridjs-td">
                                                        asdf
                                                    </td> -->
                                                    <td data-column-id="action" class="gridjs-td">
                                                        <span v-if="item.is_owner == false">
                                                            <div class="dropdown"
                                                                v-if="user.permission.team.update == true || user.permission.team.delete == true">
                                                                <BDropdown variant="link"
                                                                    toggle-class="btn btn-soft-secondary btn-sm dropdown arrow-none"
                                                                    menu-class="dropdown-menu-end"
                                                                    :offset="{ alignmentAxis: -130, crossAxis: 0, mainAxis: 10 }">
                                                                    <template #button-content> <i class="ri-more-fill"></i>
                                                                    </template>
                                                                    <Link class="dropdown-item"
                                                                        v-if="user.permission.team.update == true"
                                                                        :href="'team/edit/' + item.user.id"> <i
                                                                        class="ri-pencil-fill align-bottom me-2 text-muted"></i>
                                                                    Edit
                                                                    </Link>
                                                                    <li class="dropdown-divider"></li>
                                                                    <BDropdownItem @click="deleteData(item)"
                                                                        v-if="user.permission.team.delete == true"><i
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