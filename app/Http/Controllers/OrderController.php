<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\UserStore;
use App\Models\Checkout;
use App\Models\Product;
use App\Models\OrderPayment;
use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;

class OrderController extends Controller
{
    public function index()
    {
        $user=auth()->user();
        if($user->permission['order']['read']==true){
        $store=UserStore::where('user_id',auth()->user()->id)->with('store','user')->first();

        if($store->is_owner==true){
            $order=Order::where('store_id',$store->store_id)->with('order_item.item_product.variation','order_item.product')->orderBy('created_at','DESC')->get();
        } else {
            $order=Order::where('store_id',$store->store_id)->with('order_item.item_product.variation','order_item.product')->orderBy('created_at','DESC')->where('team_id',$store->id)->get();
        }
        
        if($store->is_owner==true){
            $paymentPaid=Order::where('store_id',$store->store_id)->where('payment_status','paid')->get();
        } else {
            $paymentPaid=Order::where('store_id',$store->store_id)->where('payment_status','paid')->where('team_id',$store->id)->get();
        }
        $paid=0;

        foreach ($paymentPaid as $key => $value) {
            $paid=$paid+$value->total_price;
        }

        if($store->is_owner==true){
            $paymentUnpaid=Order::where('store_id',$store->store_id)->where('payment_status','unpaid')->get();
        } else {
            $paymentUnpaid=Order::where('store_id',$store->store_id)->where('payment_status','unpaid')->where('team_id',$store->id)->get();
        }
        $unpaid=0;
        
        foreach ($paymentUnpaid as $key => $value) {
            $unpaid=$unpaid+$value->total_price;
        }
        if($store->is_owner==true){
            $payment=Order::where('store_id',$store->store_id)->get();
        } else {
            $payment=Order::where('store_id',$store->store_id)->where('team_id',$store->id)->get();
        }
        
        $rasio = ($payment->count() > 0) ? ($paymentPaid->count() / $payment->count()) * 100 : 0;

        $total=[
            'order'=>$order->count(),
            'paid'=>$paid,
            'unpaid'=>$unpaid,
            'rasio'=>$rasio
        ];

        $params=request();

        $product=Product::where('store_id',$store->store_id)->get();
        $checkout=Checkout::where('store_id',$store->store_id)->get();
        return Inertia::render('Order/Index',[
            'orders'=>$order,
            'store'=>$store,
            'total'=>$total,
            'params'=>$params,
            'user'=>$user,
            'product'=>$product,
            'checkout'=>$checkout
        ]);
    }
    }

    public function list()
    {
        $store=UserStore::where('user_id',auth()->user()->id)->first();
        $order=Order::where('store_id',$store->store_id)->get();

        $paymentPaid=Order::where('store_id',$store->store_id)->where('payment_status','paid')->get();
        $paid=0;

        foreach ($paymentPaid as $key => $value) {
            $paid=$paid+$value->total_price;
        }

        $paymentUnpaid=Order::where('store_id',$store->store_id)->where('payment_status','unpaid')->get();
        $unpaid=0;
        
        foreach ($paymentUnpaid as $key => $value) {
            $unpaid=$unpaid+$value->total_price;
        }
        
        $payment=Order::where('store_id',$store->store_id)->get();
        $rasio = ($payment->count() > 0) ? ($paymentPaid->count() / $payment->count()) * 100 : 0;

        $total=[
            'order'=>$order->count(),
            'paid'=>$paid,
            'unpaid'=>$unpaid,
            'rasio'=>$rasio
        ];


        return response()->json(['status'=>'success','data'=>$order,'total'=>$total]);
    }

    public function change_order_status(Request $request)
    {
        Order::where('id',$request->order_id)->update([
            'status'=>$request->status
        ]);

        return response()->json(['status'=>'success']);
    }

    public function change_payment_status(Request $request)
    {
        Order::where('id',$request->order_id)->update([
            'payment_status'=>$request->status
        ]);

        OrderPayment::where('order_id',$request->order_id)->update([
            'status'=>$request->status
        ]);

        return response()->json(['status'=>'success']);
    }

    public function invoice($id)
    {
        $store=UserStore::where('user_id',auth()->user()->id)->with('store')->first();
        $order=Order::where('id',$id)->with('order_item.item_product.variation','order_item.product')->first();
        $checkout=Checkout::where('product_id',$order->product_id)->orderBy('created_at','DESC')->first();

        $data=[
            'order'=>$order,
            'checkout'=>$checkout,
            'store'=>$store
        ];
        $pdf = Pdf::loadView('invoice',$data);
        return $pdf->stream('invoice-'.$order->order_number.'.pdf');

        // return view('invoice',$data);
    }

    public function show($id)
    {
        $order=Order::where('id',$id)->with('product','order_item.product')->first();
        $checkout=Checkout::where('product_id',$order->product_id)->orderBy('created_at','DESC')->first();
        return Inertia::render('Order/Show',[
            'order'=>$order,
            'checkout'=>$checkout
        ]);
    }
}
