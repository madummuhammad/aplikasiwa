<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
use MongoDB\Laravel\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Auth\User as Authenticatable;
use Illuminate\Support\Str;
// use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasFactory;
    use SoftDeletes;

    protected $connection = 'mongodb';
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

    function user_store()
    {
        return $this->belongsTo('App\Models\UserStore','id','user_id');
    }
}
