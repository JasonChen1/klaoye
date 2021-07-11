<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     * 
     * gid products is
     * sid store id
     * @var array
     */
    protected $fillable = [
        'no','user_id','total','subtotal','discount_total','delivery_total','paid','status','payment_type',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'paid'=>'boolean',
    ];

    public function user(){
        return $this->belongsToOne('App\Models\User');
    }

    public function items(){
        return $this->hasMany('App\Models\OrderDetail');
    }

    public function shipping(){
        return $this->hasOne('App\Models\OrderShippingAddress');
    }
}
