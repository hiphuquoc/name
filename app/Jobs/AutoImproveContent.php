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
use App\Http\Controllers\Admin\ChatGptController;
use App\Http\Controllers\Admin\HelperController;

class AutoImproveContent implements ShouldQueue {
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $ordering;
    private $idSeo;
    public  $tries = 5; // Số lần thử lại

    public function __construct($ordering, $idSeo){
        $this->ordering     = $ordering;
        $this->idSeo        = $idSeo;
    }

    public function handle(){
        try {
            $infoPage   = HelperController::getFullInfoPageByIdSeo($this->idSeo);
            $title      = $infoPage->seo->title;
            $slug       = $infoPage->seo->slug;
            $contentSource    = '';
            foreach($infoPage->seo->contents as $content){
                if($content->ordering==$this->ordering) {
                    $content = $content->content;
                    break;
                }
            }
            switch ($this->ordering) {
                case 1:
                    $promptText = 'tôi có đoạn content cần được cải thiện lại cho hay hơn, theo yêu cầu:
                                    - đoạn mở đầu tách ra 3 câu cho dễ đọc, rõ nghĩa từng câu, lời dẫn thật hay, cuốn hút và hợp lí. quan trọng dẫn dắt liên quan đến vẻ đẹp của chủ đề. rõ nghĩa từng câu bao gồm: 1 câu đặt câu hỏi để dẫn, 1 câu nói nếu khách hàng là người như thế nào, thì vẻ đẹp của hình nền điện thoại này sẽ phù hợp với họ như thế nào, 1 câu mời họ bước vào khám phá chủ đề. mãu gợi ý bên dưới - bạn hãy dựa vào đó mà sáng tạo, sửa lại đoạn mở đầu cho phù hợp với chủ đề và thật cuốn hút, đoạn gợi ý:
                                        <gợi ý>
                                            <h2>Hình nền điện thoại '.$title.': Khám phá vẻ đẹp Nghệ Thuật và Phong Cách của văn hóa '.$title.' ngay trên màn hình điện thoại của bạn</h2>
                                            <p>Bạn có biết, mỗi lần mở điện thoại cũng giống như mở ra một cánh cửa nhỏ dẫn đến thế giới riêng của chính mình?</p>
                                            <p>Và nếu bạn là người yêu thích sự sáng tạo, đam mê cái đẹp và trân trọng những giá trị nghệ thuật độc đáo, thì các bộ sưu tập <strong><a href="../../hinh-nen-dien-thoai/'.$slug.'">hình nền điện thoại '.$title.'</a></strong> mà chúng tôi mang đến chắc chắn sẽ khiến bạn cảm thấy vô cùng hứng thú - đây không chỉ đơn thuần là những bức ảnh đẹp mắt, mà còn là cả một câu chuyện về tinh thần tự do, cá tính mạnh mẽ và nguồn cảm hứng bất tận được gửi gắm qua từng chi tiết.</p>
                                            <p>Hãy để chúng tôi đồng hành cùng bạn trong hành trình khám phá những giá trị thẩm mỹ đỉnh cao, nơi mà mỗi bức ảnh đều kể câu chuyện riêng về sự đẳng cấp và phong cách đỉnh cao nhé!</p>
                                        </gợi ý>
                                        + đặt 1 link ở phần mở đầu <strong><a href="../../hinh-nen-dien-thoai/'.$slug.'">hình nền điện thoại '.$title.'</a></strong>, hoặc đa dạng các biến thể từ khóa dài kết hợp với "chất lượng cao", "cao cấp".
                                    - đoạn định nghĩa, phần nội dung bên dưới thẻ h3 bạn hãy viết lại 2 đoạn này, mở rộng cho hay hơn, định nghĩa và chú trọng nói về vể đẹp của chủ đề, không cần nói về sản phẩm của tôi chỗ này.
                                    - đoạn nói về cách nghệ sĩ ứng dụng .... tách ra 2 đoạn riêng biệt, 1 đoạn nói về sự sáng tạo của nghệ sĩ trong việc ứng dụng vẻ đẹp của chủ đề vào thiết kế hình nền điện thoại, 1 đoạn hãy nói nhiều về sự đầu tư, nghiên cứu tâm lí học, ứng dụng và gian nan như thế nào để có những tác phẩm nghệ thuật ấn tượng. đặt 1 link ở chỗ nào hợp lí trong phần này <strong><a href="../../hinh-nen-dien-thoai">hình nền điện thoại</a></strong>, hoặc đa dạng các biến thể từ khóa dài kết hợp với "chất lượng cao", "cao cấp".
                                    - đoạn nói về tầm quan trọng của hình việc trang trí bằng hình nền đẹp và phù hợp cải thiện lại theo yêu cầu bên dưới:
                                        + ở đoạn nói về những bộ sưu tập chất lượng của tôi (số nhiều), viết lại để nhấn mạnh và nói nhiều hơn nữa về vẻ đẹp, lợi ích và chất lượng của các bộ hình nền cao cấp và đặt 1 link  <strong><a href="../../hinh-nen-dien-thoai/'.$slug.'">hình nền điện thoại '.$title.'</a></strong>, hoặc đa dạng các biến thể từ khóa dài kết hợp với "chất lượng cao", "cao cấp". lưu ý đa dạng đừng trùng với anchor text phần mở đầu.
                                        + ở đoạn cuối, viết lại cho thật hay và cuốn hút (chỗ vẽ viễn cảnh để khách hàng tưởng tượng), thêm cảm thán phù hợp ở cuối đoạn để cho thân thiện và cảm xúc (nhưng ưu tiên cảm xúc nhẹ nhàng, đừng quá kích thích).
                                    - icon trước các thẻ h3, tôi cần bạn chọn lại cho thật đẹp, phù hợp với chủ đề và nội dung của thẻ.
                                    - Cuối cùng: trả về toàn bộ nội dung hoàn chỉnh sau khi đã sửa và đừng giải thích gì thêm, để tôi lưu trực tiếp vào cơ sở dữ liệu.

                                    đoạn content cần sửa:
                                    '.$contentSource;
                    break;
                
                default:
                    $promptText = '';
                    break;
            }
            
            if(!empty($promptText)){
                $infoPrompt = [
                    'version'   => 'qwen-max',
                ];
                $response   = ChatGptController::callApi($promptText, $infoPrompt);
                // Kiểm tra nếu có lỗi từ API thì đẩy lại Job
                if (!empty($response['error'])) {
                    throw new \Exception($response['error']); // Tạo Exception mới
                }
    
                $content = $response['content'] ?? '';
    
                if(!empty(trim($content))){
                    // Xóa content cũ
                    SeoContent::where('seo_id', $this->idSeo)
                        ->where('ordering', $this->ordering)
                        ->delete();
    
                    // Lưu content mới
                    SeoContent::insertItem([
                        'seo_id'    => $this->idSeo,
                        'content'   => $content,
                        'ordering'  => $this->ordering,
                    ]);
                }
            }

        } catch (\Exception $e) {
            throw $e; // Đẩy lại lỗi để Laravel tự động thử lại
        }
    }

}
