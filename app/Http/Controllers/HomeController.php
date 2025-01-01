<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Route;
use App\Models\Page;
use App\Models\Category;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\Admin\HelperController;
use App\Http\Controllers\Admin\TranslateController;
use App\Models\ISO3166;
use App\Models\Tag;
use App\Models\Seo;
use App\Models\SeoContent;
use App\Models\Product;
use GeoIp2\Database\Reader;
use Illuminate\Support\Facades\Session;
use App\Models\RelationSeoProductInfo;
use App\Models\RelationSeoCategoryInfo;
use App\Models\RelationSeoTagInfo;
use App\Models\RelationSeoPageInfo;
use App\Models\Timezone;
use App\Jobs\Tmp;

use Illuminate\Support\Facades\Mail;
use App\Mail\SendProductMail;

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
                foreach ($item->seos as $seo) {
                    if (!empty($seo->infoSeo->language) && $seo->infoSeo->language==$language) {
                        $itemSeo = $seo->infoSeo;
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

        $categories = Category::select('*')
                        ->with('seo', 'seos')
                        ->get();
        foreach($categories as $infoC) TranslateController::createJobTranslateAndCreatePage($infoC);

        $tags       = Tag::select('*')
                        ->with('seo', 'seos')
                        ->get();
        foreach($tags as $infoT) TranslateController::createJobTranslateAndCreatePage($infoT);

        $products   = Product::select('*')
                        ->with('seo', 'seos')
                        ->get();
        foreach($products as $infoP) TranslateController::createJobTranslateAndCreatePage($infoP);
        
    }

    private static function findUniqueElements($arr1, $arr2) {
        // Lọc các phần tử có trong arr1 nhưng không có trong arr2 và ngược lại
        $uniqueInArr1 = array_diff($arr1, $arr2);
        $uniqueInArr2 = array_diff($arr2, $arr1);
        
        // Kết hợp các phần tử không trùng
        return array_merge($uniqueInArr1, $uniqueInArr2);
    }
}