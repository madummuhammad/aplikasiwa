<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Order;
use App\Models\Checkout;
use App\Models\Product;
use App\Models\Store;
use App\Models\OrderPayment;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($url, $order_id)
    {
        $order = Order::where('id', $order_id)->first();
        $checkout = Checkout::where('product_id', $order->product_id)->orderBy('created_at', 'DESC')->first();
        $product = Product::where('id', $order->product_id)->with('item_product.variation')->first();
        return Inertia::render('Landing/Payment/Index', [
            'checkout' => $checkout,
            'product' => $product,
            'order' => $order,
            'success_page' => $checkout->success_page
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $url, $order_id)
    {
        $order = Order::where('id', $order_id)->first();

        $request->validate([
            'atas_nama' => $request->payment_method == 'bank_transfer' ? 'required' : 'nullable',
            'transfer_ke' => $request->payment_method == 'bank_transfer' ? 'required' : 'nullable',
            'tanggal_transfer' => $request->payment_method == 'bank_transfer' ? 'required|date' : 'nullable',
            'jumlah_transfer' => $request->payment_method == 'bank_transfer' ? 'required|numeric' : 'nullable',
            'bukti_transfer' => $request->payment_method == 'bank_transfer' ? 'required' : 'nullable',
            'payment_method' => 'required|in:bank_transfer,cod,epayment', // Ganti 'other_payment_method' dengan nilai yang benar
        ]);

        $atas_nama = $request->input('atas_nama');
        $transfer_ke = $request->input('transfer_ke');
        $tanggal_transfer = $request->input('tanggal_transfer');
        $jumlah_transfer = $request->input('jumlah_transfer');
        $bukti_transfer = $request->input('bukti_transfer');

        $payment = OrderPayment::create([
            'store_id' => $order->store_id,
            'order_id' => $order->id,
            'atas_nama' => $atas_nama,
            'transfer_ke' => $transfer_ke,
            'tanggal_transfer' => $tanggal_transfer,
            'jumlah_transfer' => $jumlah_transfer,
            'bukti_transfer' => $bukti_transfer,
            'sequence' => $this->sequence($order->store_id),
            'payment_number' => $this->payment_number($order->store_id),
            'payment_method' => $request->input('payment_method'),
            'total' => $order->total_price,
            'status' => 'confirmed',
        ]);

        Order::where('id', $order_id)->update([
            'payment_status' => 'confirmed'
        ]);

        return redirect()->route('payment-success', $payment->id);
    }

    public function payment_number($store_id)
    {
        $store = Store::where('id', $store_id)->first();

        $storeSequence = $store->sequence;

        $latestPayment = OrderPayment::where('store_id', $store_id)
            ->whereYear('created_at', date('Y'))
            ->whereMonth('created_at', date('m'))
            ->latest()
            ->first();

        $orderSequence = $latestPayment ? $latestPayment->sequence + 1 : 1;

        $formattedOrderSequence = str_pad($orderSequence, 5, '0', STR_PAD_LEFT);

        $orderNumber = date('Y') . date('m') . $storeSequence . $formattedOrderSequence;

        return $orderNumber;
    }

    public function sequence($store_id)
    {
        $latestPayment = OrderPayment::where('store_id', $store_id)->latest()->first();

        $orderSequence = $latestPayment ? $latestPayment->sequence + 1 : 1;

        return $orderSequence;
    }

    public function success($payment_id)
    {
        return Inertia::render('Landing/Payment/Success');
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
