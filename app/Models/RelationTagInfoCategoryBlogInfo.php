<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RelationTagInfoCategoryBlogInfo extends Model {
    use HasFactory;
    protected $table        = 'relation_tag_info_category_blog_info';
    protected $fillable     = [
        'tag_info_id', 
        'category_blog_info_id'
    ];
    public $timestamps      = false;

    public static function insertItem($params){
        $id             = 0;
        if(!empty($params)){
            $model      = new RelationTagInfoCategoryBlogInfo();
            foreach($params as $key => $value) $model->{$key}  = $value;
            $model->save();
            $id         = $model->id;
        }
        return $id;
    }

    public function infoTag(){
        return $this->hasOne(\App\Models\Tag::class, 'id', 'tag_info_id');
    }

    public function infoCategoryBlog(){
        return $this->hasOne(\App\Models\CategoryBlog::class, 'id', 'category_blog_info_id');
    }

    
}
