<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

use App\Helpers\Url;

class CategoryBlog extends Model {
    use HasFactory;
    protected $table        = 'category_blog_info';
    protected $fillable     = [
        'name', 
        'description', 
        'seo_id',
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
        $result     = self::select('category_blog_info.*')
                        ->whereHas('seo', function($query){
                            $query->where('level', 1);
                        })
                        ->with('seo')
                        ->join('seo', 'seo.id', '=', 'category_blog_info.seo_id')
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
            $result             = self::select('category_blog_info.*')
                                    ->whereHas('seo', function($query) use($idPage){
                                        $query->where('parent', $idPage);
                                    })
                                    ->with('seo')
                                    ->join('seo', 'seo.id', '=', 'category_blog_info.seo_id')
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

    // public static function getAllCategoryByTree($list){
    //     /* lấy phần tử level 1 build cây thư mục category */
    //     $data           = [];
    //     if(!empty($list&&$list->isNotEmpty())){
    //         foreach($list as $l){
    //             if($l->seo->level==1) $data[] = self::buildParentChild($l, $list);
    //         }
    //     }
    //     return $data;
    // }

    // private static function buildParentChild($item, $arrayData){
    //     $item->child                = new \Illuminate\Database\Eloquent\Collection;
    //     foreach($arrayData as $data){
    //         if($item->seo_id==$data->seo->parent) {
    //             /* check đệ quy */
    //             $flagNext           = false;
    //             foreach($arrayData as $d) {
    //                 if($data->seo_id==$d->seo->parent){
    //                     $flagNext   = true;
    //                     break;
    //                 }
    //             }
    //             /* trả dữ liệu */
    //             if($flagNext==true){
    //                 $item->child[]  = self::buildParentChild($data, $arrayData);
    //             }else {
    //                 $item->child[]  = $data;
    //             }
    //         }
    //     }
    //     return $item;
    // }

    public function blogs(){
        return $this->hasMany(\App\Models\RelationCategoryBlogInfoBlogInfo::class, 'category_blog_info_id', 'id');
    }

    public function seo(){
        return $this->hasOne(\App\Models\Seo::class, 'id', 'seo_id');
    }

    public function files(){
        return $this->hasMany(\App\Models\SystemFile::class, 'attachment_id', 'id');
    }

}