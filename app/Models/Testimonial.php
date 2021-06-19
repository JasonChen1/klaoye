<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
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
         'product_id','url','rating','description','name','personal_des',
    ];

    public function product(){
        return $this->hasOne('App\Models\Product','id','product_id');
    }

    public function image(){
        return $this->hasOne('App\Models\ProductImage','product_id','product_id');
    }
}
   