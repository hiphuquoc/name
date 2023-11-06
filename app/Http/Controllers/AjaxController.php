<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManagerStatic;
use App\Models\District;
use App\Models\Product;
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
        if(!empty($request->get('key_search'))){
            $keySearch          = \App\Helpers\Charactor::convertStringSearch($request->get('key_search'));
            $products           = Product::select('product_info.*')
                ->whereHas('prices.wallpapers', function(){

                })
                ->join('seo', 'seo.id', '=', 'product_info.seo_id')
                ->where('code', 'like', '%'.$keySearch.'%')
                ->orWhere('name', 'like', '%'.$keySearch.'%')
                ->orWhere('en_name', 'like', '%'.$keySearch.'%')
                ->skip(0)
                ->take(6)
                ->with('seo', 'prices.wallpapers.infoWallpaper')
                ->orderBy('seo.ordering', 'DESC')
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
                    if(!empty($product->prices[0]->wallpapers[0]->infoWallpaper->file_url_hosting)) $image = \App\Helpers\Image::getUrlImageMiniByUrlImage($product->prices[0]->wallpapers[0]->infoWallpaper->file_url_hosting);
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
                $route          = $language=='vi' ? route('main.searchProduct') : route('main.enSearchProduct');
                $response       .= '<a href="'.$route.'?key_search='.request('key_search').'" class="searchViewBefore_selectbox_item">
                                        Xem tất cả (<span style="font-size:1.1rem;">'.$count.'</span>) <i class="fa-solid fa-angles-right"></i>
                                    </a>';
            }else {
                $response       = '<div class="searchViewBefore_selectbox_item">Không có sản phẩm phù hợp!</div>';
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

    public static function loadImageWithResize(Request $request){
        $response       = '';
        if(!empty($request->get('url_image'))){
            $resize     = $request->get('resize') ?? 400;
            $response   = \App\Helpers\Image::streamResizedImage($request->get('url_image'), $resize);
        }
        echo $response;
    }

    public static function loadImageSource(Request $request){
        $result = '';
        if(!empty($request->get('order_code'))&&!empty($request->get('wallpaper_info_id'))){
            $idWallpaper    = $request->get('wallpaper_info_id');
            /* lấy từ order code để chống bug dò wallpaper */
            $infoOrder      = \App\Models\Order::select('*')
                                ->where('code', $request->get('order_code'))
                                ->first();
            $infoWallpaper  = new \Illuminate\Database\Eloquent\Collection;
            foreach($infoOrder->products as $product){
                if(empty($product->infoPrice)){
                    /* trường hợp all => duyệt qua tìm source cần tải */
                    foreach($product->infoProduct->prices as $price){
                        foreach($price->wallpapers as $wallpaper){
                            if($idWallpaper==$wallpaper->infoWallpaper->id) {
                                $infoWallpaper = $wallpaper->infoWallpaper;
                                break;
                            }
                        }
                    }
                }else {
                    /* trường hợp có product_price_id */
                    foreach($product->infoPrice->wallpapers as $wallpaper){
                        if($idWallpaper==$wallpaper->infoWallpaper->id) {
                            $infoWallpaper = $wallpaper->infoWallpaper;
                            break;
                        }
                    }
                }
            }
            dd($infoWallpaper);
        }
        echo $result;
    }

    public function settingViewBy(Request $request){
        if(!empty($request->get('view_by'))){
            Cookie::queue('view_by', $request->get('view_by'), 3600);
        }
        return redirect()->back()->withInput();
    }
}
