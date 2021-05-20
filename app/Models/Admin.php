<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;
use App\Notifications\AdminMailResetPassword;

class Admin extends Authenticatable
{
	use Notifiable,  HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
    	'name',
    	'email',
    	'password',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        // 'store_payment'=>'boolean',
        // 'stripe_payment' => 'boolean',
        // 'paypal_payment'=>'boolean',
        // 'active_site'=>'boolean',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 
        'remember_token',
    ];


    /**
     * Override the mail body for reset password notification mail. 
     * 发送密码重置邮件
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify((new AdminMailResetPassword($token))->onQueue('auth-queue'));
    }
}