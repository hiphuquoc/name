<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Route;
use App\Models\Page;
use App\Models\Category;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\Admin\HelperController;
use App\Models\Tag;
use App\Models\Seo;
use App\Models\SeoContent;
use App\Models\Product;
use Intervention\Image\ImageManagerStatic;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client;
use AdityaDees\LaravelBard\LaravelBard;
use App\Http\Controllers\Admin\TranslateController;
use App\Models\FreeWallpaper;
use App\Models\RelationSeoCategoryInfo;
use App\Models\RelationSeoProductInfo;
use App\Models\RelationSeoTagInfo;
use App\Models\RelationSeoPageInfo;
use Google\Client as Google_Client;
use Illuminate\Support\Facades\DB;

use DOMDocument;
use PhpParser\Node\Stmt\Switch_;

class HomeController extends Controller
{
    public static function home(Request $request, $language = 'vi'){
        /* ngôn ngữ */
        SettingController::settingLanguage($language);
        /* cache HTML */
        $nameCache              = $language.'home.'.config('main.cache.extension');
        $pathCache              = Storage::path(config('main.cache.folderSave')).$nameCache;
        $cacheTime    	        = env('APP_CACHE_TIME') ?? 1800;
        if(file_exists($pathCache)&&$cacheTime>(time() - filectime($pathCache))){
            $xhtml              = file_get_contents($pathCache);
        }else {
        $item               = Page::select('*')
            ->whereHas('seos.infoSeo', function ($query) use ($language) {
                $query->where('slug', $language);
            })
            ->with('seo', 'seos', 'type')
            ->first();
        /* lấy item seo theo ngôn ngữ được chọn */
        $itemSeo            = [];
        if (!empty($item->seos)) {
            foreach ($item->seos as $s) {
                if ($s->infoSeo->language == $language) {
                    $itemSeo = $s->infoSeo;
                    break;
                }
            }
        }
        $categories = Category::select('*')
                        ->whereHas('seo', function($query){
                            $query->where('level', 2);
                        })
                        ->where('flag_show', 1)
                        ->with('seo')
                        ->with('seos.infoSeo', function($query) use($language){
                            $query->where('language', $language);
                        })
                        ->get();
        $xhtml      = view('wallpaper.home.index', compact('item', 'itemSeo', 'language', 'categories'))->render();
            /* Ghi dữ liệu - Xuất kết quả */
            if(env('APP_CACHE_HTML')==true) Storage::put(config('main.cache.folderSave').$nameCache, $xhtml);
        }
        echo $xhtml;
    }

    public static function test(Request $request){
        $fullUrl    = 'https://name.dev/hinh-nen-dien-thoai/hinh-nen-dien-thoai-hai-huoc';
        TranslateController::createJobTranslateAndCreatePage($fullUrl);
        
        dd(123);
    }

    private static function reorderString($input) {
        // Các giá trị mặc định
        $defaults = [
            'fonds-d-ecran-de-telephone',
            'fondos-de-pantalla-del-telefono',
            'wallpaper-ponsel'
        ];
        
        // Kiểm tra từng giá trị mặc định xem có tồn tại trong chuỗi hay không
        foreach ($defaults as $default) {
            if (strpos($input, $default) !== false) {
                // Tách chuỗi thành hai phần
                $parts = explode($default, $input);
                // Xóa các ký tự '-' thừa ở đầu và cuối chuỗi
                $prefix = !empty($parts[0]) ? trim($parts[0], '-') : trim($parts[1], '-');
                // Nối chuỗi với phần mặc định ở sau
                return $default . '-' . $prefix;
            }
        }
        
        // Trường hợp không tìm thấy giá trị mặc định nào trong chuỗi
        return $input;
    }

    public static function copyProductBySource($urlSource, $urlSearch){
        $response  = [];
        $productSource  = Product::select('*')
            ->whereHas('seo', function ($query) use($urlSource){
                $query->where('slug', $urlSource);
            })
            ->with('seo', 'seos.infoSeo.contents')
            ->first();

        $tmp            = Product::select('*')
            ->whereHas('seo', function ($query) use($urlSearch){
                $query->where('slug', 'LIKE', $urlSearch.'%');
            })
            ->where('id', '!=', $productSource->id)
            ->with('seo', 'seos.infoSeo.contents')
            ->get();
        $k      = 1;
        foreach ($tmp as $t) {
            /* xóa relation seos -> infoSeo -> contents (nếu có) */
            foreach ($t->seos as $seo) {
                foreach ($seo->infoSeo->contents as $content) {
                    SeoContent::select('*')
                        ->where('id', $content->id)
                        ->delete();
                }
                \App\Models\RelationSeoProductInfo::select('*')
                    ->where('seo_id', $seo->seo_id)
                    ->delete();
                Seo::select('*')
                    ->where('id', $seo->seo_id)
                    ->delete();
            }
            /* tạo dữ liệu mới */
            $i = 0;
            foreach ($productSource->seos as $seoS) {
                /* tạo seo */
                $tmp2   = $seoS->infoSeo->toArray();
                $insert = [];
                foreach ($tmp2 as $key => $value) {
                    if ($key != 'contents' && $key != 'id') $insert[$key] = $value;
                }
                $insert['link_canonical']   = $tmp2['id'];
                $insert['slug']             = $tmp2['slug'] . '-' . $k;
                $insert['slug_full']        = $tmp2['slug_full'] . '-' . $k;
                $idSeo = Seo::insertItem($insert);
                /* cập nhật lại seo_id của product */
                if ($insert['language'] == 'vi') {
                    Product::updateItem($t->id, [
                        'seo_id' => $idSeo,
                    ]);
                }
                $response[] = $idSeo;
                /* tạo relation_seo_product_info */
                RelationSeoProductInfo::insertItem([
                    'seo_id'    => $idSeo,
                    'product_info_id' => $t->id,
                ]);
                /* tạo content */
                foreach ($seoS->infoSeo->contents as $content) {
                    $contentInsert = $content->content;
                    $contentInsert = str_replace($seoS->infoSeo->slug_full, $insert['slug_full'], $contentInsert);
                    SeoContent::insertItem([
                        'seo_id'    => $idSeo,
                        'content'   => $contentInsert,
                    ]);
                }
                ++$i;
            }
            /* copy relation product và category */
            \App\Models\RelationCategoryProduct::select('*')
                ->where('product_info_id', $t->id)
                ->delete();
            foreach($productSource->categories as $category){
                \App\Models\RelationCategoryProduct::insertItem([
                    'category_info_id'       => $category->category_info_id,
                    'product_info_id'      => $t->id
                ]);
            }
            /* copy relation product và tag */
            \App\Models\RelationTagInfoOrther::select('*')
                ->where('reference_type', 'product_info')
                ->where('reference_id', $t->id)
                ->delete();
            foreach($productSource->tags as $tag){
                \App\Models\RelationTagInfoOrther::insertItem([
                    'tag_info_id'       => $tag->tag_info_id,
                    'reference_type'    => 'product_info',
                    'reference_id'      => $t->id
                ]);
            }
            ++$k;
        }
        return $response;
    }
}