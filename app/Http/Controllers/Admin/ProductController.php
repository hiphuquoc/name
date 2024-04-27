<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\BuildInsertUpdateModel;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Helpers\Upload;
use App\Http\Requests\ProductRequest;
use App\Models\Seo;
use App\Models\RelationSeoEnSeo;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductPrice;
use App\Models\Wallpaper;
use App\Models\RelationCategoryProduct;
use App\Models\RelationSeoProductInfo;
use App\Models\Prompt;
use App\Models\SeoContent;
use App\Http\Controllers\Admin\SliderController;
use App\Http\Controllers\Admin\IndexController;
// use App\Http\Controllers\Admin\GalleryController;
// use App\Http\Controllers\Admin\SourceController;
use App\Models\RelationProductPriceWallpaperInfo;

class ProductController extends Controller {

    public function __construct(BuildInsertUpdateModel $BuildInsertUpdateModel){
        $this->BuildInsertUpdateModel  = $BuildInsertUpdateModel;
    }

    public function createAndUpdate(ProductRequest $request){
        try {
            DB::beginTransaction();
            /* ngôn ngữ */
            $keyTable           = 'product_info';
            $idSeo              = $request->get('seo_id');
            $idProduct          = $request->get('product_info_id');
            $language           = $request->get('language');
            $type               = $request->get('type');
            /* upload image */
            $dataPath           = [];
            if($request->hasFile('image')) {
                $name           = !empty($request->get('slug')) ? $request->get('slug') : time();
                $fileName       = $name.'.'.config('image.extension');
                $folderUpload   =  config('main.google_cloud_storage.wallpapers');
                $dataPath       = Upload::uploadWallpaper($request->file('image'), $fileName, $folderUpload);
            }
            /* update page */
            $seo                = $this->BuildInsertUpdateModel->buildArrayTableSeo($request->all(), $keyTable, $dataPath);
            if(!empty($idSeo)&&$type=='edit'){
                Seo::updateItem($idSeo, $seo);
            }else {
                $idSeo = Seo::insertItem($seo);
            }
            /* insert seo_content */
            SeoContent::select('*')
                ->where('seo_id', $idSeo)
                ->delete();
            foreach($request->get('content') as $content){
                SeoContent::insertItem([
                    'seo_id'    => $idSeo,
                    'content'   => $content
                ]);
            }
            /* insert hoặc update product_info */
            if(empty($idProduct)){ /* check xem create product hay update product */
                $infoProduct    = $this->BuildInsertUpdateModel->buildArrayTableProductInfo($request->all(), $idSeo);
                $idProduct      = Product::insertItem($infoProduct);
            }
            /* relation_seo_product_info */
            $relationSeoCategoryInfo = RelationSeoProductInfo::select('*')
                                    ->where('seo_id', $idSeo)
                                    ->where('product_info_id', $idProduct)
                                    ->first();
            if(empty($relationSeoCategoryInfo)) RelationSeoProductInfo::insertItem([
                'seo_id'            => $idSeo,
                'product_info_id'   => $idProduct
            ]);
            /* nếu là bảng việt (gốc) mới cập nhật tiếp */
            if($language=='vi'){
                /* update product_price 
                    => xóa các product_price nào id không tồn tại trong mảng mới 
                    => nào có tồn tại thì update - nào không thì thêm mới 
                */
                $priceSave          = [];
                foreach($request->get('prices') as $price){
                    if(!empty($price['id'])) $priceSave[]   = $price['id'];
                }
                $productPriceDelete = ProductPrice::select('*')
                                        ->where('product_info_id', $idProduct)
                                        ->whereNotIn('id', $priceSave)
                                        ->with('wallpapers')
                                        ->get();
                /* duyệt mảng delete files */
                foreach($productPriceDelete as $productPrice) {
                    RelationProductPriceWallpaperInfo::select('*')
                        ->where('product_price_id', $productPrice->id)
                        ->delete();
                    /* xóa product price */
                    $productPrice->delete();
                }
                /* update lại các product price còn lại */
                foreach($request->get('prices') as $price){
                    if(!empty($price['code_name'])&&!empty($price['price'])){
                        if(!empty($price['id'])){
                            /* update */
                            $dataPrice              = $this->BuildInsertUpdateModel->buildArrayTableProductPrice($price, $idProduct, 'update');
                            ProductPrice::updateItem($price['id'], $dataPrice);
                        }else {
                            /* insert */
                            $dataPrice              = $this->BuildInsertUpdateModel->buildArrayTableProductPrice($price, $idProduct, 'insert');
                            ProductPrice::insertItem($dataPrice);
                        }
                    }
                }
                /* chủ đề */
                RelationCategoryProduct::select('*')
                    ->where('product_info_id', $idProduct)
                    ->delete();
                if(!empty($request->get('categories'))){
                    foreach($request->get('categories') as $category){
                        RelationCategoryProduct::insertItem([
                            'product_info_id'   => $idProduct,
                            'category_info_id'  => $category
                        ]);
                    }
                }
                /* insert slider và lưu CSDL */
                if($request->hasFile('slider')&&!empty($idProduct)){
                    $name           = !empty($request->get('slug')) ? $request->get('slug') : time();
                    $params         = [
                        'attachment_id'     => $idProduct,
                        'relation_table'    => $keyTable,
                        'name'              => $name
                    ];
                    SliderController::upload($request->file('slider'), $params);
                }
            }
            DB::commit();
            /* Message */
            $message        = [
                'type'      => 'success',
                'message'   => '<strong>Thành công!</strong> Đã cập nhật Sản phẩm!'
            ];
            /* nếu có tùy chọn index => gửi google index */
            if($request->get('index_google')==true) {
                $flagIndex = IndexController::indexUrl($idSeo);
                if($flagIndex==200){
                    $message['message'] = '<strong>Thành công!</strong> Đã cập nhật Sản phẩm và Báo Google Index!';
                }else {
                    $message['message'] = '<strong>Thành công!</strong> Đã cập nhật Sản phẩm! <span style="color:red;">nhưng báo Google Index lỗi</span>';
                }
            }
        } catch (\Exception $exception){
            DB::rollBack();
            /* Message */
            $message        = [
                'type'      => 'danger',
                'message'   => '<strong>Thất bại!</strong> Có lỗi xảy ra, vui lòng thử lại'
            ];
        }
        $request->session()->put('message', $message);
        return redirect()->route('admin.product.view', ['id' => $idProduct, 'language' => $language]);
    }

