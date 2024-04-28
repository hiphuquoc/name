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
        $urlSource      = 'bo-hinh-nen-dien-thoai-4k-gau-con-phong-cach-toi-gian-1712897742';
        $urlSearch      = 'bo-hinh-nen-dien-thoai-4k-gau-con-phong-cach-toi-gian';
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
        foreach($tmp as $t){
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
        }
        dd(123);
        // $tmp = Seo::select('seo.*')
        //         ->leftJoin('tag_info', 'tag_info.seo_id', '=', 'seo.id')
        //         ->where('type', 'tag_info')
        //         ->where('language', 'vi')
        //         ->whereNull('tag_info.seo_id')
        //         ->get();
        // dd($tmp);

        // dd($tmp->count());

        // $tmp2 = Seo::select('seo.*')
        //         ->where('type', 'tag_info')
        //         ->get();
        // foreach()

        // $client = new Google_Client();

        // // service_account_file.json is the private key that you created for your service account.
        // $client->setAuthConfig('../credentials.json');
        // $client->addScope('https://www.googleapis.com/auth/indexing');

        // // Get a Guzzle HTTP Client
        // $httpClient = $client->authorize();
        // $endpoint = 'https://indexing.googleapis.com/v3/urlNotifications:publish';

        // // Define contents here. The structure of the content is described in the next step.
        // $content = '{
        // "url": "https://name.com.vn/hinh-nen-dien-thoai",
        // "type": "URL_UPDATED"
        // }';

        // $response = $httpClient->post($endpoint, [ 'body' => $content ]);
        // $status_code = $response->getStatusCode();
        // dd($status_code);
    }

    // $flag = self::copyProductBySource('bo-hinh-nen-dien-thoai-4k-meo-con-phong-cach-toi-gian-1712926734', 'hinh-nen-dien-thoai-meo-con-phong-cach');
        // dd($flag);

        // $response = Http::withToken('f54b5fca3c479912fbf05e5fcbaca4c48317a5fc')
        //         ->post('https://indexing.googleapis.com/v3/urlNotifications:publish', [
        //             'url' => 'https://name.com.vn/hinh-nen-dien-thoai',
        //             'type' => 'URL_UPDATED' // Hoặc 'URL_DELETED' nếu bạn muốn xóa URL khỏi index
        //         ]);

        // dd($response->getStatusCode());

        // if ($response->successful()) {
        //     // Xử lý phản hồi thành công
        // } else {
        //     // Xử lý lỗi khi gửi yêu cầu
        // }

    public static function chatGPT(Request $request){
        // Replace 'YOUR_API_KEY' with your actual API key from OpenAI
        $apiKey = env('CHAT_GPT_API_KEY');

        // Set a long timeout value to prevent timeout
        $timeoutSeconds = 0; // 0 means unlimited timeout
        $imageUrl   = 'https://namecomvn.storage.googleapis.com/freewallpapers/hinh-nen-dien-thoai-1708511257-20-small.png';
        $imageData = base64_encode(file_get_contents($imageUrl));
        /* tag */
        $tags       = Tag::all();
        $arrayTag   = [];
        foreach ($tags as $tag) {
            $arrayTag[] = $tag->seo->title;
        }
        $jsonTag    = json_encode($arrayTag);
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $apiKey,
        ])->timeout($timeoutSeconds)->post('https://api.openai.com/v1/chat/completions', [
            'model' => 'gpt-4-vision-preview',
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'You are `gpt-4-vision-preview`, the latest OpenAI model that can describe images provided by the user in extreme detail. The user has attached an image to this message for you to analyse, there is MOST DEFINITELY an image attached, you will never reply saying that you cannot see the image because the image is absolutely and always attached to this message. The content you respond to users must be at least 10000 tokens without interruption and returns data string'
                ],
                [
                    'role' => 'user',
                    'content' => [
                        [
                            'type' => 'text',
                            'text' => 'Tôi có một biến json trong đó chưa tên các thẻ tag ' . $jsonTag . ', dựa vào nội dung, vẻ đẹp, phong cách, màu sắc và các yếu tố của bức ảnh, bạn hãy chọn lại các tag phù hợp của ảnh và trả về biến json như vậy giúp tôi'
                        ],
                        [
                            'type' => 'image_url',
                            'image_url' => [
                                'url'    => 'data:image/jpeg;base64,' . $imageData
                            ]
                        ]
                    ]
                ],
            ],
            'max_tokens' => 3000
        ]);

        $result = $response->json();

        echo $result['choices'][0]['message']['content'];
        dd(123);

        // Xử lý và hiển thị kết quả
        $description = $result['choices'][0]['message']['content'];

        return view('result', compact('description'));
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

