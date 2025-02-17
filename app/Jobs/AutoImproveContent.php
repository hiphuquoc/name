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
                case 1: /* giới thiệu */
                    $promptText = 'tôi có đoạn content cần được cải thiện lại cho hay hơn, theo yêu cầu:
                                    - đoạn mở đầu tách ra 3 câu cho dễ đọc, rõ nghĩa từng câu, lời dẫn thật hay, cuốn hút và hợp lí. quan trọng dẫn dắt liên quan đến vẻ đẹp của chủ đề. rõ nghĩa từng câu bao gồm: 1 câu đặt câu hỏi để dẫn, 1 câu nói nếu khách hàng là người như thế nào, thì vẻ đẹp của hình nền điện thoại này sẽ phù hợp với họ như thế nào, 1 câu mời họ bước vào khám phá chủ đề (thêm "nhé!" cuối câu mời này cho thân thiện). mãu gợi ý bên dưới - bạn hãy dựa vào đó mà sáng tạo, sửa lại đoạn mở đầu cho phù hợp với chủ đề và thật cuốn hút, đoạn gợi ý:
                                        <gợi ý>
                                            <h2>Hình nền điện thoại '.$title.': Khám phá vẻ đẹp Nghệ Thuật và Phong Cách của văn hóa '.$title.' ngay trên màn hình điện thoại của bạn</h2>
                                            <p>Bạn có biết, mỗi lần mở điện thoại cũng giống như mở ra một cánh cửa nhỏ dẫn đến thế giới riêng của chính mình?</p>
                                            <p>Và nếu bạn là người yêu thích sự sáng tạo, đam mê cái đẹp và trân trọng những giá trị nghệ thuật độc đáo, thì các bộ sưu tập <strong><a href="../../hinh-nen-dien-thoai/'.$slug.'">hình nền điện thoại '.$title.'</a></strong> mà chúng tôi mang đến chắc chắn sẽ khiến bạn cảm thấy vô cùng hứng thú - đây không chỉ đơn thuần là những bức ảnh đẹp mắt, mà còn là cả một câu chuyện về tinh thần tự do, cá tính mạnh mẽ và nguồn cảm hứng bất tận được gửi gắm qua từng chi tiết đấy!</p>
                                            <p>Hãy để chúng tôi đồng hành cùng bạn trong hành trình khám phá những giá trị thẩm mỹ đỉnh cao, nơi mà mỗi bức ảnh đều kể câu chuyện riêng về sự đẳng cấp và phong cách đỉnh cao nhé!</p>
                                        </gợi ý>
                                        + đặt 1 link ở phần mở đầu <strong><a href="../../hinh-nen-dien-thoai/'.$slug.'">hình nền điện thoại '.$title.'</a></strong>, hoặc đa dạng các biến thể từ khóa dài kết hợp với "chất lượng cao", "cao cấp".
                                    - đoạn định nghĩa, phần nội dung bên dưới thẻ h3 bạn hãy viết lại 2 đoạn này, mở rộng cho hay hơn, định nghĩa và chú trọng nói về vể đẹp của chủ đề, không cần nói về sản phẩm của tôi chỗ này.
                                    - đoạn nói về cách nghệ sĩ ứng dụng .... viết 2 đoạn riêng biệt (mỗi đoạn trong thẻ <p></p>), 1 đoạn nói về sự sáng tạo của nghệ sĩ trong việc ứng dụng vẻ đẹp của chủ đề vào thiết kế hình nền điện thoại, 1 đoạn hãy nói nhiều về sự đầu tư, nghiên cứu tâm lí học, ứng dụng và gian nan như thế nào để có những tác phẩm nghệ thuật ấn tượng. đặt 1 link ở chỗ nào hợp lí trong phần này <strong><a href="../../hinh-nen-dien-thoai">hình nền điện thoại</a></strong>, hoặc đa dạng các biến thể từ khóa dài kết hợp với "chất lượng cao", "cao cấp".
                                    - đoạn nói về tầm quan trọng của hình việc trang trí bằng hình nền đẹp và phù hợp cải thiện lại theo yêu cầu bên dưới:
                                        + ở đoạn nói về những bộ sưu tập chất lượng của tôi (số nhiều), viết lại để nhấn mạnh và nói nhiều hơn nữa về vẻ đẹp, lợi ích và chất lượng của các bộ hình nền cao cấp và đặt 1 link  <strong><a href="../../hinh-nen-dien-thoai/'.$slug.'">hình nền điện thoại '.$title.'</a></strong>, hoặc đa dạng các biến thể từ khóa dài kết hợp với "chất lượng cao", "cao cấp". lưu ý đa dạng đừng trùng với anchor text phần mở đầu.
                                        + ở đoạn cuối, viết lại cho thật hay và cuốn hút (chỗ vẽ viễn cảnh để khách hàng tưởng tượng), thêm cảm thán phù hợp ở cuối đoạn để cho thân thiện và cảm xúc (nhưng ưu tiên cảm xúc nhẹ nhàng, đừng quá kích thích).
                                    - icon trước các thẻ h3, tôi cần bạn chọn lại cho thật đẹp, phù hợp với chủ đề và nội dung của thẻ. ví dụ chỗ định nghĩa thì cần icon của chủ đề, chỗ cách ứng dụng thì thêm icon nào thể hiện sự sáng tạo - nghiên cứu, chỗ tầm quan trọng thì icon nhấn mạnh thật đẹp.
                                    - Cuối cùng: trả về toàn bộ nội dung hoàn chỉnh sau khi đã sửa và đừng giải thích gì thêm, để tôi lưu trực tiếp vào cơ sở dữ liệu.

                                    đoạn content cần sửa:
                                    '.$contentSource;
                    break;
                case 2: /* phân loại */
                    $promptText = 'tôi có đoạn content cần được cải thiện lại cho hay hơn, theo yêu cầu:
                                    - đoạn mở đầu viết lại theo mẫu bên dưới:
                                        <gợi ý>
                                            <p>Bạn đã từng băn khoăn không biết nên chọn hình nền nào để vừa thể hiện cá tính, vừa mang đến cảm giác mới lạ cho chiếc điện thoại của mình?</p>
                                            <p>Đừng lo! Chúng tôi sẽ giúp bạn khám phá những phân loại độc đáo xoay quanh chủ đề <strong><a href="../../hinh-nen-dien-thoai/'.$slug.'">kho hình nền điện thoại '.$title.'</a></strong>. Để thông qua nội dung này, bạn sẽ dễ dàng tìm thấy những phong cách hình nền lý tưởng và phù hợp với mình nhất nhé!</p>
                                        </gợi ý>
                                        + đặt 1 link ở phần mở đầu <strong><a href="../../hinh-nen-dien-thoai/'.$slug.'">hình nền điện thoại '.$title.'</a></strong>.
                                    - đoạn thân của nội dung giữ nguyên.
                                    - đoạn kết viết lại theo mẫu bên dưới:
                                        <đoạn mẫu>
                                            <p>Tại <strong><a href="../../">name.com.vn</a></strong>, chúng tôi tự hào sở hữu <strong><a href="../../hinh-nen-dien-thoai/'.$slug.'">kho hình nền điện thoại '.$title.' đỉnh cao</a></strong> với đa dạng các thể loại, phong cách và chủ đề - mỗi bộ sưu tập đều được đầu tư kỹ lưỡng về chất lượng hình ảnh và giá trị nghệ thuật, đảm bảo mang đến trải nghiệm tuyệt vời nhất cho người dùng. Hãy để chúng tôi đồng hành cùng bạn trong việc tạo nên diện mạo độc đáo và hấp dẫn cho chiếc điện thoại ngay hôm nay nhé!</p>
                                        </đoạn mẫu>
                                        + đặt 1 link như mẫu <strong><a href="../../hinh-nen-dien-thoai/'.$slug.'">kho hình nền điện thoại '.$title.' đỉnh cao</a></strong>. đa dạng từ "đỉnh cao" với các biến thể như "đỉnh cao", "chất lượng cao", "cao cấp", "đẹp".
                                    - Icon trước các thẻ h3, tôi cần bạn chọn lại cho thật đẹp, phù hợp với chủ đề và nội dung của thẻ.
                                    - Cuối cùng: trả về toàn bộ nội dung hoàn chỉnh sau khi đã sửa và đừng giải thích gì thêm, để tôi lưu trực tiếp vào cơ sở dữ liệu.

                                    đoạn content cần sửa:
                                    '.$contentSource;
                    break;
                case 3: /* lợi ích */
                    $promptText = 'tôi có đoạn content cần được cải thiện lại cho hay hơn, theo yêu cầu:
                                    - đoạn thân của nội dung giữ nguyên nội dung, nhưng những icon trước các thẻ h3, tôi cần bạn chọn lại cho thật đẹp, phù hợp với chủ đề và nội dung của thẻ.
                                    - đoạn kết viết lại theo mẫu bên dưới:
                                        <đoạn mẫu>
                                            <p><strong><a href="../../hinh-nen-dien-thoai/'.$slug.'">Kho hình nền '.$title.' cao cấp</a></strong> tại <strong><a href="../../">name.com.vn</a></strong> được xây dựng với tất cả tâm huyết và sự chuyên nghiệp - mỗi bộ sưu tập đều là thành quả của quá trình nghiên cứu kỹ lưỡng, từ khâu lựa chọn chủ đề đến việc hoàn thiện từng chi tiết nhỏ nhất. Chúng tôi tự hào mang đến cho bạn những sản phẩm không chỉ đẹp về hình thức mà còn giàu giá trị tinh thần, vượt xa mong đợi của một bộ hình nền thông thường.</p>
                                        </đoạn mẫu>
                                        + đặt 1 link như mẫu <strong><a href="../../hinh-nen-dien-thoai/'.$slug.'">Kho hình nền '.$title.' cao cấp</a></strong>. đa dạng từ "cao cấp" với các biến thể như "đỉnh cao", "chất lượng cao", "cao cấp", "đẹp".
                                    - Cuối cùng: trả về toàn bộ nội dung hoàn chỉnh sau khi đã sửa và đừng giải thích gì thêm, để tôi lưu trực tiếp vào cơ sở dữ liệu.

                                    đoạn content cần sửa:
                                    '.$contentSource;
                        break;
                case 4: /* cách chọn */
                    $promptText = 'tôi có đoạn content cần được cải thiện lại cho hay hơn, theo yêu cầu:
                                    - đoạn mở đầu viết lại theo mẫu bên dưới:
                                        <gợi ý>
                                            <p>Bạn đang băn khoăn không biết làm thế nào để chọn được những bộ <strong><a href="../../hinh-nen-dien-thoai/'.$slug.'">hình nền điện thoại '.$title.'</a></strong> vừa đẹp, vừa phù hợp với phong cách và cá tính của mình?</p>
                                            <p>Đừng lo lắng! Chúng tôi hiểu rằng mỗi người đều có những tiêu chí lựa chọn hình nền riêng. Vậy nên, nội dung dưới đây sẽ giúp bạn khám phá những tiêu chí quan trọng để lựa chọn <strong><a href="../../hinh-nen-dien-thoai/'.$slug.'">hình nền '.$title.' đẹp</a></strong>, từ đó dễ dàng tìm thấy những bộ sưu tập hoàn hảo cho chiếc điện thoại của mình!</p>
                                        </gợi ý>
                                        + đặt 2 link ở phần mở đầu <strong><a href="../../hinh-nen-dien-thoai/'.$slug.'">hình nền điện thoại '.$title.'</a></strong>. 1 link cố định, 1 link thì đa dạng từ "đẹp" với các biến thể như "đỉnh cao", "chất lượng cao", "cao cấp", "đẹp" (như mẫu).
                                    - đoạn thân của nội dung giữ nguyên nội dung, nhưng những icon trước các thẻ h3, tôi cần bạn chọn lại cho thật đẹp, phù hợp với chủ đề và nội dung của thẻ.
                                    - đoạn kết viết lại theo mẫu bên dưới:
                                        <đoạn mẫu>
                                            <p>Kết thúc hành trình khám phá <strong>cách chọn hình nền điện thoại '.$title.'</strong>, chúng tôi tin rằng bạn đã có cái nhìn tổng quan và sâu sắc hơn về chủ đề này. Tại <strong><a href="../../">name.com.vn</a></strong>, chúng tôi tự hào có nền tảng hệ thống chuyên nghiệp, công nghệ vượt trội và tích hợp AI thông minh để hỗ trợ bạn dễ dàng tìm được những sản phẩm phù hợp theo tất cả các tiêu chí kể trên. Hãy khám phá và trải nghiệm sự khác biệt ngay hôm nay nhé!</p>
                                        </đoạn mẫu>
                                    - Cuối cùng: trả về toàn bộ nội dung hoàn chỉnh sau khi đã sửa và đừng giải thích gì thêm, để tôi lưu trực tiếp vào cơ sở dữ liệu.

                                    đoạn content cần sửa:
                                    '.$contentSource;
                        break;
                case 5: /* gợi ý */
                    $promptText = 'tôi có đoạn content cần được cải thiện lại cho hay hơn, theo yêu cầu:
                                    - đoạn thân của nội dung giữ nguyên nội dung, nhưng những icon trước các thẻ h3, tôi cần bạn chọn lại cho thật đẹp, phù hợp với chủ đề và nội dung của thẻ.
                                    - đoạn kết viết lại theo mẫu bên dưới:
                                        <đoạn mẫu>
                                            <p>Với kho hình nền đa dạng, phong phú chủ đề tại <strong><a href="../../">name.com.vn</a></strong>, chúng tôi tin rằng bạn sẽ dễ dàng tìm thấy những thiết kế ưng ý và phù hợp nhất - dù là để thỏa mãn đam mê cái đẹp hay tìm kiếm một món quà ý nghĩa, độc đáo và đầy cảm xúc. Hãy cùng chúng tôi khám phá ngay nhé!</p>
                                        </đoạn mẫu>
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
