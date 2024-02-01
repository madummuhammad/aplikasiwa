<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;
use Illuminate\Support\Str;

class Stock extends Model
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

    function item_product()
    {
        return $this->belongsTo('App\Models\ItemProduct','item_product_id','id');
    }
}
