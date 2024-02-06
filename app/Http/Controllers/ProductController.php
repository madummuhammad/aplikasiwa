<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Store;
use App\Models\Bank;
use App\Models\Product;
use App\Models\User;
use App\Models\UserStore;
use App\Models\Collection;
use App\Models\ItemProduct;
use App\Models\Variation;
use App\Models\Checkout;
use App\Models\Stock;
use Carbon\Carbon;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();
        if ($user->permission['product']['read'] == true) {
            $store = UserStore::where('user_id', auth()->user()->id)->first();
            $product = Product::where('store_id', $store->store_id)->with('item_product.variation')->orderBy('created_at', 'DESC')->get();
            return Inertia::render('Product/Index', [
                'product' => $product,
                'user' => $user
            ]);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = auth()->user();
        if ($user->permission['product']['create'] == true) {
            $store = UserStore::where('user_id', $user->id)->with('store')->first();
            $team = UserStore::where('store_id', $store->store_id)->with('user')->get();
            return Inertia::render('Product/Create', [
                'store' => $store,
                'team' => $team
            ]);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $store = UserStore::where('user_id', auth()->user()->id)->first();
        $collection = $request->productData['collection'];
        $editedCollection = [];
        $collectionCreate = [];

        foreach ($collection as $key => $item) {
            if (isset($item['id'])) {
                $editedCollection[$key] = [
                    '_id' => $item['_id'],
                    'id' => $item['id'],
                    'name' => $item['name'],
                    'store_id' => $item['store_id']
                ];
            } else {
                $collectionCreate[$key] = Collection::create([
                    'name' => $item,
                    'store_id' => $store->store_id
                ]);
                $collectionData[$key] = Collection::where('id', $collectionCreate[$key]->id)->first();
                $editedCollection[$key] = [
                    '_id' => $collectionData[$key]->_id,
                    'id' => $collectionData[$key]->id,
                    'name' => $collectionData[$key]->name,
                    'store_id' => $collectionData[$key]->store_id
                ];
            }
        }

        $productData = $request->input('productData');
        $checkoutData = $request->input('checkoutData');
        $successData = $request->input('successData');
        $rotatorData = [];
        if ($productData['global_cs_rotator_status'] == true) {
            $rotatorData = $request->input('rotatorData');
        }

        // return $request->all();
        $dataProduct = [
            'store_id' => $store->store_id,
            'name' => $request->productData['name'],
            'product_code' => $request->productData['product_code'],
            'url' => $request->productData['url'],
            'category' => $request->productData['category_product'],
            'child_category' => $request->productData['childCategory'],
            'grand_child_category' => $request->productData['grandChildCategory'],
            'collection' => $editedCollection,
            'product_status' => $request->productData['product_status'],
            'image' => $productData['image'],
            'normal_price' => $productData['normal_price'],
            'price_sale_status' => $productData['price_sale_status'],
            'price_sale' => $productData['price_sale'],
            'hpp' => $productData['hpp'],
            'price_type' => $productData['price_type'],
            'variation_name' => $productData['variation_name'],
            'many_variation_status' => $productData['many_variation_status'],
            'many_buy_status' => $productData['many_buy_status'],
            'child_variation_name' => $productData['child_variation_name'],
            'manajemen_stock' => $productData['manajemen_stock'],
            'stock_status' => $productData['stock_status'],
            'backorder_status' => $productData['backorder_status'],
            'bump_status' => $productData['bump_status'],
            'product_bump' => $productData['product_bump'],
            'shipping' => $productData['shipping'],
            'bank_transfer' => $productData['bank_transfer'],
            'cod' => $productData['cod'],
            'epayment' => $productData['epayment'],
            'unique_code' => $productData['unique_code'],
            'reseller_status' => $productData['reseller_status'],
            'global_cs_rotator_status' => $productData['global_cs_rotator_status'],
            'rotatorData' => $rotatorData,
            'checkout' => $checkoutData,
            'followUp' => $this->followup()
        ];

        $product = Product::create($dataProduct);

        Checkout::create([
            'product_id' => $product->id,
            'store_id' => $store->store_id,
            'name' => $checkoutData['name'],
            'template' => $checkoutData['template'],
            'sectionTitle' => $checkoutData['sectionTitle'],
            'header' => $checkoutData['header'],
            'productImageStatus' => $checkoutData['productImageStatus'],
            'buyButtonText' => $checkoutData['buyButtonText'],
            'buyButtonBackground' => $checkoutData['buyButtonBackground'],
            'buyButtonTextColor' => $checkoutData['buyButtonTextColor'],
            'productDescription' => $checkoutData['productDescription'],
            'kuponStatus' => $checkoutData['kuponStatus'],
            'dropship_status' => $checkoutData['dropship_status'],
            'points' => $checkoutData['points'],
            'summaryStatus' => $checkoutData['summaryStatus'],
            'testimonials' => $checkoutData['testimonials'],
            'fields' => $checkoutData['fields'],
            'olshop'=>$checkoutData['olshop'],
            'view' => 0,
            'success_page' => $successData,
        ]);

        // Tidak ada variantion
        if ($productData['price_type'] == 'simple') {
            $item_product = ItemProduct::create([
                'product_id' => $product->id,
                'store_id' => $store->store_id,
                'sequence' => 1,
                'normal_price' => $productData['normal_price'],
                'hpp' => $productData['hpp'],
                'stock' => $productData['stock'],
                'product_code' => $productData['product_code'],
                'weight' => 0,
                'variation_id' => null,
            ]);

            if ($productData['manajemen_stock']) {
                Stock::create([
                    'item_product_id' => $item_product->id,
                    'stock_type' => 'Addition',
                    'qty' => $productData['stock'],
                    'note' => 'Addition by Created Stock',
                    'stock' => $productData['stock'],
                    'by' => auth()->user()->name
                ]);
            }
        } else {
            // Ada banyak variation
            if ($productData['many_variation_status'] == false) {
                foreach ($productData['size'] as $key => $value) {
                    $variation = Variation::create([
                        'product_id' => $product->id,
                        'sequence' => $key,
                        'variation_name' => $value['name'],
                        'child_variation_name' => null,
                    ]);

                    $item_product = ItemProduct::create([
                        'product_id' => $product->id,
                        'store_id' => $store->store_id,
                        'sequence' => $key,
                        'normal_price' => $value['price'],
                        'hpp' => $value['hpp'],
                        'stock' => $value['stock'],
                        'product_code' => $value['variation_code'],
                        'weight' => $value['weight'],
                        'variation_id' => $variation->id,
                    ]);

                    if ($productData['manajemen_stock']) {
                        Stock::create([
                            'item_product_id' => $item_product->id,
                            'stock_type' => 'Addition',
                            'qty' => $value['stock'],
                            'note' => 'Addition by Created Stock',
                            'stock' => $value['stock'],
                            'by' => auth()->user()->name
                        ]);
                    }
                }
            } else {
                // Ada satu variation
                $no = 1;
                foreach ($productData['variation'] as $key => $value) {

                    foreach ($value['childVariation'] as $childKey => $child) {
                        $sequence = $no++;
                        $variation = Variation::create([
                            'product_id' => $product->id,
                            'sequence' => $sequence,
                            'variation_name' => $value['name'],
                            'child_variation_name' => $child['name'],
                        ]);

                        $item_product = ItemProduct::create([
                            'product_id' => $product->id,
                            'store_id' => $store->store_id,
                            'sequence' => $sequence,
                            'normal_price' => $child['price'],
                            'hpp' => $child['hpp'],
                            'stock' => $child['stock'],
                            'product_code' => $child['variation_code'],
                            'weight' => $child['weight'],
                            'variation_id' => $variation->id,
                        ]);


                        if ($productData['manajemen_stock']) {
                            Stock::create([
                                'item_product_id' => $item_product->id,
                                'stock_type' => 'Addition',
                                'qty' => $child['stock'],
                                'note' => 'Addition by Created Stock',
                                'stock' => $child['stock'],
                                'by' => auth()->user()->name
                            ]);
                        }
                    }
                }
            }
        }


        return response()->json(['status' => 'success', 'data' => $product]);
        // return redirect()->route('product');
    }

    public function check_url(Request $request)
    {
        $url = $request->url;
        $store = UserStore::where('user_id', auth()->user()->id)->first();
        if ($request->id) {
            $product = Product::where('url', $url)->where('store_id', $store->store_id)->whereNot('id', $request->id)->exists();
        } else {
            $product = Product::where('url', $url)->where('store_id', $store->store_id)->exists();
        }

        // return $product;

        if ($product) {
            return response()->json(['status' => 'error']);
        } else {
            return response()->json(['status' => 'success']);
        }
    }

    public function followup()
    {
        return $followUp = [
            'welcome' => [
                'bank_transfer' => [
                    'text' => '*Terima kasih, Kami sudah terima pesanan anda dengan rincian sebagai berikut*<br>
                                Produk: {{productData.value.name}}<br>
                                Harga: {{product_price}}<br>
                                Ongkir: {{shipping_cost}}<br>
                                *Total: {{total_price}}*<br>
                                <br>
                                <br>
                                *Dikirim ke:*<br>
                                Nama: {{name}}<br>
                                No HP: {{phone}}<br>
                                Alamat: {{address}}<br>
                                Kota: {{city}}<br>
                                Kecamatan: {{district}}<br>
                                Provinsi: {{province}}<br>
                                <br>
                                Silahkan Transfer Nomor Rekening Berikut : <br>
                                {{bank_accounts}}<br>
                                *Jika Sudah Transfer Mohon Konfirmasi Agar Bisa Segera Diproses*'
                ],
                'cod' => [
                    'text' => '*Terima kasih, Kami sudah terima pesanan anda dengan rincian sebagai berikut*<br>
                                Produk: {{product_name}}<br>
                                Harga: {{product_price}}<br>
                                Ongkir: {{shipping_cost}}<br>
                                Biaya COD: {{shipping_cod_cost}}<br>
                                <br>
                                *Total: {{total_price}}*<br>
                                <br>
                                <br>
                                *Dikirim ke:*<br>
                                Nama: {{name}}<br>
                                No HP: {{phone}}<br>
                                Alamat: {{address}}<br>
                                Kota: {{city}}<br>
                                Kecamatan: {{district}}<br>
                                Provinsi: {{province}}<br>
                                <br>
                                *Untuk Alamatnya Sudah Lengkap dan Benar Kak? Mohon Konfirmasinya ya kak, agar paketnya segera kami proses untuk dikirim.*'
                ],
                'epayment' => [
                    'text' => '*Terima kasih, Kami sudah terima pesanan anda dengan rincian sebagai berikut*<br>
                                Produk: {{product_name}}<br>
                                Harga: {{product_price}}<br>
                                Ongkir: {{shipping_cost}}<br>
                                *Total: {{total_price}}*<br>
                                <br>
                                <br>
                                *Dikirim ke:*<br>
                                Nama: {{name}}<br>s
                                No HP: {{phone}}<br>
                                Alamat: {{address}}<br>
                                Kota: {{city}}<br>
                                Kecamatan: {{district}}<br>
                                Provinsi: {{province}}<br>
                                <br>
                                Agar pesanannya kami proses kakak bisa melakukan pembayaran ya kak, {{e_payment_link}}'
                ]
            ],
            'order_detail' => [
                'bank_transfer' => [
                    'text' => '"Pesanan anda:<br>
                                Produk: {{product_name}}<br>
                                Harga: {{product_price}}<br>
                                Ongkir: {{shipping_cost}}<br>
                                Total: {{total_price}}<br>
                                <br>
                                <br>
                                Dikirim ke:<br>
                                Nama: {{name}}<br>
                                No HP: {{phone}}<br>
                                Alamat: {{address}}<br>
                                Kota: {{city}}<br>
                                Kecamatan: {{district}}<br>
                                <br>
                                <br>
                                Silahkan transfer senilai {{total_price}}, ke salah satu rekening dibawah ini:<br>
                                {{bank_accounts}}"'
                ],
                'cod' => [
                    'text' => 'Pesanan anda:<br>
                                Produk: {{product_name}}<br>
                                Harga: {{product_price}}<br>
                                Ongkir: {{shipping_cost}}<br>
                                Total: {{total_price}}<br>
                                <br>
                                <br>
                                Dikirim ke:<br>
                                Nama: {{name}}<br>
                                No HP: {{phone}}<br>
                                Alamat: {{address}}<br>
                                Kota: {{city}}<br>
                                Kecamatan: {{district}}<br>
                                <br>
                                kakak bisa menyiapkan {{total_price}} dan pastikan untuk nomor telpon dan alamatnya sudah benar ya kak'
                ],
                'epayment' => [
                    'text' => 'Pesanan anda:<br>
                                Produk: {{product_name}}<br>
                                Harga: {{product_price}}<br>
                                Ongkir: {{shipping_cost}}<br>
                                Total: {{total_price}}<br>
                                <br>
                                <br>
                                Dikirim ke:<br>
                                Nama: {{name}}<br>
                                No HP: {{phone}}<br>
                                Alamat: {{address}}<br>
                                Kota: {{city}}<br>
                                Kecamatan: {{district}}<br>
                                <br>
                                Agar pesanannya kami proses kakak bisa melakukan pembayaran ya kak, {{e_payment_link}}'
                ]
            ],
            'follow_up_1' => [
                'bank_transfer' => [
                    'text' => 'Halo kak, Untuk barang yang kakak pesan kami cek belum dilakukan pembayaran,<br>
                                agar orderannya kami proses kakak bisa melakukan proses pembayaran ya kak 😊<br>
                                <br>
                                <br>
                                kakak bisa melakukan pembayakan ke nomor rekening :<br>
                                {{bank_accounts}}'
                ],
                'cod' => [
                    'text' => 'Halo kak, mohon konfirmasinya  apakah alamatnya sudah benar dan lengkap untuk pesanan paket CODnya? Agar bisa segera kami kirim paketnya 😊 terima kasih.'
                ],
                'epayment' => [
                    'text' => 'Halo kak, Untuk barang yang kakak pesan kami cek belum dilakukan pembayaran,<br>
                                agar orderannya kami proses kakak bisa melakukan proses pembayaran ya kak 😊<br>
                                <br>
                                <br>
                                Agar pesanannya kami proses kakak bisa melakukan pembayaran ya kak, {{e_payment_link}}<br>'
                ]
            ],
            'follow_up_2' => [
                'bank_transfer' => [
                    'text' => 'Halo kak, Selamat siang,<br>
                                    agar orderannya kami proses kakak bisa melakukan proses pembayaran ya kak 😊<br>
                                    <br>
                                    <br>
                                    kakak bisa melakukan pembayakan ke nomor rekening :<br>
                                    {{bank_accounts}}<br>'
                ],
                'cod' => [
                    'text' => 'Halo kak, mau konfirmasi untuk pesanan paket CODnya sudah siap dikirim, mohon konfirmasinya ya kak apakah alamatnya sudah lengkap?'
                ],
                'epayment' => [
                    'text' => 'Halo kak, Selamat siang,<br>
                                    agar orderannya kami proses kakak bisa melakukan proses pembayaran ya kak 😊<br>
                                    <br>
                                    Agar pesanannya kami proses kakak bisa melakukan pembayaran ya kak, {{e_payment_link}}<br>'
                ]
            ],
            'follow_up_3' => [
                'bank_transfer' => [
                    'text' => 'Halo kak, Selamat siang,<br>
                                        produk ini ternyata laris manis loh kak, stock nya tinggal dikit apakah kakak yakin tidak akan melanjutkan ordernya😊<br>
                                        <br>
                                        <br>
                                        kakak bisa melakukan pembayakan ke nomor rekening :<br>
                                        {{bank_accounts}}'
                ],
                'cod' => [
                    'text' => 'Halo kak, mohon konfirmasinya  apakah alamatnya sudah benar dan lengkap untuk pesanan paket CODnya? Agar bisa segera kami kirim paketnya 😊 terima kasih.'
                ],
                'epayment' => [
                    'text' => 'Halo kak, Selamat siang,<br>
                                        produk ini ternyata laris manis loh kak, stock nya tinggal dikit apakah kakak yakin tidak akan melanjutkan ordernya😊<br>
                                        <br>
                                        Agar pesanannya kami proses kakak bisa melakukan pembayaran ya kak, {{e_payment_link}}'
                ]
            ],
            'follow_up_4' => [
                'bank_transfer' => [
                    'text' => 'Halo kak, Selamat siang,<br>
                                            produk yang kakak checkout sisa stocknya tipis bangett, sepertinya besok habis, buruan agar tidak kehabisan kakak bisa melakukan pembayaran sekarang juga😊<br>
                                            <br>
                                            <br>
                                            kakak bisa melakukan pembayakan ke nomor rekening :<br>
                                            {{bank_accounts}}'
                ],
                'cod' => [
                    'text' => 'Halo kak, mohon konfirmasinya  apakah alamatnya sudah benar dan lengkap untuk pesanan paket CODnya? Agar bisa segera kami kirim paketnya 😊 terima kasih.'
                ],
                'epayment' => [
                    'text' => 'Halo kak, Selamat siang,<br>
                                            produk yang kakak checkout sisa stocknya tipis bangett, sepertinya besok habis, buruan agar tidak kehabisan kakak bisa melakukan pembayaran sekarang juga😊<br>
                                            <br>
                                            Agar pesanannya kami proses kakak bisa melakukan pembayaran ya kak, {{e_payment_link}}<br>'
                ]
            ],
            'processing' => [
                'bank_transfer' => [
                    'text' => 'Pembayaran dari {{name}} untuk pembelian {{product_name}} senilai {{total_price}} telah kami terima, pesanan anda kini kami proses.😊<br>
                                                <br>
                                                <br>
                                                kakak bisa melakukan pembayakan ke nomor rekening :<br>
                                                {{bank_accounts}}'
                ],
                'cod' => [
                    'text' => 'pesananan {{name}} untuk pembelian {{product_name}} senilai {{total_price}} telah kami terima, untuk selanjutnya pesanan anda kini sedang kami proses.'
                ],
                'epayment' => [
                    'text' => 'Pembayaran dari {{name}} untuk pembelian {{product_name}} senilai {{total_price}} telah kami terima, pesanan anda kini kami proses.😊<br>
                                                <br>
                                                Agar pesanannya kami proses kakak bisa melakukan pembayaran ya kak, {{e_payment_link}}'
                ]
            ],

            'completed' => [
                'bank_transfer' => [
                    'text' => 'Terima kasih kak {{name}} sudah belanja di toko kami 🙏🏻semoga barang nya awet dan kembali berlangganan di toko kami'
                ],
                'cod' => [
                    'text' => 'Terima kasih kak {{name}} sudah belanja di toko kami 🙏🏻semoga barang nya awet dan kembali berlangganan di toko kami'
                ],
                'epayment' => [
                    'text' => 'Terima kasih kak {{name}} sudah belanja di toko kami 🙏🏻semoga barang nya awet dan kembali berlangganan di toko kami'
                ]
            ],

            'up_selling' => [
                'bank_transfer' => [
                    'text' => 'Halo, kami lagi ada promo khusus hanya untuk anda {{name}}<br>
                                                - Produk XYZ, dari Rp180.000 jadi cuma Rp140rb aja ☺<br>
                                                Stok terbatas ya, pesan sekarang sebelum kehabisan...️'
                ],
                'cod' => [
                    'text' => 'Halo, kami lagi ada promo khusus hanya untuk anda {{name}}<br>
                                                - Produk XYZ, dari Rp180.000 jadi cuma Rp140rb aja ☺<br>
                                                Stok terbatas ya, pesan sekarang sebelum kehabisan...️'
                ],
                'epayment' => [
                    'text' => 'Halo, kami lagi ada promo khusus hanya untuk anda {{name}}<br>
                                                - Produk XYZ, dari Rp180.000 jadi cuma Rp140rb aja ☺<br>
                                                Stok terbatas ya, pesan sekarang sebelum kehabisan...️'
                ]
            ],
            'redirect' => [
                'bank_transfer' => [
                    'text' => 'Halo, saya sudah melakukan pemesanan {{product_name}}, atas nama {{name}}. Mohon segera diproses ya 🙏🏻'
                ],
                'cod' => [
                    'text' => 'Halo, saya sudah melakukan pemesanan {{product_name}}, atas nama {{name}}. Mohon segera diproses ya 🙏🏻'
                ],
                'epayment' => [
                    'text' => 'Halo, saya sudah melakukan pemesanan {{product_name}}, atas nama {{name}}. Mohon segera diproses ya 🙏🏻'
                ]
            ]

        ];
    }

    public function update(Request $request, string $id)
    {
        $store = UserStore::where('user_id', auth()->user()->id)->first();
        $collection = $request->collection;
        $editedCollection = [];
        $collectionCreate = [];
        foreach ($collection as $key => $item) {
            if (isset($item['value']['id'])) {
                $editedCollection[$key] = [
                    '_id' => $item['value']['_id'],
                    'id' => $item['value']['id'],
                    'name' => $item['value']['name'],
                    'store_id' => $item['value']['store_id']
                ];
            } else {
                $collectionCreate[$key] = Collection::create([
                    'name' => $item['value'],
                    'store_id' => $store->store_id
                ]);
                $collectionData[$key] = Collection::where('id', $collectionCreate[$key]->id)->first();
                $editedCollection[$key] = [
                    '_id' => $collectionData[$key]->_id,
                    'id' => $collectionData[$key]->id,
                    'name' => $collectionData[$key]->name,
                    'store_id' => $collectionData[$key]->store_id
                ];
            }
        }

        $rotatorData = [];
        if ($request->global_cs_rotator_status == true) {
            $rotatorData = $request->input('rotatorData');
        }

        // return $rotatorData;
        $dataProduct = [
            'store_id' => $store->store_id,
            'name' => $request->name,
            'product_code' => $request->product_code,
            'url' => $request->url,
            'category' => $request->category,
            'child_category' => $request->child_category,
            'grand_child_category' => $request->grand_child_category,
            'collection' => $editedCollection,
            'product_status' => $request->product_status,
            'image' => $request->image,
            'normal_price' => $request->normal_price,
            'price_sale_status' => $request->price_sale_status,
            'price_sale' => $request->price_sale,
            'hpp' => $request->hpp,
            'price_type' => $request->price_type,
            'variation_name' => $request->variation_name,
            'many_variation_status' => $request->many_variation_status,
            'many_buy_status' => $request->many_buy_status,
            'child_variation_name' => $request->child_variation_name,
            'manajemen_stock' => $request->manajemen_stock,
            'stock_status' => $request->stock_status,
            'backorder_status' => $request->backorder_status,
            'bump_status' => $request->bump_status,
            'product_bump' => $request->product_bump,
            'shipping' => $request->shipping,
            'bank_transfer' => $request->bank_transfer,
            'cod' => $request->cod,
            'epayment' => $request->epayment,
            'unique_code' => $request->unique_code,
            'reseller_status' => $request->reseller_status,
            'rotatorData' => $rotatorData,
            'global_cs_rotator_status' => $request->global_cs_rotator_status
        ];

        $product = Product::where('id', $request->id)->update($dataProduct);

        // return;

        // Tidak ada variantion
        if ($request->price_type == 'simple') {
            ItemProduct::where('product_id', $request->id)->whereNotNull('variation_id')->delete();




            if ($request->manajemen_stock == true) {
                $item_product = ItemProduct::where('product_id', $request->id)->first();

                if (intval($item_product->stock) !== intval($request->item_product[0]['stock'])) {

                    if (intval($item_product->stock) < intval($request->item_product[0]['stock'])) {
                        $stock_type = 'Addition';
                        $qty = intval($request->item_product[0]['stock']) - intval($item_product->stock);
                    } else {
                        $stock_type = 'Deduction';
                        $qty = intval($item_product->stock) - intval($request->item_product[0]['stock']);
                    }


                    Stock::create([
                        'item_product_id' => $item_product->id,
                        'stock_type' => $stock_type,
                        'qty' => $qty,
                        'note' => 'Updated from product page',
                        'stock' => intval($request->item_product[0]['stock']),
                        'by' => auth()->user()->name
                    ]);
                }
            }

            ItemProduct::updateOrCreate(
                ['product_id' => $request->id],
                [
                    'product_id' => $request->id,
                    'store_id' => $store->store_id,
                    'sequence' => 1,
                    'normal_price' => $request->normal_price,
                    'hpp' => $request->hpp,
                    'stock' => intval($request->item_product[0]['stock']),
                    'product_code' => $request->product_code,
                    'weight' => 0,
                    'variation_id' => null,
                ]
            );
        } else {
            $itemProducts = $request->input('item_product');
            $product = Product::find($request->id);
            $variationIdsInLoop = [];
            $itemProductIdsInLoop = [];

            foreach ($itemProducts as $key => $item) {
                $variationId = isset($item['variation_id']) ? $item['variation_id'] : null;
                $variation = Variation::updateOrCreate(
                    ['id' => $variationId],
                    [
                        'product_id' => $request->id,
                        'sequence' => $key,
                        'variation_name' => $item['variation']['variation_name'],
                        'child_variation_name' => $item['variation']['child_variation_name'],
                    ]
                );
                $variationIdsInLoop[] = $variation->id;

                if ($request->manajemen_stock == true) {
                    $item_product = ItemProduct::where('id', $item['id'])->first();

                    if (intval($item_product->stock) !== intval($item['stock'])) {

                        if (intval($item_product->stock) < intval($item['stock'])) {
                            $stock_type = 'Addition';
                            $qty = intval($item['stock']) - intval($item_product->stock);
                        } else {
                            $stock_type = 'Deduction';
                            $qty = intval($item_product->stock) - intval($item['stock']);
                        }


                        Stock::create([
                            'item_product_id' => $item_product->id,
                            'stock_type' => $stock_type,
                            'qty' => $qty,
                            'note' => 'Updated from product page',
                            'stock' => intval($item['stock']),
                            'by' => auth()->user()->name
                        ]);
                    }
                }

                $itemProduct = ItemProduct::updateOrCreate(
                    ['id' => $item['id']],
                    [
                        'product_id' => $request->id,
                        'store_id' => $store->store_id,
                        'sequence' => $key,
                        'normal_price' => $item['normal_price'],
                        'hpp' => $item['hpp'],
                        'stock' => $item['stock'],
                        'product_code' => $item['product_code'],
                        'weight' => $item['weight'],
                        'variation_id' => $variation->id,
                    ]
                );
                $itemProductIdsInLoop[] = $itemProduct->id;
            }

            ItemProduct::where('product_id', $request->id)
                ->whereNotIn('id', $itemProductIdsInLoop)
                ->delete();
            // Hapus variation yang tidak ada di dalam perulangan
            Variation::where('product_id', $request->id)
                ->whereNotIn('id', $variationIdsInLoop)
                ->delete();
        }
        return redirect()->route('product.show', $request->id);
    }

    public function upload(Request $request)
    {
        $image = $request->file('image')->store('public/image');

        $url = url('/') . '/' . str_replace("public/", "storage/", $image);

        return response()->json(['status' => 'success', 'data' => $url]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = auth()->user();
        if ($user->permission['product']['read'] == true) {
            $store = UserStore::where('user_id', auth()->user()->id)->first();
            $product = Product::where('store_id', $store->store_id)
                ->where('id', $id)
                ->with(['checkout' => function ($query) {
                    $query->orderBy('created_at', 'DESC');
                }, 'item_product.variation'])
                ->firstOrFail();

            return Inertia::render('Product/Show', [
                'product' => $product,
                'user' => $user
            ]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $user = auth()->user();
        if ($user->permission['product']['update'] == true) {
            $store = UserStore::where('user_id', $user->id)->with('store')->first();
            $product = Product::where('id', $id)->with('item_product.variation')->first();

            $variations = [];
            $childVariations = [];

            $item_variations = [];
            // return $product->item_product;
            if ($product->price_type == 'variable') {
                foreach ($product->item_product as $item) {
                    $size = $item['variation']['variation_name'];
                    $color = $item['variation']['child_variation_name'];

                    if (!in_array(['name' => $size], $variations)) {
                        $variations[] = ['name' => $size];
                    }

                    if (!in_array(['name' => $color], $childVariations)) {
                        $childVariations[] = ['name' => $color];
                    }
                }

                $item_variations = [
                    'variations' => $variations,
                    'child_variation' => $childVariations
                ];
            }

            $product->item_variations = $item_variations;
            $team = UserStore::where('store_id', $store->store_id)->with('user')->where('is_owner', false)->get();
            return Inertia::render('Product/Edit', [
                'store' => $store,
                'product' => $product,
                'team' => $team
            ]);
        }
    }

    public function destroy(string $id)
    {
        Product::where('id', $id)->delete();

        return response()->json(['status' => 'success']);
    }

    public function destroy_check(Request $request)
    {
        foreach ($request['productData'] as $key => $value) {
            if ($value['check'] == true) {
                Product::where('id', $value['id'])->delete();
            }
        }

        return back();
    }
}
