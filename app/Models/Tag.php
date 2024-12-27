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

    public static function getList($params = null){
        $result     = self::select('*')
                        ->whereHas('seo', function($query){
                            
                        })
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
                        ->orderBy('created_at', 'DESC')
                        ->with(['files' => function($query){
                            $query->where('relation_table', 'tag_info');
                        }])
                        ->with('seo', 'seos', 'categories')
                        ->paginate($params['paginate']);
        return $result;
    }

    public static function listLanguageNotExists($params = null){
        $countLanguage  = count(config('language'));
        $result         = self::select('*')
                            ->with('seo', 'seos')
                            ->withCount('seos') // Đếm số lượng `seos` cho mỗi phần tử
                            ->orderBy('created_at', 'DESC')
                            ->having('seos_count', '<', $countLanguage) // Lọc các phần tử có `seos_count` < tổng ngôn ngữ
                            ->paginate($params['paginate']);
        return $result;
    }

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
        return $this->hasMany(\App\Models\RelationTagInfoOrther::class, 'tag_info_id', 'id')->where('reference_type', 'product_info');
    }

    public function freeWallpapers(){
        return $this->hasMany(\App\Models\RelationTagInfoOrther::class, 'tag_info_id', 'id')->where('reference_type', 'free_wallpaper_info');
    }

    public function categories(){
        return $this->hasMany(\App\Models\RelationCategoryInfoTagInfo::class, 'tag_info_id', 'id');
    }
}
