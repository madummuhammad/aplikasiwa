<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
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

    function variation()
    {
        return $this->hasMany('App\Models\Variation','product_id','id');
    }
    
    function item_product()
    {
        return $this->hasMany('App\Models\ItemProduct','product_id','id');
    }

    function checkout()
    {
        return $this->hasMany('App\Models\Checkout','product_id','id');
    }
}
