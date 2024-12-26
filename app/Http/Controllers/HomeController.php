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
use App\Models\Order;
use App\Models\Seo;
use App\Models\SeoContent;
use App\Models\Product;
use GeoIp2\Database\Reader;
use Illuminate\Support\Facades\Session;
use App\Models\RelationSeoProductInfo;
use App\Models\Timezone;

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

        // $infoPage = Category::select('*')
        //             ->whereHas('seo', function($query){
        //                 $query->where('slug', 'hinh-nen-dien-thoai');
        //             })
        //             ->with('seo', 'seos')
        //             ->first();
        // // dd($infoPage);
        // foreach($infoPage->seos as $seo){
        //     if(!empty($seo->infoSeo->language)){
        //         echo '<div>'.$seo->infoSeo->language.'- '.$seo->infoSeo->id.'</div>';
        //     }
            
        // }

        // dd(123);

        // $main = ["en", "zh", "es", "ja", "de", "fr", "pt", "ko", "it", "ru", 
        //     "nl", "pl", "tr", "ar", "hi", "id", "vi", "th", "sv", "da", 
        //     "fi", "no", "he", "cs", "hu", "el", "ro", "uk", "fa", "bn", 
        //     "fil", "ms", "bg", "hr", "sk", "lt", "lv", "et", "sl", "is", 
        //     "ml", "ta", "te", "kn", "mr", "gu", "km", "my", "ka", "uz"];

        // $languages = [
        //     'en', 'zh', 'es', 'fr', 'vi', 'hi', 'bn', 'ta', 'te', 'ur',
        //     'gu', 'ja', 'ko', 'id', 'ms', 'th', 'ar', 'fa', 'ru', 'de',
        //     'tr', 'it', 'pl', 'uk', 'nl', 'el', 'hu', 'cs', 'ro', 'sk',
        //     'ka', 'he', 'uz', 'pt', 'fil', 'sv', 'no', 'fi', 'da', 'sw',
        //     'ml', 'bg', 'ky', 'is', 'sr', 'mk', 'lv', 'lt', 'sl', 'mn'
        // ];
        // $test = array_merge($languages, $main);

        // $result = array_unique($test);

        // $languages = []; // Đổi từ $langauges thành $languages
        // foreach(config('language') as $key => $language){
        //     $languages[] = $key; // Chắc chắn rằng $key chứa mã ngôn ngữ
        // }

        // /* lấy tất cả seo */
        // $seos = Seo::all();
        // $countDelete = 0;
        // foreach($seos as $seo){
        //     if(!in_array($seo->language, $languages)) {
        //         $flag = $seo->delete();
        //         if($flag) ++$countDelete; // Nếu xóa thành công, tăng biến đếm
        //     }
        // }

        // // Thông báo kết quả xóa
        // echo "$countDelete SEO records have been deleted.";


        // dd(123);
        
    }

    private static function findUniqueElements($arr1, $arr2) {
        // Lọc các phần tử có trong arr1 nhưng không có trong arr2 và ngược lại
        $uniqueInArr1 = array_diff($arr1, $arr2);
        $uniqueInArr2 = array_diff($arr2, $arr1);
        
        // Kết hợp các phần tử không trùng
        return array_merge($uniqueInArr1, $uniqueInArr2);
    }
}