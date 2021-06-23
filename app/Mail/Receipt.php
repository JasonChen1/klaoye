<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Receipt extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $products;
    public $totals;
    public $orderNo;
    public $address;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($products,$totals,$orderNo,$address)
    {
        $this->products = $products;
        $this->totals = $totals;
        $this->orderNo = $orderNo;
        $this->address = $address;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $address = 'klaoyeservice@gmail.com';
        $name = 'KLAOYE';
        $subject = 'KLAOYE - Receipt';
        $template = 'emails.receipt'; //邮件前端路由地址 views/emails/receipt.blade.php
        
        $this->sendMail($address,$name,$subject,$template);
    }

    /**
     *send emails by passing data to templates
     */
    public function sendMail($address,$name,$subject,$template){
        return $this->markdown($template)
        ->from($address,$name)
        ->subject($subject)
        ->with('products',$this->products);
    }
}
