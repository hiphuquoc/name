<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Route;
use App\Models\Page;
use App\Models\Category;
use App\Http\Controllers\SettingController;
use App\Models\Product;
use App\Models\RelationSeoEnSeo;
use Intervention\Image\ImageManagerStatic;

use GuzzleHttp\Client;

class HomeController extends Controller{
    public static function home(Request $request){
        /* xác định trang tiếng anh hay tiếng việt */
        $currentRoute           = Route::currentRouteName();
        /* lưu ngôn ngữ sử dụng */
        $language               = $currentRoute=='main.home' ? 'vi' : 'en';
        SettingController::settingLanguage($language);
        /* cache HTML */
        $nameCache              = $language.'home.'.config('main.cache.extension');
        $pathCache              = Storage::path(config('main.cache.folderSave')).$nameCache;
        $cacheTime    	        = env('APP_CACHE_TIME') ?? 1800;
        if(file_exists($pathCache)&&$cacheTime>(time() - filectime($pathCache))){
            $xhtml              = file_get_contents($pathCache);
        }else {
            $item               = Page::select('*')
                                    ->whereHas('type', function($query){
                                        $query->where('code', 'home');
                                    })
                                    ->when($language=='vi', function($query){
                                        $query->whereHas('seo', function($query){
                                            $query->where('slug', '/');
                                        });
                                    })
                                    ->when($language=='en', function($query){
                                        $query->whereHas('en_seo', function($query){
                                            $query->where('slug', 'en');
                                        });
                                    })
                                    ->with('seo', 'en_seo', 'type')
                                    ->first();
            /* lấy hình nền điện thoại tết */
            $slug               = 'hinh-nen-dien-thoai-tet';
            $infoCategoryTet    = Category::select('category_info.*', 'seo.slug')
                                    ->join('seo', 'seo.id', '=', 'category_info.seo_id')
                                    ->where('seo.slug', '=', $slug)
                                    // ->whereHas('products.infoProduct.prices.wallpapers', function($query){
                                    //     // Điều kiện để kiểm tra xem có ít nhất một wallpaper
                                    //     $query->whereNotNull('id');
                                    // })
                                    ->with('seo')
                                    ->with('products', function($query){
                                        $query->orderBy('id', 'DESC');
                                    })
                                    ->first();
            /* lấy hình nền điện thoại noel */
            $slug               = 'hinh-nen-dien-thoai-giang-sinh-noel';
            $infoCategoryNoel   = Category::select('category_info.*', 'seo.slug')
                                    ->join('seo', 'seo.id', '=', 'category_info.seo_id')
                                    ->where('seo.slug', '=', $slug)
                                    // ->whereHas('products.infoProduct.prices.wallpapers', function($query){
                                    //     // Điều kiện để kiểm tra xem có ít nhất một wallpaper
                                    //     $query->whereNotNull('id');
                                    // })
                                    ->with('seo')
                                    ->with('products', function($query){
                                        $query->orderBy('id', 'DESC');
                                    })
                                    ->first();
            $viewBy             = $request->cookie('view_by') ?? 'set';
            // /* select của filter */
            // $categories         = Category::all();
            // $styles             = Style::all();
            // $events             = Event::all();
            $xhtml              = view('wallpaper.home.index', compact('item', 'language', 'infoCategoryTet', 'infoCategoryNoel', 'viewBy'))->render();
            /* Ghi dữ liệu - Xuất kết quả */
            if(env('APP_CACHE_HTML')==true) Storage::put(config('main.cache.folderSave').$nameCache, $xhtml);
        }
        echo $xhtml;
    }

    public static function test(Request $request){
        // $client = new Client();

        // $response = $client->post('https://api.cognitive.microsoft.com/bing/v7.0/search', [
        //     'headers' => [
        //         'Ocp-Apim-Subscription-Key' => env('BING_AI_API_KEY'),
        //         'Content-Type' => 'application/json',
        //     ],
        //     'json' => [
        //         'question' => 'Your chat question here',
        //     ],
        // ]);

        // $result = json_decode($response->getBody(), true);

        // dd($result);

        // $styles = \App\Models\Event::select('*')
        //             ->with('seo', 'en_seo')
        //             ->get();
        // foreach($styles as $style){
        //     /* insert SEO */
        //     $insertSeo      = $style->seo->toArray();
        //     unset($insertSeo['id'], $insertSeo['created_at'], $insertSeo['updated_at']);
        //     $idSeo          = \App\Models\Seo::insertItem($insertSeo);
        //     /* insert EN_SEO */
        //     $insertEnSeo    = $style->en_seo->toArray();
        //     unset($insertEnSeo['id'], $insertEnSeo['created_at'], $insertEnSeo['updated_at']);
        //     $idEnSeo        = \App\Models\EnSeo::insertItem($insertEnSeo);
        //     /* delete relation */
        //     $tmp = RelationSeoEnSeo::select('*')
        //             ->where('en_seo_id', $style->en_seo->id)
        //             ->delete();
        //     RelationSeoEnSeo::insertItem([
        //         'seo_id'    => $idSeo,
        //         'en_seo_id' => $idEnSeo
        //     ]);

        //     /* insert category_info */
        //     $insertCategory     = $style->toArray();
        //     unset($insertCategory['id'], $insertCategory['created_at'], $insertCategory['updated_at'], $insertCategory['seo'], $insertCategory['en_seo']);
        //     $insertCategory['seo_id'] = $idSeo;
        //     $insertCategory['en_seo_id'] = $idEnSeo;
        //     $idCategory = \App\Models\Category::insertItem($insertCategory);
        //     echo '<pre>';
        //     print_r('<div>'.$idCategory.'</div>');
        //     echo '</pre>';
        // }

        // $arrayIdSeo = [];
        // $arrayIdEnSeo = [];
        // $tmp = Category::all();
        // foreach($tmp as $t){
        //     $arrayIdSeo[] = $t->seo->id;
        //     $arrayIdEnSeo[] = $t->en_seo->id;;
        // }
        // $tmp = Page::all();
        // foreach($tmp as $t){
        //     $arrayIdSeo[] = $t->seo->id;
        //     $arrayIdEnSeo[] = $t->en_seo->id;;
        // }
        // $tmp = Product::all();
        // foreach($tmp as $t){
        //     $arrayIdSeo[] = $t->seo->id;
        //     $arrayIdEnSeo[] = $t->en_seo->id;;
        // }

        // $result = \App\Models\Seo::select('*')
        //             ->whereNotIn('id', $arrayIdSeo)
        //             ->delete();
        // dd($result->toArray());

    }
}
