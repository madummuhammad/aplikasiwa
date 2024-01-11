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
export default {
    props: {
        user: Object
    },
    setup(props) {

        let user = ref(useForm(props.user))

        user.value.password = '';
        console.log('user', user.value)

        let checkAll = ref({
            create: false,
            read: false,
            update: false,
            delete: false
        })

        const checkAllCreate = () => {
            console.log(checkAll.value.create)
            user.value.permission.dashboard.create = checkAll.value.create;
            user.value.permission.product.create = checkAll.value.create;
            user.value.permission.order.create = checkAll.value.create;
            user.value.permission.checkout.create = checkAll.value.create;
            user.value.permission.team.create = checkAll.value.create;
        }
        const checkAllRead = () => {
            console.log(checkAll.value.read)
            user.value.permission.dashboard.read = checkAll.value.read;
            user.value.permission.product.read = checkAll.value.read;
            user.value.permission.order.read = checkAll.value.read;
            user.value.permission.checkout.read = checkAll.value.read;
            user.value.permission.team.read = checkAll.value.read;
        }
        const checkAllUpdate = () => {
            console.log(checkAll.value.update)
            user.value.permission.dashboard.update = checkAll.value.update;
            user.value.permission.product.update = checkAll.value.update;
            user.value.permission.order.update = checkAll.value.update;
            user.value.permission.checkout.update = checkAll.value.update;
            user.value.permission.team.update = checkAll.value.update;
        }
        const checkAllDelete = () => {
            console.log(checkAll.value.delete)
            user.value.permission.dashboard.delete = checkAll.value.delete;
            user.value.permission.product.delete = checkAll.value.delete;
            user.value.permission.order.delete = checkAll.value.delete;
            user.value.permission.checkout.delete = checkAll.value.delete;
            user.value.permission.team.delete = checkAll.value.delete;
        }
        const submit = () => {
            console.log('user', user.value)
            user.value.transform((data) => ({
                ...data
            }))
                .post(route('team.update', user.value.id), {
                    onSuccess: () => {
                    }
                });
        }

        return {
            user,
            submit,
            checkAll,
            checkAllCreate,
            checkAllRead,
            checkAllUpdate,
            checkAllDelete
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
    data() {
    },
};
</script>

<template>
    <Layout>

        <Head title="Create Product" />
        <!-- <PageHeader title="Create Product" pageTitle="Product" /> -->
        <BRow>
            <div class="col-12 mb-5 mt-5">
                <Link href="/team">
                <h2 class="d-flex align-items-center">
                    <i class="bx bx-chevron-left"></i>
                    Kembali ke list
                </h2>
                </Link>
            </div>

            <!-- Page Checkout -->
            <BCol lg="4">
                <BCard no-body>
                    <BCardHeader>
                        <h5 class="card-title mb-0">Profile Anggota Tim</h5>
                    </BCardHeader>
                    <BCardBody>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="mb-3">
                                    <label for="">Nama</label>
                                    <input type="text" v-model="user.name" class="form-control"
                                        :class="{ 'is-invalid': user.errors.name }" id="product-title-input" />
                                    <div class="invalid-feedback">{{ user.errors.name }}</div>
                                </div>
                                <div class="mb-3">
                                    <label for="">No Wa</label>
                                    <input type="text" v-model="user.wa" class="form-control"
                                        :class="{ 'is-invalid': user.errors.wa }" id="product-title-input" />
                                    <div class="invalid-feedback">{{ user.errors.wa }}</div>
                                </div>
                                <div class="mb-3">
                                    <label for="">Email</label>
                                    <input type="text" v-model="user.email" class="form-control"
                                        :class="{ 'is-invalid': user.errors.email }" id="product-title-input" />
                                    <div class="invalid-feedback">{{ user.errors.email }}</div>
                                </div>
                                <div class="mb-3">
                                    <label for="">Password</label>
                                    <input type="password" v-model="user.password" class="form-control"
                                        :class="{ 'is-invalid': user.errors.password }" id="product-title-input" />
                                    <div class="invalid-feedback">{{ user.errors.password }}</div>
                                </div>
                                <div class="mb-3">
                                    <label for="">Role</label>
                                    <select class="form-select" :class="{ 'is-invalid': user.errors.role }"
                                        v-model="user.user_store.role">
                                        <option>Pilih Role</option>
                                        <option value="Admin">Admin</option>
                                        <option value="Customer Service">Customer Service</option>
                                    </select>
                                    <div class="invalid-feedback">{{ user.errors.role }}</div>
                                </div>
                            </div>
                        </div>
                    </BCardBody>
                </BCard>

            </BCol>
            <!-- End Page Checkout -->
            <BCol lg="8">

                <!-- Right Sidebar -->
                <BCard no-body class="bg-light">
                    <BCardBody class="pb-0">
                        <BCard no-body>
                            <BCardBody class="pt-0 pb-0">
                                <div class="row">
                                    <div class="col-12 p-2">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th class="text-center">Halaman</th>
                                                    <th class="text-center">
                                                        <div class="d-flex justify-content-center">
                                                            <span class="d-flex align-items-center">
                                                                Create <input type="checkbox" v-model="checkAll.create"
                                                                    @change="checkAllCreate" class="form-check ms-2">
                                                            </span>
                                                        </div>
                                                    </th>
                                                    <th class="text-center">
                                                        <div class="d-flex justify-content-center">
                                                            <span class="d-flex align-items-center">
                                                                Read <input type="checkbox" v-model="checkAll.read"
                                                                    @change="checkAllRead" class="form-check ms-2">
                                                            </span>
                                                        </div>
                                                    </th>
                                                    <th class="text-center">
                                                        <div class="d-flex justify-content-center">
                                                            <span class="d-flex align-items-center">
                                                                Update <input type="checkbox" v-model="checkAll.update"
                                                                    @change="checkAllUpdate" class="form-check ms-2">
                                                            </span>
                                                        </div>
                                                    </th>
                                                    <th class="text-center">
                                                        <div class="d-flex justify-content-center">
                                                            <span class="d-flex align-items-center">
                                                                Delete <input type="checkbox" v-model="checkAll.delete"
                                                                    @change="checkAllDelete" class="form-check ms-2">
                                                            </span>
                                                        </div>
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>
                                                        Dashboard
                                                    </td>
                                                    <td>
                                                        <div class="d-flex justify-content-center">
                                                            <input type="checkbox"
                                                                v-model="user.permission.dashboard.create"
                                                                class="form-check">
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex justify-content-center">
                                                            <input type="checkbox" v-model="user.permission.dashboard.read"
                                                                class="form-check">
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex justify-content-center">
                                                            <input type="checkbox"
                                                                v-model="user.permission.dashboard.update"
                                                                class="form-check">
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex justify-content-center">
                                                            <input type="checkbox"
                                                                v-model="user.permission.dashboard.delete"
                                                                class="form-check">
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        Produk
                                                    </td>
                                                    <td>
                                                        <div class="d-flex justify-content-center">
                                                            <input type="checkbox" v-model="user.permission.product.create"
                                                                class="form-check">
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex justify-content-center">
                                                            <input type="checkbox" v-model="user.permission.product.read"
                                                                class="form-check">
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex justify-content-center">
                                                            <input type="checkbox" v-model="user.permission.product.update"
                                                                class="form-check">
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex justify-content-center">
                                                            <input type="checkbox" v-model="user.permission.product.delete"
                                                                class="form-check">
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        Order
                                                    </td>
                                                    <td>
                                                        <div class="d-flex justify-content-center">
                                                            <input type="checkbox" v-model="user.permission.order.create"
                                                                class="form-check">
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex justify-content-center">
                                                            <input type="checkbox" v-model="user.permission.order.read"
                                                                class="form-check">
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex justify-content-center">
                                                            <input type="checkbox" v-model="user.permission.order.update"
                                                                class="form-check">
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex justify-content-center">
                                                            <input type="checkbox" v-model="user.permission.order.delete"
                                                                class="form-check">
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        Checkout
                                                    </td>
                                                    <td>
                                                        <div class="d-flex justify-content-center">
                                                            <input type="checkbox" v-model="user.permission.checkout.create"
                                                                class="form-check">
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex justify-content-center">
                                                            <input type="checkbox" v-model="user.permission.checkout.read"
                                                                class="form-check">
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex justify-content-center">
                                                            <input type="checkbox" v-model="user.permission.checkout.update"
                                                                class="form-check">
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex justify-content-center">
                                                            <input type="checkbox" v-model="user.permission.checkout.delete"
                                                                class="form-check">
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        Tim
                                                    </td>
                                                    <td>
                                                        <div class="d-flex justify-content-center">
                                                            <input type="checkbox" v-model="user.permission.team.create"
                                                                class="form-check">
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex justify-content-center">
                                                            <input type="checkbox" v-model="user.permission.team.read"
                                                                class="form-check">
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex justify-content-center">
                                                            <input type="checkbox" v-model="user.permission.team.update"
                                                                class="form-check">
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex justify-content-center">
                                                            <input type="checkbox" v-model="user.permission.team.delete"
                                                                class="form-check">
                                                        </div>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </BCardBody>
                        </BCard>
                    </BCardBody>
                </BCard>
                <div class="text-end mb-3">
                    <BButton type="button" variant="primary" class="w-sm" @click="submit">Simpan
                    </BButton>
                </div>
            </BCol>
        </BRow>
    </Layout>
</template>