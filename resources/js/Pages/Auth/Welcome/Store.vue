<script>
import { Head, Link, useForm } from '@inertiajs/vue3';
import Layout from "@/Layouts/main.vue";
import PageHeader from "@/Components/page-header.vue";
import { ref, watch } from "vue";
import useVuelidate from "@vuelidate/core";
import DropZone from "@/Components/widgets/dropZone.vue";
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';

export default {
    methods: {
        deleteRecord(ele) {
            // Menggunakan manipulasi DOM yang benar untuk menghapus elemen
            // ele.target.parentElement.parentElement.remove();
            this.files = []
        },

        submit() {
            const formData = new FormData();
            this.form.foto = this.files[0]

            formData.append('username', this.form.username);
            formData.append('email', this.form.email);
            formData.append('name', this.form.name);
            formData.append('phone', this.form.phone);
            formData.append('address', this.form.address);
            formData.append('web', this.form.web);
            formData.append('description', this.form.description);
            formData.append('foto', this.form.foto);

            this.form.post(route('welcome.store'), {
                onFinish: (response) => {
                    console.log(this.form.errors.foto)
                },
            });
        }
    },
    setup() {
        const files = ref([]);
        const dropzoneFile = ref("");

        const drop = (e) => {
            dropzoneFile.value = e.dataTransfer.files[0];
            files.value.push(dropzoneFile.value);
            console.log(files)
        };

        const selectedFile = () => {
            dropzoneFile.value = document.querySelector(".dropzoneFile").files[0];
            files.value.push(dropzoneFile.value);
            console.log(files.value)
        };

        watch(
            () => [...files.value],
            (currentValue) => {
                console.log('current Value', currentValue)
                // this.form.foto = currentValue;
                // console.log(this.form)
                // Lakukan sesuatu dengan nilai currentValue jika diperlukan
            }
        );

        // Memindahkan setup form di luar pernyataan return
        const form = useForm({
            username: '',
            email: '',
            name: '',
            phone: '',
            web: '',
            address: '',
            description: '',
            foto: null
        });

        return { dropzoneFile, files, drop, selectedFile, v$: useVuelidate(), form };
    },

    components: {
        Layout,
        PageHeader,
        DropZone,
        Link,
        Head
    },
};
</script>


<style>
.form-control,
.input-group-text {
    border: 2px solid #9D9FA0;
    height: 60px;
}

