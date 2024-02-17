<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FreeWallpaperContent extends Model {
    use HasFactory;
    protected $table        = 'free_wallpaper_content';
    protected $fillable     = [
        'free_wallpaper_info_id',
        'name', 
        'content',
        'en_name',
        'en_content',
        'ordering'
    ];
    public $timestamps = false;

    public static function insertItem($params){
        $id             = 0;
        if(!empty($params)){
            $model      = new FreeWallpaperContent();
            foreach($params as $key => $value) $model->{$key}  = $value;
            $model->save();
            $id         = $model->id;
        }
        return $id;
    }

    // public function customer() {
    //     return $this->hasOne(\App\Models\Customer::class, 'id', 'customer_info_id');
    // }
}
