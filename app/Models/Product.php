<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model {
    use HasFactory;
    protected $table        = 'product_info';
    protected $fillable     = [
        'seo_id',
        'code',
        'sold',
        'price',
        'price_before_promotion',
        'sale_off'
    ];
    public $timestamps = true;

    public static function getList($params = null){
        $result     = self::select('*')
                        /* tìm theo tên */
                        ->when(!empty($params['search_name']), function($query) use($params){
                            $query->whereHas('seo', function($subQuery) use($params){
                                $subQuery->where('title', 'like', '%'.$params['search_name'].'%');
                            })->orWhere('code', 'like', '%'.$params['search_name'].'%');
                        })
                        /* tìm theo danh mục */
                        ->when(!empty($params['search_category']), function($query) use($params){
                            $query->whereHas('categories.infoCategory', function($q) use ($params){
                                $q->where('id', $params['search_category']);
                            });
                        })
                        ->orderBy('created_at', 'DESC')
                        ->with(['files' => function($query){
                            $query->where('relation_table', 'product_info');
                        }])
                        ->with('seo', 'prices.wallpapers.infoWallpaper', 'categories')
                        ->paginate($params['paginate']);
        return $result;
    }

    public static function insertItem($params){
        $id             = 0;
        if(!empty($params)){
            $model      = new Product();
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
        return $this->hasMany(\App\Models\RelationSeoProductInfo::class, 'product_info_id', 'id');
    }

    public function files(){
        return $this->hasMany(\App\Models\SystemFile::class, 'attachment_id', 'id');
    }

    public function prices() {
        return $this->hasMany(\App\Models\ProductPrice::class, 'product_info_id', 'id');
    }

    public function categories(){
        return $this->hasMany(\App\Models\RelationCategoryProduct::class, 'product_info_id', 'id');
    }

    public function tags(){
        return $this->hasMany(\App\Models\RelationTagInfoOrther::class, 'reference_id', 'id')->where('reference_type', 'product_info');
    }
}
