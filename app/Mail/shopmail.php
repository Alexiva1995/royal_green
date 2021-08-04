<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class shopmail extends Mailable
{
    use Queueable, SerializesModels;
    
    

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $shopmail;
    public function __construct($shopmail)
    {
       $this->shopmail = $shopmail;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from(env('MAIL_FROM_ADDRESS'),env('MAIL_FROM_NAME'))
                    ->view('mail.shop')
                    ->subject('compra realizada')
                    ->with($this->shopmail);
    }
}
