<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderPayment;
use App\Models\UserStore;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OrderpaymentController extends Controller
{
    public function index()
    {
        $store = UserStore::where('user_id', auth()->user()->id)->first();
        $payment = OrderPayment::where('store_id', $store->store_id)->get();
        return Inertia::render('Payment/Index', [
            'payment' => $payment
        ]);
    }

    public function change_payment_status(Request $request)
    {
        $payment = OrderPayment::where('id', $request->id)->first();
        $order = Order::where('id', $payment->order_id)->first();

        OrderPayment::where('id', $request->id)->update([
            'status' => $request->status
        ]);

        $orderpayment = OrderPayment::where('order_id', $order->id)->where('status', 'paid')->get();

        $total = 0;
        foreach ($orderpayment as $key => $value) {
            $total = $total + intval($value->jumlah_transfer);
        }

        if ($total >= $order->total_price) {
            Order::where('id', $order->id)->update(['payment_status' => 'paid']);
        }

        return response()->json(['status' => 'success']);
    }

    public function list()
    {
        $store = UserStore::where('user_id', auth()->user()->id)->first();
        $payment = OrderPayment::where('store_id', $store->store_id)->get();


        return response()->json(['status' => 'success', 'data' => $payment]);
    }

    public function destroy(Request $request)
    {
        $payment = $request->input('payment');
        foreach ($payment as $key => $value) {
            OrderPayment::where('id', $value['id'])->delete();
        }
    }
}
