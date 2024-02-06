<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Stock;
use App\Models\ItemProduct;
use App\Models\Product;
use App\Models\UserStore;

class InventoryController extends Controller
{
    public function index()
    {
        $user = UserStore::where('user_id', auth()->user()->id)->first();
        $product = Product::where('store_id', $user->store_id)->with('item_product.variation')->orderBy('created_at', 'DESC')->first();
        $item_product = ItemProduct::where('product_id', $product->id)->pluck('id');
        $stock = Stock::whereIn('item_product_id', $item_product)->with('item_product.product', 'item_product.variation')->orderBy('created_at', 'DESC')->get();

        $allProduct = Product::where('store_id', $user->store_id)->orderBy('created_at', 'DESC')->get();

        return Inertia::render('Inventory/Index', ['stock' => $stock, 'allProduct' => $allProduct, 'product' => $product]);
    }

    public function list($id)
    {
        $user = UserStore::where('user_id', auth()->user()->id)->first();
        $product = Product::where('store_id', $user->store_id)->with('item_product.variation')->orderBy('created_at', 'DESC')->where('id', $id)->first();
        $item_product = ItemProduct::where('product_id', $product->id)->pluck('id');

        $item_product_id = request('item_product_id');

        if ($product->many_variation_status == false) {
            $item_product_id = null;
        }

        if ($item_product_id == null OR $item_product_id == 'null') {
            $stock = Stock::whereIn('item_product_id', $item_product)->with('item_product.product', 'item_product.variation')->orderBy('created_at', 'DESC')->get();
        } else {
            $stock = Stock::where('item_product_id', $item_product_id)->with('item_product.product', 'item_product.variation')->orderBy('created_at', 'DESC')->get();
        }

        // return $stock;

        return response()->json(['status' => 'success', 'data' => $stock, 'product' => $product]);
    }

    public function add(Request $request)
    {
        $request->validate([
            'stock' => 'required|numeric|min:1',
            'item_product_id' => 'required'
        ]);
        $item_product = ItemProduct::where('id', $request->item_product_id)->first();
        Stock::create([
            'item_product_id' => $request->item_product_id,
            'stock_type' => 'Addition',
            'qty' => $request->stock,
            'note' => $request->note,
            'stock' => intval($item_product->stock) + $request->stock,
            'by' => auth()->user()->name
        ]);

        ItemProduct::where('id', $request->item_product_id)->update([
            'stock' => intval($item_product->stock) + $request->stock
        ]);

        return response()->json(['status' => 'success']);
    }

    public function reduce(Request $request)
    {
        $request->validate([
            'stock' => 'required|numeric|min:1',
            'item_product_id' => 'required'
        ]);
        $item_product = ItemProduct::where('id', $request->item_product_id)->first();
        Stock::create([
            'item_product_id' => $request->item_product_id,
            'stock_type' => 'Deduction',
            'qty' => $request->stock,
            'note' => $request->note,
            'stock' => intval($item_product->stock) - $request->stock,
            'by' => auth()->user()->name
        ]);

        ItemProduct::where('id', $request->item_product_id)->update([
            'stock' => intval($item_product->stock) - $request->stock
        ]);

        return response()->json(['status' => 'success']);
    }

    public function adjustment(Request $request)
    {
        $request->validate([
            'stock' => 'required|numeric|min:1',
            'item_product_id' => 'required'
        ]);
        $item_product = ItemProduct::where('id', $request->item_product_id)->first();

        if ($item_product->stock !== $request->stock) {
            if ($item_product->stock < $request->stock) {
                $stock_type = 'Addition';
                $qty = $request->stock - $item_product->stock;
            } else {
                $stock_type = 'Deduction';
                $qty = $item_product->stock - $request->stock;
            }

            Stock::create([
                'item_product_id' => $request->item_product_id,
                'stock_type' => $stock_type,
                'qty' => $qty,
                'note' => $request->note,
                'stock' => $request->stock,
                'by' => auth()->user()->name
            ]);

            ItemProduct::where('id', $request->item_product_id)->update([
                'stock' => $request->stock
            ]);
        }

        return response()->json(['status' => 'success']);
    }
}
