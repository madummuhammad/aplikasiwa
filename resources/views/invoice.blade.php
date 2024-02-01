@php
function formatDate($timestamp) {
    $date = new DateTime($timestamp);
    $formattedDate = $date->format('M d, Y h:i A');

    return $formattedDate;
}
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice</title>
</head>
<style>

    .w-100{
        width:100%
    }

    .text-end{
        text-align:right;
    }

    .d-flex{
        display:flex;
    }

    .justify-content-end{
        justify-content:right;
    }

    .justify-content-between{
        justify-content:space-between;
    }

    .m-0{
        margin:0px;
    }

    .p-0{
        padding:0px;
    }

    .fw-bold{
        font-weight:bold;
    }

    .label{
        padding-left:10px;
        padding-right:10px;
        width:fit-content;
        padding-top:5px;
        padding-bottom:5px;
    }

    .float-right{
        float:right;
    }

    .paid{
        background:#67B173;
        color:white;
    }

    .mb-10px{
        margin-bottom:10px;
    }
    .mb-50px{
        margin-bottom:50px;
    }


    .fields tr td{
        white-space: nowrap;
    }

    .items{
        width:100%;
    }

    .items tr td{
        padding:20px 10px;
        border-top:1px solid black;
        border-bottom:1px solid black;
    }

    .total{
        width:100%;
        margin-top:20px;
    }

    .total tr td{
        padding:20px 10px;
        border-top:2px solid black;
        border-bottom:2px solid black;
    }

</style>
<body>
    <table class="w-100 mb-10px">
        <tr>
            <td>
                {{$store->store->name}}
            </td>
            <td class="text-end">
                <h3 class="m-0">INVOICE #{{$order->order_number}}</h3>
                <div>
                    <span style="text-transform:uppercase">
                        ({{$order->status}}) 
                    </span>
                    Order Date: {{formatDate($order->created_at)}}
                </div>
            </td>
        </tr>
    </table>
    <div class="mb-50px">
        @if($order->payment_status=='paid')
        <span class="label float-right paid">PAID</span >
        @endif
        @if($order->payment_status=='unpaid')
        <span class="label float-right paid">UNPAID</span >
        @endif
        @if($order->payment_status=='confirmed')
        <span class="label float-right paid">CONFIRMED</span >
        @endif
    </div>
<table class="mb-10px fields">
        @foreach($order->fields as $value)
        <tr>
            <td class="fw-bold">
                {{$value['label']}}
            </td>
            @if($value['field']=='Input' && $value['name']!=='city_or_subdistrict')
            <td>
                : {{$value['value']}}
            </td>
            @endif
            @if($value['field']=='Select' && $value['name']=='province')
            <td>
                : {{$value['value']['province']}}
            </td>
            @endif
            @if($value['field']=='Select' && $value['name']=='city')
            <td>
                : {{$value['value']['city_name']}}
            </td>
            @endif
            @if($value['field']=='Select' && $value['name']=='subdistrict')
            <td>
                : {{$value['value']['subdistrict_name']}}
            </td>
            @endif
        </tr>
        @endforeach
    </table>
    <h4>Items</h4>

    <table class="items">
        @foreach($order->order_item as $value)
        <tr>
            <td>
            <span>
                {{$value->product->name}}
                @if($value->item_product->variation)
                <span>
                    <span>
                        - {{ $value->item_product->variation->variation_name }}
                    </span>
                    @if($value->item_product->variation->child_variation_name)
                    <span>
                        - {{ $value->item_product->variation->child_variation_name }}
                    </span>
                    @endif
                </span>
                @endif
            </span>
            @if($value->qty==0)
            <span> (1 x)</span>
            @else
            <span> {{ $value->qty }} x</span>
            @endif
            </td>
            <td class="text-end">
                @if($value->total_price==0)
            <span>
                @if($value->product->price_sale_status==true && $value->price_sale!==0)
                <span>
                    Rp {{number_format($value->price_sale)}}
                </span>
                @else
                <span>Rp {{number_format($value->normal_price) }}</span>
                @endif
            </span>
            @else
            <span>Rp {{ number_format($value->total_price) }}</span>
            @endif
        </td>
    </tr>
    @endforeach
    </table>
    <table class="total">
        <tr>
            <td>
                Total Biaya
            </td>
            <td class="text-end">
                Rp {{number_format($order->total_price)}}
            </td>
        </tr>
    </table>
</body>
</html>