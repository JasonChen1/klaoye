<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
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
        'num','discount','subtotal','total','discount_total','price','detail_id','color_code','product_id','cart_id','discounted',
    ];

    public function cart(){
        return $this->belongsToOne('App\Models\Cart');
    }

    public function product(){
        return $this->hasOne('App\Models\Product','id','product_id');
    }
}
