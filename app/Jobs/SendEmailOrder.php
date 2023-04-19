<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\Mail\OrderMailable;
use Illuminate\Support\Facades\Mail;

class SendEmailOrder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    private $order;

    public function __construct($orderInfo){
        $this->order = $orderInfo;
    }

    public function handle(){
        if(!empty($this->order->email)) Mail::to($this->order->email)->send(new OrderMailable($this->order));
    }
}
