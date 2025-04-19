<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cookie;
use App\Models\Product;
use App\Models\FreeWallpaper;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class CategoryMoneyController extends Controller {

    public static function loadMoreWallpaper(Request $request){
        $language                                   = $request->get('language');
        $viewBy                                     = Cookie::get('view_by') ?? 'each_set';
        $response                                   = [
            'content'   => '<div>'.config('data_language_1.'.$language.'.no_suitable_results_found').'</div>',
        ];
        if($request->get('loaded')<$request->get('total')){
            $params                                 = [];
            if(!empty($request->get('id_product'))){
                /* trường hợp tải theo product liên quan */
                $idProduct                          = $request->get('id_product');
                $currentProduct                     = Product::find($idProduct);
                $arrayIdTag                         = $currentProduct->tags->pluck('tag_info_id')->toArray();
                $params['loaded']                   = $request->get('loaded') ?? 0;
                $params['request_load']             = $request->get('request_load');
                $tmp                                = self::getWallpapersByProductRelated($idProduct, $arrayIdTag, $language, $params);
            }else {
                /* trường hợp tải từ category & tag */
                $params['filters']                  = $request->get('filters') ?? [];
                $params['search']                   = $request->get('search') ?? null;
                $params['loaded']                   = $request->get('loaded');
                $params['request_load']             = $request->get('request_load');
                $params['array_category_info_id']   = json_decode($request->get('array_category_info_id'));
                $params['array_tag_info_id']        = json_decode($request->get('array_tag_info_id'));
                $params['sort_by']                  = Cookie::get('sort_by') ?? config('main_'.env('APP_NAME').'.sort_type')[0]['key'];
                $tmp                                = self::getWallpapers($params, $language);
            }
            /* đổ theme lấy html */
            $content                                = self::getXhtmlWallpapers($tmp['wallpapers'], $language, $viewBy);
            /* trả kết quả */
            $response['content']                    = $content;
            $response['loaded']                     = $tmp['loaded'];
            $response['total']                      = $tmp['total'];
        }
        return json_encode($response);
    }

    public static function getXhtmlWallpapers($wallpapers, $language, $viewBy = 'each_set'){
        $response   = '';
        if(!empty($wallpapers)){
            foreach($wallpapers as $wallpaper){
                if($viewBy=='each_set'){
                    $response    .= view('wallpaper.template.wallpaperItem', [
                        'product'   => $wallpaper,
                        'language'  => $language,
                        'lazyload'  => true,
                        'headingTitle'  => 'h2',
                    ])->render();
                }else {
                    $wallpaperName      = null;
                    $link               = env('APP_URL').'/'.$wallpaper->seo->slug_full;
                    foreach($wallpaper->seos as $seo){
                        if(!empty($seo->infoSeo->language)&&$seo->infoSeo->language==$language) {
                            $wallpaperName = $seo->infoSeo->title;
                            $link = env('APP_URL').'/'.$seo->infoSeo->slug_full;
                            break;
                        }
                    }
                    foreach($wallpaper->prices as $price){
                        foreach($price->wallpapers as $w){
                            $response .= view('wallpaper.template.perWallpaperItem', [
                                'idProduct'     => $w->id,
                                'idPrice'       => $price->id,
                                'wallpaper'     => $w, 
                                'productName'   => $wallpaperName,
                                'link'          => $link,
                                'language'      => $language,
                                'lazyload'      => true
                            ]);
                        }
                    }
                }
            }
        }
        return $response;
    }

    public static function getWallpapers($params, $language) {
        $cacheKey           = 'wallpapers:' . md5(json_encode($params) . $language);
        $cacheTime          = config('app.cache_redis_time', 86400);

        return Cache::remember($cacheKey, now()->addSeconds($cacheTime), function () use ($params, $language) {
            $keySearch      = $params['search'] ?? null;
            $filters        = $params['filters'] ?? [];
            $sortBy         = $params['sort_by'] ?? null;
            $loaded         = $params['loaded'] ?? 0;
            $arrayIdCategory = $params['array_category_info_id'] ?? [];
            $arrayIdTag     = $params['array_tag_info_id'] ?? [];
            $requestLoad    = $params['request_load'] ?? 10;
            
            $query          = Product::select('product_info.*')
                ->join('seo', 'seo.id', '=', 'product_info.seo_id')
                ->whereHas('prices.wallpapers', function() {})
                ->whereHas('seos.infoSeo', function ($query) use ($language, $keySearch) {
                    $query->where('language', $language)
                        ->where('title', 'like', '%' . $keySearch . '%');
                })
                ->when(!empty($filters), function($query) use ($filters) {
                    foreach ($filters as $filter) {
                        $query->whereHas('categories.infoCategory', function($query) use ($filter) {
                            $query->where('id', $filter);
                        });
                    }
                })
                ->when(!empty($arrayIdCategory), function($query) use ($arrayIdCategory) {
                    $query->whereHas('categories', function($query) use ($arrayIdCategory) {
                        $query->whereIn('category_info_id', $arrayIdCategory);
                    });
                })
                ->when(!empty($arrayIdTag), function($query) use ($arrayIdTag) {
                    $query->whereHas('tags', function($query) use ($arrayIdTag) {
                        $query->where('reference_type', 'product_info')
                            ->whereIn('tag_info_id', $arrayIdTag);
                    });
                });
            /* sử dụng clone để query không bị ảnh hưởng cho truy vấn tiếp theo */
            $total          = (clone $query)->count();
            $wallpapers     = $query->when(empty($sortBy), function($query) {
                                    $query->orderBy('id', 'DESC');
                                })
                                ->when($sortBy == 'new' || $sortBy == 'propose', function($query) {
                                    $query->orderBy('id', 'DESC');
                                })
                                ->when($sortBy == 'favourite', function($query) {
                                    $query->orderBy('heart', 'DESC')
                                        ->orderBy('id', 'DESC');
                                })
                                ->when($sortBy == 'old', function($query) {
                                    $query->orderBy('id', 'ASC');
                                })
                                ->with(['seos.infoSeo' => function($query) use ($language) {
                                    $query->where('language', $language);
                                }, 'seo', 'prices'])
                                ->orderBy('seo.ordering', 'DESC')
                                ->orderBy('id', 'DESC')
                                ->skip($loaded)
                                ->take($requestLoad)
                                ->get();

            return [
                'wallpapers' => $wallpapers,
                'total'      => $total,
                'loaded'     => $loaded + $requestLoad
            ];
        });
    }

    public static function getWallpapersByProductRelated($idProduct, $arrayIdTag, $language, $params){
        $cacheKey = 'wallpapers_related:' . $idProduct
                    . ':' . $language
                    . ':' . md5(json_encode($arrayIdTag))
                    . ':' . $params['loaded']
                    . ':' . $params['request_load'];
        $cacheTime = config('app.cache_redis_time', 86400);

        return Cache::remember($cacheKey, now()->addSeconds($cacheTime), function () use ($idProduct, $arrayIdTag, $language, $params) {
            $response = [];
            $tmp = Product::select('product_info.*')
                ->whereHas('seos.infoSeo', function ($query) use ($language) {
                    $query->where('language', $language);
                })
                ->join('relation_tag_info_orther as rt', 'product_info.id', '=', 'rt.reference_id')
                ->where('rt.reference_type', 'product_info')
                ->whereIn('rt.tag_info_id', $arrayIdTag)
                ->where('product_info.id', '!=', $idProduct)
                ->selectRaw('COUNT(rt.tag_info_id) as common_tags_count')
                ->groupBy('product_info.id', 'product_info.seo_id', 'product_info.code', 'product_info.sold', 'product_info.heart', 'product_info.created_at', 'product_info.updated_at', 'product_info.price', 'product_info.notes')
                ->orderByDesc('common_tags_count')
                ->with('tags')
                ->get();

            $response['wallpapers'] = $tmp->slice($params['loaded'], $params['request_load'])->values();
            $response['loaded']     = $params['loaded'] + $response['wallpapers']->count();
            $response['total']      = $tmp->count();

            return $response;
        });
    }

    public static function buildTocContentMain($contents, $language) {
        // Nếu danh sách nội dung rỗng, trả về dữ liệu mặc định
        if (empty($contents) || count($contents) === 0) {
            return [
                'content' => '',
                'toc_content' => ''
            ];
        }
    
        // Sắp xếp theo `ordering`
        $sortedContents = collect($contents)->sortBy('ordering');
    
        // Ghép các nội dung lại sau khi đã sắp xếp
        $htmlContent = '';
        foreach ($sortedContents as $content) {
            $htmlContent .= $content->content;
        }
    
        // Kiểm tra nếu nội dung rỗng
        if (empty(trim($htmlContent))) {
            return [
                'content' => '',
                'toc_content' => ''
            ];
        }
    
        // Phân tích HTML của $htmlContent để tạo TOC
        $dom = new \DOMDocument();
        @$dom->loadHTML(mb_convert_encoding($htmlContent, 'HTML-ENTITIES', 'UTF-8'));
    
        $dataTocContent = [];
        $indexToc = 0;
        foreach ($dom->getElementsByTagName('h2') as $i => $heading) {
            // Tạo id nếu không có
            $dataId = $heading->getAttribute('id');
            if (empty($dataId)) {
                $dataId = 'randomIdTocContent_' . $i;
                $heading->setAttribute('id', $dataId);
                $indexToc++;
            }
    
            $dataTocContent[$i] = [
                'id' => $dataId,
                'name' => $heading->nodeName,
                'title' => $heading->textContent
            ];
        }
    
        // Tạo nội dung TOC với view
        $xhtml = view('wallpaper.template.tocContentMain', [
            'data' => $dataTocContent,
            'language' => $language
        ])->render();
    
        // Trả về cả nội dung đã cập nhật và TOC
        return [
            'content' => $dom->saveHTML(),
            'toc_content' => $xhtml,
        ];
    }
    
}
