<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Route;
use App\Models\Page;
use App\Models\Category;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\Admin\HelperController;
use App\Models\ISO3166;
use App\Models\Tag;
use App\Models\Seo;
use App\Models\SeoContent;
use App\Models\Product;
use GeoIp2\Database\Reader;

// use App\Models\Prompt;
// use Intervention\Image\ImageManagerStatic;
// use Illuminate\Support\Facades\Http;
// use GuzzleHttp\Client;
// use AdityaDees\LaravelBard\LaravelBard;
// use App\Http\Controllers\Admin\TranslateController;
// use App\Jobs\AutoTranslateContent;
// use App\Models\FreeWallpaper;
// use App\Models\RelationSeoCategoryInfo;
use App\Models\RelationSeoProductInfo;
use App\Models\Timezone;

// use App\Models\RelationSeoTagInfo;
// use App\Models\RelationSeoPageInfo;
// use App\Models\Wallpaper;
// use Google\Client as Google_Client;
// use Illuminate\Support\Facades\DB;

// use Illuminate\Support\Facades\Mail;
// use App\Mail\SendProductMail;

// use DOMDocument;
// use PDO;
// use PhpParser\Node\Stmt\Switch_;

class HomeController extends Controller {
    public static function home(Request $request, $language = 'vi'){
        /* ngôn ngữ */
        SettingController::settingLanguage($language);
        /* cache HTML */
        $nameCache              = $language.'home.'.config('main_'.env('APP_NAME').'.cache.extension');
        $pathCache              = Storage::path(config('main_'.env('APP_NAME').'.cache.folderSave')).$nameCache;
        $cacheTime    	        = env('APP_CACHE_TIME') ?? 1800;
        if(file_exists($pathCache)&&$cacheTime>(time() - filectime($pathCache))){
            $xhtml              = file_get_contents($pathCache);
        }else {
            $item               = Page::select('*')
                ->whereHas('seos.infoSeo', function ($query) use ($language) {
                    $query->where('slug', $language);
                })
                ->with('seo', 'seos.infoSeo', 'type')
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
            $categories     = Category::select('*')
                                ->where('flag_show', 1)
                                ->get();
            $xhtml      = view('wallpaper.home.index', compact('item', 'itemSeo', 'language', 'categories'))->render();
            /* Ghi dữ liệu - Xuất kết quả */
            if(env('APP_CACHE_HTML')==true) Storage::put(config('main_'.env('APP_NAME').'.cache.folderSave').$nameCache, $xhtml);
        }
        echo $xhtml;
    }

    public static function test(Request $request){

        $allTimezone = Timezone::all();
        foreach($allTimezone as $timezone){
            $timezoneLow    = strtolower($timezone->timezone);
            Timezone::updateItem($timezone->id, [
                'timezone_lower'    => $timezoneLow
            ]);
        }


        dd(123);
        
    }

    private static function findUniqueElements($arr1, $arr2) {
        // Lọc các phần tử có trong arr1 nhưng không có trong arr2 và ngược lại
        $uniqueInArr1 = array_diff($arr1, $arr2);
        $uniqueInArr2 = array_diff($arr2, $arr1);
        
        // Kết hợp các phần tử không trùng
        return array_merge($uniqueInArr1, $uniqueInArr2);
    }

    // public static function copyProductBySource($urlSource, $urlSearch){
    //     $response  = [];
    //     $productSource  = Product::select('*')
    //         ->whereHas('seo', function ($query) use($urlSource){
    //             $query->where('slug', $urlSource);
    //         })
    //         ->with('seo', 'seos.infoSeo.contents')
    //         ->first();

