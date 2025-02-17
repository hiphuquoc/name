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
use App\Jobs\AutoTranslateContent;
use App\Jobs\AutoImproveContent;
use GuzzleHttp\Client;

use Illuminate\Support\Facades\Mail;
use App\Mail\SendProductMail;
use Illuminate\Support\Facades\Http;

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

        // // Dữ liệu mẫu để kiểm thử hàm
        // $promptText = 'tôi có đoạn content cần được cải thiện lại cho hay hơn, theo yêu cầu:
        //                 - đoạn thân của nội dung giữ nguyên nội dung, nhưng những icon trước các thẻ h3, tôi cần bạn chọn lại cho thật đẹp, phù hợp với chủ đề và nội dung của thẻ.
        //                 - đoạn kết viết lại theo mẫu bên dưới:
        //                     <đoạn mẫu>
        //                         <p>Với kho hình nền đa dạng, phong phú chủ đề tại <strong><a href="../../">name.com.vn</a></strong>, chúng tôi tin rằng bạn sẽ dễ dàng tìm thấy những thiết kế ưng ý và phù hợp nhất - dù là để thỏa mãn đam mê cái đẹp hay tìm kiếm một món quà ý nghĩa, độc đáo và đầy cảm xúc. Hãy cùng chúng tôi khám phá ngay nhé!</p>
        //                     </đoạn mẫu>
        //                 - Cuối cùng: trả về toàn bộ nội dung hoàn chỉnh sau khi đã sửa và đừng giải thích gì thêm, để tôi lưu trực tiếp vào cơ sở dữ liệu.

        //                 đoạn content cần sửa:
        //                 <h2>Gợi ý cho bạn những chủ đề hình nền Hoa Thủy Tiên độc đáo và ấn tượng nhất 2025</h2>
        //                 <h3>🌸 Bộ sưu tập "Hoa Thủy Tiên trong ánh bình minh"</h3>
        //                 <p>Bộ sưu tập này là sự kết hợp hoàn hảo giữa vẻ đẹp tinh khôi của hoa Thủy Tiên và ánh sáng dịu dàng của buổi sớm mai. Những cánh hoa mỏng manh được bao phủ bởi những giọt sương long lanh, tạo nên một bức tranh thiên nhiên đầy sức sống.</p>
        //                 <p>Với gam màu pastel nhẹ nhàng, bộ hình nền này đặc biệt phù hợp với những người yêu thích sự thanh lịch, tinh tế. Đây cũng là lựa chọn tuyệt vời cho những ai đang tìm kiếm món quà ý nghĩa dành tặng người thân yêu nhé!</p>
        //                 <h3>🎨 Bộ sưu tập "Nghệ thuật trừu tượng với Hoa Thủy Tiên"</h3>
        //                 <p>Chúng tôi đã khéo léo kết hợp những đường nét nghệ thuật hiện đại với vẻ đẹp tự nhiên của hoa Thủy Tiên để tạo nên bộ sưu tập độc đáo này. Mỗi bức ảnh là một tác phẩm nghệ thuật riêng biệt, nơi những cánh hoa được tái hiện qua góc nhìn sáng tạo.</p>
        //                 <p>Những ai đam mê nghệ thuật và muốn thể hiện cá tính riêng chắc chắn sẽ yêu thích bộ hình nền này. Đây cũng là lựa chọn hoàn hảo cho những người làm việc trong lĩnh vực sáng tạo đấy!</p>
        //                 <h3>✨ Bộ sưu tập "Hoa Thủy Tiên dưới ánh đèn nghệ thuật"</h3>
        //                 <p>Khi màn đêm buông xuống, những bông hoa Thủy Tiên như được thổi hồn qua ánh đèn nghệ thuật lung linh. Bộ sưu tập này ghi lại những khoảnh khắc kỳ diệu ấy, tạo nên những bức hình nền điện thoại Hoa Thủy Tiên đẳng cấp.</p>
        //                 <p>Với hiệu ứng ánh sáng độc đáo, bộ hình nền này rất phù hợp với những người yêu thích phong cách hiện đại, sang trọng. Đặc biệt, đây sẽ là món quà tuyệt vời cho những ai đang tìm kiếm điều gì đó thật đặc biệt ngay nhé!</p>
        //                 <h3>🌿 Bộ sưu tập "Hoa Thủy Tiên trong vườn xuân"</h3>
        //                 <p>Hình ảnh những bông hoa Thủy Tiên khoe sắc giữa khu vườn mùa xuân tạo nên một không gian tươi mát, trong lành. Bộ sưu tập này mang đến cảm giác thư thái, gần gũi với thiên nhiên cho người sử dụng.</p>
        //                 <p>Những người yêu thích sự đơn giản nhưng vẫn toát lên vẻ đẹp tinh tế sẽ tìm thấy niềm vui khi sở hữu bộ hình nền này. Đây cũng là lựa chọn lý tưởng cho những ai đang tìm kiếm món quà ý nghĩa dành tặng người thân yêu đấy!</p>
        //                 <h3>💎 Bộ sưu tập "Hoa Thủy Tiên cao cấp - Đẳng cấp hoàng gia"</h3>
        //                 <p>Với kỹ thuật chụp chuyên nghiệp và xử lý màu sắc tinh tế, bộ sưu tập này tôn vinh vẻ đẹp quý phái của hoa Thủy Tiên. Mỗi bức ảnh đều được chăm chút tỉ mỉ, tạo nên những tác phẩm hình nền điện thoại Hoa Thủy Tiên chất lượng cao.</p>
        //                 <p>Đây là lựa chọn hoàn hảo cho những người yêu cái đẹp và mong muốn thể hiện đẳng cấp riêng. Bộ hình nền này cũng rất phù hợp để làm quà tặng cho những dịp đặc biệt, chắc chắn sẽ khiến người nhận cảm thấy hạnh phúc ngay nhé!</p>
        //                 <h3>🌌 Bộ sưu tập "Hoa Thủy Tiên trong vũ điệu ánh sáng"</h3>
        //                 <p>Bộ sưu tập này khám phá vẻ đẹp của hoa Thủy Tiên qua những hiệu ứng ánh sáng độc đáo. Những cánh hoa như đang hòa mình vào vũ điệu của ánh sáng, tạo nên những bức hình nền đầy mê hoặc.</p>
        //                 <p>Với phong cách hiện đại và khác biệt, bộ hình nền này thu hút những người trẻ năng động, sáng tạo. Đây cũng là lựa chọn thú vị cho những ai muốn tạo điểm nhấn riêng cho chiếc điện thoại của mình đấy!</p>
        //                 <h3>💧 Bộ sưu tập "Hoa Thủy Tiên và giọt sương mai"</h3>
        //                 <p>Những giọt sương long lanh trên cánh hoa Thủy Tiên được ghi lại một cách tinh tế, tạo nên bộ sưu tập hình nền điện thoại Hoa Thủy Tiên đỉnh cao về mặt thẩm mỹ. Mỗi bức ảnh đều mang đến cảm giác trong lành, tươi mới.</p>
        //                 <p>Bộ hình nền này đặc biệt phù hợp với những người yêu thích sự tinh khiết, giản dị. Đây cũng là món quà ý nghĩa dành tặng những người thân yêu, giúp họ bắt đầu ngày mới với năng lượng tích cực ngay nhé!</p>
        //                 <h3>🍂 Bộ sưu tập "Hoa Thủy Tiên mùa thu"</h3>
        //                 <p>Khi mùa thu đến, những bông hoa Thủy Tiên mang một vẻ đẹp trầm mặc, sâu lắng. Bộ sưu tập này ghi lại những khoảnh khắc đặc biệt ấy, tạo nên những bức hình nền đầy cảm xúc.</p>
        //                 <p>Những người yêu thích sự lãng mạn, hoài cổ sẽ tìm thấy sự đồng điệu trong bộ hình nền này. Đây cũng là lựa chọn tuyệt vời cho những ai đang tìm kiếm món quà độc đáo dành tặng người thân đấy!</p>
        //                 <h3>🌟 Bộ sưu tập "Hoa Thủy Tiên dưới ánh sao đêm"</h3>
        //                 <p>Vẻ đẹp huyền bí của hoa Thủy Tiên được tôn lên dưới bầu trời đầy sao. Bộ sưu tập này mang đến những bức hình nền điện thoại Hoa Thủy Tiên chất lượng cao với không gian lung linh, huyền ảo.</p>
        //                 <p>Những người yêu thích sự lãng mạn và bí ẩn chắc chắn sẽ bị cuốn hút bởi bộ hình nền này. Đây cũng là món quà ý nghĩa dành tặng những người thân yêu, giúp họ luôn cảm thấy ấm áp ngay nhé!</p>
        //                 <h3>🌺 Bộ sưu tập "Hoa Thủy Tiên đa sắc màu"</h3>
        //                 <p>Khám phá vẻ đẹp đa dạng của hoa Thủy Tiên qua bộ sưu tập này. Từ trắng tinh khôi đến hồng pastel, mỗi màu sắc đều được thể hiện một cách trọn vẹn và sống động.</p>
        //                 <p>Những người yêu thích sự đa dạng và muốn thay đổi thường xuyên sẽ tìm thấy niềm vui khi sở hữu bộ hình nền này. Đây cũng là lựa chọn thú vị cho những ai đang tìm kiếm món quà độc đáo dành tặng người thân đấy!</p>
        //                 <p>Tại <strong><a href="../../">name.com.vn</a></strong>, chúng tôi tự hào mang đến kho hình nền điện thoại Hoa Thủy Tiên đa dạng và phong phú, đáp ứng mọi nhu cầu của người dùng. Dù bạn là người khó tính đến đâu, chắc chắn cũng sẽ tìm thấy những bộ sưu tập ưng ý trong thế giới hình nền của chúng tôi ngay nhé!</p>
        //                 ';
        // $testMessages = [
        //     ['role' => 'system', 'content' => 'Bạn là một chuyên gia sáng tạo nội dung với phong cách hấp dẫn và sáng tạo. Hãy giúp tôi viết những nội dung độc đáo và thu hút người đọc, với giọng văn thân thiện, dễ hiểu và sáng tạo. Sử dụng ngôn ngữ tự nhiên và tránh lặp từ.'],
        //     ['role' => 'user', 'content' => $promptText]
        // ];
        // $options        = [
        //     // 'max_tokens'    => 100000,
        //     // 'stream'        => false,
        //     // 'temperature' => 0.7, // Cân bằng giữa sáng tạo và tập trung (0-1)
        //     // 'top_p' => 0.9, // Lấy mẫu từ phần trăm xác suất cao nhất 
        //     // 'frequency_penalty' => 0.5, // Giảm lặp từ (0-1)
        //     // 'presence_penalty' => 0.3, // Khuyến khích đề cập chủ đề mới (0-1)
        //     // 'stop' => ['</html>', '<!--END-->'], // Dừng generate khi gặp các sequence này
        //     // 'best_of' => 3, // Sinh 3 response và chọn cái tốt nhất (tăng chi phí)
        //     // 'n' => 1, // Số lượng response trả về
        // ];
        // // $model  = 'deepseek-reasoner';
        // $model      = 'qwen-max';
        // $response = self::chatWithAI($testMessages, $model, $options);
        // print_r($response);
        // dd($response);


        // $tags = Tag::select('*')
        //             ->where('id', '>=', 734)
        //             ->orderBy('id', 'DESC')
        //             ->get();
        
        // $arrayNotTranslate = ['vi', 'en'];
                    
        // foreach($tags as $tag){
        //     $idSeo = $tag->seo->id ?? 0;
        //     if(!empty($idSeo)){
        //         foreach($tag->seos as $seo){
        //             if(!empty($seo->infoSeo->language)&&!in_array($seo->infoSeo->language, $arrayNotTranslate)){
        //                 AutoTranslateContent::dispatch(8, $seo->infoSeo->language, $idSeo, 3);
        //             }
        //         }
        //     }
        // }

        // $tags = Tag::select('*')
        //             ->where('id', '<', 729)
        //             ->orderBy('id', 'DESC')
        //             ->get();

        // foreach($tags as $tag){
        //     $idSeo = $tag->seo->id ?? 0;
        //     if(!empty($idSeo)){
        //         $request = new Request(['seo_id' => $idSeo]);
        //         TranslateController::createJobWriteContent($request);
        //     }
        // }

        // dd(123);


        $tags = Tag::select('*')
                    ->whereNotIn('id', [737, 744])
                    ->orderBy('id', 'DESC')
                    ->get();
        
        $arrayOrdering = [1, 2, 3, 4, 5, 8];
                    
        foreach($tags as $tag){

            $idSeo = 0;
            foreach($tag->seos as $seo){
                if(!empty($seo->infoSeo->language)&&$seo->infoSeo->language=='vi'){
                    $idSeo = $seo->infoSeo->id;
                    break;
                }
            }
            if(!empty($idSeo)){
                foreach($arrayOrdering as $ordering){
                    AutoImproveContent::dispatch($ordering, $idSeo);
                }
            }
        }

        dd(123);
        
    }

    public static function chatWithAI(array $messages, string $model = 'deepseek-reasoner', array $options = []) {
        // $apiUrl = "https://api.deepseek.com/chat/completions";
        $apiUrl = "https://dashscope-intl.aliyuncs.com/compatible-mode/v1/chat/completions";
        $apiKey = env('QWEN_API_KEY'); // Đặt API key trong file .env
    
        $payload = array_merge([
            'model' => $model,
            'messages' => $messages,
        ], $options);
    
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => "Bearer $apiKey",
        ])->timeout(3060)->post($apiUrl, $payload);
    
        if ($response->successful()) {
            return $response->json();
        }
        
        return ['error' => 'Failed to connect to DeepInfra API', 'status' => $response->status(), 'body' => $response->body()];
    }

    private static function normalizeUnicode($string) {
        return \Normalizer::normalize($string, \Normalizer::FORM_C);
    }

    public static function callAPIClaudeAI(Request $request){

        // Cấu hình Guzzle client
        $client = new Client();

        // Lấy API key từ .env
        $apiKey = env('CLAUDE_AI_API_KEY');

        // Dữ liệu bạn muốn gửi đến Claude AI API
        $data = [
            'model' => 'claude-3-5-sonnet-20241022',
            'max_tokens' => 1024,
            'messages' => [
                ['role' => 'user', 'content' => '1 + 1 bằng mấy'], 
            ],
        ];

        // Gửi yêu cầu POST đến Claude AI API
        $response = $client->post('https://api.anthropic.com/v1/messages', [
            'headers' => [
                'x-api-key' => $apiKey,
                'anthropic-version' => '2023-06-01',
                'content-type' => 'application/json',
            ],
            'json' => $data,
        ]);

        // Trả về kết quả từ API dưới dạng JSON
        $result = response()->json(json_decode($response->getBody()->getContents(), true));

        dd($result);
        
    }

    private static function findUniqueElements($arr1, $arr2) {
        // Lọc các phần tử có trong arr1 nhưng không có trong arr2 và ngược lại
        $uniqueInArr1 = array_diff($arr1, $arr2);
        $uniqueInArr2 = array_diff($arr2, $arr1);
        
        // Kết hợp các phần tử không trùng
        return array_merge($uniqueInArr1, $uniqueInArr2);
    }
}