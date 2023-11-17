<?php

namespace App\Jobs;

use Illuminate\Support\Facades\Storage;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UploadSourceAndWallpaper implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    private $filename;
    private $count;
    private $wallpaper;
    private $source;
    

    public function __construct($filename, $count, $wallpaper, $source){
        $this->filename     = $filename;
        $this->count        = $count;
        $this->wallpaper    = $wallpaper;
        $this->source       = $source;
    }

    public function handle(){
        $extensionWallpaper     = $this->wallpaper->getClientOriginalExtension();
        $extensionDefault       = config('image.extension');
        $fileNameFull           = $this->filename.'.'.$extensionWallpaper;
        $fileUrlW               = config('main.google_cloud_storage.wallpapers').$fileNameFull;
        $fileUrlS               = config('main.google_cloud_storage.sources').$fileNameFull;
        /* wallpaper sẽ được upload vào storage và cả google_cloud_storage */
        \App\Helpers\Upload::uploadWallpaper($this->wallpaper, $this->filename.'.'.$extensionDefault);
        Storage::disk('gcs')->put($fileUrlW, file_get_contents($this->wallpaper));
        /* source sẽ được tải vào google_cloud_storage */
        Storage::disk('gcs')->put($fileUrlS, file_get_contents($this->source));
    }
}
