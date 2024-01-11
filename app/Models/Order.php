<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory;

    protected $guarded=[];
    public $incrementing=false;
    protected $keyType='string';

    protected static function boot()
    {
        parent::boot();
        static::creating(function($model){
            $model->id=(string) Str::uuid();
        });
    }

    function product()
    {
        return $this->belongsTo('App\Models\Product','product_id','id');
    }

    function order_item()
    {
        return $this->hasMany('App\Models\OrderItem','order_id','id');
    }
}
