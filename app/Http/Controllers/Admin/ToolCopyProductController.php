<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use App\Models\Seo;
use App\Models\SeoContent;
use App\Models\Product;
use App\Models\RelationSeoProductInfo;

class ToolCopyProductController extends Controller {

    public function view(Request $request){
        return view('admin.toolCopyProduct.view');
    }

    public function create(Request $request){
        $response  = [];
        if(!empty($request->get('product_source'))&&!empty($request->get('product_copy'))){
            $urlSource      = $request->get('product_source');
            $urlSearch      = $request->get('product_copy');
            /* lấy sản phẩm gốc */
            $productSource  = Product::select('*')
                ->whereHas('seo', function ($query) use($urlSource){
                    $query->where('slug', $urlSource);
                })
                ->with('seo', 'seos.infoSeo.contents')
                ->first();
            if(empty($productSource)){
                $message        = [
                    'type'      => 'danger',
                    'message'   => 'Có lỗi xảy ra, không tìm thấy sản phẩm gốc!',
                ];
            }else {
                /* lấy danh sách sản phẩm cần copy */
                $tmp            = Product::select('*')
                    ->whereHas('seo', function ($query) use($urlSearch){
                        $query->where('slug', 'LIKE', $urlSearch.'%');
                    })
                    ->where('id', '!=', $productSource->id)
                    ->with('seo', 'seos.infoSeo.contents')
                    ->get();
                if($tmp->count()<=0){
                    $message        = [
                        'type'      => 'danger',
                        'message'   => 'Có lỗi xảy ra, không tìm thấy sản phẩm cần được copy!',
                    ];
                }
                /* tiến hành copy */
                $k      = 1;
                foreach ($tmp as $t) {
                    /* xóa relation seos -> infoSeo -> contents (nếu có) */
                    foreach ($t->seos as $seo) {
                        foreach ($seo->infoSeo->contents as $content) {
                            SeoContent::select('*')
                                ->where('id', $content->id)
                                ->delete();
                        }
                        \App\Models\RelationSeoProductInfo::select('*')
                            ->where('seo_id', $seo->seo_id)
                            ->delete();
                        Seo::select('*')
                            ->where('id', $seo->seo_id)
                            ->delete();
                    }
                    /* tạo dữ liệu mới */
                    $i = 0;
                    foreach ($productSource->seos as $seoS) {
                        /* tạo seo */
                        $tmp2   = $seoS->infoSeo->toArray();
                        $insert = [];
                        foreach ($tmp2 as $key => $value) {
                            if ($key != 'contents' && $key != 'id') $insert[$key] = $value;
                        }
                        $insert['link_canonical']   = $tmp2['id'];
                        $insert['slug']             = $tmp2['slug'] . '-' . $k;
                        $insert['slug_full']        = $tmp2['slug_full'] . '-' . $k;
                        $idSeo = Seo::insertItem($insert);
                        /* cập nhật lại seo_id của product */
                        if ($insert['language'] == 'vi') {
                            Product::updateItem($t->id, [
                                'seo_id' => $idSeo,
                            ]);
                        }
                        $response[] = $idSeo;
                        /* tạo relation_seo_product_info */
                        RelationSeoProductInfo::insertItem([
                            'seo_id'    => $idSeo,
                            'product_info_id' => $t->id,
                        ]);
                        /* tạo content */
                        foreach ($seoS->infoSeo->contents as $content) {
                            $contentInsert = $content->content;
                            $contentInsert = str_replace($seoS->infoSeo->slug_full, $insert['slug_full'], $contentInsert);
                            SeoContent::insertItem([
                                'seo_id'    => $idSeo,
                                'content'   => $contentInsert,
                            ]);
                        }
                        ++$i;
                    }
                    /* copy relation product và category */
                    \App\Models\RelationCategoryProduct::select('*')
                        ->where('product_info_id', $t->id)
                        ->delete();
                    foreach($productSource->categories as $category){
                        \App\Models\RelationCategoryProduct::insertItem([
                            'category_info_id'       => $category->category_info_id,
                            'product_info_id'      => $t->id
                        ]);
                    }
                    /* copy relation product và tag */
                    \App\Models\RelationTagInfoOrther::select('*')
                        ->where('reference_type', 'product_info')
                        ->where('reference_id', $t->id)
                        ->delete();
                    foreach($productSource->tags as $tag){
                        \App\Models\RelationTagInfoOrther::insertItem([
                            'tag_info_id'       => $tag->tag_info_id,
                            'reference_type'    => 'product_info',
                            'reference_id'      => $t->id
                        ]);
                    }
                    ++$k;
                }
            }
            
        }
        /* Message */
        if(count($response)>0) {
            $message        = [
                'type'      => 'success',
                'message'   => 'Đã Copy thành công '.count($response).' Url Sản Phẩm (bao gồm đa ngôn ngữ)',
            ];
        }
        $request->session()->put('message', $message);
        return redirect()->route('admin.toolCopyProduct.view');
    }

    // public function copyMultiProduct(Request $request){
        
    // }
    
}
