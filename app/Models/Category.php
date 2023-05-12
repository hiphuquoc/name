<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model {
    use HasFactory;
    protected $table        = 'category_info';
    protected $fillable     = [
        'seo_id',
        'name', 
        'description',
        'en_seo_id',
        'en_name',
        'en_description',
        'icon'
    ];
    public $timestamps = true;

    public static function insertItem($params){
        $id             = 0;
        if(!empty($params)){
            $model      = new Category();
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

    public static function getArrayIdCategoryRelatedByIdCategory($infoCategory, $variable){
        $idPage             = $infoCategory->seo->id;
        $arrayChild         = self::select('*')
                                ->whereHas('seo', function($query) use($idPage){
                                    $query->where('parent', $idPage);
                                })
                                ->with('seo')
                                ->get();
        /* kiểm tra đã là category cha chưa => chưa thì lấy id category cha gộp vào mảng */
        if(!empty($arrayChild)&&$arrayChild->isNotEmpty()){
            foreach($arrayChild as $child){
                $variable[]     = $child->id;
                self::getArrayIdCategoryRelatedByIdCategory($child, $variable);
            }
        }
        return $variable;
    }

    public static function getTreeCategory(){
        $result     = self::select('category_info.*')
                        ->whereHas('seo', function($query){
                            $query->where('level', 1);
                        })
                        ->with('seo', 'en_seo', 'products')
                        ->join('seo', 'seo.id', '=', 'category_info.seo_id')
                        ->orderBy('seo.ordering', 'DESC')
                        ->get();
        for($i=0;$i<$result->count();++$i){
            $result[$i]->childs  = self::getTreeCategoryByInfoCategory($result[$i]);
        }
        return $result;
    }

    public static function getTreeCategoryByInfoCategory($infoCategory){
        $result                 = new \Illuminate\Database\Eloquent\Collection;
        if(!empty($infoCategory)){
            $idPage             = $infoCategory->seo->id;
            $result             = self::select('category_info.*')
                                    ->whereHas('seo', function($query) use($idPage){
                                        $query->where('parent', $idPage);
                                    })
                                    ->with('seo', 'en_seo', 'products')
                                    ->join('seo', 'seo.id', '=', 'category_info.seo_id')
                                    ->orderBy('seo.ordering', 'DESC')
                                    ->get();
            if($result->isNotEmpty()){
                for($i=0;$i<$result->count();++$i){
                    $result[$i]->childs = self::getTreeCategoryByInfoCategory($result[$i]);
                }
            }
        }
        return $result;
    }

    public function seo() {
        return $this->hasOne(\App\Models\Seo::class, 'id', 'seo_id');
    }

    public function en_seo() {
        return $this->hasOne(\App\Models\EnSeo::class, 'id', 'en_seo_id');
    }

    public function files(){
        return $this->hasMany(\App\Models\SystemFile::class, 'attachment_id', 'id');
    }

    public function products(){
        return $this->hasMany(\App\Models\RelationCategoryProduct::class, 'category_info_id', 'id');
    }

    public function categoryBlogs(){
        return $this->hasMany(\App\Models\RelationCategoryInfoCategoryBlogInfo::class, 'category_info_id', 'id');
    }
}
