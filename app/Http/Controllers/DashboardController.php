<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Store;
use App\Models\UserStore;
use App\Models\Order;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        if ($user->permission['dashboard']['read'] == true) {
            return Inertia::render('Dashboard/Index', [
                'user' => $user
            ]);
        }
    }

    public function statistic()
    {
        $store = UserStore::where('user_id', auth()->user()->id)->with('store')->first();
        $order = Order::where('store_id', $store->store_id)->with('order_item.item_product.variation', 'order_item.product')->orderBy('created_at', 'DESC')->get();
        $paymentPaid = Order::where('store_id', $store->store_id)->where('payment_status', 'paid')->get();
        $paid = 0;

        foreach ($paymentPaid as $key => $value) {
            $paid = $paid + $value->total_price;
        }

        $paymentUnpaid = Order::where('store_id', $store->store_id)->where('payment_status', 'unpaid')->get();
        $unpaid = 0;

        foreach ($paymentUnpaid as $key => $value) {
            $unpaid = $unpaid + $value->total_price;
        }

        $payment = Order::where('store_id', $store->store_id)->get();
        $rasio = ($payment->count() > 0) ? ($paymentPaid->count() / $payment->count()) * 100 : 0;

        $unpaid_rasio = ($payment->count() > 0) ? ($paymentUnpaid->count() / $payment->count()) * 100 : 0;
        $total = [
            'order' => $order->count(),
            'paid' => $paid,
            'unpaid' => $unpaid_rasio,
            'rasio' => $rasio
        ];

        $data = [
            'statistic' => $total,
            'diagram' => [
                'orders' => $this->diagram_orders(),
                'earnings' => $this->diagram_earnings()
            ]
        ];

        return response()->json(['status' => 'success', 'data' => $data]);
    }

    public function diagram_orders()
    {
        $user = auth()->user();
        $store = UserStore::where('user_id', $user->id)->first();

        $orders = Order::where('store_id', $store->store_id)
            ->whereYear('created_at', now()->year)
            ->get();

        $monthlyOrderCount = array_fill(1, 12, 0);

        foreach ($orders as $order) {
            $month = (int) $order->created_at->format('m'); // Cast to integer to remove leading zero
            $monthlyOrderCount[$month]++;
        }

        $result = array_values($monthlyOrderCount);

        return $result;
    }

    public function diagram_earnings()
    {
        $user = auth()->user();
        $store = UserStore::where('user_id', $user->id)->first();

        $orders = Order::where('store_id', $store->store_id)
            ->where('payment_status', 'paid')
            ->whereYear('created_at', now()->year)
            ->get();

        $monthlyEarnings = array_fill(1, 12, 0);

        foreach ($orders as $order) {
            $month = (int) $order->created_at->format('m'); // Cast to integer to remove leading zero
            $monthlyEarnings[$month] += $order->total_price; // Assuming 'total_amount' is the field representing the order amount
        }

        $result = array_values($monthlyEarnings);

        return $result;
    }
}
