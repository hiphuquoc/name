<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class OrderMailable extends Mailable
{
    use Queueable, SerializesModels;

    public $order;
    
    public function __construct($orderInfo){
        $this->order = $orderInfo;
    }
    
    public function envelope(){
        return new Envelope(
            subject: 'Xác nhận đơn hàng thành công '.$this->order->code,
        );
    }
    
    public function content(){
        return (new Content)
                ->with('order', $this->order)
                ->view('wallpaper.email.order');
    }
}


