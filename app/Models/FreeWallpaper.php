<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class FreeWallpaper extends Model {
    use HasFactory;
    protected $table        = 'free_wallpaper_info';
    protected $fillable     = [
        'user_id',
        'name', 
        'description',
        'file_name',
        'extension',
        'file_cloud',
        'width',
        'height',
        'file_size',
        'mine_type',
        'heart',
        'ha_ha',
        'not_like',
        'vomit'
    ];
    public $timestamps = true;

    public static function getList($params = null){
        $result     = self::select('*')
                        /* tìm theo tên */
                        ->when(!empty($params['search_name']), function($query) use($params){
                            $query->where('name', 'like', '%'.$params['search_name'].'%');
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
                        ->with('categories')
                        ->paginate($params['paginate']);
        return $result;
    }

    public static function insertItem($params){
        $id             = 0;
        if(!empty($params)){
            $model      = new FreeWallpaper();
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

    public function en_seo() {
        return $this->hasOne(\App\Models\EnSeo::class, 'id', 'en_seo_id');
    }
    
    public function categories(){
        return $this->hasMany(\App\Models\RelationFreewallpaperCategory::class, 'free_wallpaper_info_id', 'id');
    }

    public function tags(){
        return $this->hasMany(\App\Models\RelationTagInfoOrther::class, 'reference_id', 'id')->where('reference_type', 'free_wallpaper_info');
    }

    public function feeling(){
        return $this->hasOne(\App\Models\RelationFreeWallpaperUser::class, 'free_wallpaper_info_id', 'id')->where('user_info_id', Auth::user()->id ?? null);
    }

    public function contents() {
        return $this->hasMany(\App\Models\FreeWallpaperContent::class, 'free_wallpaper_info_id', 'id');
    }
}