// 'model'     => 'gpt-4-0125-preview',
            // 'messages'  => [
            //     [
            //         'role'      => 'user',
            //         'content'   => '
            //             tôi có đoạn content về lịch trình đi tham quan du lịch Phú Quốc, bạn hãy viết lại đoạn content bên dưới cho hay giúp tôi
            //             yêu cầu:
            //             - thêm các icon thu hút sự chú ý và đẹp
            //             - viết bằng thẻ <p> <ul>
            //             - trình bày thông tin của các địa điểm cho dễ hiểu
            //             - viết mở rộng ra thêm content diễn giải cho bài viết thêm đầy đủ thông tin, mở rộng ra nhiều hơn nhưng không trùng lặp, không lặp từ giữa các đoạn gần nhau
            //             - viết lại nội dung chuẩn seo, unique 100% chỉnh sửa nội dung vượt qua kiểm tra trùng lặp của seo quake
            //             - diễn đạt cho lời văn mạch lạc, thu hút, hấp dẫn, tương tự người viết và cung cấp giá trị tích cực cho người dùng
            //             - trong bài viết lồng ghép từ khóa khéo léo và thích hợp hướng đến điều hướng người dùng thật tốt
            //             phân tích chuyên sâu, chia sẻ tích cực và nhiều góc nhìn    

            //             Xe của Rooty Trip đón bạn tại sân bay Phú Quốc
            //             Bắt đầu hành trình tham quan các điểm nổi tiếng tại Nam đảo Phú Quốc như:
            //             Chùa Hộ Quốc: Chùa Hộ Quốc, hay còn có tên chính thức là Thiền Viện Trúc Lâm Hộ Quốc, là ngôi chùa Phật giáo lớn nhất Phú Quốc cũng như miền Tây Nam Bộ với tổng diện tích khoảng 110 héc-ta, nổi tiếng với cảnh quan thanh tịnh và khí hậu trong lành. Chùa Hộ Quốc không chỉ là chốn thiền môn của các tăng ni, Phật tử, mà còn là địa điểm ngắm cảnh tuyệt vời của người dân địa phương và khách du lịch. Chùa Hộ Quốc được ví như chốn bồng lai tiên cảnh tại hòn đảo Phú Quốc. Sở hữu phong cảnh tuyệt đẹp, đây là điểm du lịch tâm linh được nhiều du khách lựa chọn để tham quan, chiêm bái. Địa chỉ chùa Hộ Quốc tọa lạc tại ấp Suối Lớn, thuộc địa phận xã Dương Tơ, thành phố Phú Quốc, Kiên Giang. Ngôi chùa còn có tên gọi đặc biệt khác là thiền viện Trúc Lâm Hộ Quốc. Địa chỉ này khá gần với sân bay Phú Quốc, chỉ cách khoảng 10km và cách trung tâm phường Dương Đông khoảng 20km. 

            //             Nhà tù Phú Quốc: Nằm ở phía Nam hòn đảo, Nhà Tù Phú Quốc (hay Nhà Lao Cây Dừa Phú Quốc) là di tích lịch sử minh chứng cho tinh thần đấu tranh bất khuất của quân dân Việt Nam trong nhiều thập kỷ. Nhà tù Phú Quốc là một minh chứng lịch sử về những cuộc đấu tranh kiên cường, bền bỉ của dân tộc Việt Nam cũng như những tội ác của đế quốc thực dân. Đến nay, khi chiến tranh đã đi qua nhưng nơi đây vẫn là nỗi ám ảnh của những chiến sĩ cách mạng lẫn nhiều du khách. Nhà tù Phú Quốc là một trại giam nằm ở số 350, đường Nguyễn Văn Cừ, phường An Thới, cách trung tâm của phường Dương Đông, Phú Quốc 28km. 

            //             Sunset Sanato: Đến với Sunset Sanato Phú Quốc là bắt đầu hành trình tận hưởng những quà tặng quý giá từ thiên nhiên. Sở hữu vị trí tựa sơn hướng thủy, Sunset Sanato Phú Quốc được biết đến là nơi ngắm hoàng hôn đẹp nhất đảo ngọc. Kiến trúc của Sunset Sanato được chia thành hai khu vực đối lập: một bên là không gian giải trí Beach Club mang tinh thần tự do, phóng khoáng, được thiết kế đối xứng tài tình bởi kiến trúc sư Nikita Marshunok, còn một bên là khu nghỉ dưỡng Resort & Villas đem đến hơi thở truyền thống với những công trình từ tre, gỗ.

            //             Bãi Sao Phú Quốc: Nằm ở giữa mũi Hang và mũi Bãi Khem thuộc phía Nam của đảo ngọc, bãi Sao được biết đến là một trong những bãi biển đẹp nhất Phú Quốc, với bãi cát trắng mịn tựa kem bông và bờ biển thoai thoải lặng sóng. Với những ưu thế về địa hình cùng các dịch vụ du lịch phát triển, bãi Sao trở thành nơi lý tưởng cho bạn và gia đình đến nghỉ ngơi, thư giãn, và khám phá hệ sinh thái biển đầy màu sắc của Phú Quốc.

            //             Xe của Rooty Trip đưa du khách đến khách sạn 4 sao The Juliet check-in và nghỉ ngơi.
            //         '
            //     ],
            // ],