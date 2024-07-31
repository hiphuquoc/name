<?php

namespace App\Models;

use App\Http\Controllers\Admin\RedirectController;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class Seo extends Model {
    use HasFactory;
    protected $table        = 'seo';
    protected $fillable     = [
        'title', 
        'description', 
        'image',
        'image_small',
        'level', 
        'parent', 
        'ordering',
        'topic', 
        'seo_title',
        'seo_description',
        'slug',
        'slug_full',
        'link_canonical',
        'type',
        'rating_author_name', 
        'rating_author_star',
        'rating_aggregate_count', 
        'rating_aggregate_star',
        'created_at',
        'updated_at',
        'language',
    ];

    public static function insertItem($params){
        $id             = 0;
        if(!empty($params)){
            $model      = new Seo();
            foreach($params as $key => $value) $model->{$key}  = $value;
            $model->save();
            $id         = $model->id;
        }
        return $id;
    }

    public static function updateItem($id, $params){
        // Đặt thời gian chờ không giới hạn
        set_time_limit(0);
        $flag               = false;
        if(!empty($id)&&!empty($params)){
            $model          = self::find($id);
            /* lấy slug cũ - mới để so sánh */
            $slugFullOld    = $model->slug_full;
            $slugFullNew    = self::buildFullUrl($params['slug'], $model->level, $model->parent);
            foreach($params as $key => $value) $model->{$key}  = $value;
            $flag           = $model->update();
            /* mỗi lần cập nhật lại slug thì phải build lại slug_full của toàn bộ children và thay thế internal link trong tất cả content của cả slug hiện tại và slug con */
            if($slugFullOld!=$slugFullNew) {
                /* tạo bản redirect 301 */
                $urlOldWithPrefix   = RedirectController::filterUrl($slugFullOld);
                $urlNewWithPrefix   = RedirectController::filterUrl($slugFullNew);
                RedirectController::createRedirectAndFix($urlOldWithPrefix, $urlNewWithPrefix);
                /* thay thế internal link trong tất cả content của slug hiện tại */
                self::replaceInternalLinksInSeoContents($slugFullOld, $slugFullNew);
                /* cập nhật lại slug_full của phần tử con */
                self::updateSlugChilds($model->id);
            }
        }
        return $flag;
    }

    public static function replaceInternalLinksInSeoContents($slugOld, $slugNew){
        $baseUrl        = env('APP_URL');
        $contentsMatch  = SeoContent::whereRaw('content REGEXP ?', ['href=["\']' . preg_quote($baseUrl . '/' . $slugOld, '/') . '(\?.*)?["\']'])
                            ->orWhereRaw('content REGEXP ?', ['href=["\']\.\./\.\./' . preg_quote($slugOld, '/') . '(\?.*)?["\']'])
                            ->get();
        // Xử lý từng bản ghi
        foreach ($contentsMatch as $content) {
            $content->content = self::replaceInternalLinks($slugOld, $slugNew, $content->content);
            $content->save();
        }
    }

    public static function replaceInternalLinks($slugOld, $slugNew, $content) {
        // Lấy giá trị URL từ biến môi trường
        $baseUrl = env('APP_URL');
        // Sử dụng regex để tìm và thay thế các liên kết nội bộ trong thuộc tính href
        $patterns = [
            // Định dạng URL đầy đủ
            '/href=["\']' . preg_quote($baseUrl, '/') . '\/' . preg_quote($slugOld, '/') . '(\?.*?)?["\']/u',
            // Định dạng URL tương đối
            '/href=["\']\.\.\/\.\.\/' . preg_quote($slugOld, '/') . '(\?.*?)?["\']/u'
        ];
        $replacements = [
            'href="' . $baseUrl . '/' . $slugNew . '$1"',
            'href="../../' . $slugNew . '$1"'
        ];
        // Thay thế các liên kết trong content
        $updatedContent = preg_replace($patterns, $replacements, $content);
        return $updatedContent;
    }

    public static function updateSlugChilds($idParent){
        $childs = self::select('id', 'level', 'parent', 'slug', 'slug_full')
                    ->where('parent', $idParent)
                    ->get();
        foreach($childs as $child){
            $slugFullNew     = self::buildFullUrl($child->slug, $child->level, $child->parent);
            $slugFullOld     = $child->slug_full;
            if($slugFullNew!=$slugFullOld){
                 /* cập nhật lại slug_full */
                $paramsUpdate   = [
                    'slug'      => $child->slug, /* trong updatItem slug bắt buộc để kiểm tra có thay đổi không => tuy không đổi vẫn phải truyền vào */
                    'slug_full' => $slugFullNew
                ];
                self::updateItem($child->id, $paramsUpdate);
                /* tạo redirect 301 */
                $urlOldWithPrefix   = RedirectController::filterUrl($slugFullOld);
                $urlNewWithPrefix   = RedirectController::filterUrl($slugFullNew);
                RedirectController::createRedirectAndFix($urlOldWithPrefix, $urlNewWithPrefix);
                /* thay thế internal link trong tất cả content */
                self::replaceInternalLinksInSeoContents($slugFullOld, $slugFullNew);
                /* kiểm tra xem có child cấp thấp hơn không */
                $numberChildsOfChild = self::where('parent', $child->id)->count();
                if($numberChildsOfChild>0) self::updateSlugChilds($child->id);
            }
        }
    }

    public static function getItemBySlug($slug = null){
        $result = null;
        if(!empty($slug)){
            $result = self::select('*')
                        ->where('slug', $slug)
                        ->first();
        }
        return $result;
    }

    public static function buildFullUrl($slug, $level, $parent){
        $url    = null;
        if(!empty($slug)){
            $infoSeo    = self::select('id', 'slug', 'parent')
                            ->get();
            $url        = $slug;
            for($i=1;$i<=$level;++$i){
                foreach($infoSeo as $item){
                    if($item->id==$parent) {
                        $url    = $item->slug.'/'.$url;
                        $parent = $item->parent;
                        break;
                    }
                }
            }
        }
        return $url;
    }

    // public function keywords() {
    //     return $this->hasMany(\App\Models\Keyword::class, 'seo_id', 'id');
    // }

    // public function contentspin() {
    //     return $this->hasOne(\App\Models\Contentspin::class, 'seo_id', 'id');
    // }

    // public function checkSeos() {
    //     return $this->hasMany(\App\Models\CheckSeo::class, 'seo_id', 'id');
    // }

    public function user(){
        return $this->hasOne(\App\Models\User::class, 'id', 'rating_author_name');
    }

    public function contents(){
        return $this->hasMany(\App\Models\SeoContent::class, 'seo_id', 'id')->orderBy('ordering')->orderBy('id');
    }

    public function source(){
        return $this->hasOne(\App\Models\Seo::class, 'id', 'link_canonical');
    }

    public function jobAutoTranslate(){
        return $this->hasMany(\App\Models\JobAutoTranslate::class, 'seo_id', 'id')->whereColumn('language', 'language');
    }

    public function jobAutoTranslateLinks() {
        return $this->hasMany(\App\Models\JobAutoTranslateLinks::class, 'seo_id', 'id');
    }
}
