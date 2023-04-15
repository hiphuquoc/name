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
    
    public function __construct($orderInfo){
        $this->order = $orderInfo;
    }
    
    public function envelope(){
        return new Envelope(
            subject: 'Order Mailable',
        );
    }
    
    public function content(){
        return new Content(
            view: 'wallpaper.email.order',
        );
    }

    public function attachments(){
        $attachments            = [];
        // foreach($this->order->products as $product){
        //     foreach($product->infoPrice->sources as $source){
        //         // $attachments[] = ['path' => Storage::disk('google')->path($source->file_path)];
        //         $attachments[] = Storage::disk('google')->url($source->file_path);
        //     }
        // }
        return $attachments;
    }
}

// use Illuminate\Bus\Queueable;
// use Illuminate\Mail\Mailable;
// use Illuminate\Queue\SerializesModels;
// use Illuminate\Contracts\Queue\ShouldQueue;
// use Illuminate\Support\Facades\Storage;

// class OrderMailable extends Mailable implements ShouldQueue
// {
//     use Queueable, SerializesModels;

//     protected $order;

//     /**
//      * Create a new message instance.
//      *
//      * @return void
//      */
//     public function __construct($order)
//     {
//         $this->order = $order;
//     }

//     /**
//      * Build the message.
//      *
//      * @return $this
//      */
//     public function build()
//     {
//         $attachments = [];

//         foreach ($this->order->products as $product) {
//             foreach ($product->infoPrice->sources as $source) {
//                 $path = Storage::disk('google')->url($source->file_path);
//                 $attachments[] = $this->getAttachment($path, $source->file_name);
//             }
//         }

//         return $this->view('wallpaper.email.order')
//             ->with(['order' => $this->order])
//             ->subject('Order Mailable')
//             ->attach($attachments)
//             ->withSwiftMessage(function ($message) use ($attachments) {
//                 foreach ($attachments as $attachment) {
//                     $message->embed($attachment['filePath'], $attachment['options']);
//                     // Remove the temporary file
//                     unlink($attachment['filePath']);
//                 }
//             });
//     }

//     /**
//      * Get attachment for the message.
//      *
//      * @param string $filePath
//      * @param string $fileName
//      * @return array
//      */
//     protected function getAttachment($filePath, $fileName)
//     {
//         $fileData = file_get_contents($filePath);

//         $tempFilePath = tempnam(sys_get_temp_dir(), $fileName);
//         file_put_contents($tempFilePath, $fileData);

//         return [
//             'filePath' => $tempFilePath,
//             'options' => [
//                 'fileName' => $fileName
//             ]
//         ];
//     }
// }



