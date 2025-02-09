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

        // Dữ liệu mẫu để kiểm thử hàm
        $promptText = 'Viết nội dung chuyên sâu cho phần "mở đầu" trong bài viết thuộc danh mục "hình nền điện thoại Hài Hước", với các yêu cầu cụ thể sau:

Vị trí đặt Nội dung:
- Nội dung được hiển thị bên dưới danh sách các bộ sưu tập hình nền trả phí.
Mô tả sản phẩm:
- Mỗi bộ sưu tập hình nền điện thoại Hài Hước gồm 6-8 ảnh chất lượng cao. Tôi không có các công nghệ ảnh động hay gì cao siêu, chỉ cần tập trung nói về sự tâm huyết đầu tư vẻ đẹp, chất lượng, giá trị tinh thần, nghiên cứu tâm lí học, nguồn cảm hứng,... mà sản phẩm mang lại.
- Sản phẩm hướng đến 2 đối tượng khách hàng chính: những người yêu cái đẹp, đam mê sáng tạo, muốn cá nhân hóa điện thoại bằng hình nền Hài Hước (chủ đề yêu thích của họ) và những người tìm kiếm món quà độc đáo, không đụng hàng để tặng người thân.

Độ dài:
- tối thiếu 1800 từ

Yêu cầu về trình bày:
- Sử dụng tiếng Việt chuẩn mực, không pha trộn tiếng Anh không cần thiết
- Dùng từ ngữ phổ thông, dễ hiểu với đại đa số người đọc
- Tránh biệt ngữ, tiếng lóng hoặc từ địa phương
- Sử dụng thẻ HTML đúng cách: <h2>, <h3>, <p> để phân cấp nội dung, <strong><a href="../../"></a></strong> cho tên nền tảng name.com.vn, chọn icon phù hợp với nội dung đang viết và thêm vào trước nội dung trong các thẻ <h3> để gây sự chú ý cao
- Viết các đoạn văn gồm 2-3 câu ngắn gọn, dễ đọc. Các câu trong cùng đoạn phải bổ trợ và diễn giải cho nhau.
- Nội dung giữa các đoạn phải liên kết chặt chẽ và không được trùng lặp ý.
- Sử dụng từ ngữ phù hợp với chủ đề và mang tính thực tiễn. Nhấn mạnh vừa phải về số lượng và chất lượng bằng các từ chỉ số nhiều như "những", "các".

Yêu cầu về giọng văn:
- Sử dụng giọng văn gần gũi với cách xưng hô "chúng tôi - bạn". Thêm các từ ngữ thân thiện ví dụ như: "nhé!", "ngay nhé!",... một cách hợp lý và cảm xúc.
- Cân bằng giữa ngôn ngữ chuyên môn và dễ hiểu. Tránh sử dụng quá nhiều thuật ngữ kỹ thuật.
- Trình bày nội dung với mở đầu thu hút và kết thúc ấn tượng. Chuyển đoạn mạch lạc và truyền cảm hứng tích cực.

Yêu cầu về SEO & trải nghiệm người dùng:
- Tối ưu nội dung: viết như chuyên gia trong lĩnh vực (không như AI viết), thông tin chính xác và cập nhật, phân tích chuyên sâu phù hợp chủ đề, góc nhìn đa dạng thể hiện chiều sâu, nội dung thật hay, cảm xúc và mạch lạc
- Tối ưu từ khóa: tối ưu từ khóa chính "Hình nền điện thoại Hài Hước", từ khóa phụ, từ khóa dài, từ khóa Semantic và từ khóa liên quan nhưng vẫn đảm bảo nội dung tự nhiên, tối ưu cả LSI keywords về chủ đề hình nền một cách tự nhiên, tránh nhồi nhét từ khóa
- Tối ưu chuyển đổi: call-to-action phù hợp chủ đề, đối tượng và gợi ý thực tế về chọn/tải/trải nghiệm hình nền, khuyến khích mua sản phẩm trả phí (cảm xúc, tự nhiên và đặt đúng nơi đúng chỗ - ở cuối của toàn bộ nội dung)

Yêu cầu về kết quả:
- Trả về HTML text hoàn chỉnh với thẻ đúng chuẩn
- Chỉ trả kết quả bài viết, không giải thích thêm