.border-danger {
    border: 1px solid red !important;
}
</style>
<template>
    <Head title="Informasi Toko" />

    <div class="auth-page-wrapper min-vh-100" style="background-color: #F6F8FA;">
        <div class="auth-page-content overflow-hidden pt-lg-5">
            <BContainer>
                <Brow>
                    <form @submit.prevent="submit" class="needs-validation" novalidate>
                        <BCol>
                            <div class="p-2">
                                <h3 class="fw-bold text-center fs-2">Lengkapi Identitas Tokomu</h3>
                                <p class="text-center fs-4">Isi identitas toko seperti nama, alamat, dan
                                    deskripsi agar mudah
                                    dikenali oleh pelanggan</p>
                            </div>
                            <div class="mb-3">
                                <label for="iconrightInput" class="form-label fw-bold">Username*</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" v-model="form.username"
                                        :class="{ 'is-invalid': form.errors.username }" placeholder="Username anda..."
                                        aria-label="Recipient's username" aria-describedby="basic-addon2">
                                    <span class="input-group-text" id="basic-addon2">example.com/your-product</span>
                                </div>
                                <div class="text-danger">
                                    {{ form.errors.username }}
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="iconrightInput" class="form-label fw-bold">Nama Toko*</label>
                                <input type="text" class="form-control" v-model="form.name"
                                    :class="{ 'is-invalid': form.errors.name }" placeholder="Nama Toko Anda..."
                                    aria-label="Recipient's name" aria-describedby="basic-addon2">
                                <div class="text-danger">
                                    {{ form.errors.name }}
                                </div>
                            </div>
                            <div class="row">
                                <BCol md="4">
                                    <div class="mb-3">
                                        <label for="iconrightInput" class="form-label fw-bold">Nomor Telepon*</label>
                                        <input type="number" class="form-control" v-model="form.phone"
                                            :class="{ 'is-invalid': form.errors.phone }" placeholder="Nomor Telepon Toko..."
                                            aria-label="Recipient's name" aria-describedby="basic-addon2">
                                        <div class="text-danger">
                                            {{ form.errors.phone }}
                                        </div>
                                    </div>
                                </BCol>
                                <BCol md="4">
                                    <div class="mb-3">
                                        <label for="iconrightInput" class="form-label fw-bold">Alamat Email Toko
                                            Anda*</label>
                                        <input type="text" class="form-control" v-model="form.email"
                                            :class="{ 'is-invalid': form.errors.email }" placeholder="Email Toko Anda..."
                                            aria-label="Recipient's email" aria-describedby="basic-addon2">
                                        <div class="text-danger">
                                            {{ form.errors.email }}
                                        </div>
                                    </div>
                                </BCol>
                                <BCol md="4">
                                    <div class="mb-3">
                                        <label for="iconrightInput" class="form-label fw-bold">Halaman Website
                                            (Opsional)</label>
                                        <input type="text" class="form-control" v-model="form.web"
                                            :class="{ 'is-invalid': form.errors.web }" placeholder="Web Toko Anda..."
                                            aria-label="Recipient's web" aria-describedby="basic-addon2">
                                        <div class="text-danger">
                                            {{ form.errors.web }}
                                        </div>
                                    </div>
                                </BCol>
                            </div>
                            <div class="mb-3">
                                <label for="exampleFormControlTextarea5" class="form-label fw-bold">Deskripsi Toko*</label>
                                <textarea class="form-control" id="exampleFormControlTextarea5" v-model="form.description"
                                    :class="{ 'is-invalid': form.errors.description }" rows="3"></textarea>
                                <div class="text-danger">
                                    {{ form.errors.description }}
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="exampleFormControlTextarea5" class="form-label fw-bold">Alamat Toko*</label>
                                <textarea class="form-control" id="exampleFormControlTextarea5" v-model="form.address"
                                    :class="{ 'is-invalid': form.errors.address }" rows="3"></textarea>
                                <div class="text-danger">
                                    {{ form.errors.address }}
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="exampleFormControlTextarea5" class="form-label fw-bold">Logo Toko*</label>
                                <div v-if="files.length == 0">
                                    <DropZone :class="{ 'border-danger': form.errors.foto }" @drop.prevent="drop"
                                        @change="selectedFile" />
                                </div>

                                <ul class="list-unstyled mb-0" id="dropzone-preview">
                                    <div class="border rounded" v-for="(file, index) of files" :key="index">
                                        <div class="d-flex p-2">
                                            <div class="flex-grow-1">
                                                <div class="pt-1">
                                                    <h5 class="fs-14 mb-1" data-dz-name="">
                                                        {{ file.name }}
                                                    </h5>
                                                    <p class="fs-13 text-muted mb-0" data-dz-size="">
                                                        <strong>{{ file.size / 1024 }}</strong> KB
                                                    </p>
                                                    <strong class="error text-danger" data-dz-errormessage=""></strong>
                                                </div>
                                            </div>
                                            <div class="flex-shrink-0 ms-3">
                                                <BButton variant="danger" size="sm" data-dz-remove="" @click="deleteRecord">
                                                    Delete
                                                </BButton>
                                            </div>
                                        </div>
                                    </div>
                                </ul>
                                <div class="text-danger">
                                    {{ form.errors.foto }}
                                </div>
                            </div>
                        </BCol>
                        <div class="d-flex justify-content-center">
                            <button class="btn btn-primary btn-lg px-5">Simpan Data</button>
                        </div>
                    </form>
                </Brow>
            </BContainer>
        </div>
    </div>
</template>
                