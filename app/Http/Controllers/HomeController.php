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
use App\Models\SeoContent;
use App\Models\Product;
use Intervention\Image\ImageManagerStatic;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client;
use AdityaDees\LaravelBard\LaravelBard;
use App\Models\FreeWallpaper;
use App\Models\RelationSeoProductInfo;
use App\Models\RelationSeoTagInfo;
use Google\Client as Google_Client;
use Illuminate\Support\Facades\DB;

use DOMDocument;

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
        $content = '<h2>Phone Wallpapers of Spider Lily: Discover the Enigmatic and Exquisite Beauty of the Timeless Flower</h2>

    <h3>🌸 What is Spider Lily?</h3>

    <p><strong>Spider Lily</strong>, also known as <strong>Red Spider Lily</strong>, is a flower native to Asia, particularly China and Japan. This flower typically blooms in the autumn and is known for its vivid red color, carrying an enigmatic and enchanting beauty. The shape of the Spider Lily resembles graceful rays of sunlight, creating a magical and captivating impression.</p>

    <p>According to legend, the Spider Lily is also considered a symbol of separation and reunion in the afterlife. This makes the flower even more meaningful, making it stand out and alluring in each wallpaper.</p>

    <h3>📱 Application of Spider Lily Theme in Phone Wallpapers</h3>

    <p><strong>Spider Lily Phone Wallpapers</strong> are not just visually appealing images. With the dominant red color and delicate flower structure, using Spider Lily as a <strong><a href="../../hinh-nen-dien-thoai">phone wallpaper</a></strong> helps bring an enigmatic and profound beauty to your phone screen. Not only does it refresh your digital space, but Spider Lily wallpapers also evoke romantic, gentle, and slightly melancholic emotions, stimulating contemplation and imagination of the viewer.</p>

    <h3>🌼 What are Spider Lily Phone Wallpapers?</h3>

    <p><strong><a href="../../hinh-nen-dien-thoai/hinh-nen-dien-thoai-hoa-bi-ngan">Spider Lily phone wallpapers</a></strong> are a type of wallpaper for mobile phones, where the primary image is the Spider Lily. These wallpapers are often designed with high quality, not only providing aesthetics but also transmitting powerful emotions from the beauty of the Spider Lily.</p>

    <p>They are not limited to static images but can also include dynamic wallpaper collections, offering rich visual experiences and a vibrant atmosphere for your phone.</p>

    <h3>❓ Why are Spider Lily Phone Wallpapers Popular?</h3>

    <p><strong>Spider Lily Phone Wallpapers</strong> are favored for many reasons, here are a few:</p>

    <ul>

    <li><strong>Enigmatic and Captivating Beauty:</strong> The vivid red color of the Spider Lily not only attracts the eye but also creates an enigmatic, inspirational digital space.</li>

    <li><strong>Evoking Memories:</strong> For many people, the Spider Lily holds special meaning, radiating stories, memories, and personal emotions.</li>

    <li><strong>Creating a Highlight:</strong> Using Spider Lily wallpapers makes your phone screen unique, standing out among ordinary images.</li>

    <li><strong>Easy to Change:</strong> You can easily update and change wallpapers according to the season, mood, or personal events, bringing a fresh and exciting feeling.</li>

    </ul>

    <p>Try and experience it now, using Spider Lily phone wallpapers to make your screen more beautiful, distinctive, and meaningful than ever. Create an inspiring and romantic digital space right at your fingertips!
\u{A0}
</p>';
        $tmp = \App\Jobs\AutoTranslateContent::translateSlugBySlugOnData('fr', $content);
        dd($tmp);
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