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
                    $contentSource = $content->content;
                    break;
                }
            }

            switch ($this->ordering) {
                case 1: /* giới thiệu */
                    $promptText = 'tôi có đoạn content cần được cải thiện lại cho hay hơn, mạch lạc và cảm xúc hơn dựa theo các theo yêu cầu cụ thể:
                                    - tiêu đề lớn h2 sửa lại theo mẫu và hướng dẫn:
                                        <đoạn mẫu>
                                            <h2>Hình nền điện thoại '.$title.': Khám phá vẻ đẹp Ma Mị và Kỳ Bí của búp bê huyền thoại '.$title.' ngay trên màn hình điện thoại của bạn</h2>
                                        </đoạn mẫu>
                                        + "Ma Mị" và "Kỳ Bí" thay bằng 2 tính chất để làm nổi bật vẻ đẹp của chủ đề, "búp bê huyền hoại '.$title.'" thay bằng đoạn chung chung nói lên được sự bao quát của chủ đề.
                                    - phần mở đầu - cải thiện như sau: tách ra 3 đoạn cho dễ đọc, rõ nghĩa từng đoạn, lời dẫn thật hay, cuốn hút và hợp lí. quan trọng dẫn dắt liên quan đến vẻ đẹp của chủ đề. rõ nghĩa từng câu bao gồm: 1 đoạn đặt câu hỏi để dẫn, 1 đoạn nói nếu khách hàng là người như thế nào, thì vẻ đẹp của hình nền điện thoại này sẽ phù hợp với họ như thế nào, 1 đoạn mời họ bước vào khám phá chủ đề (thêm "nhé!" cuối câu mời này cho thân thiện). mãu gợi ý bên dưới - bạn hãy dựa vào đó mà sáng tạo, sửa lại đoạn mở đầu cho phù hợp với chủ đề và thật cuốn hút:
                                        <gợi ý>
                                            <p>Bạn có biết, mỗi lần mở điện thoại cũng giống như mở ra một cánh cửa nhỏ dẫn đến thế giới riêng của chính mình?</p>
                                            <p>Và nếu bạn là người yêu thích sự sáng tạo, đam mê cái đẹp và trân trọng những giá trị nghệ thuật độc đáo, thì các bộ sưu tập <strong><a href="../../hinh-nen-dien-thoai/'.$slug.'">hình nền điện thoại '.$title.'</a></strong> mà chúng tôi mang đến chắc chắn sẽ khiến bạn cảm thấy vô cùng hứng thú - đây không chỉ đơn thuần là những bức ảnh đẹp mắt, mà còn là cả một câu chuyện về tinh thần tự do, cá tính mạnh mẽ và nguồn cảm hứng bất tận được gửi gắm qua từng chi tiết đấy!</p>
                                            <p>Hãy để chúng tôi đồng hành cùng bạn trong hành trình khám phá những giá trị thẩm mỹ đỉnh cao, nơi mà mỗi bức ảnh đều kể câu chuyện riêng về sự đẳng cấp và phong cách đỉnh cao nhé!</p>
                                        </gợi ý>
                                        + đặt 1 link ở phần mở đầu <strong><a href="../../hinh-nen-dien-thoai/'.$slug.'">hình nền điện thoại '.$title.' cao cấp</a></strong>, hoặc đa dạng các biến thể từ khóa dài kết hợp với "đẹp", "độc đáo", "chất lượng cao", "cao cấp", "4K".
                                    - phần nội dung định nghĩa, phần nội dung bên dưới thẻ h3 - cải thiện như sau: 2 đoạn 2 ý riêng biệt (mỗi đoạn trong thẻ <p></p>), mở rộng cho hay hơn, 1 đoạn định nghĩa và 1 đoạn chú trọng nói về vể đẹp của chủ đề, không cần nói về sản phẩm của tôi chỗ này.
                                    - phần nội dung nói về cách nghệ sĩ ứng dụng vẻ đẹp của chủ đề vào hình nền - cải thiện như sau: 2 đoạn 2 ý riêng biệt (mỗi đoạn trong thẻ <p></p>), 1 đoạn nói về sự sáng tạo của nghệ sĩ trong việc ứng dụng vẻ đẹp của chủ đề vào thiết kế hình nền điện thoại, 1 đoạn hãy nói nhiều về sự đầu tư, nghiên cứu tâm lí học, ứng dụng và gian nan như thế nào để có những tác phẩm nghệ thuật ấn tượng.
                                    - phần nội dung nói về tầm quan trọng của việc sử dụng hình nền đẹp và phù hợp - cải thiện như sau:
                                        + 1 đoạn dẫn chứng số liệu, tôi cần bạn dựa vào content cũ viết bổ sung thêm dẫn chứng cụ thể hơn, số liệu và thông tin đầy đủ, đáng tin cậy hơn.
                                        + 1 đoạn nói về những bộ sưu tập chất lượng của tôi (số nhiều), tôi cần bạn dựa vào content cũ viết lại để kết nối với câu trên, nhấn mạnh và nói nhiều hơn nữa về vẻ đẹp, lợi ích và chất lượng của các bộ hình nền cao cấp và đặt 1 link  <strong><a href="../../hinh-nen-dien-thoai/'.$slug.'">hình nền điện thoại '.$title.'</a></strong>, hoặc đa dạng các biến thể từ khóa dài kết hợp với "đẹp", "độc đáo", "chất lượng cao", "cao cấp", "4K". lưu ý đa dạng đừng trùng với anchor text phần mở đầu.
                                        + 1 đoạn cuối, tôi cần bạn dựa vào content cũ viết lại cho thật hay và cuốn hút (chỗ vẽ viễn cảnh để khách hàng tưởng tượng), thêm cảm thán phù hợp ở cuối đoạn để cho thân thiện và cảm xúc (nhưng cảm xúc nhẹ nhàng, đừng quá kích thích).
                                    - Tôi cần icon trước các thẻ h3 (nếu chưa có thì chọn bổ sung vào - nếu có rồi thì chọn lại) cho thật đẹp, phù hợp với chủ đề và nội dung của thẻ. ví dụ chỗ định nghĩa thì cần icon của chủ đề, chỗ cách ứng dụng thì thêm icon nào thể hiện sự sáng tạo - nghiên cứu, chỗ tầm quan trọng thì icon nhấn mạnh thật đẹp.

                                    Yêu cầu về kết quả:
                                    - Trả về HTML text hoàn chỉnh và đầy đủ nội dung với thẻ đúng chuẩn  (chỉ cần text, không cần định dạng trong khung html)
                                    - Chỉ trả kết quả bài viết, không giải thích thêm, không thêm các ký tự định dạng thừa

                                    đoạn content cần sửa:
                                    '.$contentSource;
                    break;
                case 2: /* phân loại */
                    $promptText = 'tôi có đoạn content cần được cải thiện lại cho hay hơn, mạch lạc và cảm xúc hơn dựa theo các theo yêu cầu cụ thể:
                                    - tiêu đề lớn h2 giữ nguyên nội dung
                                    - đoạn mở đầu viết lại theo mẫu bên dưới:
                                        <gợi ý>
                                            <p>Bạn đã từng băn khoăn không biết nên chọn hình nền nào để vừa thể hiện cá tính, vừa mang đến cảm giác mới lạ cho chiếc điện thoại của mình?</p>
                                            <p>Đừng lo! Chúng tôi sẽ giúp bạn khám phá những phân loại độc đáo xoay quanh chủ đề <strong><a href="../../hinh-nen-dien-thoai/'.$slug.'">kho hình nền điện thoại '.$title.'</a></strong>. Để thông qua nội dung này, bạn sẽ dễ dàng tìm thấy những phong cách hình nền lý tưởng và phù hợp với mình nhất nhé!</p>
                                        </gợi ý>
                                        + đặt 1 link ở phần mở đầu <strong><a href="../../hinh-nen-dien-thoai/'.$slug.'">hình nền điện thoại '.$title.'</a></strong>.
                                    - phần nội dung thân - nội dung ý chính giữ nguyên, chau chuốt lại lời văn cho hay, mạch lạc, cảm xúc và phù hợp với chủ đề hơn. không cần đặt thêm link phần này.
                                    - đoạn kết - viết lại theo mẫu bên dưới:
                                        <đoạn mẫu>
                                            <p>Tại <strong><a href="../../">name.com.vn</a></strong>, chúng tôi tự hào sở hữu <strong><a href="../../hinh-nen-dien-thoai/'.$slug.'">kho hình nền điện thoại '.$title.' đỉnh cao</a></strong> với đa dạng các thể loại, phong cách và chủ đề - mỗi bộ sưu tập đều được đầu tư kỹ lưỡng về chất lượng hình ảnh và giá trị nghệ thuật, đảm bảo mang đến trải nghiệm tuyệt vời nhất cho người dùng. Hãy để chúng tôi đồng hành cùng bạn trong việc tạo nên diện mạo độc đáo và hấp dẫn cho chiếc điện thoại ngay hôm nay nhé!</p>
                                        </đoạn mẫu>
                                        + đặt 1 link như mẫu <strong><a href="../../hinh-nen-dien-thoai/'.$slug.'">kho hình nền điện thoại '.$title.' đỉnh cao</a></strong>. đa dạng từ "đỉnh cao" với các biến thể như "độc đáo", "đỉnh cao", "chất lượng cao", "cao cấp", "đẹp", "4K" (như mẫu).
                                    - Tôi cần icon trước các thẻ h3 (nếu chưa có thì chọn bổ sung vào - nếu có rồi thì chọn lại) cho thật đẹp, phù hợp với chủ đề và nội dung của thẻ. ví dụ chỗ định nghĩa thì cần icon của chủ đề, chỗ cách ứng dụng thì thêm icon nào thể hiện sự sáng tạo - nghiên cứu, chỗ tầm quan trọng thì icon nhấn mạnh thật đẹp.

                                    Yêu cầu về kết quả:
                                    - Trả về HTML text hoàn chỉnh và đầy đủ nội dung với thẻ đúng chuẩn  (chỉ cần text, không cần định dạng trong khung html)
                                    - Chỉ trả kết quả bài viết, không giải thích thêm, không thêm các ký tự định dạng thừa

                                    đoạn content cần sửa:
                                    '.$contentSource;
                    break;
                case 3: /* lợi ích */
                    $promptText = 'tôi có đoạn content cần được cải thiện lại cho hay hơn, mạch lạc và cảm xúc hơn dựa theo các theo yêu cầu cụ thể:
                                    - tiêu đề lớn h2 giữ nguyên nội dung
                                    - phần nội dung thân - nội dung ý chính giữ nguyên, chau chuốt lại lời văn cho hay, mạch lạc, cảm xúc và phù hợp với chủ đề hơn. không cần đặt thêm link phần này.
                                    - đoạn kết - viết lại theo mẫu bên dưới:
                                        <đoạn mẫu>
                                            <p><strong><a href="../../hinh-nen-dien-thoai/'.$slug.'">Kho hình nền '.$title.' cao cấp</a></strong> tại <strong><a href="../../">name.com.vn</a></strong> được xây dựng với tất cả tâm huyết và sự chuyên nghiệp - mỗi bộ sưu tập đều là thành quả của quá trình nghiên cứu kỹ lưỡng, từ khâu lựa chọn chủ đề đến việc hoàn thiện từng chi tiết nhỏ nhất. Chúng tôi tự hào mang đến cho bạn những sản phẩm không chỉ đẹp về hình thức mà còn giàu giá trị tinh thần, vượt xa mong đợi của một bộ hình nền thông thường.</p>
                                        </đoạn mẫu>
                                        + đặt 1 link như mẫu <strong><a href="../../hinh-nen-dien-thoai/'.$slug.'">Kho hình nền '.$title.' cao cấp</a></strong>. đa dạng từ "cao cấp" với các biến thể như "độc đáo", "đỉnh cao", "chất lượng cao", "cao cấp", "đẹp", "4K" (như mẫu).
                                    - Tôi cần icon trước các thẻ h3 (nếu chưa có thì chọn bổ sung vào - nếu có rồi thì chọn lại) cho thật đẹp, phù hợp với chủ đề và nội dung của thẻ. ví dụ chỗ định nghĩa thì cần icon của chủ đề, chỗ cách ứng dụng thì thêm icon nào thể hiện sự sáng tạo - nghiên cứu, chỗ tầm quan trọng thì icon nhấn mạnh thật đẹp.

                                    Yêu cầu về kết quả:
                                    - Trả về HTML text hoàn chỉnh và đầy đủ nội dung với thẻ đúng chuẩn  (chỉ cần text, không cần định dạng trong khung html)
                                    - Chỉ trả kết quả bài viết, không giải thích thêm, không thêm các ký tự định dạng thừa

                                    đoạn content cần sửa:
                                    '.$contentSource;
                        break;
                case 5: /* cách chọn */
                    $promptText = 'tôi có đoạn content cần được cải thiện lại cho hay hơn, mạch lạc và cảm xúc hơn dựa theo các theo yêu cầu cụ thể:
                                    - tiêu đề lớn h2 giữ nguyên nội dung
                                    - phần nội dung mở đầu - viết lại theo mẫu bên dưới:
                                        <gợi ý>
                                            <p>Bạn đang băn khoăn không biết làm thế nào để chọn được những bộ <strong><a href="../../hinh-nen-dien-thoai/'.$slug.'">hình nền điện thoại '.$title.'</a></strong> vừa đẹp, vừa phù hợp với phong cách và cá tính của mình?</p>
                                            <p>Đừng lo lắng! Chúng tôi hiểu rằng mỗi người đều có những tiêu chí lựa chọn hình nền riêng. Vậy nên, nội dung dưới đây sẽ giúp bạn khám phá những tiêu chí quan trọng để lựa chọn <strong><a href="../../hinh-nen-dien-thoai/'.$slug.'">hình nền '.$title.' đẹp</a></strong>, từ đó dễ dàng tìm thấy những bộ sưu tập hoàn hảo cho chiếc điện thoại của mình!</p>
                                        </gợi ý>
                                        + đặt 2 link ở phần mở đầu <strong><a href="../../hinh-nen-dien-thoai/'.$slug.'">hình nền điện thoại '.$title.'</a></strong>. 1 link cố định, 1 link thì đa dạng từ "đẹp" với các biến thể như "độc đáo", "đỉnh cao", "chất lượng cao", "cao cấp", "đẹp", "4K" (như mẫu).
                                    - phần nội dung thân - nội dung ý chính giữ nguyên, chau chuốt lại lời văn cho hay, mạch lạc, cảm xúc và phù hợp với chủ đề hơn. không cần đặt thêm link phần này.
                                    - đoạn kết - viết lại theo mẫu bên dưới:
                                        <đoạn mẫu>
                                            <p>Kết thúc hành trình khám phá <strong>cách chọn hình nền điện thoại '.$title.'</strong>, chúng tôi tin rằng bạn đã có cái nhìn tổng quan và sâu sắc hơn về chủ đề này. Tại <strong><a href="../../">name.com.vn</a></strong>, chúng tôi tự hào có nền tảng hệ thống chuyên nghiệp, công nghệ vượt trội và tích hợp AI thông minh để hỗ trợ bạn dễ dàng tìm được những sản phẩm phù hợp theo tất cả các tiêu chí kể trên. Hãy khám phá và trải nghiệm sự khác biệt ngay hôm nay nhé!</p>
                                        </đoạn mẫu>
                                    - Tôi cần icon trước các thẻ h3 (nếu chưa có thì chọn bổ sung vào - nếu có rồi thì chọn lại) cho thật đẹp, phù hợp với chủ đề và nội dung của thẻ. ví dụ chỗ định nghĩa thì cần icon của chủ đề, chỗ cách ứng dụng thì thêm icon nào thể hiện sự sáng tạo - nghiên cứu, chỗ tầm quan trọng thì icon nhấn mạnh thật đẹp.

                                    Yêu cầu về kết quả:
                                    - Trả về HTML text hoàn chỉnh và đầy đủ nội dung với thẻ đúng chuẩn  (chỉ cần text, không cần định dạng trong khung html)
                                    - Chỉ trả kết quả bài viết, không giải thích thêm, không thêm các ký tự định dạng thừa

                                    đoạn content cần sửa:
                                    '.$contentSource;
                        break;
                case 4: /* gợi ý */
                    $promptText = 'tôi có đoạn content cần được cải thiện lại cho hay hơn, mạch lạc và cảm xúc hơn dựa theo các theo yêu cầu cụ thể:
                                    - tiêu đề lớn h2 giữ nguyên nội dung
                                    - phần nội dung thân - nội dung ý chính giữ nguyên, chau chuốt lại lời văn cho hay, mạch lạc, cảm xúc và phù hợp với chủ đề hơn. không cần đặt thêm link phần này.
                                    - đoạn kết - viết lại theo mẫu bên dưới:
                                        <đoạn mẫu>
                                            <p>Tại <strong><a href="../../">name.com.vn</a></strong>, chúng tôi mang đến <strong><a href="../../hinh-nen-dien-thoai">kho hình nền điện thoại</a></strong> đa sắc màu và đầy đủ các chủ đề – nơi mỗi bức ảnh là một câu chuyện, mỗi thiết kế là một mảnh ghép cảm xúc. Từ những gam màu rực rỡ dành cho tâm hồn nghệ sĩ yêu cái đẹp, đến những hình ảnh tinh tế, sâu lắng phù hợp làm quà tặng ý nghĩa, tất cả đều đang chờ bạn khám phá đấy!</p>
                                        </đoạn mẫu>
                                    - Tôi cần icon trước các thẻ h3 (nếu chưa có thì chọn bổ sung vào - nếu có rồi thì chọn lại) cho thật đẹp, phù hợp với chủ đề và nội dung của thẻ. ví dụ chỗ định nghĩa thì cần icon của chủ đề, chỗ cách ứng dụng thì thêm icon nào thể hiện sự sáng tạo - nghiên cứu, chỗ tầm quan trọng thì icon nhấn mạnh thật đẹp.

                                    Yêu cầu về kết quả:
                                    - Trả về HTML text hoàn chỉnh và đầy đủ nội dung với thẻ đúng chuẩn  (chỉ cần text, không cần định dạng trong khung html)
                                    - Chỉ trả kết quả bài viết, không giải thích thêm, không thêm các ký tự định dạng thừa

                                    đoạn content cần sửa:
                                    '.$contentSource;
                        break;
                    case 8: /* kết luận chung */
                        $promptText = 'tôi có đoạn content cần được cải thiện lại cho hay hơn, mạch lạc và cảm xúc hơn dựa theo các theo yêu cầu cụ thể:
                                        - tiêu đề lớn h2 giữ nguyên nội dung
                                        - phần nội dung mở đầu - viết lại theo mẫu bên dưới:
                                            <gợi ý>
                                                <p>Tiếp theo, chúng tôi sẽ cùng bạn khám phá một số bí quyết để giúp bạn quản lí và tối ưu hóa trải nghiệm cá nhân với những bộ <strong><a href="../../hinh-nen-dien-thoai/'.$slug.'">hình nền điện thoại '.$title.'</a></strong> mà bạn đã sưu tầm - đầu tư nhé!</p>
                                                <p>Đây không chỉ là những hướng dẫn kỹ thuật mà còn là hành trình giúp bạn kết nối sâu sắc hơn với niềm đam mê nghệ thuật và tận hưởng được tối đa giá trị tinh thần mà các bộ sưu tập này mang lại. Bắt đầu ngay thôi!</p>
                                            </gợi ý>
                                        - phần nội dung thân - icon và nội dung chính của các mẹo giữ nguyên, chau chuốt lại lời văn cho hay, mạch lạc, cảm xúc và phù hợp với chủ đề hơn. không cần đặt thêm link phần này.
                                        - phần nội dung kết luận chung - cải thiện như sau: tách ra 5 đoạn cho dễ đọc, rõ nghĩa từng đoạn, lời dẫn thật hay, cuốn hút và hợp lí. rõ nghĩa từng đoạn theo các hướng dẫn cụ thể cho từng đoạn bên dưới: 
                                            + 1 đoạn kết luận chung chung về vai trò, lợi ích và vẻ đẹp của hình nền điện thoại '.$title.', đoạn mẫu bên dưới - dựa vào đoạn mãu bạn hãy sáng tạo và viết lại cho unique, đa dạng và phù hợp với chủ đề:
                                                <đoạn mẫu>
                                                    <p>Trong thế giới hiện đại ngày nay, nơi công nghệ thường lấn át cảm xúc, <strong><a title="Hình Nền Điện Thoại '.$title.'" href="../../hinh-nen-dien-thoai/'.$slug.'">hình nền '.$title.'</a></strong> đóng vai trò như một cây cầu nối giữa nghệ thuật và cuộc sống hàng ngày. Chúng không chỉ đơn thuần là những hình ảnh trang trí mà còn là phương tiện thể hiện bản thân, nuôi dưỡng tâm hồn và thậm chí trở thành "<strong>liệu pháp tinh thần</strong>" mỗi khi bạn cần nguồn cảm hứng vô tận. Mỗi đường nét, mỗi gam màu đều kể câu chuyện riêng về truyền thống và sự sáng tạo, mang đến cho bạn nguồn cảm hứng bất tận trong cuộc sống hằng ngày..</p>
                                                </đoạn mẫu>
                                            + 1 đoạn nhấn mạnh về sự đầu tư thiết kế, kì công của chung tôi để tạo ra các sản phẩm chất lượng, với mong muốn truyền tải nhiều giá trị tích cực và ý nghĩa nhất có thể cho người dùng, đoạn mẫu bên dưới - dựa vào đoạn mãu bạn hãy sáng tạo và viết lại cho unique, đa dạng và phù hợp với chủ đề. đặt 1 link như mẫu <strong><a href="../../hinh-nen-dien-thoai/'.$slug.'">hình nền điện thoại '.$title.' cao cấp</a></strong>. đa dạng từ "cao cấp" với các biến thể như "độc đáo", "đỉnh cao", "chất lượng cao", "cao cấp", "đẹp", "4K" (như mẫu).
                                                <đoạn mẫu>
                                                    <p>Tại <strong><a title="name.com.vn" href="../../en">name.com.vn</a></strong>, mỗi <strong><a href="../../hinh-nen-dien-thoai/'.$slug.'">hình nền điện thoại '.$title.' cao cấp</a></strong> đại diện cho đỉnh cao của một quá trình sáng tạo nghiêm túc: từ nghiên cứu tâm lý học màu sắc, hiểu biết về xu hướng thẩm mỹ đương đại đến việc cân bằng hoàn hảo giữa vẻ đẹp truyền thống và phong cách hiện đại. Chúng tôi tin rằng việc cá nhân hóa thiết bị công nghệ là cách để tôn trọng chính mình – một tuyên ngôn tự hào giữa nhịp sống bận rộn.</p>
                                                </đoạn mẫu>
                                            + 1 đoạn vẽ ra viễn cảnh tươi đẹp cho khách hàng tưởng tượng, đoạn mẫu bên dưới - dựa vào đoạn mãu bạn hãy sáng tạo và viết lại cho unique, đa dạng và phù hợp với chủ đề. đặt 1 link như mẫu <strong><a href="../../hinh-nen-dien-thoai">hình nền điện thoại cao cấp</a></strong>. đa dạng từ "cao cấp" với các biến thể như "độc đáo", "đỉnh cao", "chất lượng cao", "cao cấp", "đẹp", "4K" (như mẫu).
                                                <đoạn mẫu>
                                                    <p>Hãy tưởng tượng mỗi sáng thức dậy, mở điện thoại và nhìn thấy hình ảnh yêu thích rực rỡ trên màn hình – có thể là một khoảnh khắc đáng nhớ, một nguồn cảm hứng mới cho ngày làm việc, hoặc đơn giản là một niềm vui nhỏ bạn tự dành tặng bản thân. Tất cả những cảm xúc ấy đang chờ bạn khám phá trong từng bộ sưu tập <strong><a href="../../hinh-nen-dien-thoai">hình nền điện thoại cao cấp</a></strong> của chúng tôi – nơi cái đẹp không chỉ được chiêm ngưỡng mà còn trở thành một phần trong cuộc sống hằng ngày của bạn!</p>
                                                </đoạn mẫu>
                                            + 1 đoạn kêu gọi nhẹ nhàng, không cần link - đoạn mẫu bên dưới - dựa vào đoạn mãu bạn hãy sáng tạo và viết lại cho unique, đa dạng và phù hợp với chủ đề:
                                                <đoạn mẫu>
                                                   <p>Đừng ngần ngại thử nghiệm những sự kết hợp mới, thay đổi gu thẩm mỹ hoặc thậm chí "<strong>phá vỡ quy tắc</strong>" để tìm ra phiên bản hình nền phản ánh chân thực nhất con người bạn. Sau cùng, điện thoại không chỉ là một công cụ – nó là tấm gương phản chiếu cá tính của bạn, một không gian riêng tư nơi bạn có thể tự do thể hiện mọi khía cạnh của tâm hồn. Và chúng tôi luôn ở đây, đồng hành cùng bạn trên hành trình khám phá ấy!</p>
                                                </đoạn mẫu>
                                                + đa dạng từ <strong>phá vỡ quy tắc</strong> cho unique
                                            + 1 đoạn chúc khách hàng - viết nguyên và giữ theo đoạn mẫu bên dưới:
                                                <đoạn mẫu>
                                                   <p>Chúc bạn có những trải nghiệm tuyệt vời và tràn đầy cảm hứng cùng những <strong><a href="../../hinh-nen-dien-thoai">hình nền điện thoại đẹp</a></strong> mà bạn yêu thích!</p>
                                                </đoạn mẫu>
    
                                        Yêu cầu về kết quả:
                                        - Trả về HTML text hoàn chỉnh và đầy đủ nội dung với thẻ đúng chuẩn  (chỉ cần text, không cần định dạng trong khung html)
                                        - Chỉ trả kết quả bài viết, không giải thích thêm, không thêm các ký tự định dạng thừa
    
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
