<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RelationStyleInfoCategoryBlogInfo extends Model {
    use HasFactory;
    protected $table        = 'relation_style_info_category_blog_info';
    protected $fillable     = [
        'style_info_id', 
        'category_blog_info_id'
    ];
    public $timestamps      = false;

    public static function insertItem($params){
        $id             = 0;
        if(!empty($params)){
            $model      = new RelationStyleInfoCategoryBlogInfo();
            foreach($params as $key => $value) $model->{$key}  = $value;
            $model->save();
            $id         = $model->id;
        }
        return $id;
    }

    public function infoStyle(){
        return $this->hasOne(\App\Models\Style::class, 'id', 'sylte_info_id');
    }

    public function infoCategoryBlog(){
        return $this->hasOne(\App\Models\CategoryBlog::class, 'id', 'category_blog_info_id');
    }

    
}
