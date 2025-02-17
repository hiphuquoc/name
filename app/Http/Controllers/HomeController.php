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
        //     - đoạn mở đầu tách ra 3 câu cho dễ đọc, rõ nghĩa từng câu, lời dẫn thật hay, cuốn hút và hợp lí. quan trọng dẫn dắt liên quan đến vẻ đẹp của chủ đề. rõ nghĩa từng câu bao gồm: 1 câu đặt câu hỏi để dẫn, 1 câu nói nếu khách hàng là người như thế nào, thì vẻ đẹp của hình nền điện thoại này sẽ phù hợp với họ như thế nào, 1 câu mời họ bước vào khám phá chủ đề. mãu gợi ý bên dưới - bạn hãy dựa vào đó mà sáng tạo, sửa lại đoạn mở đầu cho phù hợp với chủ đề và thật cuốn hút, đoạn gợi ý:
        //         <gợi ý>
        //             <h2>Hình nền điện thoại Hip Hop: Khám phá vẻ đẹp Nghệ Thuật và Phong Cách của văn hóa Hip Hop ngay trên màn hình điện thoại của bạn</h2>
        //             <p>Bạn có biết, mỗi lần mở điện thoại cũng giống như mở ra một cánh cửa nhỏ dẫn đến thế giới riêng của chính mình?</p>
        //             <p>Và nếu bạn là người yêu thích sự sáng tạo, đam mê cái đẹp và trân trọng những giá trị nghệ thuật độc đáo, thì các bộ sưu tập <strong><a href="../../hinh-nen-dien-thoai/hinh-nen-dien-thoai-hip-hop">hình nền điện thoại Hip Hop</a></strong> mà chúng tôi mang đến chắc chắn sẽ khiến bạn cảm thấy vô cùng hứng thú - đây không chỉ đơn thuần là những bức ảnh đẹp mắt, mà còn là cả một câu chuyện về tinh thần tự do, cá tính mạnh mẽ và nguồn cảm hứng bất tận được gửi gắm qua từng chi tiết.</p>
        //             <p>Hãy để chúng tôi đồng hành cùng bạn trong hành trình khám phá những giá trị thẩm mỹ đỉnh cao, nơi mà mỗi bức ảnh đều kể câu chuyện riêng về sự đẳng cấp và phong cách đỉnh cao nhé!</p>
        //         </gợi ý>
        //         + đặt 1 link ở phần mở đầu <strong><a href="../../hinh-nen-dien-thoai/hinh-nen-dien-thoai-hip-hop">hình nền điện thoại Hip Hop</a></strong>, hoặc đa dạng các biến thể từ khóa dài kết hợp với "chất lượng cao", "cao cấp".
        //     - đoạn định nghĩa, phần nội dung bên dưới thẻ h3 bạn hãy viết lại 2 đoạn này, mở rộng cho hay hơn, định nghĩa và chú trọng nói về vể đẹp của chủ đề, không cần nói về sản phẩm của tôi chỗ này.
        //     - đoạn nói về cách nghệ sĩ ứng dụng .... tách ra 2 đoạn riêng biệt, 1 đoạn nói về sự sáng tạo của nghệ sĩ trong việc ứng dụng vẻ đẹp của chủ đề vào thiết kế hình nền điện thoại, 1 đoạn hãy nói nhiều về sự đầu tư, nghiên cứu tâm lí học, ứng dụng và gian nan như thế nào để có những tác phẩm nghệ thuật ấn tượng. đặt 1 link ở chỗ nào hợp lí trong phần này <strong><a href="../../hinh-nen-dien-thoai">hình nền điện thoại</a></strong>, hoặc đa dạng các biến thể từ khóa dài kết hợp với "chất lượng cao", "cao cấp".
        //     - đoạn nói về tầm quan trọng của hình việc trang trí bằng hình nền đẹp và phù hợp cải thiện lại theo yêu cầu bên dưới:
        //         + ở đoạn nói về những bộ sưu tập chất lượng của tôi (số nhiều), viết lại để nhấn mạnh và nói nhiều hơn nữa về vẻ đẹp, lợi ích và chất lượng của các bộ hình nền cao cấp và đặt 1 link  <strong><a href="../../hinh-nen-dien-thoai/hinh-nen-dien-thoai-hip-hop">hình nền điện thoại Hip Hop</a></strong>, hoặc đa dạng các biến thể từ khóa dài kết hợp với "chất lượng cao", "cao cấp". lưu ý đa dạng đừng trùng với anchor text phần mở đầu.
        //         + ở đoạn cuối, viết lại cho thật hay và cuốn hút (chỗ vẽ viễn cảnh để khách hàng tưởng tượng), thêm cảm thán phù hợp ở cuối đoạn để cho thân thiện và cảm xúc (nhưng ưu tiên cảm xúc nhẹ nhàng, đừng quá kích thích).
        //     - Cuối cùng: trả về toàn bộ nội dung hoàn chỉnh sau khi đã sửa và đừng giải thích gì thêm, để tôi lưu trực tiếp vào cơ sở dữ liệu.

        //     đoạn content cần sửa:
        //     <h2>Hình nền điện thoại Công Sở: Khám phá vẻ đẹp tinh tế và đẳng cấp của không gian làm việc ngay trên màn hình điện thoại của bạn</h2>
        //     <p>Bạn có biết rằng chiếc điện thoại của mình không chỉ là công cụ liên lạc, mà còn là một không gian nghệ thuật thu nhỏ? Những bộ sưu tập hình nền điện thoại Công Sở mà chúng tôi mang đến chính là cầu nối hoàn hảo giữa thế giới công việc chuyên nghiệp và niềm đam mê cái đẹp. Hãy để chúng tôi đồng hành cùng bạn trong hành trình khám phá những giá trị thẩm mỹ đỉnh cao, nơi mà mỗi bức ảnh đều kể câu chuyện riêng về sự sáng tạo và đẳng cấp nhé!</p>
        //     <h3>💼 Định nghĩa về Công Sở?</h3>
        //     <p>Công Sở không đơn thuần chỉ là nơi làm việc, mà còn là biểu tượng của sự chuyên nghiệp, kỷ luật và tinh thần sáng tạo không ngừng nghỉ. Đây là không gian đặc biệt, nơi những ý tưởng lớn được thai nghén và hiện thực hóa thành những thành quả đáng tự hào.</p>
        //     <p>Với những đường nét kiến trúc hiện đại, nội thất sang trọng cùng bầu không khí năng động, chủ đề Công Sở đã trở thành nguồn cảm hứng bất tận cho các nghệ sĩ và nhà thiết kế. Mỗi góc nhìn đều ẩn chứa vẻ đẹp riêng, từ bàn làm việc ngăn nắp đến khung cửa sổ rộng mở hướng ra thành phố nhộn nhịp.</p>
        //     <h3>🎨 Cách nghệ sĩ ứng dụng chủ đề Công Sở vào hình nền điện thoại</h3>
        //     <p>Chúng tôi đã dành nhiều tâm huyết để biến những khoảnh khắc bình dị của không gian văn phòng thành những tác phẩm nghệ thuật độc đáo. Mỗi bức hình nền đều là kết tinh của quá trình nghiên cứu tỉ mỉ về ánh sáng, bố cục và màu sắc - những yếu tố then chốt tạo nên vẻ đẹp hoàn mỹ.</p>
        //     <p>Đặc biệt, các bộ sưu tập hình nền điện thoại Công Sở được thiết kế với độ phân giải cao, đảm bảo từng chi tiết nhỏ nhất đều được thể hiện rõ nét trên màn hình của bạn. Từ những vật dụng văn phòng quen thuộc đến khung cảnh thành phố hiện đại qua ô cửa sổ, tất cả đều được chăm chút kỹ lưỡng để mang đến trải nghiệm thị giác tuyệt vời nhất.</p>
        //     <h3>🌟 Tầm quan trọng của việc trang trí điện thoại bằng hình nền phù hợp</h3>
        //     <p>Theo nghiên cứu của Đại học Harvard, việc sử dụng hình ảnh tích cực làm hình nền điện thoại có thể cải thiện tâm trạng lên đến 40% và tăng hiệu suất làm việc khoảng 25%. Điều này cho thấy tầm quan trọng của việc lựa chọn hình nền phù hợp với cá tính và phong cách sống của mỗi người.</p>
        //     <p>Những bộ hình nền điện thoại Công Sở trả phí của chúng tôi không chỉ đơn thuần là những bức ảnh đẹp. Chúng được phát triển dựa trên nghiên cứu tâm lý học sâu rộng, nhằm mang đến những giá trị tinh thần tích cực cho người dùng. Mỗi bộ sưu tập đều được thiết kế để truyền cảm hứng, tạo động lực và phản ánh đúng phong cách sống chuyên nghiệp của người sở hữu.</p>
        //     <p>Hãy tưởng tượng mỗi lần mở điện thoại, bạn đều được chào đón bởi một không gian nghệ thuật thu nhỏ, nơi mà sự sáng tạo và chuyên nghiệp hòa quyện trong từng khung hình. Đó không chỉ là hình nền, mà còn là nguồn cảm hứng bất tận cho những ý tưởng mới, là món quà tinh thần quý giá giúp bạn luôn giữ được tinh thần phấn chấn trong công việc và cuộc sống.</p>

        // ';
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

        // foreach($tags as $tag){
        //     $idSeo = $tag->seo->id ?? 0;
        //     if(!empty($idSeo)){
        //         $request = new Request(['seo_id' => $idSeo]);
        //         TranslateController::createJobWriteContent($request);
        //     }
        // }

        // dd(123);


        $tags = Tag::select('*')
                    ->where('id', '<', 729)
                    ->orderBy('id', 'DESC')
                    ->get();
        
        // $arrayNotTranslate = ['vi', 'en'];
                    
        foreach($tags as $tag){

            $idSeo = 0;
            foreach($tag->seos as $seo){
                if(!empty($seo->infoSeo->language)&&$seo->infoSeo->language=='vi'){
                    $idSeo = $seo->infoSeo->id;
                    break;
                }
            }
            if(!empty($idSeo)){
                AutoImproveContent::dispatch(1, $idSeo);
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