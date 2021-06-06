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
        'user_id','num','discount','subtotal','total','discount_total','price','detail_id','color_code','product_id',
    ];

    public function user(){
        return $this->belongsToOne('App\Models\User');
    }

    public function products(){
        return $this->hasMany('App\Models\Product','id','product_id');
    }

}
