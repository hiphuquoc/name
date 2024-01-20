<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\File;
use Intervention\Image\ImageManagerStatic;
use App\Models\District;
use App\Models\Product;
use App\Models\Category;
use App\Models\Style;
use App\Models\Event;
use App\Models\FreeWallpaper;
use App\Models\RegistryEmail;
use Illuminate\Support\Facades\Cookie;
use App\Services\BuildInsertUpdateModel;

class AjaxController extends Controller {

    public function __construct(BuildInsertUpdateModel $BuildInsertUpdateModel){
        $this->BuildInsertUpdateModel  = $BuildInsertUpdateModel;
    }

    public static function loadLoading(){
        $xhtml      = view('wallpaper.template.loading')->render();
        echo $xhtml;
    }

    public static function loadDistrictByIdProvince(Request $request){
        $response           = '<option value="0" selected>- Vui lòng chọn -</option>';
        if(!empty($request->get('province_info_id'))){
            $districts      = District::select('*')
                                ->where('province_id', $request->get('province_info_id'))
                                ->get();
            foreach($districts as $district){
                $response   .= '<option value="'.$district->id.'">'.$district->district_name.'</option>';
            }
        }
        echo $response;
    }

    public static function searchProductAjax(Request $request){
        if(!empty($request->get('search'))){
            $keySearch          = \App\Helpers\Charactor::convertStringSearch($request->get('search'));
            $products           = Product::select('product_info.*')
                ->whereHas('prices.wallpapers', function(){

                })
                ->join('seo', 'seo.id', '=', 'product_info.seo_id')
                ->where('code', 'like', '%'.$keySearch.'%')
                ->orWhere('name', 'like', '%'.$keySearch.'%')
                ->orWhere('en_name', 'like', '%'.$keySearch.'%')
                ->skip(0)
                ->take(5)
                ->with('seo', 'prices.wallpapers.infoWallpaper')
                ->orderBy('seo.ordering', 'DESC')
                ->orderBy('id', 'DESC')
                ->get();
            $count              = Product::select('product_info.*')
                ->whereHas('prices.wallpapers', function(){
                        
                })
                ->where('code', 'like', '%'.$keySearch.'%')
                ->orWhere('name', 'like', '%'.$keySearch.'%')
                ->orWhere('en_name', 'like', '%'.$keySearch.'%')
                ->count();
            $response           = null;
            $language           = $request->get('language') ?? 'vi';
            if(!empty($products)&&$products->isNotEmpty()){
                foreach($products as $product){
                    if($language=='vi'){
                        $title  = $product->name ?? $product->seo->title ?? null;
                        $url    = $product->seo->slug_full;
                    }else {
                        $title  = $product->en_name ?? $product->en_seo->title ?? null;
                        $url    = $product->en_seo->slug_full;
                    }
                    $priceOld   = null;
                    if($product->prices<$product->price_before_promotion) {
                        $priceOld   = '<div class="searchViewBefore_selectbox_item_content_price_old">'.\App\Helpers\Number::getFormatPriceByLanguage($product->price_before_promotion, $language).'</div>';
                    }
                    $image      = Storage::url(config('image.loading_main_gif'));
                    if(!empty($product->prices[0]->wallpapers[0]->infoWallpaper->file_cloud_wallpaper)) $image = \App\Helpers\Image::getUrlImageMiniByUrlImage($product->prices[0]->wallpapers[0]->infoWallpaper->file_cloud_wallpaper);
                    $response   .= '<a href="/'.$url.'" class="searchViewBefore_selectbox_item">
                                        <div class="searchViewBefore_selectbox_item_image">
                                            <img src="'.$image.'" alt="'.$title.'" title="'.$title.'" />
                                        </div>
                                        <div class="searchViewBefore_selectbox_item_content">
                                            <div class="searchViewBefore_selectbox_item_content_title maxLine_2">
                                                '.$title.'
                                            </div>
                                            <div class="searchViewBefore_selectbox_item_content_price">
                                                <div>'.\App\Helpers\Number::getFormatPriceByLanguage($product->price, $language).'</div>
                                                '.$priceOld.'
                                            </div>
                                        </div>
                                    </a>';
                }
                if(empty($language)||$language=='vi'){
                    $url            = route('routing', ['slug' => 'hinh-nen-dien-thoai']).'?search='.$keySearch;
                    $contentButton  = 'Xem tất cả (<span>'.$count.'</span>) kết quả <i class="fa-solid fa-angles-right"></i>';
                }else {
                    $url            = route('routing', ['slug' => 'phone-wallpapers']).'?search='.$keySearch;
                    $contentButton  = 'See all (<span>'.$count.'</span>) results <i class="fa-solid fa-angles-right"></i>';
                }
                $response           .= '<a href="'.$url.'" class="searchViewBefore_selectbox_item">'.$contentButton.'</a>';
            }else {
                if(empty($language)||$language=='vi'){
                    $response       = '<div class="searchViewBefore_selectbox_item">'.config('main.message.vi.product_empty').'</div>';
                }else {
                    $response       = '<div class="searchViewBefore_selectbox_item">'.config('main.message.en.product_empty').'</div>';
                }
            }
            echo $response;
        }
    }