    //     $tmp            = Product::select('*')
    //         ->whereHas('seo', function ($query) use($urlSearch){
    //             $query->where('slug', 'LIKE', $urlSearch.'%');
    //         })
    //         ->where('id', '!=', $productSource->id)
    //         ->with('seo', 'seos.infoSeo.contents')
    //         ->get();
    //     $k      = 1;
    //     foreach ($tmp as $t) {
    //         /* xóa relation seos -> infoSeo -> contents (nếu có) */
    //         foreach ($t->seos as $seo) {
    //             foreach ($seo->infoSeo->contents as $content) {
    //                 SeoContent::select('*')
    //                     ->where('id', $content->id)
    //                     ->delete();
    //             }
    //             \App\Models\RelationSeoProductInfo::select('*')
    //                 ->where('seo_id', $seo->seo_id)
    //                 ->delete();
    //             Seo::select('*')
    //                 ->where('id', $seo->seo_id)
    //                 ->delete();
    //         }
    //         /* tạo dữ liệu mới */
    //         $i = 0;
    //         foreach ($productSource->seos as $seoS) {
    //             /* tạo seo */
    //             $tmp2   = $seoS->infoSeo->toArray();
    //             $insert = [];
    //             foreach ($tmp2 as $key => $value) {
    //                 if ($key != 'contents' && $key != 'id') $insert[$key] = $value;
    //             }
    //             $insert['link_canonical']   = $tmp2['id'];
    //             $insert['slug']             = $tmp2['slug'] . '-' . $k;
    //             $insert['slug_full']        = $tmp2['slug_full'] . '-' . $k;
    //             $idSeo = Seo::insertItem($insert);
    //             /* cập nhật lại seo_id của product */
    //             if ($insert['language'] == 'vi') {
    //                 Product::updateItem($t->id, [
    //                     'seo_id' => $idSeo,
    //                 ]);
    //             }
    //             $response[] = $idSeo;
    //             /* tạo relation_seo_product_info */
    //             RelationSeoProductInfo::insertItem([
    //                 'seo_id'    => $idSeo,
    //                 'product_info_id' => $t->id,
    //             ]);
    //             /* tạo content */
    //             foreach ($seoS->infoSeo->contents as $content) {
    //                 $contentInsert = $content->content;
    //                 $contentInsert = str_replace($seoS->infoSeo->slug_full, $insert['slug_full'], $contentInsert);
    //                 SeoContent::insertItem([
    //                     'seo_id'    => $idSeo,
    //                     'content'   => $contentInsert,
    //                     'ordering'  => $content->ordering,
    //                 ]);
    //             }
    //             ++$i;
    //         }
    //         /* copy relation product và category */
    //         \App\Models\RelationCategoryProduct::select('*')
    //             ->where('product_info_id', $t->id)
    //             ->delete();
    //         foreach($productSource->categories as $category){
    //             \App\Models\RelationCategoryProduct::insertItem([
    //                 'category_info_id'       => $category->category_info_id,
    //                 'product_info_id'      => $t->id
    //             ]);
    //         }
    //         /* copy relation product và tag */
    //         \App\Models\RelationTagInfoOrther::select('*')
    //             ->where('reference_type', 'product_info')
    //             ->where('reference_id', $t->id)
    //             ->delete();
    //         foreach($productSource->tags as $tag){
    //             \App\Models\RelationTagInfoOrther::insertItem([
    //                 'tag_info_id'       => $tag->tag_info_id,
    //                 'reference_type'    => 'product_info',
    //                 'reference_id'      => $t->id
    //             ]);
    //         }
    //         ++$k;
    //     }
    //     return $response;
    // }

    // public static function getCategories($params){
    //     $language       = session()->get('language');
    //     $sortBy         = $params['sort_by'] ?? null;
    //     $loaded         = $params['loaded'] ?? 0;
    //     $requestLoad    = $params['request_load'] ?? 10;
    //     $type           = $params['type'] ?? 'category_info'; /* category_info, style_info, event_info */
    //     $response       = [];
    //     $items          = Category::select('*')
    //                         ->whereHas('seo', function($query) use($type){
    //                             $query->where('level', 2)
    //                                 ->where('type', $type);
    //                         })
    //                         ->whereHas('seos.infoSeo', function($query) use($language){
    //                             $query->where('language', $language);
    //                         })
    //                         ->where('flag_show', 1)
    //                         ->when(empty($sortBy), function($query){
    //                             $query->orderBy('id', 'ASC');
    //                         })
    //                         ->when($sortBy=='newest'||$sortBy=='propose', function($query){
    //                             $query->orderBy('id', 'DESC');
    //                         })
    //                         ->when($sortBy=='favourite', function($query){
    //                             $query->orderBy('heart', 'DESC')
    //                                     ->orderBy('id', 'DESC');
    //                         })
    //                         ->when($sortBy=='oldest', function($query){
    //                             $query->orderBy('id', 'ASC');
    //                         })
    //                         // ->with(['seo', 'seos.infoSeo' => function($query) use($language) {
    //                         //     $query->where('language', $language);
    //                         // }])
    //                         ->skip($loaded)
    //                         ->take($requestLoad)
    //                         ->get();
    //     $total          = Category::select('*')
    //                         ->whereHas('seo', function($query) use($type){
    //                             $query->where('level', 2)
    //                                 ->where('type', $type);
    //                         })
    //                         ->whereHas('seos.infoSeo', function($query) use($language){
    //                             $query->where('language', $language);
    //                         })
    //                         ->where('flag_show', 1)
    //                         ->count();
    //     $response['items']      = $items;
    //     $response['total']      = $total;
    //     $response['loaded']     = $loaded + $requestLoad;
    //     return $response;
    // }
}