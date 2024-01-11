<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import Checkbox from '@/Components/Checkbox.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';

defineProps({
    loginError: String,
});

const form = useForm({
    email: '',
    password: '',
});

const submit = () => {
    form
        .transform((data) => ({
            ...data
        }))
        .post(route('login'), {
            onSuccess: (response) => {
                // form.reset('password');
                console.log(response)
                // console.log(response.component)
                if(response.component=='Auth/Login'){
                    window.location.href = '/dashboard';
                }
                // axios.get('token_refresh')
                //     .then(response => {
                //         console.log('create token', response.data);
                //     })
                //     .catch(error => {
                //         console.error(error);
                //     });
            }
        });
};

</script>

<script>
export default {
    data() {
        return {
            togglePassword: false
        }
    }
}
</script>

<style>
ul li {
    list-style-type: none;
    font-size: 18px;
    color: #638091;
}

ul {
    padding: 0px;
}
</style>

<template>
    <Head title="Log in" />
    <div class="auth-page-wrapper" style="background-color: #F6F8FA;">
        <div class="p-2">
            <img style="height: 50px;" src="@assets/images/logo-white.png" alt="" class="img-fluid">
        </div>
        <div class="auth-page-content mt-5">
            <BContainer>
                <BRow>
                    <BCol class="d-flex align-items-center">
                        <div class="p-3">
                            <h1 class="mb-5 fw-bold">Everything you need to grow your business on <span
                                    class="text-success">WhatsApp</span>
                            </h1>
                            <ul>
                                <li>
                                    <i class="bx bx-check"></i>Targeted Campaigns to deliver personalized offers
                                </li>
                                <li>
                                    <i class="bx bx-check"></i>Pre-built templates to send updates & reminders
                                </li>
                                <li>
                                    <i class="bx bx-check"></i>24x7 instant engagement with no-code chatbots
                                </li>
                                <li>
                                    <i class="bx bx-check"></i>Powerful automations to resolve issues faster
                                </li>
                                <li>
                                    <i class="bx bx-check"></i>Integrations to bring in context from Zoho, Shopify, etc.
                                </li>
                            </ul>
                        </div>
                    </BCol>
                    <BCol md="12" lg="6" xl="6">
                        <BCard no-body class="mt-4">

                            <BCardBody class="p-4">

                                <div class="text-center mt-2 img-thumbnail">
                                    <img style="height: 50px;" src="@assets/images/logo-white.png" alt="" class="img-fluid">
                                </div>
                                <p class="text-muted mt-3 text-center">Sign in to continue to aplikasiWA.</p>
                                <div v-if="loginError" class="alert alert-danger text-danger">
                                    {{ loginError }}
                                </div>
                                <div class="p-2 mt-3">
                                    <form @submit.prevent="submit">

                                        <div class="mb-3">
                                            <InputLabel for="email" value="Email" />
                                            <TextInput id="email" v-model="form.email" type="email" class="form-control"
                                                autofocus placeholder="Please enter email" autocomplete="email" required
                                                :class="{ 'is-invalid': form.errors.email }" />
                                            <InputError :message="form.errors.email" />
                                        </div>

                                        <div class="mb-3">
                                            <!-- <div class="float-end">
                                                                                        <Link v-if="canResetPassword" :href="route('password.request')"
                                                                                    class="text-muted">Forgot
                                            password?</Link>
                                        </div> -->
                                            <InputLabel for="password" value="Password" />
                                            <div class="position-relative auth-pass-inputgroup mb-3">
                                                <input :type="togglePassword ? 'text' : 'password'"
                                                    class="form-control pe-5" placeholder="Enter password"
                                                    id="password-input" v-model="form.password" autocomplete="password"
                                                    required :class="{ 'is-invalid': form.errors.password }">
                                                <BButton variant="link"
                                                    class="position-absolute end-0 top-0 text-decoration-none text-muted"
                                                    type="button" id="password-addon"
                                                    @click="togglePassword = !togglePassword">
                                                    <i class="ri-eye-fill align-middle"></i>
                                                </BButton>
                                                <InputError :message="form.errors.password" />
                                            </div>
                                        </div>

                                        <div class="mt-4">
                                            <BButton variant="secondary" class="w-100" type="submit"
                                                :class="{ 'opacity-25': form.processing }" :disabled="form.processing">Sign
                                                In</BButton>
                                        </div>
                                    </form>
                                </div>
                            </BCardBody>
                        </BCard>

                        <div class="mt-4 text-center">
                            <p class="mb-0">Belum punya akun ?
                                <Link :href="route('register')" class="fw-semibold text-primary text-decoration-underline">
                                Signup </Link>
                            </p>
                        </div>

                    </BCol>
                </BRow>
            </BContainer>
        </div>
        <!-- 
                                                                                                                                                                                                                                <footer class="footer">
                                                                                                                                                                                                                                    <BContainer>
                                                                                                                                                                                                                                        <BRow>
                                                                                                                                                                                                                                            <BCol lg="12">
                                                                                                                                                                                                                                                <div class="text-center">
                                                                                                                                                                                                                                                    <p class="mb-0 text-muted">&copy; {{ new Date().getFullYear() }} Velzon. Crafted with <i
                                                                                                                                                                                                                                                            class="mdi mdi-heart text-danger"></i> by Themesbrand</p>
                                                                                                                                                                                                                                                </div>
                                                                                                                                                                                                                                            </BCol>
                                                                                                                                                                                                                                        </BRow>
                                                                                                                                                                                                                                    </BContainer>
                                                                                                                                                                                                                                </footer> -->
    </div>
</template>