    public static function registryEmail(Request $request){
        $idRegistryEmail        = RegistryEmail::insertItem([
            'email'     => $request->get('registry_email')
        ]);
        if(!empty($idRegistryEmail)){
            $result['type']     = 'success';
            $result['title']    = 'Đăng ký email thành công!';
            $result['content']  = '<div>Cảm ơn bạn đã đăng ký nhận tin!</div>
                                    <div>Trong thời gian tới nếu có bất kỳ chương trình khuyến mãi nào '.config('main.company_name').' sẽ gửi cho bạn đầu tiên.</div>'; 
        }else {
            $result['type']     = 'error';
            $result['title']    = 'Đăng ký email thất bại!';
            $result['content']  = 'Có lỗi xảy ra, vui lòng thử lại'; 
        }
        return json_encode($result);
    }

    public function buildTocContentMain(Request $request){
        $xhtml       = null;
        if(!empty($request->get('data'))){
            $xhtml   = view('main.template.tocContentMain', ['data' => $request->get('data')])->render();
        }
        echo $xhtml;
    }

    public static function setMessageModal(Request $request){
        $response   = view('main.modal.contentMessageModal', [
            'title'     => $request->get('title') ?? null,
            'content'   => $request->get('content') ?? null
        ])->render();
        echo $response;
    }

    public static function checkLoginAndSetShow(Request $request){
        $xhtmlModal             = '';
        $xhtmlButton            = '';
        $xhtmlButtonMobile      = '';
        $user = $request->user();
        $language               = $request->get('language') ?? 'vi';
        if(!empty($user)){
            /* đã đăng nhập => hiển thị button thông tin tài khoản */
            $xhtmlButton        = view('wallpaper.template.buttonLogin', ['user' => $user, 'language' => $language])->render();
            $xhtmlButtonMobile  = view('wallpaper.template.buttonLoginMobile', ['user' => $user, 'language' => $language])->render();
        }else {
            /* chưa đăng nhập => hiển thị button đăng nhập + modal */
            $xhtmlButton        = view('wallpaper.template.buttonLogin', ['language' => $language])->render();
            $xhtmlModal         = view('wallpaper.template.loginCustomerModal', ['language' => $language])->render();
            $xhtmlButtonMobile  = view('wallpaper.template.buttonLoginMobile', ['language' => $language])->render();
        }
        $result['modal']            = $xhtmlModal;
        $result['button']           = $xhtmlButton;
        $result['button_mobile']    = $xhtmlButtonMobile;
        return json_encode($result);
    }

    public function settingViewBy(Request $request){
        if(!empty($request->get('view_by'))){
            Cookie::queue('view_by', $request->get('view_by'), 3600);
        }
        return redirect()->back()->withInput();
    }

    public function showSortBox(Request $request){
        $xhtml              = '';
        $type               = $request->get('type');
        $id                 = $request->get('id');
        $totalSet           = $request->get('totalSet');
        $totalWallpaper     = $request->get('totalWallpaper');
        $viewBy             = Cookie::get('view_by') ?? 'set';
        /* select của filter */
        $categories         = Category::all();
        $styles             = Style::all();
        $events             = Event::all();
        /* giá trị selectBox */
        $categoryChoose     = new \Illuminate\Database\Eloquent\Collection;
        if($type=='category_info'){
            $categoryChoose = Category::select('*')
                                ->where('id', $id)
                                ->with('seo', 'en_seo')
                                ->first();
        }
        $styleChoose        = new \Illuminate\Database\Eloquent\Collection;
        if($type=='style_info'){
            $styleChoose    = Style::select('*')
                                ->where('id', $id)
                                ->with('seo', 'en_seo')
                                ->first();
        }
        $eventChoose        = new \Illuminate\Database\Eloquent\Collection;
        if($type=='event_info'){
            $eventChoose    = Event::select('*')
                                ->where('id', $id)
                                ->with('seo', 'en_seo')
                                ->first();
        }
        $xhtml              = view('wallpaper.template.sortContent', [
            'language'          => $language ?? 'vi',
            'totalSet'          => $totalSet,
            'totalWallpaper'    => $totalWallpaper,
            'viewBy'            => $viewBy,
            'categories'        => $categories,
            'styles'            => $styles,
            'events'            => $events,
            'categoryChoose'    => $categoryChoose,
            'styleChoose'       => $styleChoose,
            'eventChoose'       => $eventChoose
        ])->render();
        return $xhtml;
    }

