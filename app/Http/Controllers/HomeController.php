<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Route;
use App\Models\Page;
use App\Models\Category;
use App\Http\Controllers\SettingController;
use App\Models\Tag;
use App\Models\Seo;
use App\Models\EnSeo;
use Intervention\Image\ImageManagerStatic;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client;
use AdityaDees\LaravelBard\LaravelBard;

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
                                        $query->orderBy('id', 'DESC')
                                            ->skip(0)
                                            ->take(10);
                                    })
                                    ->first();
            /* lấy hình nền điện thoại noel */
            $slug               = 'hinh-nen-dien-thoai-giang-sinh-noel';
            $infoCategoryNoel   = Category::select('category_info.*', 'seo.slug')
                                    ->join('seo', 'seo.id', '=', 'category_info.seo_id')
                                    ->where('seo.slug', '=', $slug)
                                    ->with('seo')
                                    ->with('products', function($query){
                                        $query->orderBy('id', 'DESC')
                                            ->skip(0)
                                            ->take(10);
                                    })
                                    ->first();
            $viewBy             = $request->cookie('view_by') ?? 'set';
            $arrayIdCategory    = [];
            $xhtml              = view('wallpaper.home.index', compact('item', 'language', 'infoCategoryTet', 'infoCategoryNoel', 'viewBy', 'arrayIdCategory'))->render();
            /* Ghi dữ liệu - Xuất kết quả */
            if(env('APP_CACHE_HTML')==true) Storage::put(config('main.cache.folderSave').$nameCache, $xhtml);
        }
        echo $xhtml;
    }

    public static function test(Request $request){
        $tags = Tag::select('*')
                    ->with('seo', 'en_seo')
                    ->get();
        
        foreach($tags as $tag){
            $description = 'Nâng tầm phong cách điện thoại của bạn với Hình Nền Điện Thoại '.$tag->name.' từ Name.com.vn. Độ phân giải 3072x6144px, màu sắc tươi sáng. Khám phá ngay!';
            $seoTitle   = '+1000 Hình nền điện thoại '.$tag->name.' tuyệt đẹp @name.com.vn';
            $insert = [
                'description'       => $description,
                'seo_description'   => $description,
                'seo_title'         => $seoTitle
            ];
            Seo::updateItem($tag->seo->id, $insert);

            $descriptionEn = "Enhance your phone's style with ".$tag->en_seo->name." Phone Wallpapers from Name.com.vn. Resolution 3072x6144px, bright colors. Explore now!";
            $seoTitleEn   = "+1000 ".$tag->en_seo->name." Phone Wallpapers Wonderful @name.com.vn";
            $insert = [
                'description'       => $descriptionEn,
                'seo_description'   => $descriptionEn,
                'seo_title'         => $seoTitleEn
            ];
            EnSeo::updateItem($tag->en_seo->id, $insert);

            Tag::updateItem($tag->id, [
                'name'  => $seoTitle,
                'description'   => $description,
                'en_name'   => $seoTitleEn,
                'en_description'    => $descriptionEn
            ]);
        }
    }

    // public static function chatGPT(Request $request){
    //     // Replace 'YOUR_API_KEY' with your actual API key from OpenAI
    //     $apiKey = env('CHAT_GPT_API_KEY');

    //     $response = Http::withHeaders([
    //         'Content-Type' => 'application/json',
    //         'Authorization' => 'Bearer ' . $apiKey,
    //     ])->post('https://api.openai.com/v1/chat/completions', [
    //         'model' => 'gpt-3.5-turbo-1106',
    //         'prompt' => 'Phân tích giúp tôi nội dung trong ảnh này',
    //         'images' => [
    //             'https://namecomvn.storage.googleapis.com/freewallpapers/hinh-nen-co-gai-xinh-dep-de-thuong-goi-cam-quyen-ru-duoi-anh-nang-dep-cua-hoang-hon-1705861656-20-small.webp'
    //         ], // Assuming $imagePath is the path to your image file
    //         'max_tokens' => 2048, // Adjust as needed
    //     ]);

    //     $result = $response->json();
    //     dd($result);
    //     // Process and display the result
    //     $description = $result['choices'][0]['text'];

    //     dd($description);

    //     return view('result', compact('description'));
    // }
}
