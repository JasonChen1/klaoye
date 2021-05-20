<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
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
    	'code','name','price','status','description','size','stock','discount',
    ];

    public function details(){
        return $this->hasMany('App\Models\ProductDetail');
    }

    public function images(){
        return $this->hasMany('App\Models\ProductImage');
    }
}
