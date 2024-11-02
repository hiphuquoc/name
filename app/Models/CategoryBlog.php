<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Database\Eloquent\Model;

class CategoryBlog extends BaseCategory {
    use HasFactory;
    protected $table        = 'category_blog';
    protected $fillable     = [
        'seo_id', 
        'flag_show',
    ];
    public $timestamps      = false;

    public static function insertItem($params){
        $id             = 0;
        if(!empty($params)){
            $model      = new CategoryBlog();
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

    public function blogs(){
        return $this->hasMany(\App\Models\RelationCategoryBlogBlogInfo::class, 'category_blog_id', 'id');
    }

    public function seo() {
        return $this->hasOne(\App\Models\Seo::class, 'id', 'seo_id');
    }

    public function seos() {
        return $this->hasMany(\App\Models\RelationSeoCategoryBlog::class, 'category_blog_id', 'id');
    }

    public function files(){
        return $this->hasMany(\App\Models\SystemFile::class, 'attachment_id', 'id')->where('relation_table', 'category_blog')->inRandomOrder();
    }

}