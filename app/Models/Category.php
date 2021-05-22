<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    public $timestamps = false;


    /**
     * The attributes that are mass assignable.
     * 
     * gid products is
     * sid store id
     * @var array
     */
    protected $fillable = [
    	'name',
    ];

    public function products(){
        return $this->hasMany('App\Models\Product');
    }

    public function subcategories(){
        return $this->hasMany('App\Models\SubCategory');
    }
}
