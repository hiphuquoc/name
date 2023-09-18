<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wallpaper extends Model {
    use HasFactory;
    protected $table        = 'wallpaper_info';
    protected $fillable     = [
        'user_id',
        'name', 
        'description',
        'file_name',
        'file_url_hosting',
        'file_url_cloud',
        'width',
        'height',
        'file_size',
        'extension',
        'mime_type'
    ];
    public $timestamps = true;

    public static function getList($params = null){
        $result     = self::select('*')
                        /* tìm theo tên */
                        ->when(!empty($params['search_name']), function($query) use($params){
                            $query->where('code', 'like', '%'.$params['search_name'].'%')
                            ->orWhere('name', 'like', '%'.$params['search_name'].'%');
                        })
                        // /* tìm theo nhãn hàng */
                        // ->when(!empty($params['search_brand']), function($query) use($params){
                        //     $query->whereHas('brand', function($q) use ($params){
                        //         $q->where('id', $params['search_brand']);
                        //     });
                        // })
                        // /* tìm theo danh mục */
                        // ->when(!empty($params['search_category']), function($query) use($params){
                        //     $query->whereHas('categories.infoCategory', function($q) use ($params){
                        //         $q->where('id', $params['search_category']);
                        //     });
                        // })
                        ->orderBy('created_at', 'DESC')
                        // ->with('seo')
                        ->paginate($params['paginate']);
        return $result;
    }

    public static function insertItem($params){
        $id             = 0;
        if(!empty($params)){
            $model      = new Wallpaper();
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

    // public function productPrices() {
    //     return $this->hasMany(\App\Models\RelationCategoryBlogInfoBlogInfo::class, 'id', 'seo_id');
    // }
}
