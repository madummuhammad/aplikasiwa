<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Store;
use App\Models\Product;
use App\Models\Checkout;
use App\Models\ItemProduct;
use App\Models\Order;
use App\Models\Stock;
use App\Models\OrderItem;
use Inertia\Inertia;

class LandingCheckoutController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($url)
    {
        $username = null;
        if (env('APP_ENV') == 'local') {
            $username = 'madum';
        } else {
            $request = Request::capture();
            $url = $request->url();
            $host = parse_url($url, PHP_URL_HOST);
            $parts = explode('.', $host);
            $username = $parts[0];
        }

        $store = Store::where('username', $username)->first();
        $product = Product::where('url', $url)->where('store_id', $store->id)->with('item_product.variation')->first();
        $checkout_id = request('checkout_id');
        if ($checkout_id) {
            $checkout = Checkout::where('product_id', $product->id)->where('id', $checkout_id)->orderBy('created_at', 'DESC')->first();
        } else {
            $checkout = Checkout::where('product_id', $product->id)->orderBy('created_at', 'DESC')->first();
        }

        return Inertia::render('Landing/Checkout/Index', [
            'store' => $store,
            'checkout' => $checkout,
            'product' => $product
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $url)
    {
        $product = $request->input('product');
        // Rotator
        $rotatorData = $product['rotatorData'];
        $team_id = null;
        if ($product['global_cs_rotator_status'] == true and $rotatorData) {

            $role = $rotatorData['role'];
            $teamIds = $rotatorData['team'];
            $countAllOrder = Order::where('store_id', $product['store_id'])->whereIn('team_id', $teamIds)->count() ?: 0;
            $countTeamOrder = [];

            $rulePercentage = 100 / count($teamIds);

            for ($i = 0; $i < count($teamIds); $i++) {
                $countTeamOrder[$i] = Order::where('store_id', $product['store_id'])->where('team_id', $teamIds[$i])->count() ?: 0;
                $percentage[$i] = ($countAllOrder !== 0) ? ($countTeamOrder[$i] / $countAllOrder * 100) : 0;
                if ($percentage[$i] <= $rulePercentage) {
                    $team_id = $teamIds[$i];
                }
            }
        }

        $fields = $request->input('fields');
        $item_product = $request->input('item_product');
        $payment_method = $request->input('payment_method');
        $bump_status = $request->input('bump_status');
        $request->input('total_price');

        $product_bump = null;
        if ($bump_status == true) {
            $product_bump = $product['product_bump'];
        }


        $order = Order::create([
            'product_id' => $product['id'],
            'order_number' => $this->orderNumber($product['store_id']),
            'sequence' => $this->sequence($product['store_id']),
            'fields' => $fields,
            'product_bump' => $product_bump,
            'payment_method' => $payment_method,
            'store_id' => $product['store_id'],
            'total_price' => intval($request->input('total_price')) + intval($request->input('shipping')['shipping_cost']),
            'team_id' => $team_id,
            'shipping' => $request->input('shipping'),
            'status' => 'pending', //process, pending, complete, cancel, refund
            'payment_status' => 'unpaid' //Unpaid,Confirmed,Paid
        ]);


        if (isset($item_product[0])) {
            foreach ($item_product as $key => $value) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product['id'],
                    'store_id' => $product['store_id'],
                    'item_product_id' => $value['id'],
                    'qty' => $value['qty'],
                    'normal_price' => intval($value['normal_price']),
                    'price_sale' => 0,
                    'hpp' => intval($value['hpp']),
                    'weight' => intval($value['weight']),
                    'total_price' => intval($value['normal_price']) * $value['qty']
                ]);

                Stock::create([
                    'item_product_id' => $value['id'],
                    'stock_type' => 'Deduction',
                    'qty' => $value['qty'],
                    'note' => 'Deduction by Customer',
                    'stock' => intval($value['stock'] - intval($value['qty'])),
                    'by' => null
                ]);

                ItemProduct::where('id', $value['id'])->update([
                    'stock' => intval($value['stock'] - intval($value['qty']))
                ]);
            }
        } else {
            $price_sale = 0;

            if ($product['price_sale_status'] == true) {
                $price_sale = intval($product['price_sale']);
            }


            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $product['id'],
                'store_id' => $product['store_id'],
                'item_product_id' => $item_product['id'],
                'qty' => $item_product['qty'],
                'normal_price' => intval($item_product['normal_price']),
                'price_sale' => $price_sale,
                'hpp' => intval($item_product['hpp']),
                'weight' => intval($item_product['weight']),
                'total_price' => intval($item_product['normal_price']) * $item_product['qty']
            ]);

            Stock::create([
                'item_product_id' => $item_product['id'],
                'stock_type' => 'Deduction',
                'qty' => 1,
                'note' => 'Deduction by Customer',
                'stock' => intval($item_product['stock']) - 1,
                'by' => null
            ]);

            ItemProduct::where('id', $item_product['id'])->update([
                'stock' => intval($item_product) - 1
            ]);
        }

        return redirect()->route('success', ['url' => $product['url'], 'id' => $order->id]);
    }

    public function orderNumber($store_id)
    {
        $store = Store::where('id', $store_id)->first();

        $storeSequence = $store->sequence;

        $latestOrder = Order::where('store_id', $store_id)
            ->whereYear('created_at', date('Y'))
            ->whereMonth('created_at', date('m'))
            ->latest()
            ->first();

        $orderSequence = $latestOrder ? $latestOrder->sequence + 1 : 1;

        $formattedOrderSequence = str_pad($orderSequence, 5, '0', STR_PAD_LEFT);

        $orderNumber = date('Y') . date('m') . $storeSequence . $formattedOrderSequence;

        return $orderNumber;
    }

    public function sequence($store_id)
    {
        $latestOrder = Order::where('store_id', $store_id)->latest()->first();

        $orderSequence = $latestOrder ? $latestOrder->sequence + 1 : 1;

        return $orderSequence;
    }


    public function success($url, $id)
    {
        $order = Order::where('id', $id)->first();
        $checkout = Checkout::where('product_id', $order->product_id)->orderBy('created_at', 'DESC')->first();
        $product = Product::where('id', $order->product_id)->with('item_product.variation')->first();
        return Inertia::render('Landing/Success/Index', [
            'checkout' => $checkout,
            'product' => $product,
            'order' => $order,
            'success_page' => $checkout->success_page
        ]);
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