    public static function loadImageFromGoogleCloud(Request $request){
        $response               = '';
        if(!empty($request->get('url_google_cloud'))){
            $url                = $request->get('url_google_cloud');
            $size               = $request->get('size') ?? null;
            $response           = config('admin.images.default_750x460');
            $contentImage       = Storage::disk('gcs')->get($url);
            if(!empty($contentImage)){
                if(!empty($size)){
                    $thumbnail  = ImageManagerStatic::make($contentImage)->resize($size, null, function ($constraint) {
                        $constraint->aspectRatio();
                    })->encode();
                }else {
                    $thumbnail  = ImageManagerStatic::make($contentImage)->encode();
                }
                $response       = 'data:image/jpeg;base64,'.base64_encode($thumbnail);
            }
        }
        echo $response;
    }

    // public static function loadImageWithResize(Request $request){
    //     $response       = '';
    //     if(!empty($request->get('url_image'))){
    //         $resize     = $request->get('resize') ?? 400;
    //         $response   = \App\Helpers\Image::streamResizedImage($request->get('url_image'), $resize);
    //     }
    //     echo $response;
    // }

    public static function loadImageSource(Request $request){
        $response       = null;
        if(!empty($request->get('order_code'))&&!empty($request->get('file_name'))){
            $fileName   = $request->get('file_name');
            $codeOrder  = $request->get('order_code');
            /* kiểm tra xem có được phép tải không */
            $flag   = self::checkShowSource($fileName, $codeOrder);
            if($flag==true){
                /* tiến hành tải bản xem trước ra */
                $contentImage   = Storage::disk('gcs')->get(config('main.google_cloud_storage.sources').'/'.$fileName);
                $thumbnail      = ImageManagerStatic::make($contentImage)->encode();
                $response       = 'data:image/jpeg;base64,'.base64_encode($thumbnail);
            }
        }
        echo $response;
    }

    public static function downloadImageSource(Request $request){
        if (!empty($request->get('order_code')) && !empty($request->get('file_name'))) {
            $fileName = $request->get('file_name');
            $codeOrder = $request->get('order_code');
    
            // Kiểm tra xem có được phép tải không
            $flag = self::checkShowSource($fileName, $codeOrder);

            if ($flag == true) {
                $urlImage       = config('main.google_cloud_storage.default_domain').config('main.google_cloud_storage.sources').$fileName;
                return response()->json([
                    'file_name' => pathinfo($urlImage)['filename'],
                    'url'       => $urlImage
                ]);
            }
        }
    }

    private static function checkShowSource($fileName, $codeOrder){
        $flag = false;
        if(!empty($fileName)&&!empty($codeOrder)){
            /* kiểm tra xem source có nằm trong đơn hàng không mới cho phép tải */
            $infoOrder      = \App\Models\Order::select('*')
                                ->where('code', $codeOrder)
                                ->with('products.infoPrice.wallpapers.infoWallpaper')
                                ->first();
            $arrayWallpaperFileName = [];
            if(!empty($infoOrder)){
                /* lấy tất cả wallpaper có trong đơn hàng (lọc all) */
                foreach($infoOrder->products as $product){
                    if($product->product_price_id=='all'){
                        foreach($product->infoProduct->prices as $price){
                            foreach($price->wallpapers as $wallpaper){
                                $arrayWallpaperFileName[] = $wallpaper->infoWallpaper->file_name;
                            }
                        }
                    }else {
                        foreach($product->infoPrice->wallpapers as $wallpaper){
                            $arrayWallpaperFileName[] = $wallpaper->infoWallpaper->file_name;
                        }
                    }
                }
            }
            /* kiểm tra xem có được phép tải không */
            $flag = in_array($fileName, $arrayWallpaperFileName);
        }
        return $flag;
    }

    public static function loadmoreFreeWallpapers(Request $request){
        $response           = [];
        $content            = '';
        if(!empty($request->get('total'))){
            $language       = Cookie::get('language') ?? 'vi';
            $loaded         = $request->get('loaded');
            $requestLoad    = $request->get('requestLoad');
            $wallpapers     = FreeWallpaper::select('*')
                                ->orderBy('id', 'DESC')
                                ->skip($loaded)
                                ->take($requestLoad)
                                ->get();
            foreach($wallpapers as $wallpaper){
                $content    .= view('wallpaper.free.item', compact('wallpaper', 'language'))->render();
            }
        }
        $response['content']    = $content;
        $response['loaded']     = $loaded + $requestLoad;
        return json_encode($response);
    }
}
