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
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\ProductContent;
use App\Models\ProductPrice;
use App\Models\RelationCategoryProduct;
use App\Http\Controllers\Admin\SliderController;
use App\Http\Controllers\Admin\GalleryController;
use App\Http\Controllers\Admin\SourceController;
use PHPUnit\TextUI\XmlConfiguration\CodeCoverage\Report\Php;

class ProductController extends Controller {

    public function __construct(BuildInsertUpdateModel $BuildInsertUpdateModel){
        $this->BuildInsertUpdateModel  = $BuildInsertUpdateModel;
    }

    public function create(ProductRequest $request){
        try {
            DB::beginTransaction();
            /* upload image */
            $dataPath           = [];
            if($request->hasFile('image')) {
                $name           = !empty($request->get('slug')) ? $request->get('slug') : time();
                $dataPath       = Upload::uploadThumnail($request->file('image'), $name);
            }
            /* insert page */
            $insertSeo          = $this->BuildInsertUpdateModel->buildArrayTableSeo($request->all(), 'product_info', $dataPath);
            $idSeo              = Seo::insertItem($insertSeo);
            /* insert product_info */
            $insertProduct      = $this->BuildInsertUpdateModel->buildArrayTableProductInfo($request->all(), $idSeo);
            $idProduct          = Product::insertItem($insertProduct);
            /* insert product_content */
            if(!empty($request->get('contents'))){
                foreach($request->get('contents') as $content){
                    if(!empty($content['name'])&&!empty($content['content'])){
                        ProductContent::insertItem([
                            'product_info_id'   => $idProduct,
                            'name'              => $content['name'],
                            'content'           => $content['content']
                        ]);
                    }
                }
            }
            /* insert product_price */
            foreach($request->get('prices') as $price){
                $insertPrice    = $this->BuildInsertUpdateModel->buildArrayTableProductPrice($price, $idProduct);
                ProductPrice::insertItem($insertPrice);
            }
            /* danh mục sản phẩm */
            foreach($request->get('categories') as $category){
                RelationCategoryProduct::insertItem([
                    'product_info_Id'   => $idProduct,
                    'category_info_id'  => $category
                ]);
            }
            /* insert slider và lưu CSDL */
            if($request->hasFile('slider')&&!empty($idProduct)){
                $name           = !empty($request->get('slug')) ? $request->get('slug') : time();
                $params         = [
                    'attachment_id'     => $idProduct,
                    'relation_table'    => 'product_info',
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
            $idSeo              = $request->get('seo_id');
            $idProduct          = $request->get('product_info_id');
            /* upload image */
            $dataPath           = [];
            if($request->hasFile('image')) {
                $name           = !empty($request->get('slug')) ? $request->get('slug') : time();
                $dataPath       = Upload::uploadThumnail($request->file('image'), $name);
            }
            /* update page */
            $insertSeo          = $this->BuildInsertUpdateModel->buildArrayTableSeo($request->all(), 'product_info', $dataPath);
            Seo::updateItem($idSeo, $insertSeo);
            /* insert product_info */
            $insertProduct      = $this->BuildInsertUpdateModel->buildArrayTableProductInfo($request->all(), $idSeo);
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
                            'content'           => $content['content']
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
                                    ->with('files')
                                    ->get();
            /* duyệt mảng delete files */
            foreach($productPriceDelete as $productPrice) {
                foreach($productPrice->files as $file) {
                    Gallerycontroller::removeById($file->id);
                    SourceController::removeById($file->id);
                }
                /* xóa product price */
                $productPrice->delete();
            }
            /* update lại các product price còn lại */
            // dd($request->get('prices'));
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
            /* danh mục sản phẩm */
            RelationCategoryProduct::select('*')
                                        ->where('product_info_id', $idProduct)
                                        ->delete();
            foreach($request->get('categories') as $category){
                RelationCategoryProduct::insertItem([
                    'product_info_Id'   => $idProduct,
                    'category_info_id'  => $category
                ]);
            }
            /* insert slider và lưu CSDL */
            if($request->hasFile('slider')&&!empty($idProduct)){
                $name           = !empty($request->get('slug')) ? $request->get('slug') : time();
                $params         = [
                    'attachment_id'     => $idProduct,
                    'relation_table'    => 'product_info',
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
                                ->with('seo', 'contents', 'prices.files', 'categories', 'brand')
                                ->first();
        $categories         = Category::all();
        $brands             = Brand::all();
        $parents            = $categories;
        /* type */
        $type               = !empty($item) ? 'edit' : 'create';
        $type               = $request->get('type') ?? $type;
        return view('admin.product.view', compact('item', 'type', 'categories', 'brands', 'parents', 'message'));
    }

    public static function list(Request $request){
        $params                         = [];
        /* Search theo tên */
        if(!empty($request->get('search_name'))) $params['search_name'] = $request->get('search_name');
        /* Search theo nhãn hàng */
        if(!empty($request->get('search_brand'))) $params['search_brand'] = $request->get('search_brand');
        /* Search theo danh mục */
        if(!empty($request->get('search_category'))) $params['search_category'] = $request->get('search_category');
        /* paginate */
        $viewPerPage        = Cookie::get('viewProductInfo') ?? 20;
        $params['paginate'] = $viewPerPage;
        $list               = Product::getList($params);
        $brands             = Brand::all();
        $categories         = Category::all();
        return view('admin.product.list', compact('list', 'brands', 'categories', 'viewPerPage', 'params'));
    }

    public function delete(Request $request){
        if(!empty($request->get('id'))){
            try {
                DB::beginTransaction();
                $id         = $request->get('id');
                $info       = Product::select('*')
                                ->where('id', $id)
                                ->with('seo', 'prices.files')
                                ->first();
                /* xóa ảnh đại diện sản phẩm trong thư mục */
                $imageSmallPath     = Storage::path(config('admin.images.folderUpload').basename($info->seo->image_small));
                if(file_exists($imageSmallPath)) @unlink($imageSmallPath);
                $imagePath          = Storage::path(config('admin.images.folderUpload').basename($info->seo->image));
                if(file_exists($imagePath)) @unlink($imagePath);
                /* xóa ảnh của product_price */
                foreach($info->prices as $price){
                    foreach($price->files as $file){
                        GalleryController::removeById($file->id);
                    }
                    foreach($price->sources as $source){
                        SourceController::removeById($source->id);
                    }
                }
                /* xóa bảng product_price */
                $info->prices()->delete();
                /* xóa relation_product_category */
                $info->categories()->delete();
                /* delete bảng seo của product_info */
                $info->seo()->delete();
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

    public static function uploadImageProductPriceAjaxToFile(Request $request){
        set_time_limit(-1);
        ini_set('max_execution_time', -1);
        ini_set('max_input_time', -1);
        $result             = [];
        $idProductPrice     = $request->get('product_price_id') ?? 0;
        if(!empty($idProductPrice)&&$request->hasFile('files')){
            $files          = $request->file('files');
            $name           = $request->get('slug') ?? time();
            $params         = [
                'attachment_id'     => $idProductPrice,
                'relation_table'    => 'product_price',
                'name'              => $name
            ];
            $result         = GalleryController::upload($files, $params);
        }
        
        return json_encode($result);
    }

    public static function uploadImageProductPriceAjaxToSource(Request $request){
        set_time_limit(-1);
        ini_set('max_execution_time', -1);
        ini_set('max_input_time', -1);
        $result             = [];
        $idProductPrice     = $request->get('product_price_id') ?? 0;
        if(!empty($idProductPrice)&&$request->hasFile('files')){
            $files          = $request->file('files');
            $name           = $request->get('slug') ?? time();
            $folderDrive    = $request->get('folder_drive').'-'.$name;
            $params         = [
                'attachment_id'     => $idProductPrice,
                'relation_table'    => 'product_price',
                'folder_drive'      => $folderDrive
            ];
            $result         = SourceController::upload($files, 'wallpaper_mobile', $params);
        }
        return json_encode($result);
    }
}
