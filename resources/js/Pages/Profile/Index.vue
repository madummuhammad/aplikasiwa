<script>
import { Head, useForm, Link } from '@inertiajs/vue3';
import { ref, watch } from "vue";
import Layout from "@/Layouts/main.vue";

import { usePage } from '@inertiajs/vue3';

const page = usePage();
import Multiselect from "@vueform/multiselect";
import "@vueform/multiselect/themes/default.css";
import flatPickr from "vue-flatpickr-component";
import "flatpickr/dist/flatpickr.css";


export default {
    props: {
        profile: Object,
        message: Object,
        bank:Object,
        params:Object
    },
    setup(props) {
        console.log('props profile', props.profile)
        let paramsData=ref({})
        if(props.params.page){
            paramsData.value=props.params
        } else {
            paramsData.value={
                page:'profile'
            }
        }
        console.log('params',paramsData.value)

        const pageMethod=(page)=>{
            console.log('page',page)
            paramsData.value.page=page;
        }
        let profileData = ref(useForm(props.profile));
        if (!profileData.value.image) {
            profileData.value.image = "";
        }
        const submit = () => {
            console.log(profileData.value)
            profileData.value.transform((data) => ({
                ...data,
            }))
                .post(route('profile.update'), {
                    onSuccess: () => {
                        console.log('message', page.props.message.message)
                    }
                });
        }

        const selectedFile = async (event) => {
            try {
                const file = event.target.files[0];
                const formData = new FormData();
                formData.append('image', file);
                const response = await axios.post('/api/products/upload', formData);
                const data = response.data;

                profileData.value.image = data.data;
                console.log(profileData.value)

            } catch (error) {
                console.error('error', error);
            }

        };

        // Change Password
        const createModalPassword = ref(false);

        const toggleModalPassword = () => {
            createModalPassword.value = !createModalPassword.value;
        }

        let changePasswordData = ref(useForm({
            old_password: "",
            new_password: ""
        }));

        const toggleOldPassword = ref(false)
        const toggleNewPassword = ref(false)

        const changePassword = () => {
            console.log(changePasswordData.value)
            changePasswordData.value.transform((data) => ({
                ...data,
            }))
                .post(route('profile.change_password'), {
                    onSuccess: (result) => {
                        console.log('result', result)
                        console.log('props', page.props.message)
                        if (page.props.message.status == 'success') {
                            createModalPassword.value = false;
                        }
                    }
                });
        }
        const accountBankShow = ref(false);

        let listBank = [
            {
                label: 'BCA',
                value: 'BCA'
            },
            {
                label: 'BCA Syariah',
                value: 'BCA Syariah'
            },
            {
                label: 'BNI',
                value: 'BNI'
            },
        ]

        let tempBankAccount = ref(
            {
                bank: '',
                atas_nama: '',
                rekening: ''
            }
        )

        let bankData=ref(props.bank);
        let bankAccountError = ref([])
        const addBankAccount = async () => {
            try {
                bankData.value.push({ ...tempBankAccount.value });

                const response = await axios.post('/api/bank', tempBankAccount.value);
                const data = response.data;

                if (data.status === 'success') {
                    console.log('Success:', data.message);
                }

                tempBankAccount.value = {
                    bank: '',
                    atas_nama: '',
                    rekening: ''
                };

                getBank();
                accountBankShow.value = false;
            } catch (error) {
                console.error('error', error);
                bankAccountError.value = error.response.data.errors;
            }
        };

        const deleteAccountBank = async (index) => {
            console.log(bankData.value[index]);
            try {
                const response = await  axios.delete('/api/bank/'+bankData.value[index].id);
                const data = response.data;
                console.log(response)
                if (data.status === 'success') {
                    bankData.value.splice(index, 1);
                }

            } catch (error) {
                console.error('error', error);
            }
        }

        const listBankAccount = ref([]);
        const getBank = async () => {
            try {
                const response = await axios.get('/api/bank');
                const data = response.data;

                if (data.status === 'success') {
                    console.log('Bank:', data);
                    listBankAccount.value = data.data;
                }

            } catch (error) {
                console.error('error', error);
            }
        }

        return {
            profileData,
            paramsData,
            pageMethod,
            submit,
            selectedFile,
            page,

            // Bank
            bankData,
            accountBankShow,
            listBank,
            tempBankAccount,
            bankAccountError,
            listBankAccount,
            getBank,
            addBankAccount,
            deleteAccountBank,

            // Change Password
            createModalPassword,
            toggleModalPassword,
            changePasswordData,
            toggleOldPassword,
            toggleNewPassword,
            changePassword
        }
    },
    data() {
        return {
            value: ['javascript'],
            date: null,
            value1: null,
            value2: null,
        };
    },
    mounted() {
    },
    components: {
        Layout,
        Multiselect,
        flatPickr,
        Link, Head
    },
};
</script>

