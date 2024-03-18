<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model {
    use HasFactory;
    protected $table        = 'tag_info';
    protected $fillable     = [
        'seo_id',
        'icon',
        'flag_show',
    ];
    public $timestamps = true;

    public static function insertItem($params){
        $id             = 0;
        if(!empty($params)){
            $model      = new Tag();
            foreach($params as $key => $value) $model->{$key}  = $value;
            $model->save();
            $id         = $model->id;
        }
        return $id;
    }

    public static function updateItem($id, $params){
        $flag           = false;
        if(!empty($id)&&!empty($params)){
            $model      = self::find($id);
            foreach($params as $key => $value) $model->{$key}  = $value;
            $flag       = $model->update();
        }
        return $flag;
    }

    public function seo() {
        return $this->hasOne(\App\Models\Seo::class, 'id', 'seo_id');
    }

    public function seos() {
        return $this->hasMany(\App\Models\RelationSeoTagInfo::class, 'tag_info_id', 'id');
    }

    public function files(){
        return $this->hasMany(\App\Models\SystemFile::class, 'attachment_id', 'id');
    }

    public function products(){
        return $this->hasMany(\App\Models\RelationTagProduct::class, 'tag_info_id', 'id');
    }

    public function freeWallpapers(){
        return $this->hasMany(\App\Models\RelationTagInfoOrther::class, 'tag_info_id', 'id')->where('reference_type', 'free_wallpaper_info');
    }

    public function blogs(){
        return $this->hasMany(\App\Models\RelationTagInfoCategoryBlogInfo::class, 'tag_info_id', 'id');
    }
}