    public static function view(Request $request){
        $keyTable           = 'product_info';
        $message            = $request->get('message') ?? null;
        $id                 = $request->get('id') ?? 0;
        $language           = $request->get('language') ?? null;
        /* chức năng copy source */
        $idSeoSourceToCopy  = $request->get('id_seo_source') ?? 0;
        $itemSourceToCopy   = Product::select('*')
                                ->whereHas('seos.infoSeo', function($query) use($idSeoSourceToCopy){
                                    $query->where('id', $idSeoSourceToCopy);
                                })
                                ->with(['files' => function($query) use($keyTable){
                                    $query->where('relation_table', $keyTable);
                                }])
                                ->with('seo', 'seos', 'prices.wallpapers.infoWallpaper', 'categories')
                                ->first();
        $itemSeoSourceToCopy    = [];
        if(!empty($itemSourceToCopy->seos)){
            foreach($itemSourceToCopy->seos as $s){
                if($s->infoSeo->language==$language) {
                    $itemSeoSourceToCopy = $s->infoSeo;
                    break;
                }
            }
        }
        /* lấy thông tin item */
        $item               = Product::select('*')
                                ->where('id', $id)
                                ->with(['files' => function($query) use($keyTable){
                                    $query->where('relation_table', $keyTable);
                                }])
                                ->with('seo', 'seos', 'prices.wallpapers.infoWallpaper', 'categories')
                                ->first();
        /* lấy item seo theo ngôn ngữ được chọn */
        $itemSeo            = [];
        if(!empty($item->seos)){
            foreach($item->seos as $s){
                if($s->infoSeo->language==$language) {
                    $itemSeo = $s->infoSeo;
                    break;
                }
            }
        }
        /* prompts */
        $prompts            = Prompt::select('*')
                                ->where('reference_table', $keyTable)
                                ->get();
        /* gộp lại thành parents và lọc bỏ page hinh-nen-dien-thoai */
        $parents            = Category::all();
        $wallpapers         = Wallpaper::select('*')
                                ->get();
        $categories         = $parents;
        /* trang canonical -> cùng là sản phẩm */
        $idProduct          = $item->id ?? 0;
        $sources            = Product::select('*')
                                ->whereHas('seos.infoSeo', function($query) use($language){
                                    $query->where('language', $language);
                                })
                                ->where('id', '!=', $idProduct)
                                ->get();
        /* type */
        $type               = !empty($item) ? 'edit' : 'create';
        $type               = $request->get('type') ?? $type;
        return view('admin.product.view', compact('item', 'itemSeo', 'itemSourceToCopy', 'itemSeoSourceToCopy', 'prompts', 'language', 'wallpapers', 'type', 'categories', 'sources', 'parents', 'message'));
    }

    public static function list(Request $request){
        $params                         = [];
        /* Search theo tên */
        if(!empty($request->get('search_name'))) $params['search_name'] = $request->get('search_name');
        /* Search theo danh mục */
        if(!empty($request->get('search_category'))) $params['search_category'] = $request->get('search_category');
        /* paginate */
        $viewPerPage        = Cookie::get('viewProductInfo') ?? 20;
        $params['paginate'] = $viewPerPage;
        $list               = Product::getList($params);
        $categories         = Category::select('*')
                                ->whereHas('products', function(){
                                    /* có sản phẩm mới lấy ra */
                                })
                                ->with('products')
                                ->get();
        return view('admin.product.list', compact('list', 'categories', 'viewPerPage', 'params'));
    }

    public function delete(Request $request){
        if(!empty($request->get('id'))){
            try {
                DB::beginTransaction();
                $id         = $request->get('id');
                $info       = Product::select('*')
                                ->where('id', $id)
                                ->with(['files' => function($query){
                                    $query->where('relation_table', 'seo.type');
                                }])
                                ->with('seo', 'seos', 'seo.contents', 'prices.wallpapers')
                                ->first();
                /* xóa ảnh đại diện trên google_clouds */ 
                Upload::deleteWallpaper($info->seo->image);
                /* xóa bảng product_price */
                $info->prices->each(function ($price) {
                    $price->wallpapers()->delete();
                });
                $info->prices()->delete();
                $info->categories()->delete();
                $info->files()->delete();
                /* delete các trang seos ngôn ngữ */
                foreach($info->seos as $s){
                    /* xóa ảnh đại diện trên google_clouds */ 
                    Upload::deleteWallpaper($s->infoSeo->image);
                    foreach($s->infoSeo->contents as $c) $c->delete();
                    $s->infoSeo()->delete();
                    $s->delete();
                }
                /* xóa product_info */
                $info->delete();
                DB::commit();
                return true;
            } catch (\Exception $exception){
                DB::rollBack();
                return false;
            }
        }
    }
}
