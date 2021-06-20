<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAddress extends Model
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
        'user_id','name','email','address','phone','city','state','country','postal_code',
    ];

    public function user(){
        return $this->belongsToOne('App\Models\User');
    }
}
