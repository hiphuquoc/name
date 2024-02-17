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
use App\Models\EnSeo;
use App\Models\RelationSeoEnSeo;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductContent;
use App\Models\ProductPrice;
use App\Models\Wallpaper;
use App\Models\RelationCategoryProduct;
use App\Models\RelationStyleProduct;
use App\Models\RelationEventProduct;
use App\Http\Controllers\Admin\SliderController;
// use App\Http\Controllers\Admin\GalleryController;
// use App\Http\Controllers\Admin\SourceController;
use App\Models\RelationProductPriceWallpaperInfo;

class ProductController extends Controller {

    public function __construct(BuildInsertUpdateModel $BuildInsertUpdateModel){
        $this->BuildInsertUpdateModel  = $BuildInsertUpdateModel;
    }

    public function create(ProductRequest $request){
        try {
            DB::beginTransaction();
            $keyTable           = 'product_info';
            /* upload image */
            $dataPath           = [];
            if($request->hasFile('image')) {
                $name           = !empty($request->get('slug')) ? $request->get('slug') : time();
                $dataPath       = Upload::uploadThumnail($request->file('image'), $name);
            }
            /* insert page */
            $insertSeo          = $this->BuildInsertUpdateModel->buildArrayTableSeo($request->all(), $keyTable, $dataPath);
            $seoId              = Seo::insertItem($insertSeo);
            $insertEnSeo        = $this->BuildInsertUpdateModel->buildArrayTableEnSeo($request->all(), $keyTable, $dataPath);
            $enSeoId            = EnSeo::insertItem($insertEnSeo);
            /* kết nối bảng vi và en */
            RelationSeoEnSeo::insertItem([
                'seo_id'    => $seoId,
                'en_seo_id' => $enSeoId
            ]);
            /* insert product_info */
            $insertProduct      = $this->BuildInsertUpdateModel->buildArrayTableProductInfo($request->all(), $seoId, $enSeoId);
            $idProduct          = Product::insertItem($insertProduct);
            /* insert product_content */
            if(!empty($request->get('contents'))){
                foreach($request->get('contents') as $content){
                    if(!empty($content['name'])&&!empty($content['content'])){
                        ProductContent::insertItem([
                            'product_info_id'   => $idProduct,
                            'name'              => $content['name'],
                            'content'           => $content['content'],
                            'en_name'           => $content['en_name'],
                            'en_content'        => $content['en_content']
                        ]);
                    }
                }
            }
            /* insert product_price */
            foreach($request->get('prices') as $price){
                $insertPrice    = $this->BuildInsertUpdateModel->buildArrayTableProductPrice($price, $idProduct, 'insert');
                ProductPrice::insertItem($insertPrice);
            }
            /* chủ đề */
            foreach($request->get('categories') as $category){
                RelationCategoryProduct::insertItem([
                    'product_info_id'   => $idProduct,
                    'category_info_id'  => $category
                ]);
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
            DB::commit();
            /* Message */
            $message        = [
                'type'      => 'success',
                'message'   => '<strong>Thành công!</strong> Dã tạo Sản phẩm mới'
            ];
        } catch (\Exception $exception){
            DB::rollBack();
            /* Message */
            $message        = [
                'type'      => 'danger',
                'message'   => '<strong>Thất bại!</strong> Có lỗi xảy ra, vui lòng thử lại'
            ];
        }
        $request->session()->put('message', $message);
        return redirect()->route('admin.product.view', ['id' => $idProduct]);
    }

    public function update(ProductRequest $request){
        try {
            DB::beginTransaction();
            $seoId              = $request->get('seo_id');
            $enSeoId            = $request->get('en_seo_id');
            $idProduct          = $request->get('product_info_id');
            $keyTable           = 'product_info';
            /* upload image */
            $dataPath           = [];
            if($request->hasFile('image')) {
                $name           = !empty($request->get('slug')) ? $request->get('slug') : time();
                $dataPath       = Upload::uploadThumnail($request->file('image'), $name);
            }
            /* update page */
            $insertSeo          = $this->BuildInsertUpdateModel->buildArrayTableSeo($request->all(), $keyTable, $dataPath);
            Seo::updateItem($seoId, $insertSeo);
            if(!empty($enSeoId)){
                $updateEnSeo        = $this->BuildInsertUpdateModel->buildArrayTableEnSeo($request->all(), $keyTable, $dataPath);
                EnSeo::updateItem($enSeoId, $updateEnSeo);
            }else {
                $insertEnSeo        = $this->BuildInsertUpdateModel->buildArrayTableEnSeo($request->all(), $keyTable, $dataPath);
                $enSeoId            = EnSeo::insertItem($insertEnSeo);
            }
            /* kết nối bảng vi và en */
            RelationSeoEnSeo::select('*')
                ->where('seo_id', $seoId)
                ->delete();
            RelationSeoEnSeo::insertItem([
                'seo_id'    => $seoId,
                'en_seo_id' => $enSeoId
            ]);
            /* insert product_info */
            $insertProduct      = $this->BuildInsertUpdateModel->buildArrayTableProductInfo($request->all(), $seoId, $enSeoId);
            Product::updateItem($idProduct, $insertProduct);
            /* insert product_content */
            ProductContent::select('*')
                ->where('product_info_id', $idProduct)
                ->delete();
            if(!empty($request->get('contents'))){
                foreach($request->get('contents') as $content){
                    if(!empty($content['name'])&&!empty($content['content'])){
                        ProductContent::insertItem([
                            'product_info_id'   => $idProduct,
                            'name'              => $content['name'],
                            'content'           => $content['content'],
                            'en_name'           => $content['en_name'],
                            'en_content'        => $content['en_content']
                        ]);
                    }
                }
            }
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
                if(!empty($price['name'])&&!empty($price['price'])){
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
            DB::commit();
            /* Message */
            $message        = [
                'type'      => 'success',
                'message'   => '<strong>Thành công!</strong> Đã cập nhật Sản phẩm!'
            ];
        } catch (\Exception $exception){
            DB::rollBack();
            /* Message */
            $message        = [
                'type'      => 'danger',
                'message'   => '<strong>Thất bại!</strong> Có lỗi xảy ra, vui lòng thử lại'
            ];
        }
        $request->session()->put('message', $message);
        return redirect()->route('admin.product.view', ['id' => $idProduct]);
    }

    public static function view(Request $request){
        $message            = $request->get('message') ?? null;
        $id                 = $request->get('id') ?? 0;
        $item               = Product::select('*')
                                ->where('id', $id)
                                ->with(['files' => function($query){
                                    $query->where('relation_table', 'product_info');
                                }])
                                ->with('seo', 'en_seo', 'contents', 'prices.wallpapers.infoWallpaper', 'categories')
                                ->first();
        $categories         = Category::all();
        /* gộp lại thành parents và lọc bỏ page hinh-nen-dien-thoai */
        $parents            = Category::all();
        $wallpapers         = Wallpaper::select('*')
                                ->get();
        /* type */
        $type               = !empty($item) ? 'edit' : 'create';
        $type               = $request->get('type') ?? $type;
        return view('admin.product.view', compact('item', 'wallpapers', 'type', 'categories', 'parents', 'message'));
    }

    public static function list(Request $request){
        $params                         = [];
        /* Search theo tên */
        if(!empty($request->get('search_name'))) $params['search_name'] = $request->get('search_name');
        /* Search theo nhãn hàng */
        if(!empty($request->get('search_event'))) $params['search_event'] = $request->get('search_event');
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
                                ->with('seo', 'en_seo', 'prices.wallpapers', 'contents')
                                ->first();
                /* xóa ảnh đại diện sản phẩm trong thư mục */
                $imageSmallPath     = Storage::path(config('admin.images.folderUpload').basename($info->seo->image_small));
                if(file_exists($imageSmallPath)) @unlink($imageSmallPath);
                $imagePath          = Storage::path(config('admin.images.folderUpload').basename($info->seo->image));
                if(file_exists($imagePath)) @unlink($imagePath);
                /* xóa content */
                $info->contents()->delete();
                /* xóa bảng product_price */
                $info->prices->each(function ($price) {
                    $price->wallpapers()->delete();
                });
                $info->prices()->delete();
                /* xóa relation_category_product */
                $info->categories()->delete();
                /* delete relation seo_en_seo */
                RelationSeoEnSeo::select('*')
                    ->where('seo_id', $info->seo->id)
                    ->where('en_seo_id', $info->en_seo->id)
                    ->delete();
                /* delete bảng seo */
                $info->seo()->delete();
                /* delete bảng en_seo */
                $info->en_seo()->delete();
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