<template>
    <Layout>

        <Head title="Profile Setting" />


        <BRow class="mt-5">
            <BCol xxl="3">
                <BCard no-body class="mt-n5">
                    <BCardBody class="p-4">
                        <div class="text-center">
                            <div class="profile-user position-relative d-inline-block mx-auto mb-4">
                                <img src="@assets/images/users/avatar-1.jpg" v-if="!profileData.image"
                                    class="rounded-circle avatar-xl img-thumbnail user-profile-image"
                                    alt="user-profile-image" />
                                <img :src="profileData.image" v-if="profileData.image"
                                    class="rounded-circle avatar-xl img-thumbnail user-profile-image"
                                    alt="user-profile-image" />
                                <div class="avatar-xs p-0 rounded-circle profile-photo-edit">
                                    <input id="profile-img-file-input" accept=".png,.jpeg,.jpg" @change="selectedFile"
                                        type="file" class="profile-img-file-input" />
                                    <label for="profile-img-file-input" class="profile-photo-edit avatar-xs">
                                        <span class="avatar-title rounded-circle bg-light text-body">
                                            <i class="ri-camera-fill"></i>
                                        </span>
                                    </label>
                                </div>
                            </div>
                            <h5 class="mb-1">{{ profileData.name }}</h5>
                            <p class="text-muted mb-0">{{ profileData.user_store.role }}</p>
                        </div>
                    </BCardBody>
                </BCard>
                <BCard no-body>
                    <BCardBody>
                        <div class="d-flex align-items-center mb-4">
                            <div class="flex-grow-1">
                                <h5 class="card-title mb-0">Paket Berlangganan Anda</h5>
                            </div>
                            <div class="flex-shrink-0">
                                <BLink class="btn btn-secondary text-white fs-12"><i
                                        class="ri-add-fill align-bottom me-1"></i> Upgrade Paket Berlangganan</BLink>
                            </div>
                        </div>
                        <div>
                            <h2 class="text-success text-center text-uppercase">
                                {{ profileData.user_store.store.plan }}
                            </h2>
                            <p class="text-center">Aktif Sampai: {{ profileData.user_store.store.expired_at }}</p>
                        </div>
                    </BCardBody>
                </BCard>
            </BCol>
            <BCol xxl="9">
                <BCard no-body class="mt-xxl-n5">
                    <BCardBody class="p-4 pt-2">
                        <div nav-class="nav-tabs-custom rounded border-bottom-0">
                            <div class="btn-group w-100 mb-3">
                    <button @click="pageMethod('profile')" class="btn"
                        :class="{ 'btn-primary': paramsData.page == 'profile', 'btn-outline-secondary': paramsData.page !== 'profile' }">
                        Proifile Anda</button>
                    <button @click="pageMethod('payment')" class="btn"
                        :class="{ 'btn-primary': paramsData.page == 'payment', 'btn-outline-secondary': paramsData.page !== 'payment' }">
                        Pembayaran
                    </button>
                </div>
                            <div v-if="paramsData.page=='profile'" class="nav-item" >
                                <div class="alert alert-success mt-5"
                                    v-if="page.props && page.props.message && page.props.message.status == 'success'">
                                    {{ page.props.message.message }}
                                </div>
                                <div class="alert alert-danger mt-5"
                                    v-if="page.props && page.props.message && page.props.message.status == 'error'">
                                    {{ page.props.message.message }}
                                </div>
                                <form action="javascript:void(0);">
                                    <BRow class="pt-4">
                                        <BCol lg="6">
                                            <div class="mb-3">
                                                <label for="firstnameInput" class="form-label">Nama Lengkap</label>
                                                <input type="text" class="form-control"
                                                    :class="{ 'is-invalid': profileData.errors.name }" id="firstnameInput"
                                                    v-model="profileData.name" placeholder="Enter your name" />
                                                <div class="invalid-feedback">
                                                    {{ profileData.errors.name }}
                                                </div>
                                            </div>
                                        </BCol>
                                        <BCol lg="6">
                                            <div class="mb-3">
                                                <label for="lastnameInput" class="form-label">Username</label>
                                                <input type="text" class="form-control"
                                                    v-model="profileData.user_store.store.username" id="lastnameInput"
                                                    placeholder="Enter your username" />
                                                <div class="invalid-feedback">
                                                    {{ profileData.errors.username }}
                                                </div>
                                            </div>
                                        </BCol>
                                        <BCol lg="6">
                                            <div class="mb-3">
                                                <label for="phonenumberInput" class="form-label">No Wa</label>
                                                <input type="text" class="form-control" id="phonenumberInput"
                                                    placeholder="Enter your wa"
                                                    :class="{ 'is-invalid': profileData.errors.wa }"
                                                    v-model="profileData.wa" />
                                                <div class="invalid-feedback">
                                                    {{ profileData.errors.wa }}
                                                </div>
                                            </div>
                                        </BCol>
                                        <BCol lg="6">
                                            <div class="mb-3">
                                                <label for="emailInput" class="form-label">Email Address</label>
                                                <input type="email" class="form-control" id="emailInput"
                                                    placeholder="Enter your email"
                                                    :class="{ 'is-invalid': profileData.errors.email }"
                                                    v-model="profileData.email" />
                                                <div class="invalid-feedback">
                                                    {{ profileData.errors.email }}
                                                </div>
                                            </div>
                                        </BCol>
                                        <BCol lg="12">
                                            <button class="btn border btn-light text-dark"
                                                @click="toggleModalPassword">Ganti Password <i
                                                    class="bx bx-lock-open-alt"></i></button>
                                            <BModal v-model="createModalPassword" hide-footer title="Ganti Password"
                                                header-class="p-3 bg-light" class="v-modal-custom" centered>
                                                <b-form action="#" id="addform" class="tablelist-form" autocomplete="off">
                                                    <div class="alert alert-success"
                                                        v-if="page.props && page.props.message && page.props.message.status == 'success'">
                                                        {{ page.props.message.message }}
                                                    </div>
                                                    <div class="alert alert-danger"
                                                        v-if="page.props && page.props.message && page.props.message.status == 'error'">
                                                        {{ page.props.message.message }}
                                                    </div>
                                                    <div class="mb-3">
                                                        <InputLabel for="password" value="Password" />
                                                        <div class="position-relative auth-pass-inputgroup">
                                                            <input :type="toggleOldPassword ? 'text' : 'password'"
                                                                class="form-control pe-5 password-input"
                                                                placeholder="Old password" id="password" required
                                                                v-model="changePasswordData.old_password"
                                                                :class="{ 'is-invalid': changePasswordData.errors.old_password }"
                                                                autocomplete="new-password">
                                                            <BButton variant="link"
                                                                class="position-absolute end-0 top-0 text-decoration-none text-muted password-addon"
                                                                type="button"
                                                                @click="toggleOldPassword = !toggleOldPassword"><i
                                                                    class="ri-eye-fill align-middle"></i></BButton>
                                                            <div class="invalid-feedback">
                                                                {{ changePasswordData.errors.old_password }}
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="mb-3">
                                                        <InputLabel for="new_password" value="Confirm Password" />
                                                        <div class="position-relative auth-pass-inputgroup">
                                                            <input :type="toggleNewPassword ? 'text' : 'password'"
                                                                class="form-control pe-5 password-input"
                                                                placeholder="New password" id="new_password" required
                                                                v-model="changePasswordData.new_password"
                                                                :class="{ 'is-invalid': changePasswordData.errors.new_password }"
                                                                autocomplete="new-password">
                                                            <BButton variant="link"
                                                                class="position-absolute end-0 top-0 text-decoration-none text-muted password-addon"
                                                                type="button"
                                                                @click="toggleNewPassword = !toggleNewPassword"><i
                                                                    class="ri-eye-fill align-middle"></i></BButton>
                                                            <div class="invalid-feedback">
                                                                {{ changePasswordData.errors.new_password }}
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="hstack gap-2 justify-content-end">
                                                        <BButton type="button" variant="light"
                                                            @click="createModalPassword = false" id="closemodal">
                                                            Tutup
                                                        </BButton>
                                                        <BButton type="button" variant="primary" id="add-btn"
                                                            @click="changePassword">
                                                            Kirim
                                                        </BButton>
                                                    </div>
                                                </b-form>
                                            </BModal>
                                        </BCol>
                                        <BCol lg="12">
                                            <div class="hstack gap-2 justify-content-end">
                                                <BButton type="button" @click="submit" variant="primary"
                                                    :disabled="profileData.processing">
                                                    Update
                                                </BButton>
                                            </div>
                                        </BCol>
                                    </BRow>
                                </form>
                            </div>
                            <div v-if="paramsData.page=='payment'" class="nav-item" title="Pembayaran" :active="paramsData.page==='payment'">
                                <div class="alert alert-success mt-5"
                                    v-if="page.props && page.props.message && page.props.message.status == 'success'">
                                    {{ page.props.message.message }}
                                </div>
                                <div class="alert alert-danger mt-5"
                                    v-if="page.props && page.props.message && page.props.message.status == 'error'">
                                    {{ page.props.message.message }}
                                </div>
                                    <div class="d-flex justify-content-between mt-3">
                                            <label for="choices-publish-status-input" class="form-label">Akun Bank</label>
                                            <div class="cursor-pointer text-secondary fw-bold"
                                                @click="accountBankShow = !accountBankShow">+ Tambah No. Rekening</div>
                                            <BModal v-model="accountBankShow" hide-footer title="Informasi  Bank" size="lg"
                                                class="v-modal-custom">
                                                <div class="row">
                                                    <div class="col-lg-12">
                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold"
                                                                for="product-title-input">Bank</label>
                                                            <Multiselect class="form-control"
                                                                :class="{ 'is-invalid': bankAccountError.bank }"
                                                                placeholder="Select option" v-model="tempBankAccount.bank"
                                                                :close-on-select="true" :searchable="true"
                                                                :create-option="true" :options="listBank" />
                                                            <div class="text-danger" v-if="bankAccountError.bank">
                                                                {{ bankAccountError.bank[0] }}
                                                            </div>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold">Atas Nama</label>
                                                            <input v-model='tempBankAccount.atas_nama' class="form-control"
                                                                :class="{ 'is-invalid': bankAccountError.atas_nama }"
                                                                type="text">
                                                            <div class="text-danger" v-if="bankAccountError.atas_nama">
                                                                {{ bankAccountError.atas_nama[0] }}
                                                            </div>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold">Nomor Rekening</label>
                                                            <input v-model="tempBankAccount.rekening" class="form-control"
                                                                :class="{ 'is-invalid': bankAccountError.rekening }"
                                                                type="number">
                                                            <div class="text-danger" v-if="bankAccountError.rekening">
                                                                {{ bankAccountError.rekening[0] }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer v-modal-footer">
                                                    <BButton type="button" variant="primary" @click="addBankAccount">
                                                        Tambah Akun Bank
                                                    </BButton>
                                                </div>
                                            </BModal>
                                        </div>
                                       
                                <div class="mb-3 mt-3">
                                        <div v-for="(item, index) in bankData"
                                            class="border py-4 px-2 d-flex justify-content-between align-items-center">
                                            <div>
                                                {{ index + 1 }}. {{ item.bank }} - {{ item.rekening }} - {{ item.atas_nama
                                                }}
                                            </div>
                                            <div @click="deleteAccountBank(index)">
                                                <i class="bx bx-trash fs-2 cursor-pointer"></i>
                                            </div>
                                        </div>
                                    </div>
                            </div>
                        </div>
                    </BCardBody>
                </BCard>
            </BCol>
        </BRow>
    </Layout>
</template>