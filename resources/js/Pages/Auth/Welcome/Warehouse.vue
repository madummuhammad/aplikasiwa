<script>
import { Head, Link, useForm } from '@inertiajs/vue3';
import Layout from "@/Layouts/main.vue";
import { ref, watch } from "vue";
import PageHeader from "@/Components/page-header.vue";
import { onMounted, onBeforeMount } from 'vue';
export default {
    setup() {
        const form = ref(useForm({
            name: '',
            pic: '',
            address: '',
            province: '',
            city: '',
            subdistrict: '',
            postcode: '',
            phone: '',
            second_phone: ''
        }));

        let provinceData = ref([]);
        let cityData = ref([]);
        let kecamatanData = ref([]);
        const province = ref("");
        const city = ref("");
        const subdistrict = ref("");

        const getProvince = () => {
            axios.get('/api/rajaongkir/province')
                .then(response => {
                    let data = response.data;
                    console.log('Data berhasil dikirim ke backend:', data);
                    provinceData.value = data.rajaongkir.results;
                    console.log(data.rajaongkir.results)
                })
                .catch(error => {
                    console.error('Error saat mengirim data ke backend:', error);
                });
        }

        const getCity = () => {
            cityData.value = [];
            kecamatanData.value = [];
            form.value.city = "";
            city.value = "";
            subdistrict.value = ""
            axios.get('/api/rajaongkir/city?province_id=' + form.value.province.province_id)
                .then(response => {
                    let data = response.data;
                    console.log('Data berhasil dikirim ke backend:', data);
                    cityData.value = data.rajaongkir.results;
                    // console.log(data.rajaongkir.results)
                })
                .catch(error => {
                    console.error('Error saat mengirim data ke backend:', error);
                });
        }

        const getKecamatan = () => {
            kecamatanData.value = [];
            form.value.kecamatanData = "";
            subdistrict.value = ""
            axios.get('/api/rajaongkir/kecamatan?city_id=' + form.value.city.city_id)
                .then(response => {
                    let data = response.data;
                    console.log('Data berhasil dikirim ke backend:', data);
                    kecamatanData.value = data.rajaongkir.results;
                    // console.log(data.rajaongkir.results)
                })
                .catch(error => {
                    console.error('Error saat mengirim data ke backend:', error);
                });
        }

        onMounted(() => {
            getProvince();
        })

        return {
            form,
            provinceData,
            cityData,
            getCity,
            kecamatanData,
            getKecamatan,
            province,
            city,
            subdistrict
        }
    },
    methods: {
        submit() {
            this.form.post(route('welcome.warehouse'))
            console.log('form', this.form)
        },

        selectProvince(value) {
            this.form.province = value;
            this.province = value.province;
            this.getCity();
        },

        selectCity(value) {
            this.form.city = value;
            this.city = value.city_name;
            this.getKecamatan();
        },

        selectSubdistrict(value) {
            this.form.subdistrict = value;
            this.subdistrict = value.subdistrict_name;
        }
    },
    props: {

    },
    data() {
        return {
            provinceStatus: false,
            // province: "",
            cityStatus: false,
            // city: "",
            subdistrictStatus: false,
            // subdistrict: "",
        }
    },
    components: {
        Layout,
        PageHeader,
        Link,
        Head
    },
}
</script>
<style>
.form-control,
.input-group-text {
    border: 2px solid #9D9FA0;
    height: 60px;
}
</style>
<template>
    <Head title="Gudang" />

    <div class="auth-page-wrapper min-vh-100" style="background-color: #F6F8FA;">
        <div class="auth-page-content overflow-hidden pt-lg-5">
            <BContainer>
                <form @submit.prevent="submit">
                    <Brow>
                        <BCol>
                            <div class="p-2">
                                <h3 class="fw-bold text-center fs-2">Atur Asal Pengiriman</h3>
                                <p class="text-center fs-4">Atur alamat asal pengiriman barang untuk perhitungan biaya
                                    ongkos
                                    kirim</p>
                            </div>
                            <div class="row">
                                <BCol lg="6">
                                    <div class="mb-3">
                                        <label for="iconrightInput" class="form-label fw-bold">Nama Gudang*</label>
                                        <input type="text" class="form-control" v-model="form.name"
                                            :class="{ 'is-invalid': form.errors.name }" placeholder="Nama Gudang Anda..."
                                            aria-label="Recipient's username" aria-describedby="basic-addon2">
                                        <div class="text-danger">
                                            {{ form.errors.name }}
                                        </div>
                                    </div>
                                </BCol>
                                <BCol lg="6">
                                    <div class="mb-3">
                                        <label for="iconrightInput" class="form-label fw-bold">Nama Penanggung Jawab
                                            Gudang*</label>
                                        <input type="text" class="form-control" v-model="form.pic"
                                            :class="{ 'is-invalid': form.errors.pic }"
                                            placeholder="Nama Penanggung Jawab..." aria-label="Recipient's username"
                                            aria-describedby="basic-addon2">
                                        <div class="text-danger">
                                            {{ form.errors.pic }}
                                        </div>
                                    </div>
                                </BCol>
                            </div>
                            <div class="mb-3">
                                <label for="exampleFormControlTextarea5" class="form-label fw-bold">Alamat Gudang*</label>
                                <textarea class="form-control" id="exampleFormControlTextarea5" rows="3"
                                    v-model="form.address" :class="{ 'is-invalid': form.errors.address }"></textarea>
                                <div class="text-danger">
                                    {{ form.errors.address }}
                                </div>
                            </div>
                            <div class="row mb-3">
                                <BCol md="3">
                                    <div>
                                        <label class="form-label">Province*</label>
                                        <div data-input-flag data-option-flag-name @click="provinceStatus = !provinceStatus"
                                            id="simpleSelect">
                                            <input type="text" class="form-control rounded-end flag-input"
                                                :class="{ 'is-invalid': form.errors.province }" readonly
                                                placeholder="Select province" v-model="province">
                                            <div class="dropdown-menu w-100" :class="{ show: provinceStatus }"
                                                id="simpleSelectMenu">
                                                <ul class="list-unstyled dropdown-menu-list mb-0">
                                                    <li class="dropdown-item d-flex cursor-pointer"
                                                        v-for="(item, index) in provinceData" @click="selectProvince(item)">
                                                        {{ item.province }}
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="text-danger">
                                            {{ form.errors.province }}
                                        </div>
                                    </div>
                                </BCol>
                                <BCol md="3">
                                    <div>
                                        <label class="form-label">Kota*</label>
                                        <div data-input-flag data-option-flag-name @click="cityStatus = !cityStatus"
                                            id="simpleSelect">
                                            <input type="text" class="form-control rounded-end flag-input"
                                                :class="{ 'is-invalid': form.errors.city }" readonly
                                                placeholder="Select city" v-model="city">
                                            <div class="dropdown-menu w-100" :class="{ show: cityStatus }"
                                                id="simpleSelectMenu">
                                                <ul class="list-unstyled dropdown-menu-list mb-0">
                                                    <li class="dropdown-item d-flex cursor-pointer"
                                                        v-for="(item, index) in cityData" @click="selectCity(item)">
                                                        {{ item.city_name }}
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="text-danger">
                                            {{ form.errors.city }}
                                        </div>
                                    </div>
                                </BCol>
                                <BCol md="3">
                                    <div>
                                        <label class="form-label">Kecamatan*</label>
                                        <div data-input-flag data-option-flag-name
                                            @click="subdistrictStatus = !subdistrictStatus" id="simpleSelect">
                                            <input type="text" class="form-control rounded-end flag-input"
                                                :class="{ 'is-invalid': form.errors.subdistrict }" readonly
                                                placeholder="Select subdistrict" v-model="subdistrict">
                                            <div class="dropdown-menu w-100" :class="{ show: subdistrictStatus }"
                                                id="simpleSelectMenu">
                                                <ul class="list-unstyled dropdown-menu-list mb-0">
                                                    <li class="dropdown-item d-flex cursor-pointer"
                                                        v-for="(item, index) in kecamatanData"
                                                        @click="selectSubdistrict(item)">
                                                        {{ item.subdistrict_name }}
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="text-danger">
                                            {{ form.errors.subdistrict }}
                                        </div>
                                    </div>
                                </BCol>
                                <BCol md="3">
                                    <div class="mb-3">
                                        <label for="iconrightInput" class="form-label fw-bold">Kode Pos*</label>
                                        <input type="text" class="form-control"
                                            :class="{ 'is-invalid': form.errors.postcode }" v-model="form.postcode"
                                            placeholder="Kode pos..." aria-label="Recipient's username"
                                            aria-describedby="basic-addon2">
                                        <div class="text-danger">
                                            {{ form.errors.postcode }}
                                        </div>
                                    </div>
                                </BCol>
                            </div>
                            <div class="row mb-3">
                                <div class="col-6">
                                    <div class="mb-3">
                                        <label for="iconrightInput" class="form-label fw-bold">Nomor Telepon*</label>
                                        <input type="text" class="form-control" placeholder="Nomor Telepon..."
                                            :class="{ 'is-invalid': form.errors.phone }" v-model="form.phone"
                                            aria-label="Recipient's username" aria-describedby="basic-addon2">
                                        <div class="text-danger">
                                            {{ form.errors.phone }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="mb-3">
                                        <label for="iconrightInput" class="form-label fw-bold">Second Phone Number*</label>
                                        <input type="text" class="form-control" placeholder="Second Phone Number..."
                                            :class="{ 'is-invalid': form.errors.second_phone }" v-model="form.second_phone"
                                            aria-label="Recipient's username" aria-describedby="basic-addon2">
                                        <div class="text-danger">
                                            {{ form.errors.second_phone }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </BCol>
                        <div class="d-flex justify-content-center">
                            <button class="btn btn-primary btn-lg px-5" type="submit"
                                :class="{ 'opacity-25': form.processing }" :disabled="form.processing">Simpan Data</button>
                        </div>
                    </Brow>
                </form>

            </BContainer>
        </div>
    </div>
</template>