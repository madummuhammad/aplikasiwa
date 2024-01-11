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
import { Link, Head } from '@inertiajs/vue3';
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
import { Global } from '../../global'
export default {
    data(props) {
        return {
            debounceTimer: null,
        };
    },
    watch:{
        'productData.url': async function(newVal){
            clearTimeout(this.debounceTimer);
            this.debounceTimer = setTimeout(async () => {
                console.log(newVal);
                const response = await axios.post('/api/products/check_url', { url: newVal});

                let data=response.data;
                console.log(response.data);
                console.log(this.productDataError)
                if(data.status=='error'){
                    this.productDataError.url='URL Sudah Dipakai'
                }
            }, 500);
        }
    },
    setup(props) {
        const global = Global();
        const submit = () => {

            const postData = {
                productData: { ...productData.value, size: size.value },
                checkoutData: { ...checkoutData.value },
                successData: { ...successData.value },
                rotatorData: {
                    ...rotatorData.value
                }
            };

            console.log(postData)
            axios.post('/api/products', postData)
                .then(response => {
                    console.log('Data berhasil dikirim ke backend:', response.data);
                    const newUrl = '/product/' + response.data.data.id;
                    window.history.pushState({ path: newUrl }, '', newUrl);
                    window.location.href = newUrl;
                })
                .catch(error => {
                    console.error('Error saat mengirim data ke backend:', error);
                });
        }

        let teamData = ref(props.team);
        let teamOption = ref([]);

        onMounted(() => {
            console.log('team', teamData);
            teamOption.value = [];
            let selectOption = [];
            teamData.value.forEach((item, index) => {
                selectOption[index] = {
                    value: item.id,
                    label: item.user.name
                }
            });

            teamOption.value = selectOption;

            console.log('teamOption', teamOption.value)
        })

        // Product Data

        let productData = ref({
            name: '',
            url: '',
            product_code: '',
            category_product: [],
            childCategory: [],
            grandChildCategory: [],
            collection: [],
            product_status: 'live',
            image: [],
            normal_price: 0,
            price_sale_status: false,
            price_sale: 0,
            hpp: 0,
            price_type: "simple",
            // variation_status: false,
            variation_name: 'Ukuran',
            many_variation_status: false,
            many_buy_status: false,
            child_variation_name: 'Warna',
            variation: [],
            manajemen_stock: false,
            stock: 0,
            stock_status: 'tersedia',
            backorder_status: false,
            bump_status: false,
            product_bump: {
                name: '',
                price: 0,
                weight: 0,
                image: null
            },
            shipping: {
                weight: 0,
                otomatic_price_shipping: false,
                shipping_type: 'otomatic',
                otomatic: [
                    {
                        name: 'JNE',
                        status: true,
                    },
                    {
                        name: 'J&T',
                        status: true
                    }
                ],
                flat: {
                    price: 0
                }
            },
            bank_transfer: {
                status: false,
                description: ''
            },
            cod: {
                status: false,
                descriptin: ''
            },
            epayment: {
                status: false
            },
            unique_code: {
                status: false,
                type: 'down',
                min: 0,
                max: 999
            },
            reseller_status: false,
            global_cs_rotator_status: false

        });

        let productDataError = ref([]);

        const urlSlug = () => {
            if (productData.value.url === '') {
                productData.value.url = generateSlug(productData.value.name);
            }
        };

        const generateSlug = (text) => {
            return text
                .toLowerCase()
                .replace(/ /g, '-')
                .replace(/[^\w-]+/g, '');
        };

        // Kode Unique
        const uniqueMethod = (type) => {
            productData.value.unique_code.type = type
        }

        const changePriceType = (type) => {
            productData.value.price_type = type;
        }


        // Size Variation
        var sizeValue = ref('')
        let size = ref([]);
        let variation = ref([]);

        const addSize = () => {
            size.value.push(
                {
                    name: sizeValue.value,
                    price: 0,
                    weight: 0,
                    hpp: 0,
                    stock: 0,
                    variation_code: ''
                }
            )
            console.log(size.value)
            sizeValue.value = '';
            addVariation()
        }

        const deleteSize = (index) => {
            size.value.splice(index, 1);
            productData.value.variation.splice(index, 1);
        }

        const copyPriceFalseManyVariation = (price) => {
            size.value.forEach((item, index) => {
                item.price = price
            })
        }

        const copyWeightFalseManyVariation = (weight) => {
            size.value.forEach((item, index) => {
                item.weight = weight
            })
        }

        const copyHppFalseManyVariation = (hpp) => {
            size.value.forEach((item, index) => {
                item.hpp = hpp
            })
        }

        const copyPriceTrueManyVariation = (price) => {

            productData.value.variation.forEach((item, index) => {
                item.childVariation.forEach((itemColor, index) => {
                    itemColor.price = price
                })
            })
        }
        const copyWeightTrueManyVariation = (weight) => {
            productData.value.variation.forEach((item, index) => {
                item.childVariation.forEach((itemColor, index) => {
                    itemColor.weight = weight
                })
            })
        }

        const copyHppTrueManyVariation = (hpp) => {
            productData.value.variation.forEach((item, index) => {
                item.childVariation.forEach((itemColor, index) => {
                    itemColor.hpp = hpp
                })
            })
        }


        // Color Variation
        var colorValue = ref('')
        let color = ref([]);

        const addColor = () => {
            color.value.push({ name: colorValue.value });
            colorValue.value = '';
            addVariation();
        };


        const deleteColor = (index) => {
            color.value.splice(index, 1);
            size.value.forEach((item, itemIndex) => {
                if (productData.value.variation[itemIndex]) {
                    productData.value.variation[itemIndex].childVariation.splice(index, 1);
                }
            });
            console.log('variation', productData.value.variation);
        };

        const addVariation = () => {
            const childVariation = [];
            size.value.forEach((item, index) => {
                color.value.forEach((itemColor, indexColor) => {
                    childVariation[indexColor] = {
                        name: itemColor.name,
                        price: 0,
                        weight: 0,
                        hpp: 0,
                        variation_code: '',
                        stock: 0
                    };
                });
                productData.value.variation[index] = {
                    name: item.name,
                    childVariation: [...childVariation]
                };
            });
            console.log('variation', productData.value.variation);
        }

        // Tipe Pengiriman

        const shippingMethod = (type) => {
            productData.value.shipping.shipping_type = type;
            console.log('productData.shipping.shipping_type', productData.value.shipping.shipping_type)
        }



        // Product Image
        let dropzoneFile = ref("");
        const drop = (e) => {
            dropzoneFile.value = e.dataTransfer.files[0];
            productData.value.image.push(dropzoneFile.value);
        };

        var tempProductImage = ref('')

        const selectedFile = async (event) => {
            try {
                const file = event.target.files[0];
                const formData = new FormData();
                formData.append('image', file);
                const response = await axios.post('/api/products/upload', formData);
                const data = response.data;

                console.log(data)

                let images = {
                    url: data.data
                }
                tempProductImage.value = images.url;
                productData.value.image.push(images);

            } catch (error) {
                console.error('error', error);
            }

        };


        const deleteProductImage = (index) => {
            productData.value.image.splice(index, 1);
        };

        // Bump Image
        const bumpImageChange = async (event) => {
            try {
                const file = event.target.files[0];
                const formData = new FormData();
                formData.append('image', file);
                const response = await axios.post('/api/products/upload', formData);
                const data = response.data;

                productData.value.product_bump.image = data.data;

            } catch (error) {
                console.error('error', error);
            }
        }

        // Option Category

        let optionChildCategory = ref([]);
        let tempOptionChildCategory = [];

        const selectCategory = () => {

            const interval = setInterval(() => {
                optionChildCategory.value = [];
                optionGrandChildCategory.value = []
                if (productData.value.category_product.child) {
                    productData.value.category_product.child.forEach((item, index) => {
                        let option = {
                            value: item,
                            label: item.name
                        };
                        tempOptionChildCategory[index] = option;
                    });

                    console.log(tempOptionChildCategory)
                    optionChildCategory.value.push(...tempOptionChildCategory);
                    clearInterval(interval);
                }
            }, 1000);

        }

        let optionGrandChildCategory = ref([]);
        let tempOptionGrandChildCategory = [];
        const selectChildCategory = () => {

            const interval = setInterval(() => {
                optionGrandChildCategory.value = []
                if (productData.value.childCategory.child) {
                    productData.value.childCategory.child.forEach((item, index) => {
                        let option = {
                            value: item,
                            label: item.name
                        };
                        tempOptionGrandChildCategory[index] = option;
                    });

                    console.log('temp', tempOptionGrandChildCategory)

                    optionGrandChildCategory.value.push(...tempOptionGrandChildCategory);

                    console.log('optiongrandchild', optionGrandChildCategory)
                    clearInterval(interval);
                }
            }, 500);

        }

        let collection = [
            {
                id: 1,
                name: 'Collection 1'
            },
            {
                id: 2,
                name: 'Collection 2'
            }
        ]

        let optionCollection = ref([]);

        const getCollection = async () => {
            optionCollection.value = [];
            try {
                const response = await axios.get('/api/collections');
                const data = response.data;

                if (data.status === 'success') {
                    console.log('collection:', data.data);

                    data.data.forEach((item) => {
                        let option = {
                            value: item,
                            label: item.name
                        }

                        optionCollection.value.push(option)
                    })
                }

            } catch (error) {
                console.error('error', error);
            }
        };



        let category = [
            {
                id: 1,
                name: 'Buku',
                child: [
                    {
                        id: 11,
                        name: 'Agama & Filsafat',
                        child: [
                            {
                                id: 111,
                                name: 'Budah'
                            },
                            {
                                id: 112,
                                name: 'Filosofi'
                            }
                        ]
                    },
                    {
                        id: 12,
                        name: 'Aristektur & Desain',
                        child: [
                            {
                                id: 121,
                                name: 'Bangunan'
                            },
                            {
                                id: 122,
                                name: 'Codes & Standars'
                            }
                        ]
                    }
                ]
            },
            {
                id: 2,
                name: 'Motor',
                child: [
                    {
                        id: 21,
                        name: 'Agama2 & Filsafat',
                        child: [
                            {
                                id: 211,
                                name: 'Budah2'
                            },
                            {
                                id: 212,
                                name: 'Filosofi2'
                            }
                        ]
                    },
                    {
                        id: 22,
                        name: 'Aristektur & Desain2',
                        child: [
                            {
                                id: 221,
                                name: 'Bangunan2'
                            },
                            {
                                id: 222,
                                name: 'Codes & Standars2'
                            }
                        ]
                    }
                ]
            }
        ]

        let optionCategory = [];
        category.forEach((item) => {
            let option = {
                value: item,
                label: item.name
            };
            optionCategory.push(option);


        });

        // console.log(optionCategory);


        const testimonialStatus = ref(true);

        const addTestimonials = () => {
            checkoutData.value.testimonials.push(
                {
                    image: null,
                    name: '',
                    message: ''
                }
            )

            console.log(checkoutData)
        }

        const deleteTestimonial = (index) => {
            checkoutData.value.testimonials.splice(index, 1);
        }

        const handleChangeTestimonial = async (event, index) => {
            try {
                const file = event.target.files[0];
                const formData = new FormData();
                formData.append('image', file);
                const response = await axios.post('/api/products/upload', formData);
                const data = response.data;
                checkoutData.value.testimonials[index].image = data.data;
                // checkoutData.value.header.image = data.data;

            } catch (error) {
                console.error('error', error);
            }
        }

        // Pembayaran

        let paymentType = ref({
            bank_transfer: true,
            bank_transfer_description: '',
            cod_status: false,
            cod_description: '',
            epayment_status: false
        })

        // Halaman

        const page = ref('product');

        const pageMethod = (value) => {
            // Cek name kosong
            productDataError.value.name = productData.value.name === '' ? 'Nama Produk tidak boleh kosong' : null;

            // Cek url kosong
            productDataError.value.url = productData.value.url === '' ? 'URL tidak boleh kosong' : null;

            // Cek product_code kosong
            productDataError.value.product_code = productData.value.product_code === '' ? 'Kode tidak boleh kosong' : null;

            // Cek apakah masih ada error
            const hasError = Object.values(productDataError.value).some(error => error !== null);

            if (hasError) {
                // Jika masih ada error, tidak lanjut ke halaman berikutnya
                console.log(productDataError.value);
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
                return;
            }


            // Jika tidak ada error, lanjutkan ke halaman berikutnya
            page.value = value;
        };



        // Checkout Page

        const template = ref('right-sidebar');

        const templateChange = (value) => {
            template.value = value;
            console.log(checkoutData)
        }

        const fieldCustomShow = ref(false)

        let checkoutData = ref({
            name: 'Checkout Page 1',
            template: template.value,
            sectionTitle: {
                form: 'Data Penerima:',
                variation: 'Pilihan Produk:',
                payment_method: 'Metode Pembayaran:',
                points: 'Yang Anda Dapatkan:',
                delivery: 'Pengiriman:'
            },
            header: {
                image: null,
                titleStatus: false,
                taglineStatus: false,
                title: '',
                tagline: ''
            },
            productImageStatus: false,
            buyButtonText: 'Beli Sekarang',
            buyButtonBackground: "#FFC84B",
            buyButtonTextColor: "#ffffff",
            productDescription: '',
            kuponStatus: true,
            dropship_status: false,
            points: [
                {
                    poin: 'Fitur 1'
                },
                {
                    poin: 'Fitur 2'
                }
            ],
            summaryStatus: true,
            testimonials: [],
            fields: [
                {
                    inputType: 'Text',
                    field: 'Input',
                    name: 'name',
                    required: true,
                    label: 'Nama',
                    placeholder: 'Nama',
                    can_be_edited: true,
                    can_be_deleted: false,
                    show: true,
                },
                {
                    inputType: 'Text',
                    field: 'Input',
                    name: 'wa',
                    required: true,
                    label: 'No Handphone/WhatsApp',
                    placeholder: 'No. Whatsapp Anda',
                    can_be_edited: false,
                    can_be_deleted: false,
                    show: true,
                },
                {
                    inputType: 'Email',
                    field: 'Input',
                    name: 'email',
                    required: true,
                    label: 'Email',
                    placeholder: 'Email Anda',
                    can_be_edited: true,
                    can_be_deleted: false,
                    show: false,
                },
                {
                    inputType: 'Number',
                    field: 'Input',
                    name: 'qty',
                    required: true,
                    label: 'Jumlah Pesanan',
                    placeholder: 'Jumlah Pesanan',
                    min: 1,
                    max: 99,
                    default: 1,
                    can_be_edited: true,
                    can_be_deleted: false,
                    show: false,
                },
                {
                    inputType: 'Text',
                    field: 'Input',
                    name: 'note',
                    required: true,
                    label: 'Catatan',
                    placeholder: 'Masukan Catatan Pemesanan',
                    can_be_edited: true,
                    can_be_deleted: false,
                    show: false,
                },
                {
                    inputType: 'Text',
                    field: 'Input',
                    name: 'address',
                    required: true,
                    label: 'Alamat Lengkap',
                    placeholder: 'Alamat Lengkap Anda',
                    can_be_edited: true,
                    can_be_deleted: false,
                    show: true,
                },
                {
                    inputType: 'Text',
                    field: 'Select',
                    name: 'province',
                    required: true,
                    label: 'Provinsi',
                    placeholder: 'Pilih Provinsi',
                    can_be_edited: true,
                    can_be_deleted: false,
                    show: false,
                },
                {
                    inputType: 'Text',
                    field: 'Select',
                    name: 'city',
                    required: true,
                    label: 'Kota/Kabupaten',
                    placeholder: 'Pilih Kota/Kabupaten',
                    can_be_edited: true,
                    can_be_deleted: false,
                    show: false,
                },
                {
                    inputType: 'Text',
                    field: 'Select',
                    name: 'subdistrict',
                    required: true,
                    label: 'Kecamatan',
                    placeholder: 'Pilih Kecamatan',
                    can_be_edited: true,
                    can_be_deleted: false,
                    show: false,
                },
                {
                    inputType: 'Text',
                    field: 'Input',
                    name: 'city_or_subdistrict',
                    required: true,
                    label: 'Kota / Kecamatan',
                    placeholder: 'Masukan Kota atau Kecamatan',
                    can_be_edited: true,
                    can_be_deleted: false,
                    show: false,
                },
            ]
        });

        const headerImageChange = async (event) => {
            try {
                const file = event.target.files[0];
                const formData = new FormData();
                formData.append('image', file);
                const response = await axios.post('/api/products/upload', formData);
                const data = response.data;

                checkoutData.value.header.image = data.data;

            } catch (error) {
                console.error('error', error);
            }
        }

        const checkField = (item) => {
            item.show = !item.show;

            console.log(checkoutData.value)
            checkoutData.value.fields.forEach((items, index) => {

                if (item.name == 'province') {
                    if (item.show == false) {
                        if (items.name == 'city') {
                            items.show = false
                        }
                        if (items.name == 'subdistrict') {
                            items.show = false
                        }
                    }
                }

                if (item.name == 'city') {
                    if (item.show == false) {
                        if (items.name == 'subdistrict') {
                            items.show = false
                        }
                    }
                }


                if (item.name == 'city') {
                    if (items.name == 'province') {
                        items.show = true;
                    }
                }

                if (item.name == 'subdistrict') {
                    if (items.name == 'province') {
                        items.show = true;
                    }
                    if (items.name == 'city') {
                        items.show = true;
                    }
                }
            })

            console.log(item)
        }

        let newCustomField = ref({
            name: 'Custom Field',
            field: 'Input',
            inputType: 'Text',
            required: true,
            label: 'Custom Field',
            placeholder: 'Custom Field',
            can_be_edited: true,
            can_be_deleted: true,
            show: true,
            options: [
                { text: 'Option 1' }
            ]
        })

        const addCustomField = () => {
            checkoutData.value.fields.push({ ...newCustomField.value })
            fieldCustomShow.value = false;
            console.log(checkoutData)

            newCustomField.value = {
                name: 'Custom Field',
                field: 'Input',
                inputType: 'Text',
                required: true,
                label: 'Custom Field',
                placeholder: 'Custom Field',
                can_be_edited: true,
                can_be_deleted: true,
                show: true,
                options: [
                    { text: 'Option 1' }
                ]
            }

        }

        var fieldCustomEdit = ref(false)
        const fieldCustomEditIndex = ref(0)
        let fieldCustomEditItem = ref({})

        const openModalFieldCustomEdit = (index, item) => {
            console.log('edit', item)
            fieldCustomEdit.value = true;
            fieldCustomEditItem.value = { ...item };
            fieldCustomEditIndex.value = index;
            console.log('fieldCu', fieldCustomEditItem.value)
        }

        const closeModalFieldCustomEdit = () => {
            fieldCustomEdit = false;
        }

        const editFieldCustom = () => {
            fieldCustomEdit.value = false;
            checkoutData.value.fields[fieldCustomEditIndex.value] = { ...fieldCustomEditItem.value };
            console.log(checkoutData)
        }

        const removeCustomField = (index) => {
            checkoutData.value.fields.splice(index, 1);
        };

        const addOption = () => {
            var index = newCustomField.value.options.length + 1;
            let newOption = { text: 'Option ' + index }
            newCustomField.value.options.push({ ...newOption });

            console.log(newCustomField)
        }


        const addOptionEdit = () => {
            var index = fieldCustomEditItem.value.options.length + 1;
            let newOption = { text: 'Option ' + index }
            fieldCustomEditItem.value.options.push({ ...newOption });

            console.log(newCustomField)
        }


        const removeOption = (index) => {
            newCustomField.value.options.splice(index, 1);
        };



        const removePoint = (index) => {
            checkoutData.value.points.splice(index, 1);
        };

        const addPoint = () => {
            checkoutData.value.points.push({
                poin: ''
            });
        };

        // Success Page
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

        let bankAccountError = ref([])
        const addBankAccount = async () => {
            try {
                successData.value.accountBank.push({ ...tempBankAccount.value });

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

        const tempSelectedBank = ref();
        const selectBank = () => {
            if (!isBankAlreadySelected(tempSelectedBank.value)) {
                successData.value.accountBank.push({ ...tempSelectedBank.value });
                console.log(successData.value.accountBank);
            }
        };

        const isBankAlreadySelected = (selectedBank) => {
            return successData.value.accountBank.some((bank) => {
                return bank.bank === selectedBank.bank && bank.atas_nama === selectedBank.atas_nama && bank.rekening === selectedBank.rekening;
            });
        };

        const deleteAccountBank = (index) => {
            successData.value.accountBank.splice(index, 1);
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


        onBeforeMount(() => {
            getBank();
            getCollection();
        });


        let successData = ref(
            {
                showTitle: false,
                title: '',
                type: 'success_page',
                wa: '',
                headline: 'Terimakasih sudah melakukan order {{productData.value.name}}',
                accountBank: [],
                video: '',
                bodyText: {
                    payment_type: 'bank_transfer',
                    bank_transfer_body: 'Untuk menyelesaikan order, anda akan redirect ke WA kami',
                    cod_body: 'Orderan anda akan segera kami proses',
                    payment_body: 'Selamat! Order anda berhasil',
                }
            }
        )

        const changeBodyText = (type) => {
            successData.value.bodyText.payment_type = type;
        }

        const embedYouTubeVideo = () => {
            const videoIdMatch = successData.value.video.match(/(?:https?:\/\/)?(?:www\.)?(?:youtube\.com\/(?:[^\/\n\s]+\/\S+\/|(?:v|e(?:mbed)?)\/|\S*?[?&]v=)|youtu\.be\/)([a-zA-Z0-9_-]{11})/);
            if (videoIdMatch) {
                const videoId = videoIdMatch[1];
                successData.value.video = `https://www.youtube.com/embed/${videoId}`;
            } else {
                successData.value.video = 'Invalid YouTube link';
            }
        }

        const evalExpression = (expression) => {
            const matches = expression.match(/{{(.*?)}}/g);

            if (matches) {
                let evaluatedString = expression;

                matches.forEach(match => {
                    const expressionWithoutBrackets = match.replace(/{{(.*?)}}/, '$1').trim();

                    try {
                        const evaluatedValue = eval(expressionWithoutBrackets);
                        evaluatedString = evaluatedString.replace(match, evaluatedValue);
                    } catch (error) {
                        console.error('Error evaluating expression:', error);
                    }
                });

                return evaluatedString;
            }

            return expression;
        }

        // Cs Rotator
        let rotatorData = ref({
            role: 'Customer Service',
            team: []
        });
        const changeRotatorRole = (role) => {
            rotatorData.value.role = role;
            rotatorData.value.team = [];
        }

        return {
            // Cs rotator
            rotatorData,
            changeRotatorRole,
            teamOption,

            // Global
            dropzoneFile,
            drop,
            global,
            embedYouTubeVideo,
            selectedFile,
            bumpImageChange,

            // Product
            tempProductImage,
            v$: useVuelidate(),
            deleteProductImage,
            productData,
            shippingMethod,
            paymentType,
            copyPriceFalseManyVariation,
            copyWeightFalseManyVariation,
            copyHppFalseManyVariation,
            copyPriceTrueManyVariation,
            copyWeightTrueManyVariation,
            copyHppTrueManyVariation,

            // Testimonials
            testimonialStatus,
            addTestimonials,
            handleChangeTestimonial,
            deleteTestimonial,

            uniqueMethod,
            page,
            pageMethod,
            template,
            templateChange,
            checkoutData,
            checkField,

            // Cs Rotator
            teamData,

            // Custom Field
            addCustomField,
            removeCustomField,
            addOption,
            removeOption,
            removePoint,
            fieldCustomShow,
            newCustomField,
            fieldCustomEdit,
            addOptionEdit,
            openModalFieldCustomEdit,
            closeModalFieldCustomEdit,
            fieldCustomEditItem,
            editFieldCustom,
            fieldCustomEditIndex,
            addPoint,
            editor: ClassicEditor,

            changeBodyText,
            successData,
            urlSlug,

            listBank,
            accountBankShow,
            tempBankAccount,
            listBankAccount,
            tempSelectedBank,
            selectBank,
            deleteAccountBank,
            productDataError,

            evalExpression,
            addBankAccount,
            bankAccountError,
            optionCategory,
            optionChildCategory,
            optionGrandChildCategory,
            selectChildCategory,
            selectCategory,

            optionCollection,
            getCollection,

            changePriceType,
            sizeValue,
            size,
            colorValue,
            color,
            variation,
            addSize,
            deleteSize,
            addColor,
            deleteColor,
            headerImageChange,
            submit
        };
    },
    components: {
        ckeditor: CKEditor.component,
        DropZone,
        Layout,
        PageHeader,
        Multiselect,
        flatPickr,
        Link,
        Head
    },
    props: {
        store: Object,
        team: Object
    },
    data() {
        return {
            sectionTitleShow: false,
        }
    },
    // methods: {
    //     deleteRecord(ele) {
    //         ele.target.parentElement.parentElement.parentElement.remove();
    //     },
    // },
};
</script>

<template>
    <Layout>

        <Head title="Create Product" />
        <!-- <PageHeader title="Create Product" pageTitle="Product" /> -->

        <BRow>
            <div class="col-12">
                <div class="btn-group w-100 mb-3">
                    <button @click="pageMethod('product')" class="btn"
                        :class="{ 'btn-primary': page == 'product', 'btn-outline-secondary': page !== 'product' }">
                        <i
                            class="ri-user-2-line fs-16 avatar-xs d-inline-flex align-items-center justify-content-center bg-primary-subtle text-primary rounded-circle align-middle me-2"></i>
                        Produk</button>
                    <button @click="pageMethod('checkout')" class="btn"
                        :class="{ 'btn-primary': page == 'checkout', 'btn-outline-secondary': page !== 'checkout' }">
                        <i
                            class="ri-truck-line fs-16 avatar-xs d-inline-flex align-items-center justify-content-center bg-primary-subtle text-primary rounded-circle align-middle me-2"></i>
                        Halaman Checkout
                    </button>
                    <button @click="pageMethod('success')" class="btn"
                        :class="{ 'btn-primary': page == 'success', 'btn-outline-secondary': page !== 'success' }">
                        <i
                            class="ri-bank-card-line fs-16 avatar-xs d-inline-flex align-items-center justify-content-center bg-primary-subtle text-primary rounded-circle align-middle me-2"></i>
                        Halaman Sukses
                    </button>
                </div>
            </div>
            <!-- Page Product -->
            <BCol lg="6" v-if="page == 'product'">
                <BCard no-body>
                    <BCardBody>
                        <div class="mb-3">
                            <label class="form-label" for="product-title-input">Nama Produk (akan muncul di cart &
                                invoice)</label>
                            <input type="text" @blur="urlSlug" v-model="productData.name" class="form-control"
                                :class="{ 'is-invalid': productDataError.name && !productData.name }"
                                id="product-title-input" />
                            <div class="invalid-feedback" v-if="!productData.name">{{ productDataError.name }}</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="product-price-input">URL Halaman Checkout</label>
                            <div class="input-group has-validation mb-3">
                                <span class="input-group-text" id="product-price-addon">/</span>
                                <input type="text" class="form-control"
                                    :class="{ 'is-invalid': productDataError.url && !productData.url }"
                                    v-model="productData.url" id="product-price-input" aria-label="Price"
                                    aria-describedby="product-price-addon" required="">
                                <div class="invalid-feedback" v-if="!productData.url">{{ productDataError.url }}</div>
                            </div>
                            <div>URL Checkout Page: https://{{ store.store.username }}.aplikasiwa.com/{{ productData.url }}
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="product-title-input">Kode Produk</label>
                            <input type="text" class="form-control"
                                :class="{ 'is-invalid': productDataError.product_code && !productData.product_code }"
                                v-model="productData.product_code" id="product-title-input" />
                            <div class="invalid-feedback" v-if="!productData.product_code">{{ productDataError.product_code
                            }}</div>
                        </div>
                        <div class="mb-3">
                            <label for="choices-publish-status-input" class="form-label">Kategori Produk</label>
                            <Multiselect class="form-control mb-4" @change="selectCategory"
                                v-model="productData.category_product" :close-on-select="true" :searchable="false"
                                :create-option="true" :options="optionCategory" />
                            <div class="row">
                                <div class="col-6" v-if="optionChildCategory.length !== 0">
                                    <Multiselect class="form-control" @change="selectChildCategory"
                                        v-model="productData.childCategory" :close-on-select="true" :searchable="false"
                                        :create-option="true" :options="optionChildCategory" />
                                </div>
                                <div class="col-6" v-if="optionGrandChildCategory.length !== 0">
                                    <Multiselect class="form-control" v-model="productData.grandChildCategory"
                                        :close-on-select="true" :searchable="false" :create-option="false"
                                        :options="optionGrandChildCategory" />
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="choices-publish-status-input" class="form-label">Koleksi Produki</label>
                            <Multiselect class="form-control" v-model="productData.collection" mode="tags"
                                :close-on-select="false" :searchable="true" :create-option="true"
                                :options="optionCollection" />
                        </div>
                        <div class="mb-3">
                            <label for="choices-publish-status-input" class="form-label">Status Produk</label>
                            <div class="hstack gap-2 flex-wrap">
                                <input type="radio" class="btn-check" id="product_status_1" name="product_status"
                                    v-model="product_status" value="archived"
                                    :checked="productData.product_status == 'archived'">
                                <label class="btn btn-outline-success" for="product_status_1">Diarsipkan</label>

                                <input type="radio" class="btn-check" id="product_status_2" name="product_status"
                                    v-model="product_status" value="test_mode"
                                    :checked="productData.product_status == 'test_mode'">
                                <label class="btn btn-outline-success" for="product_status_2">Test Mode</label>

                                <input type="radio" class="btn-check" id="product_status_3" name="product_status"
                                    v-model="product_status" value="live" :checked="productData.product_status == 'live'">
                                <label class="btn btn-outline-success" for="product_status_3">Live</label>
                            </div>

                        </div>
                    </BCardBody>
                </BCard>

                <BCard no-body>
                    <BCardHeader>
                        <h5 class="card-title mb-0">Gambar</h5>
                    </BCardHeader>
                    <BCardBody>
                        <div class="vstack gap-2">
                            <h5 class="fs-14 mb-1">Unggah Gambar</h5>
                            <p class="text-muted">Unggah gambar produk Anda (maks. 10 gambar)</p>
                            <DropZone @change="selectedFile" />

                            <div class="border rounded" v-for="(file, index) of productData.image" :key="index">
                                <div class="d-flex align-items-center p-2">
                                    <div class="flex-grow-1">
                                        <img class="img-fluid" style="width:100px" :src="file.url" />
                                    </div>
                                    <div class="flex-shrink-0 ms-3">
                                        <BButton data-dz-remove="" variant="danger" size="sm"
                                            @click="deleteProductImage(index)">
                                            Delete
                                        </BButton>
                                    </div>
                                </div>
                            </div>
                            <!-- <div class="border rounded" v-for="(file, index) of productData.image" :key="index">
                                <div class="d-flex align-items-center p-2">
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
                                        <BButton data-dz-remove="" variant="danger" size="sm"
                                            @click="deleteProductImage(index)">
                                            Delete
                                        </BButton>
                                    </div>
                                </div>
                            </div> -->
                        </div>
                    </BCardBody>
                </BCard>

                <BCard no-body>
                    <BCardHeader>
                        <h5 class="card-title mb-0">Pengaturan Harga</h5>
                    </BCardHeader>
                    <BCardBody>
                        <div class="d-flex justify-content-between mb-3">
                            <div type="button" class="text-center w-100 py-2" @click="changePriceType('simple')"
                                :class="{ 'border-bottom border-secondary': productData.price_type == 'simple' }">Simple
                            </div>
                            <div type="button" class="text-center w-100 py-2" @click="changePriceType('variable')"
                                :class="{ 'border-bottom border-secondary': productData.price_type == 'variable' }">
                                Variabel
                            </div>
                        </div>
                        <div v-if="productData.price_type == 'simple'">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="mb-3">
                                        <label class="form-label" for="product-price-input">Harga Normal</label>
                                        <div class="input-group has-validation mb-3">
                                            <span class="input-group-text" id="product-price-addon">Rp</span>
                                            <input type="text" class="form-control" v-model="productData.normal_price"
                                                id="product-price-input" placeholder="Enter price" aria-label="Price"
                                                aria-describedby="product-price-addon" required="">
                                            <div class="invalid-feedback">Please Enter a product price.</div>
                                        </div>

                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="mb-3">
                                        <div class="form-check form-switch form-switch-custom form-switch-success">
                                            <input class="form-check-input" type="checkbox"
                                                v-model="productData.price_sale_status" role="switch" id="SwitchCheck11">
                                            <label class="form-check-label" for="SwitchCheck11">Harga
                                                Penjualan</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-12" v-if="productData.price_sale_status == true">
                                    <div class="mb-3">
                                        <label class="form-label" for="product-price-input">Harga Penjualan</label>
                                        <div class="input-group has-validation mb-3">
                                            <span class="input-group-text" id="product-price-addon">Rp</span>
                                            <input type="text" class="form-control" v-model="productData.price_sale"
                                                id="product-price-input" placeholder="Enter price" aria-label="Price"
                                                aria-describedby="product-price-addon" required="">
                                            <div class="invalid-feedback">Please Enter a product price.</div>
                                        </div>

                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="mb-3">
                                        <label class="form-label" for="product-price-input">HPP</label>
                                        <div class="input-group has-validation mb-3">
                                            <span class="input-group-text" id="product-price-addon">Rp</span>
                                            <input type="text" class="form-control" v-model="productData.hpp"
                                                id="product-price-input" placeholder="Enter price" aria-label="Price"
                                                aria-describedby="product-price-addon" required="">
                                            <div class="invalid-feedback">Please Enter a product price.</div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-if="productData.price_type == 'variable'">
                            <BRow>
                                <div class="col-lg-12">
                                    <div class="mb-3">
                                        <div class="form-check form-switch form-switch-custom form-switch-success">
                                            <input class="form-check-input" v-model="productData.many_buy_status"
                                                type="checkbox" role="switch" id="variation">
                                            <label class="form-check-label" for="variation">Aktifkan pilihan lebih dari
                                                satu?</label>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <div class="form-check form-switch form-switch-custom form-switch-success">
                                            <input class="form-check-input" v-model="productData.many_variation_status"
                                                type="checkbox" role="switch" id="variation">
                                            <label class="form-check-label" for="variation">Aktifkan multi variasi?</label>
                                        </div>
                                    </div>
                                </div>

                                <BCol lg="6">
                                    <div class="mb-3">
                                        <label class="form-label" for="meta-keywords-input">Atribut</label>
                                        <input type="text" class="form-control border-0"
                                            v-model="productData.variation_name" />
                                        <input type="text" class="form-control" @keyup.enter="addSize"
                                            v-model="sizeValue" />
                                        <div class="mt-3">
                                            <div class="badge bg-success me-2" v-for="(item, index) in size">
                                                <div class="d-flex align-items-center">
                                                    {{ item.name }}
                                                    <i class="bx bx-x-circle ms-2 cursor-pointer"
                                                        @click="deleteSize(index)"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </BCol>
                                <BCol lg="6" v-if="productData.many_variation_status == true">
                                    <div class="mb-3">
                                        <label class="form-label" for="meta-keywords-input">&nbsp;</label>
                                        <input type="text" class="form-control border-0"
                                            v-model="productData.child_variation_name" />
                                        <input type="text" class="form-control" @keyup.enter="addColor"
                                            v-model="colorValue" />
                                        <div class="mt-3">
                                            <div class="badge bg-success me-2" v-for="(item, index) in color">
                                                <div class="d-flex align-items-center">
                                                    {{ item.name }}
                                                    <i class="bx bx-x-circle ms-2 cursor-pointer"
                                                        @click="deleteColor(index)"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </BCol>
                            </BRow>
                            <BRow>
                                <div class="col-lg-12">
                                    <div class="card-header">
                                        <h5 class="card-title">Variasi Harga</h5>
                                    </div>
                                    <div v-if="productData.many_variation_status == false">
                                        <div class="card" v-for="(item, index) in size">
                                            <div class="card-body">
                                                <h5>{{ item.name }}</h5>
                                                <div class="row">
                                                    <div class="col-lg-6">
                                                        <div class="mb-3">
                                                            <label class="form-label"
                                                                for="product-price-input">Harga</label>
                                                            <div class="input-group has-validation mb-3">
                                                                <span class="input-group-text"
                                                                    id="product-price-addon">Rp</span>
                                                                <input type="text" class="form-control" v-model="item.price"
                                                                    id="product-price-input" aria-label="Price"
                                                                    aria-describedby="product-price-addon" required="">
                                                                <span class="input-group-text"
                                                                    @click="copyPriceFalseManyVariation(item.price)"
                                                                    id="product-price-addon"><i
                                                                        class="bx bx-paste"></i></span>
                                                                <div class="invalid-feedback">Please Enter a product
                                                                    price.
                                                                </div>
                                                            </div>

                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6">
                                                        <div class="mb-3">
                                                            <label class="form-label"
                                                                for="product-price-input">Berat</label>
                                                            <div class="input-group has-validation mb-3">
                                                                <span class="input-group-text"
                                                                    id="product-price-addon">Berat
                                                                    (gr)</span>
                                                                <input type="text" class="form-control"
                                                                    v-model="item.weight" id="product-price-input"
                                                                    aria-label="Price"
                                                                    aria-describedby="product-price-addon" required="">
                                                                <span class="input-group-text"
                                                                    @click="copyWeightFalseManyVariation(item.weight)"
                                                                    id="product-price-addon"><i
                                                                        class="bx bx-paste"></i></span>
                                                                <div class="invalid-feedback">Please Enter a product
                                                                    price.
                                                                </div>
                                                            </div>

                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6">
                                                        <div class="mb-3">
                                                            <label class="form-label" for="product-price-input">HPP
                                                                (Opsional)</label>
                                                            <div class="input-group has-validation mb-3">
                                                                <span class="input-group-text"
                                                                    id="product-price-addon">Rp</span>
                                                                <input type="text" class="form-control" v-model="item.hpp"
                                                                    id="product-price-input" aria-label="Price"
                                                                    aria-describedby="product-price-addon" required="">
                                                                <span class="input-group-text"
                                                                    @click="copyHppFalseManyVariation(item.hpp)"
                                                                    id="product-price-addon"><i
                                                                        class="bx bx-paste"></i></span>
                                                                <div class="invalid-feedback">Please Enter a product
                                                                    price.
                                                                </div>
                                                            </div>

                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6">
                                                        <div class="mb-3">
                                                            <label class="form-label" for="product-price-input">Variation
                                                                Code
                                                                (Opsional)</label>
                                                            <div class="has-validation mb-3">
                                                                <input type="text" class="form-control"
                                                                    v-model="item.variation_code" id="product-price-input"
                                                                    aria-label="Price"
                                                                    aria-describedby="product-price-addon" required="">
                                                                <div class="invalid-feedback">Please Enter a product
                                                                    price.
                                                                </div>
                                                            </div>

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div v-if="productData.many_variation_status == true">
                                        <div v-for="(item, index) in productData.variation">
                                            <div class="card" v-for="(itemColor, index) in item.childVariation">
                                                <div class="card-body">
                                                    <h5>{{ item.name }} - {{ itemColor.name }}</h5>
                                                    <div class="row">
                                                        <div class="col-lg-6">
                                                            <div class="mb-3">
                                                                <label class="form-label"
                                                                    for="product-price-input">Harga</label>
                                                                <div class="input-group has-validation mb-3">
                                                                    <span class="input-group-text"
                                                                        id="product-price-addon">Rp</span>
                                                                    <input type="text" class="form-control"
                                                                        v-model="itemColor.price" id="product-price-input"
                                                                        aria-label="Price"
                                                                        aria-describedby="product-price-addon" required="">
                                                                    <span class="input-group-text cursor-pointer"
                                                                        v-if="index == 0" id="product-price-addon"
                                                                        @click="copyPriceTrueManyVariation(itemColor.price)"><i
                                                                            class="bx bx-paste"></i></span>
                                                                    <div class="invalid-feedback">Please Enter a product
                                                                        price.
                                                                    </div>
                                                                </div>

                                                            </div>
                                                        </div>
                                                        <div class="col-lg-6">
                                                            <div class="mb-3">
                                                                <label class="form-label"
                                                                    for="product-price-input">Berat</label>
                                                                <div class="input-group has-validation mb-3">
                                                                    <span class="input-group-text"
                                                                        id="product-price-addon">Berat
                                                                        (gr)</span>
                                                                    <input type="text" class="form-control"
                                                                        v-model="itemColor.weight" id="product-price-input"
                                                                        aria-label="Price"
                                                                        aria-describedby="product-price-addon" required="">
                                                                    <span class="input-group-text cursor-pointer"
                                                                        v-if="index == 0" id="product-price-addon"
                                                                        @click="copyWeightTrueManyVariation(itemColor.weight)"><i
                                                                            class="bx bx-paste"></i></span>
                                                                    <div class="invalid-feedback">Please Enter a product
                                                                        price.
                                                                    </div>
                                                                </div>

                                                            </div>
                                                        </div>
                                                        <div class="col-lg-6">
                                                            <div class="mb-3">
                                                                <label class="form-label" for="product-price-input">HPP
                                                                    (Opsional)</label>
                                                                <div class="input-group has-validation mb-3">
                                                                    <span class="input-group-text"
                                                                        id="product-price-addon">Rp</span>
                                                                    <input type="text" class="form-control"
                                                                        v-model="itemColor.hpp" id="product-price-input"
                                                                        aria-label="Price"
                                                                        aria-describedby="product-price-addon" required="">
                                                                    <span class="input-group-text cursor-pointer"
                                                                        v-if="index == 0" id="product-price-addon"
                                                                        @click="copyHppTrueManyVariation(itemColor.hpp)"><i
                                                                            class="bx bx-paste"></i></span>
                                                                    <div class="invalid-feedback">Please Enter a product
                                                                        price.
                                                                    </div>
                                                                </div>

                                                            </div>
                                                        </div>
                                                        <div class="col-lg-6">
                                                            <div class="mb-3">
                                                                <label class="form-label"
                                                                    for="product-price-input">Variation
                                                                    Code
                                                                    (Opsional)</label>
                                                                <div class="has-validation mb-3">
                                                                    <input type="text" class="form-control"
                                                                        v-model="itemColor.variation_code"
                                                                        id="product-price-input" aria-label="Price"
                                                                        aria-describedby="product-price-addon" required="">
                                                                    <div class="invalid-feedback">Please Enter a product
                                                                        price.
                                                                    </div>
                                                                </div>

                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </BRow>
                        </div>
                    </BCardBody>
                </BCard>

                <BCard no-body>
                    <BCardHeader>
                        <h5 class="card-title mb-0">Persediaan</h5>

                    </BCardHeader>
                    <BCardBody>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="mb-3">
                                    <h5>Manajemen Stock</h5>
                                    <div class="form-check form-switch form-switch-custom form-switch-success">
                                        <input class="form-check-input" v-model="productData.manajemen_stock"
                                            type="checkbox" role="switch" id="variation">
                                        <label class="form-check-label" for="variation">Atur jumlah stook barang toko
                                            anda</label>
                                    </div>
                                </div>
                                <div class="mb-3" v-if="productData.manajemen_stock == false">
                                    <label for="choices-publish-status-input" class="form-label">Status Stok</label>
                                    <div class="hstack gap-2 flex-wrap">
                                        <input type="radio" class="btn-check" name="stock_status" value="habis"
                                            v-model="productData.stock_status" id="stock_status_1" checked>
                                        <label class="btn btn-outline-success" for="stock_status_1">Stok
                                            Habis</label>

                                        <input type="radio" class="btn-check" name="stock_status"
                                            v-model="productData.stock_status" value="tersedia" id="stock_status_2">
                                        <label class="btn btn-outline-success" for="stock_status_2">Stok
                                            Tersedia</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <BRow v-if="productData.manajemen_stock == true">
                            <div class="col-lg-12">
                                <div class="card-header">
                                    <h5 class="card-title">Izinkan Backorder</h5>
                                    <p>Apablia di-allow, maka pembeli masih dapat memesan barang walaupun stok sudah
                                        habis</p>
                                    <div class="mb-3">
                                        <div class="hstack gap-2 flex-wrap">
                                            <input type="radio" class="btn-check" name="backorder_status"
                                                v-model="productData.backorder_status" :value="true" id="backorder_status_1"
                                                checked>
                                            <label class="btn btn-outline-success" for="backorder_status_1">Izinkan</label>

                                            <input type="radio" class="btn-check" name="backorder_status"
                                                v-model="productData.backorder_status" :value="false"
                                                id="backorder_status_2">
                                            <label class="btn btn-outline-success" for="backorder_status_2">Tidak
                                                Diizinkan</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title">Kuantitas Stok</h5>
                                        <div class="row">
                                            <div class="col-lg-12"
                                                v-if="productData.many_variation_status == false && productData.price_type == 'variable'">
                                                <table class="table table-bordered">
                                                    <tbody>
                                                        <tr v-for="(item, index) in size">
                                                            <th class="w-50">{{ item.name }}</th>
                                                            <th class="w-50"><input class="form-control"
                                                                    v-model="item.stock" /></th>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="col-lg-12"
                                                v-if="productData.many_variation_status == true && productData.price_type == 'variable'">
                                                <table class="table table-bordered">
                                                    <tbody>
                                                        <template v-for="(item, index) in productData.variation">
                                                            <tr v-for="(itemChild, indexChild) in item.childVariation">
                                                                <th class="w-50">{{ item.name }} - {{ itemChild.name }}</th>
                                                                <th class="w-50"><input class="form-control"
                                                                        v-model="itemChild.stock" /></th>
                                                            </tr>
                                                        </template>
                                                    </tbody>
                                                </table>
                                            </div>

                                            <div class="col-lg-12"
                                                v-if="productData.many_variation_status == false, productData.price_type == 'simple'">
                                                <input v-model="productData.stock" type="text" class="form-control"
                                                    id="product-price-input" aria-label="Price"
                                                    aria-describedby="product-price-addon" required="">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </BRow>
                    </BCardBody>
                </BCard>
                <BCard no-body>
                    <BCardHeader>
                        <h5 class="card-title mb-0">Bump</h5>
                    </BCardHeader>
                    <BCardBody>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="mb-3">
                                    <div class="form-check form-switch form-switch-custom form-switch-success">
                                        <input class="form-check-input" v-model="productData.bump_status" type="checkbox"
                                            role="switch" id="variation">
                                        <label class="form-check-label" for="variation">Apakah Anda ingin menampilkan
                                            Bump
                                            untuk produk ini?</label>
                                    </div>
                                </div>
                            </div>
                            <template v-if="productData.bump_status == true">
                                <div class="col-lg-12">
                                    <div class="mb-3">
                                        <label class="form-label" for="product-price-input">Nama Produk Bump (akan muncul di
                                            cart & invoice)</label>
                                        <div class="has-validation mb-3">
                                            <input v-model="productData.product_bump.name" type="text" class="form-control"
                                                id="product-price-input" aria-label="Price"
                                                aria-describedby="product-price-addon" required="">
                                            <div class="invalid-feedback">Please Enter a product
                                                price.
                                            </div>
                                        </div>

                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="mb-3">
                                        <label class="form-label" for="product-price-input">Harga</label>
                                        <div class="input-group has-validation mb-3">
                                            <span class="input-group-text" id="product-price-addon">Rp</span>
                                            <input v-model="productData.product_bump.price" type="text" class="form-control"
                                                id="product-price-input" aria-label="Price"
                                                aria-describedby="product-price-addon" required="">
                                            <div class="invalid-feedback">Please Enter a product
                                                price.
                                            </div>
                                        </div>

                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="mb-3">
                                        <label class="form-label" for="product-price-input">Bump Weight (gr)</label>
                                        <div class="has-validation mb-3">
                                            <input v-model="productData.product_bump.weight" type="text"
                                                class="form-control" id="product-price-input" aria-label="Price"
                                                aria-describedby="product-price-addon" required="">
                                            <div class="invalid-feedback">Please Enter a product
                                                price.
                                            </div>
                                        </div>

                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="mb-3">
                                        <label class="form-label" for="product-price-input">Bump Image</label>
                                        <div class="has-validation mb-3">
                                            <label for="bump-image" class="btn-primary btn">Unggah Gambar</label>
                                            <input type="file" @change="bumpImageChange" class="form-control"
                                                id="bump-image" hidden aria-label="Price"
                                                aria-describedby="product-price-addon" required="">
                                            <div class="invalid-feedback">Please Enter a product
                                                price.
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </template>
                        </div>
                    </BCardBody>
                </BCard>
                <BCard no-body>
                    <BCardHeader>
                        <h5 class="card-title mb-0">Pengiriman</h5>

                    </BCardHeader>
                    <BCardBody>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="mb-3">
                                    <label class="form-label" for="product-price-input">Berat (gr)</label>
                                    <div class="has-validation mb-3">
                                        <input type="text" class="form-control" v-model="productData.shipping.weight"
                                            id="product-price-input" aria-label="Price"
                                            aria-describedby="product-price-addon" required="">
                                        <div class="invalid-feedback">Please Enter a product
                                            price.
                                        </div>
                                    </div>

                                </div>
                                <div class="mb-3">
                                    <div class="form-check form-switch form-switch-custom form-switch-success">
                                        <input class="form-check-input"
                                            v-model="productData.shipping.otomatic_price_shipping" type="checkbox"
                                            role="switch" id="variation">
                                        <label class="form-check-label" for="variation">Apakah Anda ingin menghitung
                                            ongkos kirim otomatis?</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <BRow v-if="productData.shipping.otomatic_price_shipping == true">
                            <div class="col-lg-12">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title">Tipe Pengiriman</h5>
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="btn-group w-100 mb-3">
                                                    <button type="button" class="btn w-100"
                                                        @click="shippingMethod('otomatic')"
                                                        :class="{ 'btn-primary': productData.shipping.shipping_type == 'otomatic', 'btn-light': productData.shipping.shipping_type !== 'otomatic' }">Otomatis</button>
                                                    <button type="button" class="btn w-100" @click="shippingMethod('flat')"
                                                        :class="{ 'btn-primary': productData.shipping.shipping_type == 'flat', 'btn-light': productData.shipping.shipping_type !== 'flat' }">Flat</button>
                                                    <button type="button" class="btn w-100" @click="shippingMethod('free')"
                                                        :class="{ 'btn-primary': productData.shipping.shipping_type == 'free', 'btn-light': productData.shipping.shipping_type !== 'free' }">Gratis</button>
                                                </div>
                                            </div>

                                            <!-- Otomatis Tipe Pengiriman -->

                                            <div v-if="productData.shipping.shipping_type == 'otomatic'">
                                                <div class="col-lg-12">
                                                    <div class="row">
                                                        <div class="col-12 col-lg-4"
                                                            v-for="(item, index) in productData.shipping.otomatic">
                                                            <div class="form-check">
                                                                <input class="form-check-input" v-model="item.status"
                                                                    type="checkbox" id="formCheck2">
                                                                <label class="form-check-label" for="formCheck2">
                                                                    {{ item.name }}
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Flat Tipe Pengiriman -->
                                            <div v-if="productData.shipping.shipping_type == 'flat'">
                                                <div class="col-lg-12">
                                                    <div class="row">
                                                        <div class="col-12 col-lg-12">
                                                            <div class="mb-3">
                                                                <label class="form-label" for="product-price-input">Harga
                                                                    Pengiriman
                                                                    Flat</label>
                                                                <div class="has-validation mb-3">
                                                                    <input type="text" class="form-control"
                                                                        v-model="productData.shipping.flat.price"
                                                                        id="product-price-input" aria-label="Price"
                                                                        aria-describedby="product-price-addon" required="">
                                                                </div>

                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </BRow>
                    </BCardBody>
                </BCard>
                <BCard no-body>
                    <BCardHeader>
                        <h5 class="card-title mb-0">Pembayaran</h5>
                    </BCardHeader>
                    <BCardBody>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="mb-3">
                                    <div class="form-check form-switch form-switch-custom form-switch-success">
                                        <input class="form-check-input" type="checkbox" role="switch" id="variation"
                                            v-model="productData.bank_transfer.status">
                                        <label class="form-check-label" for="variation">Bank Transfer</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12" v-if="productData.bank_transfer.status == true">
                                <div class="mb-3">
                                    <label class="form-label" for="product-price-input">Bank Transfer -
                                        Deskripsi</label>
                                    <div class="has-validation mb-3">
                                        <input type="text" v-model="productData.bank_transfer.description"
                                            class="form-control" id="product-price-input" aria-label="Price"
                                            aria-describedby="product-price-addon" required="">
                                        <div class="invalid-feedback">Please Enter a product
                                            price.
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="mb-3">
                                    <div class="form-check form-switch form-switch-custom form-switch-success">
                                        <input class="form-check-input" type="checkbox" v-model="productData.cod.status"
                                            role="switch" id="variation">
                                        <label class="form-check-label" for="variation">COD</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12" v-if="productData.cod.status == true">
                                <div class="mb-3">
                                    <label class="form-label" for="product-price-input">COD -
                                        Deskripsi</label>
                                    <div class="has-validation mb-3">
                                        <input v-model="productData.cod.description" type="text" class="form-control"
                                            id="product-price-input" aria-label="Price"
                                            aria-describedby="product-price-addon" required="">
                                        <div class="invalid-feedback">Please Enter a product
                                            price.
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="mb-3">
                                    <div class="form-check form-switch form-switch-custom form-switch-success">
                                        <input class="form-check-input" type="checkbox"
                                            v-model="productData.epayment.status" role="switch" id="variation">
                                        <label class="form-check-label" for="variation">E - Payment</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </BCardBody>
                </BCard>
                <BCard no-body>
                    <BCardHeader>
                        <h5 class="card-title mb-0">Kode Unik</h5>
                    </BCardHeader>
                    <BCardBody>
                        <h5 class="mb-3">Pengaturan Kode Unik Untuk Pembayaran</h5>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="mb-3">
                                    <div class="form-check form-switch form-switch-custom form-switch-success">
                                        <input class="form-check-input" type="checkbox" role="switch" id="variation"
                                            v-model="productData.unique_code.status">
                                        <label class="form-check-label" for="variation">Aktifkan Kode Unik</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <template v-if="productData.unique_code.status == true">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="btn-group w-100 mb-3">
                                        <button type="button" class="btn w-100" @click="uniqueMethod('down')"
                                            :class="{ 'btn-primary': productData.unique_code.type == 'down', 'btn-light': productData.unique_code.type !== 'down' }">Turunkan</button>
                                        <button type="button" class="btn w-100" @click="uniqueMethod('up')"
                                            :class="{ 'btn-primary': productData.unique_code.type == 'up', 'btn-light': productData.unique_code.type !== 'up' }">Naikkan</button>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-6 col-12">
                                    <div class="mb-3">
                                        <label class="form-label" for="product-price-input">Min</label>
                                        <div class="has-validation mb-3">
                                            <input type="number" v-model="productData.unique_code.min" class="form-control"
                                                id="product-price-input" aria-label="Price"
                                                aria-describedby="product-price-addon" required="">
                                        </div>

                                    </div>
                                </div>
                                <div class="col-lg-6 col-12">
                                    <div class="mb-3">
                                        <label class="form-label" for="product-price-input">Max</label>
                                        <div class="has-validation mb-3">
                                            <input type="number" v-model="productData.unique_code.max" class="form-control"
                                                id="product-price-input" aria-label="Price"
                                                aria-describedby="product-price-addon" required="">
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </template>
                    </BCardBody>
                </BCard>
                <BCard no-body>
                    <BCardHeader>
                        <h5 class="card-title mb-0">Reseller</h5>
                    </BCardHeader>
                    <BCardBody>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="mb-3">
                                    <div class="form-check form-switch form-switch-custom form-switch-success">
                                        <input class="form-check-input" v-model="productData.reseller_status"
                                            type="checkbox" role="switch" id="variation">
                                        <label class="form-check-label" for="variation">Aktifkan Reseller untuk produk
                                            ini</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </BCardBody>
                </BCard>
                <BCard no-body>
                    <BCardHeader>
                        <h5 class="card-title mb-0">Tugaskan Produk Ke</h5>
                    </BCardHeader>
                    <BCardBody>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="mb-3">
                                    <div class="form-check form-switch form-switch-custom form-switch-success">
                                        <input v-model="productData.global_cs_rotator_status" class="form-check-input"
                                            type="checkbox" role="switch" id="variation">
                                        <label class="form-check-label" for="variation">Override Global CS
                                            Rotator</label>
                                    </div>
                                </div>
                                <div class="mb-3" v-if="productData.global_cs_rotator_status">
                                    <div class="border p-3" v-if="team.length == 0">
                                        <p class="text-center">Untuk menetapkan produk ke Anggota Tim, Anda harus
                                            menambahkan Angota ke tim
                                            Anda
                                        </p>
                                        <div class="d-flex justify-content-center">
                                            <Link href="/team" class="btn btn-secondary">Tambah Anggota Tim</Link>
                                        </div>
                                    </div>
                                    <div class="border p-3" v-if="team.length !== 0">
                                        <div class="mb-3">
                                            <div class="border">
                                                <div class="d-flex justify-content-between mb-3 p-2">
                                                    <div type="button" class="text-center w-100 py-2"
                                                        @click="changeRotatorRole('Customer Service')"
                                                        :class="{ 'border-bottom border-secondary': rotatorData.role == 'Customer Service' }">
                                                        Customer Service
                                                    </div>
                                                    <div type="button" class="text-center w-100 py-2"
                                                        :class="{ 'border-bottom border-secondary': rotatorData.role == 'Admin' }"
                                                        @click="changeRotatorRole('Admin')">
                                                        Admin
                                                    </div>
                                                </div>
                                                <div class="p-2" v-if="rotatorData.role == 'Customer Service'">
                                                    <Multiselect v-model="rotatorData.team" class="form-control"
                                                        placeholder="Pilih Customer Service" mode="tags"
                                                        :close-on-select="false" :searchable="true" :create-option="false"
                                                        :options="teamOption" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </BCardBody>
                </BCard>
                <div class="text-end mb-3">
                    <BButton type="button" variant="primary" class="w-sm" @click="pageMethod('checkout')">Selanjutnya
                    </BButton>
                </div>
            </BCol>
            <!-- End Page Product -->

            <!-- Page Checkout -->
            <BCol lg="6" v-if="page == 'checkout'">
                <BCard no-body>
                    <BCardHeader>
                        <h5 class="card-title mb-0">Nama Halaman Checkout</h5>
                    </BCardHeader>
                    <BCardBody>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="mb-3">
                                    <input type="text" v-model="checkoutData.name" class="form-control"
                                        id="product-title-input" />
                                </div>
                            </div>
                        </div>
                    </BCardBody>
                </BCard>

                <!-- Pengaturan Template Posisi-->
                <BCard no-body>
                    <BCardHeader>
                        <h5 class="card-title mb-0">Template</h5>
                    </BCardHeader>
                    <BCardBody>
                        <div class="row">
                            <div class="col">
                                <p class="text-center">Right Sidebar</p>
                                <div class="border border-2 p-2"
                                    :class="{ 'border-secondary': template == 'right-sidebar', 'border-light': template !== 'right-sidebar' }"
                                    @click="templateChange('right-sidebar')" style="height:150px;cursor:pointer">
                                    <div class="container-fluid px-1">
                                        <div class="row">
                                            <div class="col-8 bg-light">
                                                <div style="height:130px"></div>
                                            </div>
                                            <div class="col-3 ms-2 bg-light">
                                                <div style="height:130px"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col">
                                <p class="text-center">Left Sidebar</p>
                                <div class="border border-2 p-2"
                                    :class="{ 'border-secondary': template == 'left-sidebar', 'border-light': template !== 'left-sidebar' }"
                                    @click="templateChange('left-sidebar')" style="height:150px;cursor:pointer">
                                    <div class="container-fluid px-1">
                                        <div class="row">
                                            <div class="col-3 bg-light">
                                                <div style="height:130px"></div>
                                            </div>
                                            <div class="col-8 ms-2 bg-light">
                                                <div style="height:130px"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col">
                                <p class="text-center">No Sidebar</p>
                                <div class="border border-2 p-2"
                                    :class="{ 'border-secondary': template == 'no-sidebar', 'border-light': template !== 'no-sidebar' }"
                                    @click="templateChange('no-sidebar')" style="height:150px;cursor:pointer">
                                    <div class="container-fluid px-1">
                                        <div class="row d-flex justify-content-center">
                                            <div class="col-8 bg-light">
                                                <div style="height:130px"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12 d-flex justify-content-end">
                                <button class="btn border border-dark mt-3 d-flex align-items-center fs-5"
                                    @click="sectionTitleShow = !sectionTitleShow">Judul Section <i
                                        class="bx bx-pencil fs-5"></i>
                                </button>
                                <BModal v-model="sectionTitleShow" hide-footer title="Section Titles" size="lg"
                                    class="v-modal-custom">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label class="form-label" for="product-title-input">Form:</label>
                                                <input type="text" class="form-control"
                                                    v-model="checkoutData.sectionTitle.form" />
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label class="form-label" for="product-title-input">Variasi:</label>
                                                <input type="text" class="form-control"
                                                    v-model="checkoutData.sectionTitle.variation" />
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label class="form-label" for="product-title-input">Metode
                                                    Pembayaran:</label>
                                                <input type="text" class="form-control"
                                                    v-model="checkoutData.sectionTitle.payment_method" />
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label class="form-label" for="product-title-input">Poin Poin:</label>
                                                <input type="text" class="form-control"
                                                    v-model="checkoutData.sectionTitle.points" />
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label class="form-label" for="product-title-input">Pengiriman:</label>
                                                <input type="text" class="form-control"
                                                    v-model="checkoutData.sectionTitle.delivery" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer v-modal-footer">
                                        <BButton type="button" variant="primary" @click="sectionTitleShow = false">OK
                                        </BButton>
                                    </div>
                                </BModal>
                            </div>
                        </div>
                    </BCardBody>
                </BCard>
                <BCard no-body>
                    <BCardHeader>
                        <h5 class="card-title mb-0">Header</h5>
                    </BCardHeader>
                    <BCardBody>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="mb-3">
                                    <h5>Unggan Gambar Di Header</h5>
                                    <p>(Ini biasanya logo produk atau perusahaan Anda)</p>
                                </div>
                                <div class="d-flex align-items-center mb-3">
                                    <div>
                                        <label class="btn btn-secondary mb-0" for="header-image"><i
                                                class="bx bx-image-alt"></i>
                                            Pilih Gambar</label>
                                        <input type="file" @change="headerImageChange" hidden id="header-image">
                                    </div>
                                    <div class="ms-2">
                                        <button class="btn btn-light"><i class="bx bx-trash"></i> Hapus</button>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <div class="form-check form-switch form-switch-custom form-switch-success">
                                        <input class="form-check-input" type="checkbox" role="switch" id="headerTitle"
                                            v-model="checkoutData.header.titleStatus">
                                        <label class="form-check-label" for="headerTitle">Apakah Anda Ingin menampilkan
                                            judul
                                            di atas keranjang Anda?</label>
                                    </div>
                                </div>
                                <div class="mb-3" v-if="checkoutData.header.titleStatus == true">
                                    <input class="form-control" type="text" placeholder="Title"
                                        v-model="checkoutData.header.title">
                                </div>
                                <div class="mb-3">
                                    <div class="form-check form-switch form-switch-custom form-switch-success">
                                        <input class="form-check-input" type="checkbox" role="switch" id="headerTagline"
                                            v-model="checkoutData.header.taglineStatus">
                                        <label class="form-check-label" for="headerTagline">Apakah Anda Ingin menampilkan
                                            judul
                                            di atas keranjang Anda?</label>
                                    </div>
                                </div>
                                <div class="mb-3" v-if="checkoutData.header.taglineStatus == true">
                                    <input class="form-control" type="text" placeholder="Tagline"
                                        v-model="checkoutData.header.tagline">
                                </div>
                            </div>
                        </div>
                    </BCardBody>
                </BCard>
                <BCard no-body>
                    <BCardHeader>
                        <h5 class="card-title mb-0">Gambar</h5>
                    </BCardHeader>
                    <BCardBody>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="mb-3">
                                    <div class="form-check form-switch form-switch-custom form-switch-success">
                                        <input class="form-check-input" type="checkbox" role="switch" id="checkImageProduct"
                                            v-model="checkoutData.productImageStatus">
                                        <label class="form-check-label" for="checkImageProduct">Tampilkan gambar produk di
                                            halaman checkout?</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </BCardBody>
                </BCard>
                <!-- Pengaturan Bidang yang Diminta -->
                <BCard no-body>
                    <BCardHeader>
                        <h5 class="card-title mb-0">Bidang yang Diminta</h5>
                    </BCardHeader>
                    <BCardBody>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="mb-3 d-flex align-items-center" v-for="(item, index) in  checkoutData.fields ">
                                    <div v-if="item.name != 'wa'"
                                        class="form-check me-3 form-check-outline form-check-primary ">
                                        <input class="form-check-input" type="checkbox" :id="'formCheck' + index"
                                            @click="checkField(item)" :checked="item.show ? true : false">
                                    </div>
                                    <div v-else class="form-check me-3 form-check-outline form-check-primary ">

                                    </div>
                                    <div class="input-group has-validation ">
                                        <input type="text" class="form-control rounded-0 border-end-0"
                                            :id="'product-price-input' + index" :placeholder="item.label" aria-label="Price"
                                            aria-describedby="product-price-addon" required="" readonly>

                                        <button v-if="item.can_be_deleted == true" @click="removeCustomField(index)"
                                            class="input-group-text rounded-0 bg-white" id="product-price-addon"><i
                                                class="bx bx-trash"></i>
                                        </button>
                                        <button class="input-group-text rounded-0 bg-white"
                                            @click="openModalFieldCustomEdit(index, item)" id="product-price-addon"><i
                                                class="bx bx-cog"></i>
                                        </button>
                                    </div>

                                </div>
                                <div class="d-flex justify-content-end">
                                    <button class="btn btn-secondary" @click="fieldCustomShow = !fieldCustomShow">Tambah
                                        Field Custom</button>
                                    <BModal v-model="fieldCustomShow" hide-footer title="Custom Field" size="lg"
                                        class="v-modal-custom">
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <div class="mb-3">
                                                    <label class="form-label" for="product-title-input">Field:</label>
                                                    <select v-model="newCustomField.field" class="form-select">
                                                        <option value="Input">Input</option>
                                                        <option value="Textarea">Textarea</option>
                                                        <option value="Select">Select</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-lg-6" v-if="newCustomField.field == 'Input'">
                                                <div class="mb-3">
                                                    <label class="form-label" for="product-title-input">Input Type:</label>
                                                    <select v-model="newCustomField.inputType" class="form-select">
                                                        <option value="Text">Text</option>
                                                        <option value="Date">Date</option>
                                                        <option value="Number">Number</option>
                                                        <option value="Email">Email</option>
                                                        <option value="Url">Url</option>
                                                        <option value="File">File</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-lg-6">
                                                <div class="mb-3">
                                                    <label class="form-label" for="product-title-input">Label</label>
                                                    <input type="text" v-model="newCustomField.label"
                                                        class="form-control" />
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="mb-3">
                                                    <label class="form-label" for="product-title-input">Placeholder</label>
                                                    <input type="text" v-model="newCustomField.placeholder"
                                                        class="form-control" />
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="form-check form-switch form-switch-custom form-switch-success">
                                                    <input v-model="newCustomField.required" class="form-check-input"
                                                        type="checkbox" role="switch" id="required">
                                                    <label class="form-check-label" for="required">Required</label>
                                                </div>
                                            </div>
                                            <div class="col-lg-12 mt-3" v-if="newCustomField.field == 'Select'">
                                                <h5>Options</h5>
                                                <div class="input-group has-validation mb-3"
                                                    v-for="(item, index) in newCustomField.options" :key="index">
                                                    <input type="text" v-model="item.text"
                                                        class="form-control rounded-0 border-end-0" aria-label="Price"
                                                        aria-describedby="product-price-addon">

                                                    <button @click="removeOption(index)" class="input-group-text"
                                                        id="product-price-addon"><i class="bx bx-trash"></i>
                                                    </button>
                                                </div>
                                                <div class="d-flex justify-content-end">
                                                    <button class="btn btn-secondary" @click="addOption">Add Option</button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer v-modal-footer">
                                            <BButton type="button" variant="secondary" @click="addCustomField">Simpan
                                            </BButton>
                                        </div>
                                    </BModal>
                                </div>
                                <BModal v-model="fieldCustomEdit" hide-footer title="Custom Field" size="lg"
                                    class="v-modal-custom">
                                    <div class="row">
                                        <div class="col-lg-6" v-if="fieldCustomEditItem.name == 'Custom Field'">
                                            <div class="mb-3">
                                                <label class="form-label" for="product-title-input">Field:</label>
                                                <select v-model="fieldCustomEditItem.field" class="form-select">
                                                    <option value="Input">Input</option>
                                                    <option value="Textarea">Textarea</option>
                                                    <option value="Select">Select</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-6"
                                            v-if="fieldCustomEditItem.field == 'Input' && fieldCustomEditItem.name == 'Custom Field'">
                                            <div class="mb-3">
                                                <label class="form-label" for="product-title-input">Input Type:</label>
                                                <select v-model="fieldCustomEditItem.inputType" class="form-select">
                                                    <option value="Text">Text</option>
                                                    <option value="Date">Date</option>
                                                    <option value="Number">Number</option>
                                                    <option value="Email">Email</option>
                                                    <option value="Url">Url</option>
                                                    <option value="File">File</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label class="form-label" for="product-title-input">Label</label>
                                                <input type="text" v-model="fieldCustomEditItem.label"
                                                    class="form-control" />
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label class="form-label" for="product-title-input">Placeholder</label>
                                                <input type="text" v-model="fieldCustomEditItem.placeholder"
                                                    class="form-control" />
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-check form-switch form-switch-custom form-switch-success">
                                                <input v-model="fieldCustomEditItem.required" class="form-check-input"
                                                    type="checkbox" role="switch" id="required">
                                                <label class="form-check-label" for="required">Required</label>
                                            </div>
                                        </div>
                                        <div class="col-lg-12 mt-3"
                                            v-if="fieldCustomEditItem.field == 'Select' && fieldCustomEditItem.name == 'Custom Field'">
                                            <h5>Options</h5>
                                            <div class="input-group has-validation mb-3"
                                                v-for="(item, index) in fieldCustomEditItem.options" :key="index">
                                                <input type="text" v-model="item.text"
                                                    class="form-control rounded-0 border-end-0" aria-label="Price"
                                                    aria-describedby="product-price-addon">

                                                <button @click="removeOption(index)" class="input-group-text"
                                                    id="product-price-addon"><i class="bx bx-trash"></i>
                                                </button>
                                            </div>
                                            <div class="d-flex justify-content-end">
                                                <button class="btn btn-secondary" @click="addOptionEdit">Add Option</button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer v-modal-footer">
                                        <BButton type="button" variant="secondary" @click="editFieldCustom">Simpan
                                        </BButton>
                                    </div>
                                </BModal>
                            </div>
                        </div>
                    </BCardBody>
                </BCard>
                <BCard no-body>
                    <BCardHeader>
                        <h5 class="card-title mb-0">Dropship</h5>
                    </BCardHeader>
                    <BCardBody>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="mb-3">
                                    <div class="form-check form-switch form-switch-custom form-switch-success">
                                        <input class="form-check-input" type="checkbox" role="switch" id="dropship"
                                            v-model="checkoutData.dropship_status">
                                        <label class="form-check-label" for="dropship">Apakah Anda ingin
                                            mengaktifkan dropship di keranjang Anda?</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </BCardBody>
                </BCard>
                <BCard no-body>
                    <BCardHeader>
                        <h5 class="card-title mb-0">Tombol Beli</h5>
                    </BCardHeader>
                    <BCardBody>
                        <p>Sesuaikan template tombol pembelian Anda di bawah ini</p>
                        <div class="row">
                            <div class="col-lg-12 mb-3">
                                <button
                                    :style="'background-color:' + checkoutData.buyButtonBackground + ';color:' + checkoutData.buyButtonTextColor"
                                    class="btn w-100 fs-4 fw-bold d-flex align-items-center justify-content-center">{{
                                        checkoutData.buyButtonText }}
                                    <i class="bx bx-right-arrow-circle ms-2"></i></button>
                            </div>
                            <div class="col-lg-12 mb-3">
                                <p class="fw-bold m-0 p-0">Masukan teks untuk tombol pembelian Anda.</p>
                                <p class="m-0 p-0">(Saran: 'Beli Sekarang!' atau 'Selesaikan Pesanan')</p>
                            </div>
                            <div class="col-lg-12">
                                <input class="form-control" type="text" v-model="checkoutData.buyButtonText">
                            </div>
                            <div class="col-lg-12 mt-3">
                                <div class="form-group">
                                    <label class="form-label">Warna Background</label><br>
                                    <input type="color" v-model="checkoutData.buyButtonBackground">
                                </div>
                            </div>
                            <div class="col-lg-12 mt-3">
                                <div class="form-group">
                                    <label class="form-label">Warna Teks</label><br>
                                    <input type="color" v-model="checkoutData.buyButtonTextColor">
                                </div>
                            </div>
                        </div>
                    </BCardBody>
                </BCard>
                <BCard no-body>
                    <BCardHeader>
                        <h5 class="card-title mb-0">Konten</h5>
                    </BCardHeader>
                    <BCardBody>
                        <div class="row">
                            <div class="col-lg-12">
                                <p>Masukan deskripsi produk untuk menjelaskan lebih lanjtu tentang produk Anda</p>
                                <div class="mb-3">
                                    <textarea name="" v-model="checkoutData.productDescription" class="form-control" id=""
                                        cols="30" rows="5"></textarea>
                                </div>
                            </div>
                        </div>
                    </BCardBody>
                </BCard>
                <BCard no-body>
                    <BCardHeader>
                        <h5 class="card-title mb-0">Kolom Kupon</h5>
                    </BCardHeader>
                    <BCardBody>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="mb-3">
                                    <div class="form-check form-switch form-switch-custom form-switch-success">
                                        <input class="form-check-input" type="checkbox" role="switch" id="checkImageProduct"
                                            v-model="checkoutData.kuponStatus">
                                        <label class="form-check-label" for="checkImageProduct">Apakan Anda ingin menunjukan
                                            bidang kupon di keranjang Anda?</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </BCardBody>
                </BCard>
                <BCard no-body>
                    <BCardHeader>
                        <h5 class="card-title mb-0">Poin Poin</h5>
                    </BCardHeader>
                    <BCardBody>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="mb-3">
                                    <p>Tambahkan poin-poin untuk menampilkan manfaat dan fitur produk anda.</p>
                                </div>
                                <div class="mb-3" v-for="( item, index ) in  checkoutData.points " :key="index">
                                    <div class="input-group">
                                        <input type="text" v-model="item.poin" class="form-control">
                                        <span class="input-group-text" style="cursor:pointer" @click="removePoint(index)">
                                            <i class="bx bx-trash"></i> Hapus
                                        </span>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-end">
                                    <button class="btn btn-secondary" @click="addPoint">
                                        Tambahkan poin-poin
                                    </button>
                                </div>
                            </div>
                        </div>
                    </BCardBody>
                </BCard>
                <BCard no-body>
                    <BCardHeader>
                        <h5 class="card-title mb-0">Ringkasan Pesanan</h5>
                    </BCardHeader>
                    <BCardBody>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="mb-3">
                                    <div class="form-check form-switch form-switch-custom form-switch-success">
                                        <input class="form-check-input" type="checkbox" role="switch" id="summaryStatus"
                                            v-model="checkoutData.summaryStatus">
                                        <label class="form-check-label" for="summaryStatus">Apakah Anda ingin
                                            menampilkan ringkasan pesanan di keranjang Anda?</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </BCardBody>
                </BCard>
                <BCard no-body>
                    <BCardHeader>
                        <h5 class="card-title mb-0">Testimonials</h5>
                    </BCardHeader>
                    <BCardBody>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="mb-3">
                                    <p>Tambahkan testimoni untuk meningkatkan bukti sosial untuk menarik lebih banyak
                                        pelanggan.</p>
                                </div>
                                <div class="mb-3" v-for="( item, index ) in    checkoutData.testimonials" :key="index">
                                    <div class="row">
                                        <div class="col-4 d-flex align-items-center justify-content-center">
                                            <div v-if="item.image">
                                                <label :for="'testimonials' + index" class="img-thumbnail cursor-pointer"
                                                    style="width: 150px;">
                                                    <img class="img-fluid w-100" :src="item.image" alt="">
                                                </label>
                                            </div>
                                            <input type="file" hidden :id="'testimonials' + index"
                                                @change="handleChangeTestimonial($event, index)">
                                            <div v-if="!item.image">
                                                <label :for="'testimonials' + index"
                                                    class="img-thumbnail d-flex align-items-center justify-content-center text-dark cursor-pointer"
                                                    style="height:150px;width: 150px;">
                                                    <i class="bx bx-user " style="font-size:80px"></i>
                                                </label>

                                            </div>
                                        </div>
                                        <div class="col-8">
                                            <input type="text" class="form-control mb-3" v-model="item.name">
                                            <textarea name="" id="" cols="30" rows="5" class="form-control"
                                                v-model="item.message"></textarea>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-end mt-2">
                                        <button class="btn btn-danger" @click="deleteTestimonial(index)">Hapus</button>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-end">
                                    <button class="btn btn-secondary" @click="addTestimonials">
                                        Tambahkan Testimonials
                                    </button>
                                </div>
                            </div>
                        </div>
                    </BCardBody>
                </BCard>
                <div class="text-end mb-3">
                    <BButton type="button" variant="primary" class="w-sm" @click="pageMethod('success')">Selanjutnya
                    </BButton>
                </div>
            </BCol>
            <!-- End Page Checkout -->
            <BCol lg="6" v-if="page !== 'success'">

                <!-- Right Sidebar -->
                <BCard no-body class="bg-light" v-if="template == 'right-sidebar'"
                    style="position: sticky; top: 130px;overflow: auto; max-height: calc(100vh - 200px);">
                    <div class="row d-flex justify-content-center mt-4 mb-2">
                        <div class="col-12 col-lg-6 mb-3">
                            <img class="img-fluid w-100" :src="checkoutData.header.image" alt="">
                        </div>
                        <div class="col-12"
                            v-if="checkoutData.header.titleStatus == true || checkoutData.header.taglineStatus == true">
                            <h5 class="text-center" v-if="checkoutData.header.titleStatus == true">{{
                                checkoutData.header.title }}</h5>
                            <p class="text-center" v-if="checkoutData.header.taglineStatus == true">{{
                                checkoutData.header.tagline }}</p>
                        </div>
                    </div>
                    <BCardBody>
                        <BCard no-body>
                            <BCardBody class="pt-0 pb-0">
                                <div class="row">
                                    <div class="col-7 p-2">
                                        <div class="mb-3" v-if="productData.price_type == 'variable'">
                                            <h5 class="fw-bold" v-if="productData.many_buy_status == false">Pilihan Produk
                                            </h5>
                                            <template v-if="productData.many_buy_status == false">
                                                <div class="row" v-if="productData.many_variation_status == false">
                                                    <div class="col-6" v-for="(item, index) in size">
                                                        <div class="form-check">
                                                            <input class="form-check-input" name="product" type="radio">
                                                            <label class="form-check-label">
                                                                {{ item.name }}
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row" v-if="productData.many_variation_status == true">
                                                    <template v-for="(item, index) in productData.variation">
                                                        <div class="col-6"
                                                            v-for="(itemColor, index) in item.childVariation">
                                                            <div class="form-check">
                                                                <input class="form-check-input" name="product" type="radio">
                                                                <label class="form-check-label">
                                                                    {{ item.name }} - {{ itemColor.name }}
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </template>
                                                </div>
                                            </template>
                                            <template v-if="productData.many_buy_status == true">
                                                <div class="row" v-if="productData.many_variation_status == false">
                                                    <table class="table table-bordered">
                                                        <thead>
                                                            <tr>
                                                                <th>Pilihan Produk:</th>
                                                                <th>Jumlah</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr v-for="(item, index) in size">
                                                                <td class="w-75">{{ item.name }}</td>
                                                                <td>
                                                                    <input class="form-control" />
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div class="row" v-if="productData.many_variation_status == true">
                                                    <table class="table table-bordered">
                                                        <thead>
                                                            <tr>
                                                                <th>Pilihan Produk:</th>
                                                                <th>Jumlah</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <template v-for="(item, index) in productData.variation">
                                                                <tr v-for="(itemColor, index) in item.childVariation">
                                                                    <td class="w-75">{{ item.name }} - {{ itemColor.name }}
                                                                    </td>
                                                                    <td>
                                                                        <input class="form-control" />
                                                                    </td>
                                                                </tr>
                                                            </template>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </template>
                                        </div>
                                        <div class="mb-3">
                                            <p class="fw-bold">{{ checkoutData.sectionTitle.form }}</p>
                                        </div>
                                        <div class="mb-3" v-for="(  item, index  ) in   checkoutData.fields  " :key="index">
                                            <div v-if="item.show == true">
                                                <input v-if="item.field == 'Input'" :type="item.inputType"
                                                    class="form-control" :placeholder="item.placeholder" />
                                                <Multiselect v-if="item.field == 'Select'" class="form-control"
                                                    v-model="value1" :close-on-select="true" :placeholder="item.placeholder"
                                                    :searchable="true" :create-option="true" :options="[
                                                        { value: '', label: item.placeholder },
                                                    ]
                                                        " />
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <p class="fw-bold">{{ checkoutData.sectionTitle.payment_method }}</p>
                                            <div class="p-4 border" v-if="productData.bank_transfer.status == true">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="formCheck2">
                                                    <label class="form-check-label" for="formCheck2">
                                                        Bank Transfer
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="p-4 border" v-if="productData.cod.status == true">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="formCheck2">
                                                    <label class="form-check-label" for="formCheck2">
                                                        COD
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="p-4 border" v-if="productData.epayment.status == true">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="formCheck2">
                                                    <label class="form-check-label" for="formCheck2">
                                                        E - Payment
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="border border-dashed border-danger border-2 p-2 mb-3"
                                            v-if="productData.bump_status">
                                            <div class="d-flex align-items-center mb-1 bg-warning py-1 px-1 mb-1">
                                                <img class="me-2" src="@assets/images/bump-arrow.gif" alt="">
                                                <input type="checkbox" name="" class="form-check-input me-2" id="bump">
                                                <label class="form-check-label fw-bold" for="bump">Centang untuk
                                                    memesan juga</label>
                                            </div>
                                            <div class="fw-bold text-danger mb-1">Nama Produk Bump Anda + Harga</div>
                                            <div class="mb-1">
                                                <img class="img-fluid w-100" :src="productData.product_bump.image" alt="">
                                            </div>
                                            <div class="fs-12">Penjelasan Tentang Produk Bump Anda Disini</div>
                                        </div>
                                        <button
                                            :style="'background-color:' + checkoutData.buyButtonBackground + ';color:' + checkoutData.buyButtonTextColor"
                                            class="btn w-100 fs-4 fw-bold d-flex align-items-center justify-content-center">{{
                                                checkoutData.buyButtonText }}
                                            <i class="bx bx-right-arrow-circle ms-2"></i></button>
                                    </div>
                                    <div class="col-5 bg-light p-2">
                                        <div class="mb-3" v-if="checkoutData.productImageStatus == true">
                                            <img class="img-fluid w-100" :src="tempProductImage" alt="">
                                        </div>
                                        <div class="mb-3">{{ checkoutData.productDescription }}</div>
                                        <p class="fw-bold">{{ checkoutData.sectionTitle.points }}</p>
                                        <div class="mb-3" v-for="(   item, index   ) in    checkoutData.points   "
                                            :key="index">
                                            <div class="d-flex align-items-center">
                                                <i class="bx bx-check text-success fs-5"></i>
                                                {{ item.poin }}
                                            </div>
                                        </div>
                                        <div class="border border-secondary p-2 mb-3"
                                            v-if="checkoutData.summaryStatus == true">
                                            <p class="fs-12">
                                                RINCIAN PESANAN
                                            </p>
                                            <table class="w-100">
                                                <tr>
                                                    <td style="vertical-align: top;">
                                                        <p class="fs-12">
                                                            {{ productData.name }}
                                                        </p>
                                                    </td>
                                                    <td class="text-end">
                                                        <p v-if="productData.price_sale_status == true"
                                                            class="fs-12 text-decoration-line-through text-danger p-0 m-0">
                                                            {{ global.formatNumber(productData.normal_price) }}
                                                        </p>
                                                        <p class="fs-12" v-if="productData.price_sale_status == true">
                                                            {{ global.formatNumber(productData.price_sale) }}
                                                        </p>
                                                        <p class="fs-12" v-if="productData.price_sale_status == false">
                                                            {{ global.formatNumber(productData.normal_price) }}
                                                        </p>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                        <div class="mb-3" v-if="checkoutData.kuponStatus">
                                            <label class="form-label" for="product-price-input">GUNAKAN KODE
                                                KUPON</label>
                                            <div class="has-validation mb-3">
                                                <input type="text" class="form-control" id="product-price-input"
                                                    aria-label="Price" aria-describedby="product-price-addon" required="">
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </BCardBody>
                        </BCard>
                        <div class="container p-3" style="background:#00579e" v-if="checkoutData.testimonials.length > 0">
                            <div class="row">
                                <div class="col-12 col-lg-6" v-for="(item, index) in checkoutData.testimonials"
                                    :key="index">
                                    <div class="row align-items-center mb-3 p-2">
                                        <div class="col-auto">
                                            <div style="background:white">
                                                <img v-if="item.image" :src="item.image" alt="" style="width:100px">
                                                <div v-if="!item.image">
                                                    <i class="bx bx-user " style="font-size:80px"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <h5 class="mt-0 font-weight-bold text-white">{{ item.name }}</h5>
                                            <p class="text-white">{{ item.message }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </BCardBody>
                </BCard>

                <!-- Left Sidebar -->
                <BCard no-body class="bg-light" v-if="template == 'left-sidebar'"
                    style="position: sticky; top: 130px;overflow: auto; max-height: calc(100vh - 200px);">
                    <div class="row d-flex justify-content-center mt-4 mb-2">
                        <div class="col-12 col-lg-6 mb-3">
                            <img class="img-fluid w-100" :src="checkoutData.header.image" alt="">
                        </div>
                        <div class="col-12"
                            v-if="checkoutData.header.titleStatus == true || checkoutData.header.taglineStatus == true">
                            <h5 class="text-center" v-if="checkoutData.header.titleStatus == true">{{
                                checkoutData.header.title }}</h5>
                            <p class="text-center" v-if="checkoutData.header.taglineStatus == true">{{
                                checkoutData.header.tagline }}</p>
                        </div>
                    </div>
                    <BCardBody>
                        <BCard no-body>
                            <BCardBody class="pt-0 pb-0">
                                <div class="row">
                                    <div class="col-5 bg-light p-2">
                                        <div class="mb-3" v-if="checkoutData.productImageStatus == true">
                                            <img class="img-fluid w-100" :src="tempProductImage" alt="">
                                        </div>
                                        <div class="mb-3">{{ checkoutData.productDescription }}</div>
                                        <p class="fw-bold">{{ checkoutData.sectionTitle.points }}</p>
                                        <div class="mb-3" v-for="(   item, index   ) in    checkoutData.points   "
                                            :key="index">
                                            <div class="d-flex align-items-center">
                                                <i class="bx bx-check text-success fs-5"></i>
                                                {{ item.poin }}
                                            </div>
                                        </div>
                                        <div class="border border-secondary p-2 mb-3"
                                            v-if="checkoutData.summaryStatus == true">
                                            <p class="fs-12">
                                                RINCIAN PESANAN
                                            </p>
                                            <table class="w-100">
                                                <tr>
                                                    <td style="vertical-align: top;">
                                                        <p class="fs-12">
                                                            {{ productData.name }}
                                                        </p>
                                                    </td>
                                                    <td class="text-end">
                                                        <p v-if="productData.price_sale_status == true"
                                                            class="fs-12 text-decoration-line-through text-danger p-0 m-0">
                                                            {{ global.formatNumber(productData.normal_price) }}
                                                        </p>
                                                        <p class="fs-12" v-if="productData.price_sale_status == true">
                                                            {{ global.formatNumber(productData.price_sale) }}
                                                        </p>
                                                        <p class="fs-12" v-if="productData.price_sale_status == false">
                                                            {{ global.formatNumber(productData.normal_price) }}
                                                        </p>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                        <div class="mb-3" v-if="checkoutData.kuponStatus">
                                            <label class="form-label" for="product-price-input">GUNAKAN KODE
                                                KUPON</label>
                                            <div class="has-validation mb-3">
                                                <input type="text" class="form-control" id="product-price-input"
                                                    aria-label="Price" aria-describedby="product-price-addon" required="">
                                            </div>

                                        </div>
                                    </div>
                                    <div class="col-7 p-2">
                                        <div class="mb-3" v-if="productData.price_type == 'variable'">
                                            <h5 class="fw-bold" v-if="productData.many_buy_status == false">Pilihan Produk
                                            </h5>
                                            <template v-if="productData.many_buy_status == false">
                                                <div class="row" v-if="productData.many_variation_status == false">
                                                    <div class="col-6" v-for="(item, index) in size">
                                                        <div class="form-check">
                                                            <input class="form-check-input" name="product" type="radio">
                                                            <label class="form-check-label">
                                                                {{ item.name }}
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row" v-if="productData.many_variation_status == true">
                                                    <template v-for="(item, index) in productData.variation">
                                                        <div class="col-6"
                                                            v-for="(itemColor, index) in item.childVariation">
                                                            <div class="form-check">
                                                                <input class="form-check-input" name="product" type="radio">
                                                                <label class="form-check-label">
                                                                    {{ item.name }} - {{ itemColor.name }}
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </template>
                                                </div>
                                            </template>
                                            <template v-if="productData.many_buy_status == true">
                                                <div class="row" v-if="productData.many_variation_status == false">
                                                    <table class="table table-bordered">
                                                        <thead>
                                                            <tr>
                                                                <th>Pilihan Produk:</th>
                                                                <th>Jumlah</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr v-for="(item, index) in size">
                                                                <td class="w-75">{{ item.name }}</td>
                                                                <td>
                                                                    <input class="form-control" />
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div class="row" v-if="productData.many_variation_status == true">
                                                    <table class="table table-bordered">
                                                        <thead>
                                                            <tr>
                                                                <th>Pilihan Produk:</th>
                                                                <th>Jumlah</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <template v-for="(item, index) in productData.variation">
                                                                <tr v-for="(itemColor, index) in item.childVariation">
                                                                    <td class="w-75">{{ item.name }} - {{ itemColor.name }}
                                                                    </td>
                                                                    <td>
                                                                        <input class="form-control" />
                                                                    </td>
                                                                </tr>
                                                            </template>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </template>
                                        </div>
                                        <div class="mb-3">
                                            <p class="fw-bold">{{ checkoutData.sectionTitle.form }}</p>
                                        </div>
                                        <div class="mb-3" v-for="(  item, index  ) in   checkoutData.fields  " :key="index">
                                            <div v-if="item.show == true">
                                                <input v-if="item.field == 'Input'" :type="item.inputType"
                                                    class="form-control" :placeholder="item.placeholder" />
                                                <Multiselect v-if="item.field == 'Select'" class="form-control"
                                                    v-model="value1" :close-on-select="true" :placeholder="item.placeholder"
                                                    :searchable="true" :create-option="true" :options="[
                                                        { value: '', label: item.placeholder },
                                                    ]
                                                        " />
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <p class="fw-bold">{{ checkoutData.sectionTitle.payment_method }}</p>
                                            <div class="p-4 border" v-if="productData.bank_transfer.status == true">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="formCheck2">
                                                    <label class="form-check-label" for="formCheck2">
                                                        Bank Transfer
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="p-4 border" v-if="productData.cod.status == true">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="formCheck2">
                                                    <label class="form-check-label" for="formCheck2">
                                                        COD
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="p-4 border" v-if="productData.epayment.status == true">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="formCheck2">
                                                    <label class="form-check-label" for="formCheck2">
                                                        E - Payment
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="border border-dashed border-danger border-2 p-2 mb-3"
                                            v-if="productData.bump_status == true">
                                            <div class="d-flex align-items-center mb-1 bg-warning py-1 px-1 mb-1">
                                                <img class="me-2" src="@assets/images/bump-arrow.gif" alt="">
                                                <input type="checkbox" name="" class="form-check-input me-2" id="bump">
                                                <label class="form-check-label fw-bold" for="bump">Centang untuk
                                                    memesan juga</label>
                                            </div>
                                            <div class="fw-bold text-danger mb-1">Nama Produk Bump Anda + Harga</div>
                                            <div class="mb-1" v-if="productData.product_bump.image">
                                                <img class="img-fluid w-100" :src="productData.product_bump.image" alt="">
                                            </div>
                                            <div class="fs-12">Penjelasan Tentang Produk Bump Anda Disini</div>
                                        </div>
                                        <button
                                            :style="'background-color:' + checkoutData.buyButtonBackground + ';color:' + checkoutData.buyButtonTextColor"
                                            class="btn w-100 fs-4 fw-bold d-flex align-items-center justify-content-center">{{
                                                checkoutData.buyButtonText }}
                                            <i class="bx bx-right-arrow-circle ms-2"></i></button>
                                    </div>

                                </div>
                            </BCardBody>
                        </BCard>
                        <div class="container p-3" style="background:#00579e" v-if="checkoutData.testimonials.length > 0">
                            <div class="row">
                                <div class="col-12 col-lg-6" v-for="(item, index) in checkoutData.testimonials"
                                    :key="index">
                                    <div class="row align-items-center mb-3 p-2">
                                        <div class="col-auto">
                                            <div style="background:white">
                                                <img v-if="item.image" :src="item.image" alt="" style="width:100px">
                                                <div v-if="!item.image">
                                                    <i class="bx bx-user " style="font-size:80px"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <h5 class="mt-0 font-weight-bold text-white">{{ item.name }}</h5>
                                            <p class="text-white">{{ item.message }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </BCardBody>
                </BCard>

                <!-- No Sidebar -->
                <BCard no-body class="bg-light" v-if="template == 'no-sidebar'"
                    style="position: sticky; top: 130px;overflow: auto; max-height: calc(100vh - 200px);">
                    <div class="row d-flex justify-content-center mt-4 mb-2">
                        <div class="col-12 col-lg-6 mb-3">
                            <img class="img-fluid w-100" :src="checkoutData.header.image" alt="">
                        </div>
                        <div class="col-12"
                            v-if="checkoutData.header.titleStatus == true || checkoutData.header.taglineStatus == true">
                            <h5 class="text-center" v-if="checkoutData.header.titleStatus == true">{{
                                checkoutData.header.title }}</h5>
                            <p class="text-center" v-if="checkoutData.header.taglineStatus == true">{{
                                checkoutData.header.tagline }}</p>
                        </div>
                    </div>
                    <BCardBody>
                        <BCard no-body>
                            <BCardBody class="pt-0 pb-0">
                                <div class="row">
                                    <div class="col-12 px-5 pt-5">
                                        <div class="mb-3" v-if="checkoutData.productImageStatus == true">
                                            <img class="img-fluid w-100" :src="tempProductImage" alt="">
                                        </div>
                                        <div class="mb-3">{{ checkoutData.productDescription }}</div>
                                        <p class="fw-bold">{{ checkoutData.sectionTitle.points }}</p>
                                        <div class="mb-3" v-for="(   item, index   ) in    checkoutData.points   "
                                            :key="index">
                                            <div class="d-flex align-items-center">
                                                <i class="bx bx-check text-success fs-5"></i>
                                                {{ item.poin }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 px-5 pb-5">
                                        <div class="mb-3" v-if="productData.price_type == 'variable'">
                                            <h5 class="fw-bold" v-if="productData.many_buy_status == false">Pilihan Produk
                                            </h5>
                                            <template v-if="productData.many_buy_status == false">
                                                <div class="row" v-if="productData.many_variation_status == false">
                                                    <div class="col-6" v-for="(item, index) in size">
                                                        <div class="form-check">
                                                            <input class="form-check-input" name="product" type="radio">
                                                            <label class="form-check-label">
                                                                {{ item.name }}
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row" v-if="productData.many_variation_status == true">
                                                    <template v-for="(item, index) in productData.variation">
                                                        <div class="col-6"
                                                            v-for="(itemColor, index) in item.childVariation">
                                                            <div class="form-check">
                                                                <input class="form-check-input" name="product" type="radio">
                                                                <label class="form-check-label">
                                                                    {{ item.name }} - {{ itemColor.name }}
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </template>
                                                </div>
                                            </template>
                                            <template v-if="productData.many_buy_status == true">
                                                <div class="row" v-if="productData.many_variation_status == false">
                                                    <table class="table table-bordered">
                                                        <thead>
                                                            <tr>
                                                                <th>Pilihan Produk:</th>
                                                                <th>Jumlah</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr v-for="(item, index) in size">
                                                                <td class="w-75">{{ item.name }}</td>
                                                                <td>
                                                                    <input class="form-control" />
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div class="row" v-if="productData.many_variation_status == true">
                                                    <table class="table table-bordered">
                                                        <thead>
                                                            <tr>
                                                                <th>Pilihan Produk:</th>
                                                                <th>Jumlah</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <template v-for="(item, index) in productData.variation">
                                                                <tr v-for="(itemColor, index) in item.childVariation">
                                                                    <td class="w-75">{{ item.name }} - {{ itemColor.name }}
                                                                    </td>
                                                                    <td>
                                                                        <input class="form-control" />
                                                                    </td>
                                                                </tr>
                                                            </template>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </template>
                                        </div>
                                        <div class="mb-3">
                                            <p class="fw-bold">{{ checkoutData.sectionTitle.form }}</p>
                                        </div>
                                        <div class="mb-3" v-for="(  item, index  ) in   checkoutData.fields  " :key="index">
                                            <div v-if="item.show == true">
                                                <input v-if="item.field == 'Input'" :type="item.inputType"
                                                    class="form-control" :placeholder="item.placeholder" />
                                                <Multiselect v-if="item.field == 'Select'" class="form-control"
                                                    v-model="value1" :close-on-select="true" :placeholder="item.placeholder"
                                                    :searchable="true" :create-option="true" :options="[
                                                        { value: '', label: item.placeholder },
                                                    ]
                                                        " />
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <p class="fw-bold">{{ checkoutData.sectionTitle.payment_method }}</p>
                                            <div class="p-4 border" v-if="productData.bank_transfer.status == true">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="formCheck2">
                                                    <label class="form-check-label" for="formCheck2">
                                                        Bank Transfer
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="p-4 border" v-if="productData.cod.status == true">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="formCheck2">
                                                    <label class="form-check-label" for="formCheck2">
                                                        COD
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="p-4 border" v-if="productData.epayment.status == true">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="formCheck2">
                                                    <label class="form-check-label" for="formCheck2">
                                                        E - Payment
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="border border-dashed border-danger border-2 p-2 mb-3"
                                            v-if="productData.bump_status == true">
                                            <div class="d-flex align-items-center mb-1 bg-warning py-1 px-1 mb-1">
                                                <img class="me-2" src="@assets/images/bump-arrow.gif" alt="">
                                                <input type="checkbox" name="" class="form-check-input me-2" id="bump">
                                                <label class="form-check-label fw-bold" for="bump">Centang untuk
                                                    memesan juga</label>
                                            </div>
                                            <div class="fw-bold text-danger mb-1">Nama Produk Bump Anda + Harga</div>
                                            <div class="mb-1">
                                                <img class="img-fluid w-100" :src="productData.product_bump.image" alt="">
                                            </div>
                                            <div class="fs-12">Penjelasan Tentang Produk Bump Anda Disini</div>
                                        </div>
                                        <div class="border border-secondary p-2 mb-3"
                                            v-if="checkoutData.summaryStatus == true">
                                            <p class="fs-12">
                                                RINCIAN PESANAN
                                            </p>
                                            <table class="w-100">
                                                <tr>
                                                    <td style="vertical-align: top;">
                                                        <p class="fs-12">
                                                            {{ productData.name }}
                                                        </p>
                                                    </td>
                                                    <td class="text-end">
                                                        <p v-if="productData.price_sale_status == true"
                                                            class="fs-12 text-decoration-line-through text-danger p-0 m-0">
                                                            {{ global.formatNumber(productData.normal_price) }}
                                                        </p>
                                                        <p class="fs-12" v-if="productData.price_sale_status == true">
                                                            {{ global.formatNumber(productData.price_sale) }}
                                                        </p>
                                                        <p class="fs-12" v-if="productData.price_sale_status == false">
                                                            {{ global.formatNumber(productData.normal_price) }}
                                                        </p>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                        <div class="mb-3" v-if="checkoutData.kuponStatus">
                                            <label class="form-label" for="product-price-input">GUNAKAN KODE
                                                KUPON</label>
                                            <div class="has-validation mb-3">
                                                <input type="text" class="form-control" id="product-price-input"
                                                    aria-label="Price" aria-describedby="product-price-addon" required="">
                                            </div>

                                        </div>
                                        <button
                                            :style="'background-color:' + checkoutData.buyButtonBackground + ';color:' + checkoutData.buyButtonTextColor"
                                            class="btn w-100 fs-4 fw-bold d-flex align-items-center justify-content-center">{{
                                                checkoutData.buyButtonText }}
                                            <i class="bx bx-right-arrow-circle ms-2"></i></button>
                                    </div>

                                </div>
                            </BCardBody>
                        </BCard>
                        <div class="container p-3" style="background:#00579e" v-if="checkoutData.testimonials.length > 0">
                            <div class="row">
                                <div class="col-12 col-lg-6" v-for="(item, index) in checkoutData.testimonials"
                                    :key="index">
                                    <div class="row align-items-center mb-3 p-2">
                                        <div class="col-auto">
                                            <div style="background:white">
                                                <img v-if="item.image" :src="item.image" alt="" style="width:100px">
                                                <div v-if="!item.image">
                                                    <i class="bx bx-user " style="font-size:80px"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <h5 class="mt-0 font-weight-bold text-white">{{ item.name }}</h5>
                                            <p class="text-white">{{ item.message }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </BCardBody>
                </BCard>
            </BCol>

            <!-- Page Success -->
            <BCol lg="6" v-if="page == 'success'">
                <BCard no-body>
                    <BCardHeader>
                        <h5 class="card-title mb-0">Saat Pembelian Selesai, Arahkan Menuju</h5>
                    </BCardHeader>
                    <BCardBody>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="mb-3">
                                    <label class="form-check-label fw-bold mb-3" for="headline_success">Masukan
                                        Headline</label>
                                    <select class="form-select" v-model="successData.type">
                                        <option value="success_page">
                                            Success Page
                                        </option>
                                        <option value="redirect_wa">
                                            Redirect to WA
                                        </option>
                                    </select>
                                </div>
                                <div class="mb-3" v-if="successData.type == 'redirect_wa'">
                                    <label class="form-check-label fw-bold mb-3" for="headline_success">Masukan no WA
                                    </label>
                                    <input class="form-control" v-model="successData.wa" type="text" required>
                                </div>
                            </div>
                        </div>
                    </BCardBody>
                </BCard>
                <template v-if="successData.type == 'success_page'">
                    <BCard no-body>
                        <BCardHeader>
                            <h5 class="card-title mb-0">Headline Text</h5>
                        </BCardHeader>
                        <BCardBody>
                            <div class="row">
                                <div class="col-lg-12">
                                    <p>Masukan Judul Success Page's Anda di bawah ini</p>
                                    <div class="mb-3">
                                        <div class="form-check form-switch form-switch-custom form-switch-success">
                                            <input class="form-check-input" v-model="successData.showTitle" type="checkbox"
                                                role="switch" id="title_success">
                                            <label class="form-check-label" for="title_success">Tampilkan judul di bagian
                                                atas
                                                success page?</label>
                                        </div>
                                    </div>
                                    <div class="mb-3" v-if="successData.showTitle">
                                        <label class="form-label fw-bold mb-3">Success Title</label>
                                        <input class="form-control" v-model="successData.title" placeholder="Judul"
                                            type="text">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-check-label fw-bold mb-3" for="headline_success">Masukan
                                            Headline</label>
                                        <input class="form-control" v-model="successData.headline" type="text"
                                            id="headline_success">
                                    </div>
                                </div>
                            </div>
                        </BCardBody>
                    </BCard>
                    <BCard no-body>
                        <BCardHeader>
                            <h5 class="card-title mb-0">Video</h5>
                        </BCardHeader>
                        <BCardBody>
                            <div class="row">
                                <div class="col-lg-12">
                                    <p>Tambahkan video ke success page Anda di bawah ini</p>
                                    <div class="mb-3">
                                        <label class="form-check-label mb-3" for="headline_success">Masukan
                                            URL Video YouTube</label>
                                        <input class="form-control" v-model="successData.video" @keyup="embedYouTubeVideo"
                                            type="text" id="headline_success">
                                    </div>
                                </div>
                            </div>
                        </BCardBody>
                    </BCard>
                    <BCard no-body>
                        <BCardHeader>
                            <h5 class="card-title mb-0">Akun Bank</h5>
                        </BCardHeader>
                        <BCardBody>
                            <div class="row">
                                <div class="col-lg-12">
                                    <p>Choose bank account for your success page</p>
                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between">
                                            <label for="choices-publish-status-input" class="form-label">Akun Bank</label>
                                            <Link href="/profile?page=payment" class="cursor-pointer text-secondary fw-bold">+ Tambah No. Rekening</Link>
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
                                        <select class="form-select" v-model="tempSelectedBank" @change="selectBank">
                                            <option value="">Pilih akun bank</option>
                                            <option :value="item" v-for="(item, index) in listBankAccount">{{ item.bank }}
                                                - {{ item.atas_nama }} - {{ item.rekening }}
                                            </option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <div v-for="(item, index) in successData.accountBank"
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
                    <BCard no-body>
                        <BCardHeader>
                            <h5 class="card-title mb-0">Body Text</h5>
                        </BCardHeader>
                        <BCardBody>
                            <div class="row">
                                <div class="col-lg-12">
                                    <p class="text-muted">Edit Konten success page dan tautan Anda di bawah</p>
                                    <div class="mb-3">
                                        <label class="form-check-label mb-3" for="headline_success">Masukan beberapa teks
                                            untuk
                                            ditampilkan di Success Page</label>
                                        <div class="d-flex justify-content-between mb-3">
                                            <div type="button" class="text-center w-100 py-2"
                                                @click="changeBodyText('bank_transfer')"
                                                :class="{ 'border-bottom border-secondary': successData.bodyText.payment_type == 'bank_transfer' }">
                                                Bank Transfer
                                            </div>
                                            <div type="button" class="text-center w-100 py-2" @click="changeBodyText('cod')"
                                                :class="{ 'border-bottom border-secondary': successData.bodyText.payment_type == 'cod' }">
                                                COD
                                            </div>
                                            <div type="button" class="text-center w-100 py-2"
                                                @click="changeBodyText('payment')"
                                                :class="{ 'border-bottom border-secondary': successData.bodyText.payment_type == 'payment' }">
                                                Payment
                                            </div>
                                        </div>
                                        <div nav-class="row mb-3">
                                            <div class="col-12" title="Bank Transfer"
                                                v-if="successData.bodyText.payment_type == 'bank_transfer'">
                                                <div class="ckeditor-classic">
                                                    <ckeditor v-model="successData.bodyText.bank_transfer_body"
                                                        :editor="editor"></ckeditor>
                                                </div>
                                            </div>
                                            <div class="col-12" title="COD"
                                                v-if="successData.bodyText.payment_type == 'cod'">
                                                <div class="ckeditor-classic">
                                                    <ckeditor v-model="successData.bodyText.cod_body" :editor="editor">
                                                    </ckeditor>
                                                </div>
                                            </div>
                                            <div class="col-12" title="Payment"
                                                v-if="successData.bodyText.payment_type == 'payment'">
                                                <div class="ckeditor-classic">
                                                    <ckeditor v-model="successData.bodyText.payment_body" :editor="editor">
                                                    </ckeditor>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </BCardBody>
                    </BCard>
                </template>
                <div class="text-end mb-3">
                    <BButton type="button" variant="primary" class="w-sm" @click="submit">SIMPAN
                    </BButton>
                </div>
            </BCol>
            <template v-if="page == 'success'">
                <BCol lg="6" v-if="successData.type == 'success_page'">

                    <!-- Success Page -->
                    <BCard no-body class="bg-light"
                        style="position: sticky; top: 130px;overflow: auto; max-height: calc(100vh - 200px);">
                        <div class="row d-flex justify-content-center mt-4 mb-2">
                            <div class="col-12" v-if="successData.showTitle">
                                <h5 class="text-center">{{
                                    successData.title }}</h5>
                            </div>
                        </div>
                        <BCardBody>
                            <BCard no-body>
                                <BCardBody class="pt-0 pb-0 border-bottom border-success">
                                    <div class="row">
                                        <div class="col-12 p-2">
                                            <div class="mb-3">
                                                <h5 class="text-center">{{ evalExpression(successData.headline) }}</h5>
                                            </div>
                                            <div class="d-flex justify-content-center" v-if="successData.video != ''">
                                                <iframe :src="successData.video" frameborder="0" allowfullscreen></iframe>
                                            </div>
                                            <div class="text-center"
                                                v-if="successData.bodyText.payment_type == 'bank_transfer'"
                                                v-html="successData.bodyText.bank_transfer_body"></div>
                                            <div class="text-center" v-if="successData.bodyText.payment_type == 'cod'"
                                                v-html="successData.bodyText.cod_body"></div>
                                            <div class="text-center" v-if="successData.bodyText.payment_type == 'payment'"
                                                v-html="successData.bodyText.payment_body"></div>
                                            <div class="mb-3">
                                                <h5 class="text-center text-success fw-bold fs-3">{{
                                                    global.formatNumber(productData.normal_price)
                                                }}
                                                </h5>
                                            </div>
                                            <div class="row" v-if="successData.bodyText.payment_type == 'bank_transfer'">
                                                <div class="col-12 col-lg-6"
                                                    v-for="(item, index) in successData.accountBank">
                                                    <div class="card border">
                                                        <div class="card-body text-center">
                                                            <div>{{ item.bank }}</div>
                                                            <div>No. Rek: {{ item.rekening }}</div>
                                                            <div>Atas Nama: <span class="fw-bold">{{ item.atas_nama
                                                            }}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <p class="text-center"
                                                    v-if="successData.bodyText.payment_type == 'bank_transfer'">Konfirmasi
                                                    pembayaran anda di : <span class="text-secondary">Konfirmasi
                                                        Pembayaran</span></p>
                                                <!-- <p class="text-center text-muted">Anda akan segera redirect ke wa
                                                    setelah 10 detik</p> -->

                                            </div>
                                        </div>
                                    </div>
                                </BCardBody>
                            </BCard>
                        </BCardBody>
                    </BCard>
                </BCol>
                <BCol lg="6" v-if="successData.type == 'redirect_wa'">

                    <!-- Success Page -->
                    <BCard no-body class="bg-light"
                        style="position: sticky; top: 130px;overflow: auto; max-height: calc(100vh - 200px);">
                        <BCardBody>
                            <BCard no-body>
                                <BCardBody class="border-bottom border-success">
                                    <div class="d-flex justify-content-around align-items-center px-5">
                                        <img style="height:60px;" src="@assets/images/logo-white.png">
                                        <img style="height:40px" src="@assets/images/icon-arrow-right.png">
                                        <img style="height:100px" src="@assets/images/icon-www.png">
                                    </div>
                                    <div class="text-center">Anda akan redirect ke WA Kami</div>
                                </BCardBody>
                            </BCard>
                        </BCardBody>
                    </BCard>
                </BCol>
            </template>
            <!-- End page success -->
        </BRow>
    </Layout>
</template>