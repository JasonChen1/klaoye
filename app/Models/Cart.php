<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
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
        'user_id','total','subtotal','discount_total','delivery_total',
    ];

    public function user(){
        return $this->belongsToOne('App\Models\User');
    }

    public function items(){
        return $this->hasMany('App\Models\CartItem');
    }

}