Lưu ý quan trọng:
- Sau khi viết xong, trước khi trả kết quả, hãy đứng ở góc nhìn của độc giả (người đang tìm kiếm hình nền điện thoại Hài Hước) để đọc lại nội dung một lần nữa, tự kiểm tra và sửa lỗi dựa trên những yêu cầu đã nêu để có nội dung hay nhất.

Gợi ý cấu trúc bài viết: (đây chỉ là gợi ý, bạn phải viết sáng tạo cho phù hợp với chủ đề và unique) - ghi chú: tập trung vào dàn ý này thôi, những phần khác tôi viết riêng
<h2>Hình nền điện thoại Hài Hước: Khám phá vẻ đẹp ... và ... của ... Hài Hước ngay trên màn hình điện thoại của bạn</h2> - Hoàn thiện tiêu đề bằng cách chọn những điểm đặc sắc nhất của chủ đề Hài Hước.
<p>mở đầu</p>
<h3>Định nghĩa về Hài Hước?</h3>
<p>Định nghĩa, giải thích rõ ràng về Hài Hước</p>
<p>Những đặc trưng nổi bật, vẻ đẹp, ý nghĩa và sự thu hút của chủ đề này trong lĩnh vực nghệ thuật.</p>
-<h3>Cách nghệ sĩ ứng dụng chủ đề Hài Hước vào hình nền điện thoại</h3>
<p>Dẫn dắt và nói về cách ứng dụng vẻ đẹp, ý nghĩa và sự đặc sắc của chủ đề Hài Hước vào trong hình nền điện thoại</p>
<p>nhấn mạnh đến giá trị mang lại, ý nghĩa, trải nghiệm và tính thẩm mỹ</p>
<p></p>
<h3>Tầm quan trọng của việc trang trí điện thoại bằng hình nền phù hợp</p>
<p>Đưa ra những nghiên cứu (có con số cụ thể) về tác động tinh thần, lợi ích tích cực của việc sử dụng hình nền điện thoại đẹp và phù hợp</p>
<p>đưa ra góc nhìn đa chiều về sự phù hợp của những bộ hình nền điện thoại Hài Hước với các đối tượng người dùng cụ thể.</p>
<p>nhấn mạnh hình nền điện thoại trả phí còn có tác dụng hơn thế nữa bởi được chúng tôi nghiên cứu tâm lí học, chăm chút và thiết kế,...</p>
<p>Kết thúc ngắn gọn, tinh tế cho phần dàn ý này (chứ không phải cho toàn bài) - kèm lời kêu gọi tinh tế</p>';
        $testMessages = [
            ['role' => 'system', 'content' => 'Bạn là một chuyên gia sáng tạo nội dung với phong cách hấp dẫn và sáng tạo. Hãy giúp tôi viết những nội dung độc đáo và thu hút người đọc, với giọng văn thân thiện, dễ hiểu và sáng tạo. Sử dụng ngôn ngữ tự nhiên và tránh lặp từ.'],
            ['role' => 'user', 'content' => $promptText]
        ];
        $options        = [
            'max_tokens'    => 100000,
            // 'stream'        => false,
            // 'temperature' => 0.7, // Cân bằng giữa sáng tạo và tập trung (0-1)
            // 'top_p' => 0.9, // Lấy mẫu từ phần trăm xác suất cao nhất 
            // 'frequency_penalty' => 0.5, // Giảm lặp từ (0-1)
            // 'presence_penalty' => 0.3, // Khuyến khích đề cập chủ đề mới (0-1)
            // 'stop' => ['</html>', '<!--END-->'], // Dừng generate khi gặp các sequence này
            // 'best_of' => 3, // Sinh 3 response và chọn cái tốt nhất (tăng chi phí)
            // 'n' => 1, // Số lượng response trả về
        ];
        $model  = 'deepseek-reasoner';
        // $model      = 'qwen-max';
        // $response = self::chatWithDeepInfra($testMessages, $model, $options);
        // print_r($response);
        // dd($response);

        dd(123);
        
    }

    public static function chatWithDeepInfra(array $messages, string $model = 'deepseek-reasoner', array $options = []) {
        $apiUrl = "https://api.deepseek.com/chat/completions";
        // $apiUrl = "https://dashscope-intl.aliyuncs.com/compatible-mode/v1/chat/completions";
        $apiKey = env('DEEP_SEEK_API_KEY'); // Đặt API key trong file .env
    
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