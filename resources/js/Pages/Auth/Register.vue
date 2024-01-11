<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import Checkbox from '@/Components/Checkbox.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';

const form = useForm({
    name: '',
    wa: '',
    email: '',
    password: '',
    password_confirmation: '',
    payment_method: 'Bank Transfer',
    permission: {
        dashboard: {
            read: true,
            create: true,
            update: true,
            delete: true
        },
        product: {
            read: true,
            create: true,
            update: true,
            delete: true
        },
        order: {
            read: true,
            create: true,
            update: true,
            delete: true
        },
        checkout: {
            read: true,
            create: true,
            update: true,
            delete: true
        },
        team: {
            read: true,
            create: true,
            update: true,
            delete: true
        }
    }
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<script>
export default {
    data() {
        return {
            togglePassword: false,
            togglePassword_conf: false,
        }
    }
}
</script>

<template>
    <Head title="Register" />

    <div class="auth-page-wrapper pt-5">
        <div class="auth-one-bg-position auth-one-bg" id="auth-particles">
            <div class="bg-overlay"></div>

            <div class="shape">

                <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink"
                    viewBox="0 0 1440 120">
                    <path d="M 0,36 C 144,53.6 432,123.2 720,124 C 1008,124.8 1296,56.8 1440,40L1440 140L0 140z"></path>
                </svg>
            </div>
        </div>

        <div class="auth-page-content">
            <BContainer>
                <BRow>
                    <BCol lg="12">
                        <div class="text-center mt-sm-5 mb-4 text-white-50">
                            <p class="mt-3 fs-15 fw-medium text-white">Olshop AplikasiWA</p>
                            <h1 class="text-white">Tingkatkan Konversi Penjualan Online Anda</h1>
                        </div>
                    </BCol>
                </BRow>

                <BRow class="justify-content-center">
                    <BCol md="12" lg="12" xl="12">
                        <BCard no-body class="mt-4">

                            <BCardBody class="p-4">
                                <Brow class="row">
                                    <BCol xl="8">
                                        <div class="text-left mt-2">
                                            <h3 class="text-primary fw-bold">DATA PENERIMA :</h3>

                                        </div>
                                        <div class="p-2 mt-1">
                                            <form @submit.prevent="submit" class="needs-validation" novalidate>
                                                <div class="mb-3">
                                                    <InputLabel for="name" value="Nama Anda" />
                                                    <TextInput id="name" v-model="form.name" type="text"
                                                        class="mt-1 block w-full" required autofocus autocomplete="name"
                                                        placeholder="Enter your name"
                                                        :class="{ 'is-invalid': form.errors.name }" />
                                                    <InputError :message="form.errors.name" />
                                                </div>
                                                <div class="mb-3">
                                                    <InputLabel for="wa" value="No Wa Anda" />
                                                    <TextInput id="wa" v-model="form.wa" type="number"
                                                        class="mt-1 block w-full" required autofocus autocomplete="wa"
                                                        placeholder="Enter your wa"
                                                        :class="{ 'is-invalid': form.errors.wa }" />
                                                    <InputError :message="form.errors.wa" />
                                                </div>
                                                <div class="mb-3">
                                                    <InputLabel for="email" value="Email" />
                                                    <TextInput id="email" v-model="form.email" type="email"
                                                        class="mt-1 block w-full" required autocomplete="email"
                                                        :class="{ 'is-invalid': form.errors.email }"
                                                        placeholder="Enter your email" />
                                                    <InputError :message="form.errors.email" />
                                                </div>
                                                <div class="mb-3">
                                                    <InputLabel for="password" value="Password" />
                                                    <div class="position-relative auth-pass-inputgroup">
                                                        <input :type="togglePassword ? 'text' : 'password'"
                                                            class="form-control pe-5 password-input"
                                                            placeholder="Enter password" id="password" required
                                                            v-model="form.password"
                                                            :class="{ 'is-invalid': form.errors.password }"
                                                            autocomplete="new-password">
                                                        <BButton variant="link"
                                                            class="position-absolute end-0 top-0 text-decoration-none text-muted password-addon"
                                                            type="button" @click="togglePassword = !togglePassword"><i
                                                                class="ri-eye-fill align-middle"></i></BButton>
                                                        <InputError :message="form.errors.password" />
                                                    </div>
                                                </div>

                                                <div class="mb-3">
                                                    <InputLabel for="password_confirmation" value="Confirm Password" />
                                                    <div class="position-relative auth-pass-inputgroup">
                                                        <input :type="togglePassword_conf ? 'text' : 'password'"
                                                            class="form-control pe-5 password-input"
                                                            placeholder="Enter password" id="password_confirmation" required
                                                            v-model="form.password_confirmation"
                                                            :class="{ 'is-invalid': form.errors.password_confirmation }"
                                                            autocomplete="new-password">
                                                        <BButton variant="link"
                                                            class="position-absolute end-0 top-0 text-decoration-none text-muted password-addon"
                                                            type="button"
                                                            @click="togglePassword_conf = !togglePassword_conf"><i
                                                                class="ri-eye-fill align-middle"></i></BButton>
                                                        <InputError :message="form.errors.password_confirmation" />
                                                    </div>
                                                </div>
                                                <div class="text-left mt-2">
                                                    <h3 class="text-primary fw-bold">METODE PEMBAYARAN :</h3>
                                                    {{ form.payment_method }}
                                                </div>
                                                <div class="live-preview">
                                                    <BListGroup>
                                                        <BListGroupItem tag="label">
                                                            <input name="payment_method" v-model="form.payment_method"
                                                                class="form-check-input me-1" type="radio"
                                                                value="Bank Transfer">
                                                            Bank Transfer
                                                        </BListGroupItem>
                                                        <BListGroupItem tag="label">
                                                            <input name="payment_method" v-model="form.payment_method"
                                                                class="form-check-input me-1" type="radio"
                                                                value="Virtual Account"> BCA
                                                            Virtual Account
                                                        </BListGroupItem>
                                                        <BListGroupItem tag="label">
                                                            <input name="payment_method" v-model="form.payment_method"
                                                                class="form-check-input me-1" type="radio" value="Midtrans">
                                                            Midtrans
                                                        </BListGroupItem>

                                                    </BListGroup>
                                                </div>
                                                <div class="mt-4">
                                                    <BButton variant="secondary" class="w-100" type="submit"
                                                        :class="{ 'opacity-25': form.processing }"
                                                        :disabled="form.processing">PESAN SEKARANG</BButton>
                                                </div>
                                            </form>
                                        </div>

                                    </BCol>
                                    <BCol xl="4">
                                        <div>
                                            <div class="img-thumbnail p-4">
                                                <img class="img-fluid" src="@assets/images/aplikasiwa.png" alt="">
                                            </div>
                                        </div>
                                        <div class="p-4">
                                            <h2>Yang Anda Dapatkan:</h2>
                                            <div>
                                                <ul>
                                                    <li>100% web-based software - Anda tinggal login lewat browser, tidak
                                                        perlu
                                                        install atau download apapun.</li>
                                                    <li>Gratis support dan updates.</li>
                                                    <li>Tutorial lengkap & komunitas Facebook group.</li>
                                                </ul>
                                            </div>
                                        </div>

                                        <div class="card" style="border:2px solid #5B71B9">
                                            <div class="card-body">
                                                <p class="text-decoration-underline">RINCIAN PESANAN</p>
                                                <table class="w-100">
                                                    <tr>
                                                        <td>AplikasiWA Personal</td>
                                                        <td>Rp 343.000</td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            Kode Unik
                                                        </td>
                                                        <td></td>
                                                    </tr>
                                                    <tr class="fw-bold border-top">
                                                        <td class="pt-2">
                                                            Total
                                                        </td>
                                                        <td class="pt-2">
                                                            Rp 345.000
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>

                                    </BCol>
                                </Brow>

                            </BCardBody>
                        </BCard>

                        <div class="mt-4 text-center">
                            <p class="mb-0">Already have an account ?
                                <Link :href="route('login')" class="fw-semibold text-primary text-decoration-underline">
                                LOGIN </Link>
                            </p>
                        </div>
                    </BCol>
                </BRow>
            </BContainer>
        </div>

        <!-- <footer class="footer">
                                                                                                                                                                                                                                                                                                                                                    <BContainer>
                                                                                                                                                                                                                                                                                                                                                        <BRow>
                                                                                                                                                                                                                                                                                                                                                            <BCol lg="12">
                                                                                                                                                                                                                                                                                                                                                                <div class="text-center">
                                                                                                                                                                                                                                                                                                                                                                    <p class="mb-0 text-muted">&copy; {{ new Date().getFullYear() }} Velzon. Crafted with <i class="mdi mdi-heart text-danger"></i> by Themesbrand</p>
                                                                                                                                                                                                                                                                                                                                                                </div>
                                                                                                                                                                                                                                                                                                                                                            </BCol>
                                                                                                                                                                                                                                                                                                                                                        </BRow>
                                                                                                                                                                                                                                                                                                                                                    </BContainer>
                                                                                                                                                                                                                                                                                                                                            </footer> -->
    </div>
</template>
