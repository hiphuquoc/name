<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\Models\Seo;
use App\Models\Prompt;
use App\Models\SeoContent;
use App\Http\Controllers\HomeController;

class TranslateQuestionProduct implements ShouldQueue {
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $infoLanguage;
    public  $tries = 5; // Số lần thử lại

    public function __construct($infoLanguage){
        $this->infoLanguage     = $infoLanguage;
    }

    public function handle(){
        try {
            $model              = 'qwen-max';
            $options            = [];
            $configFilePath     = base_path('config/data_question_product.php');
            $promptText     = 'dịch array này ra ngôn ngữ '.$this->infoLanguage['key'].' - '.$this->infoLanguage['name'].' giúp tôi, yêu cầu:
                                    - dịch hay, chuẩn ngôn ngữ và văn phong địa phương
                                    - trình bày trong array như vậy
                                    - chỉ cần trả về array tôi cần, đừng giải thích gì thêm và cũng đừng thêm những ký tự đặc biệt
            
                                "vi" => [
                                    [
                                        "question" => "Hình nền có độ phân giải và dung lượng bao nhiêu?",
                                        "answer" => "Mỗi hình nền trong bộ sưu tập đều được thiết kế với độ phân giải cao, đảm bảo chất lượng hình ảnh sắc nét, sống động và chi tiết trên mọi màn hình điện thoại. Để biết thông tin cụ thể về độ phân giải của từng ảnh, bạn có thể xem ở bảng <strong><a href=\"#detailPerWallpaper\">chi tiết từng hình nền</a></strong>"
                                    ],
                                    [
                                        "question" => "Hình nền này có tương thích với mọi loại điện thoại và đã được tối ưu cho các dòng điện thoại mới nhất chưa?",
                                        "answer" => "Chắc chắn rồi! Bộ hình nền này được thiết kế để tương thích hoàn hảo với mọi dòng điện thoại thông minh hiện nay, bao gồm iPhone, Samsung, Xiaomi, Oppo, Vivo, Realme, Huawei và tất cả các thiết bị Android khác. Chúng tôi đã tối ưu hóa từng hình ảnh để đảm bảo hiển thị mượt mà, sắc nét trên mọi kích thước màn hình, kể cả các dòng điện thoại mới nhất với công nghệ hiển thị tiên tiến. Dù bạn sử dụng thiết bị nào, hình nền vẫn sẽ tự động điều chỉnh để mang đến trải nghiệm hình ảnh ấn tượng nhất."
                                    ],
                                    [
                                        "question" => "Hình nền được cung cấp ở định dạng file nào?",
                                        "answer" => "Tất cả hình nền trong bộ sưu tập được cung cấp dưới định dạng PNG chất lượng cao. Đây là định dạng lý tưởng để giữ cho màu sắc hiển thị chân thực, không bị viền hay mất chi tiết, đồng thời duy trì vẻ đẹp nguyên bản của hình ảnh trên mọi loại màn hình. Với PNG, bạn có thể yên tâm rằng mỗi hình nền sẽ luôn rực rỡ và rõ nét, bất kể thiết bị bạn sử dụng."
                                    ],
                                    [
                                        "question" => "Làm thế nào để tôi tải hình nền sau khi mua?",
                                        "answer" => "Ngay sau khi hoàn tất thanh toán, bạn sẽ nhận được một đường dẫn tải trực tiếp để truy cập và tải xuống các hình nền gốc với chất lượng cao nhất. Đồng thời, để tiện lợi hơn cho việc lưu trữ và sử dụng, chúng tôi cũng sẽ gửi một bản sao của bộ hình nền qua email của bạn. Quy trình đơn giản này đảm bảo bạn có thể nhanh chóng sở hữu và cài đặt hình nền mà không gặp bất kỳ khó khăn nào."
                                    ],
                                    [
                                        "question" => "Tôi sẽ nhận được hình nền trong bao lâu sau khi mua?",
                                        "answer" => "Bạn sẽ nhận được hình nền ngay lập tức sau khi hoàn tất thanh toán. Đường dẫn tải sẽ xuất hiện trên trang xác nhận đơn hàng, và email chứa bản sao hình nền cũng sẽ được gửi đến bạn chỉ trong vòng vài phút. Với tốc độ này, bạn có thể bắt đầu sử dụng hình nền yêu thích mà không cần chờ đợi."
                                    ],
                                    [
                                        "question" => "Có hỗ trợ kỹ thuật nếu tôi gặp vấn đề khi tải hoặc sử dụng hình nền không?",
                                        "answer" => "Có! Chúng tôi cung cấp dịch vụ hỗ trợ kỹ thuật 24/7 tận tâm để đảm bảo bạn có trải nghiệm tuyệt vời nhất. Đội ngũ chuyên viên của chúng tôi luôn sẵn sàng hỗ trợ bạn qua hệ thống chat trực tuyến hoặc chatbot AI thông minh, giải đáp mọi thắc mắc và xử lý nhanh chóng bất kỳ vấn đề nào bạn gặp phải trong quá trình tải hoặc sử dụng hình nền."
                                    ],
                                    [
                                        "question" => "Tôi có nhận được cập nhật hoặc phiên bản mới của hình nền sau khi mua không?",
                                        "answer" => "Chắc chắn có! Khi sở hữu bộ hình nền này, bạn sẽ được hưởng đặc quyền cập nhật miễn phí trọn đời. Mỗi khi có phiên bản hình ảnh mới hoặc các tối ưu hóa để tương thích với các dòng điện thoại sắp ra mắt, chúng tôi sẽ thông báo qua email và cung cấp bản cập nhật hoàn toàn miễn phí. Điều này đảm bảo bạn luôn sở hữu những hình nền đẹp nhất, hiện đại nhất mà không cần chi trả thêm bất kỳ chi phí nào."
                                    ]
                                ],';
            $testMessages = [
                ['role' => 'system', 'content' => 'Bạn là một chuyên gia sáng tạo nội dung và dịch thuật với hơn 10 năm kinh nghiệm. Hãy giúp người dùng dịch những nội dung chính xác và phù hợp văn phong, văn hóa địa phương, với giọng văn thân thiện và dễ hiểu.'],
                ['role' => 'user', 'content' => $promptText]
            ];

            $response           = HomeController::chatWithAI($testMessages, $model, $options);

            $content            = $response['choices'][0]['message']['content'] ?? '';

            if (!empty($content)) {
                // Đọc file hiện tại
                $currentContent = file_exists($configFilePath) ? file_get_contents($configFilePath) : "<?php\n\nreturn [\n];";

                // Chèn nội dung mới vào giữa array return
                $currentContent = preg_replace('/return\s*\[\s*/', "return [\n" . $content . ",\n", $currentContent);

                // Ghi lại vào file
                file_put_contents($configFilePath, $currentContent);
            }

        } catch (\Exception $e) {
            throw $e; // Đẩy lại lỗi để Laravel tự động thử lại
        }
    }

}
