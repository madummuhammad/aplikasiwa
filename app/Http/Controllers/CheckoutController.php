<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\UserStore;
use App\Models\Product;
use App\Models\Checkout;

class CheckoutController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($id)
    {
        $user=auth()->user();
        $store=UserStore::where('user_id',$user->id)->with('store')->first();
        $product=Product::where('id',$id)->with('item_product.variation')->first();
        return Inertia::render('Product/Checkout/Create',[
            'store'=>$store,
            'product'=>$product
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request,$id)
    {
        $store=UserStore::where('user_id',auth()->user()->id)->first();
        $checkoutData = $request->input('checkoutData');
        $successData = $request->input('successData');

        $product=Product::where('id',$id)->first();

        Checkout::create([
            'product_id'=>$product->id,
            'store_id'=>$store->store_id,
            'name'=>$checkoutData['name'],
            'template'=>$checkoutData['template'],
            'sectionTitle'=>$checkoutData['sectionTitle'],
            'header'=>$checkoutData['header'],
            'productImageStatus'=>$checkoutData['productImageStatus'],
            'buyButtonText'=>$checkoutData['buyButtonText'],
            'buyButtonText'=>$checkoutData['buyButtonText'],
            'buyButtonBackground'=>$checkoutData['buyButtonBackground'],
            'buyButtonTextColor'=>$checkoutData['buyButtonTextColor'],
            'productDescription'=>$checkoutData['productDescription'],
            'kuponStatus'=>$checkoutData['kuponStatus'],
            'dropship_status'=>$checkoutData['dropship_status'],
            'points'=>$checkoutData['points'],
            'summaryStatus'=>$checkoutData['summaryStatus'],
            'testimonials'=>$checkoutData['testimonials'],
            'fields'=>$checkoutData['fields'],
            'olshop'=>$checkoutData['olshop'],
            'view'=>0,
            'success_page'=>$successData,
        ]);

        return redirect()->route('product.show',$product->id);
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
        $user=auth()->user();
        $store=UserStore::where('user_id',$user->id)->with('store')->first();
        $checkout=Checkout::where('id',$id)->first();
        $product=Product::where('id',$checkout->product_id)->with('item_product.variation')->first();
        return Inertia::render('Product/Checkout/Edit',[
            'store'=>$store,
            'checkout'=>$checkout,
            'product'=>$product
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $store=UserStore::where('user_id',auth()->user()->id)->first();
        $checkoutData = $request->input('checkoutData');

        $product=Product::where('id',$checkoutData['product_id'])->first();

        Checkout::where('id',$id)->update([
            'product_id'=>$product->id,
            'store_id'=>$store->store_id,
            'name'=>$checkoutData['name'],
            'template'=>$checkoutData['template'],
            'sectionTitle'=>$checkoutData['sectionTitle'],
            'header'=>$checkoutData['header'],
            'productImageStatus'=>$checkoutData['productImageStatus'],
            'buyButtonText'=>$checkoutData['buyButtonText'],
            'buyButtonText'=>$checkoutData['buyButtonText'],
            'buyButtonBackground'=>$checkoutData['buyButtonBackground'],
            'buyButtonTextColor'=>$checkoutData['buyButtonTextColor'],
            'productDescription'=>$checkoutData['productDescription'],
            'kuponStatus'=>$checkoutData['kuponStatus'],
            'dropship_status'=>$checkoutData['dropship_status'],
            'points'=>$checkoutData['points'],
            'summaryStatus'=>$checkoutData['summaryStatus'],
            'testimonials'=>$checkoutData['testimonials'],
            'fields'=>$checkoutData['fields'],
            'olshop'=>$checkoutData['olshop'],
            'success_page'=>$checkoutData['success_page'],
        ]);

        return redirect()->route('product.show',$product->id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
