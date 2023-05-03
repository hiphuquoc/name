<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\District;
use App\Models\Product;
use App\Models\RegistryEmail;
use App\Models\Seller;
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
                ->join('seo', 'seo.id', '=', 'product_info.seo_id')
                ->where('code', 'like', '%'.$keySearch.'%')
                ->orWhere('name', 'like', '%'.$keySearch.'%')
                ->skip(0)
                ->take(6)
                ->with('seo')
                ->orderBy('seo.ordering', 'DESC')
                ->get();
            $count              = Product::select('product_info.*')
                ->where('code', 'like', '%'.$keySearch.'%')
                ->orWhere('name', 'like', '%'.$keySearch.'%')
                ->count();
            $response           = null;
            if(!empty($products)&&$products->isNotEmpty()){
                foreach($products as $product){
                    $title      = $product->name ?? $product->seo->title ?? null;
                    $priceOld   = null;
                    if($product->prices[0]->price<$product->prices[0]->price_before_promotion) $priceOld = '<div class="searchViewBefore_selectbox_item_content_price_old">'.number_format($product->prices[0]->price_before_promotion).config('main.currency_unit').'</div>';
                    $response       .= '<a href="/'.$product->seo->slug_full.'" class="searchViewBefore_selectbox_item">
                                            <div class="searchViewBefore_selectbox_item_image">
                                                <img src="'.Storage::url($product->prices[0]->files[0]->file_path).'" alt="'.$title.'" title="'.$title.'" />
                                            </div>
                                            <div class="searchViewBefore_selectbox_item_content">
                                                <div class="searchViewBefore_selectbox_item_content_title maxLine_2">
                                                    '.$title.'
                                                </div>
                                                <div class="searchViewBefore_selectbox_item_content_price">
                                                    <div>'.number_format($product->prices[0]->price).config('main.currency_unit').'</div>
                                                    '.$priceOld.'
                                                </div>
                                            </div>
                                        </a>';
                }
                $response           .= '<a href="'.route('main.searchProduct').'?key_search='.request('key_search').'" class="searchViewBefore_selectbox_item">
                                            Xem tất cả (<span style="font-size:1.1rem;">'.$count.'</span>) <i class="fa-solid fa-angles-right"></i>
                                        </a>';
            }else {
                $response       = '<div class="searchViewBefore_selectbox_item">Không có sản phẩm phù hợp!</div>';
            }
            echo $response;
        }
    }

    public static function registryEmail(Request $request){
        $idRegistryEmail    = RegistryEmail::insertItem([
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

    // public function registrySeller(Request $request){
    //     /* insert seller_info */
    //     $insertSeller   = $this->BuildInsertUpdateModel->buildArrayTableSellerInfo($request->all());
    //     $idSeller       = Seller::insertItem($insertSeller);
    //     if(!empty($idSeller)){
    //         $result['type']     = 'success';
    //         $result['title']    = 'Đăng ký phân phối thành công!';
    //         $result['content']  = 'Cảm ơn bạn đã cộng tác cùng '.config('main.company_name').'. Chúng tôi sẽ liên hệ lại với bạn trong thời gian sớm nhất'; 
    //     }else {
    //         $result['type']     = 'error';
    //         $result['title']    = 'Đăng ký phân phối thất bại!';
    //         $result['content']  = 'Có lỗi xảy ra, vui lòng thử lại hoặc liên hệ trực tiếp chúng tôi qua hotline: '.config('main.hotline'); 
    //     }
    //     return json_encode($result);
    // }

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
        if(!empty($user)){
            /* đã đăng nhập => hiển thị button thông tin tài khoản */
            $xhtmlButton        = view('wallpaper.template.buttonLogin', ['user' => $user])->render();
            $xhtmlButtonMobile  = view('wallpaper.template.buttonLoginMobile', ['user' => $user])->render();
        }else {
            /* chưa đăng nhập => hiển thị button đăng nhập + modal */
            $xhtmlButton        = view('wallpaper.template.buttonLogin')->render();
            $xhtmlModal         = view('wallpaper.template.loginCustomerModal')->render();
            $xhtmlButtonMobile  = view('wallpaper.template.buttonLoginMobile')->render();
        }
        $result['modal']            = $xhtmlModal;
        $result['button']           = $xhtmlButton;
        $result['button_mobile']    = $xhtmlButtonMobile;
        return json_encode($result);
    }
}
