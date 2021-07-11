<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class Enquiry extends Notification implements ShouldQueue
{
    use Queueable;

    public $enquiry;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($enquiry)
    {
        $this->enquiry = $enquiry;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject('New Enquiry')
                    ->line('Please check new enquiry below.')
                    ->line('Name: '.$this->enquiry->name)
                    ->line('Email: '.$this->enquiry->email)
                    ->line('Phone: '.$this->enquiry->phone)
                    ->line('Product: '.$this->enquiry->product)
                    ->line('Message: '.$this->enquiry->message)
                    ->action('Go to dashboard', url('/admin'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
