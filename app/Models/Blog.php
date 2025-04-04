<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Blog extends Model {
    use HasFactory;
    protected $table        = 'blog_info';
    protected $fillable     = [
        'seo_id', 
        'outstanding',
        'status',
        'viewed',
        'shared',
        'notes',
    ];
    public $timestamps      = false;

    public static function getList($params = null){
        $result     = self::select('*')
                        /* tìm theo tên */
                        ->when(!empty($params['search_name']), function($query) use($params){
                            $searchName = $params['search_name'];
                            $query->whereHas('seo', function($subQuery) use($searchName){
                                $subQuery->where('title', 'like', '%'.$searchName.'%');
                            });
                        })
                        /* tìm theo danh mục */
                        ->when(!empty($params['search_category']), function($query) use($params){
                            $query->whereHas('categories.infoCategory', function($q) use ($params){
                                $q->where('id', $params['search_category']);
                            });
                        })
                        ->with('seo')
                        ->orderBy('id', 'DESC')
                        ->paginate($params['paginate']);
        return $result;
    }

    public static function insertItem($params){
        $id             = 0;
        if(!empty($params)){
            $model      = new Blog();
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
        return $this->hasMany(\App\Models\RelationSeoBlogInfo::class, 'blog_info_id', 'id');
    }

    public function categories(){
        return $this->hasMany(\App\Models\RelationCategoryBlogBlogInfo::class, 'blog_info_id', 'id');
    }

}
