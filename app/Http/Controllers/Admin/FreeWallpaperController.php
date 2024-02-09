<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

use Intervention\Image\ImageManagerStatic;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;

use App\Services\BuildInsertUpdateModel;
use App\Models\FreeWallpaper;
use App\Models\RelationFreewallpaperCategory;
use App\Helpers\Charactor;
use App\Models\Category;
use App\Models\RelationTagInfoOrther;
use App\Models\Tag;

class FreeWallpaperController extends Controller {

    public function list(Request $request){
        $params             = [];
        /* paginate */
        $viewPerPage        = Cookie::get('viewFreeWallpaperInfo') ?? 20;
        $params['paginate'] = $viewPerPage;
        /* Search theo tên */
        if(!empty($request->get('search_name'))) {
            $params['search_name'] = $request->get('search_name');
            $list           = FreeWallpaper::getList($params);
            $total          = $list->total();
        } else {
            $list           = new \Illuminate\Database\Eloquent\Collection;
            $total          = FreeWallpaper::count();
        }
        return view('admin.freeWallpaper.list', compact('list', 'total', 'params', 'viewPerPage'));
    }

    public function loadOneRow(Request $request){
        $response           = '';
        if(!empty($request->get('id'))){
            $item           = FreeWallpaper::select('*')
                                ->where('id', $request->get('id'))
                                ->first();
            $response       = view('admin.freeWallpaper.oneRow', compact('item'))->render();
        }
        echo $response;
    }

    public function addFormUpload(Request $request){
        set_time_limit(0);
        $xhtml = '';
        if(!empty($request->get('data_id'))){
            $categories = Category::all();
            /* tag name */
            $tags           = Tag::all();
            $arrayTag       = [];
            foreach($tags as $tag) $arrayTag[] = $tag->name;
            // $strTag         = implode(',', $arrayTag);
            foreach($request->get('data_id') as $idBox){
                $xhtml .= view('admin.freeWallpaper.oneFormUpload', compact('idBox', 'categories', 'tags', 'arrayTag'))->render();
            }
        }
        echo $xhtml;
    }

    public function uploadWallpaper(Request $request){
        try {
            DB::beginTransaction();
            if (!empty($request->file('files.wallpaper'))){
                $wallpaper          = $request->file('files.wallpaper');
                $i                  = $request->get('count');
                /* Lấy thông tin ảnh wallpaper */
                $imageInfoW                     = getimagesize($wallpaper);
                $widthW                         = $imageInfoW[0];
                $heightW                        = $imageInfoW[1];
                $miniTypeW                      = $imageInfoW['mime'];
                $fileSizeW                      = filesize($wallpaper);
                $extensionW                     = config('image.extension');
                $fileNameNonHaveExtensionW      = \App\Helpers\Charactor::convertStrToUrl($request->get('name')).'-'.time().'-'.$i;
                $folderW                        = config('main.google_cloud_storage.freeWallpapers');
                $fileUrlW                       = $folderW.$fileNameNonHaveExtensionW.'.'.$extensionW;
                /* upload wallpaper lên google_cloud_storage với 3 bản Full Small Mini (thông qua function Upload) */
                $fileUpload = \App\Helpers\Upload::uploadWallpaper($wallpaper, $fileNameNonHaveExtensionW.'.'.$extensionW, $folderW);
                /* Lưu thông tin vào CSDL */
                $idWallpaper = FreeWallpaper::insertItem([
                    'user_id'       => Auth::user()->id,
                    'name'          => $request->get('name'),
                    'en_name'       => $request->get('en_name') ?? null,
                    'description'   => $request->get('description') ?? null,
                    'file_name'     => $fileNameNonHaveExtensionW,
                    'extension'     => $extensionW,
                    'file_cloud'    => $fileUrlW,
                    'width'         => $widthW,
                    'height'        => $heightW,
                    'file_size'     => $fileSizeW,
                    'mine_type'     => $miniTypeW
                ]);
                /* lưu relation */
                foreach(config('main.category_type') as $type){
                    if(!empty($request->get($type['key']))){
                        $arrayCategory = explode(',', $request->get($type['key']));
                        foreach($arrayCategory as $idCategory){
                            RelationFreewallpaperCategory::insertItem([
                                'free_wallpaper_info_id'    => $idWallpaper,
                                'category_info_id'          => $idCategory
                            ]);
                        }
                    }
                }
                /* lưu tag name */
                if(!empty($request->get('tag'))){
                    $tag    = json_decode($request->get('tag'), true);
                    foreach($tag as $t){
                        $nameTag    = strtolower($t['value']);
                        /* kiểm tra xem tag name đã tồn tại chưa */
                        $infoTag    = Tag::select('*')
                                        ->where('name', $nameTag)
                                        ->first();
                        /* chưa tồn tại -> tạo và láy ra */
                        if(empty($infoTag)){
                            $enNameTag  = strtolower(Charactor::translateViToEn($nameTag));
                            $idTag      = Tag::insertItem([
                                'name'      => $nameTag,
                                'en_name'   => $enNameTag
                            ]);
                            $infoTag    = Tag::select('*')
                                            ->where('id', $idTag)
                                            ->first();
                        }
                        /* insert relation */
                        RelationTagInfoOrther::insertItem([
                            'tag_info_id'       => $infoTag->id,
                            'reference_type'    => 'free_wallpaper_info',
                            'reference_id'      => $idWallpaper
                        ]);
                    }
                }
                DB::commit();
                if(!empty($idWallpaper)){
                    $response = [];
                    $infoWallpaper = FreeWallpaper::select('*')
                                        ->where('id', $idWallpaper)
                                        ->first();
                    $response['id'] = $idWallpaper;
                    $response['content'] = view('admin.freeWallpaper.oneRow', ['item' => $infoWallpaper])->render();
                    return json_encode($response);
                }
                return true;
            }
        } catch (\Exception $exception){
            DB::rollBack();
            return '';
        }
    }

    public function updateWallpaper(Request $request){
        try {
            DB::beginTransaction();
            $idWallpaper        = $request->get('wallpaper_info_id');
            /* cập nhật cở sở dữ liệu */
            FreeWallpaper::updateItem($idWallpaper, [
                'name'              => $request->get('name'),
                'en_name'           => $request->get('en_name'),
                'description'       => $request->get('description') ?? null,
            ]);
            /* lưu relation */
            RelationFreewallpaperCategory::select('*')
                ->where('free_wallpaper_info_id', $idWallpaper)
                ->delete();
            foreach(config('main.category_type') as $type){
                if(!empty($request->get($type['key']))){
                    $arrayCategory = explode(',', $request->get($type['key']));
                    foreach($arrayCategory as $idCategory){
                        RelationFreewallpaperCategory::insertItem([
                            'free_wallpaper_info_id'    => $idWallpaper,
                            'category_info_id'          => $idCategory
                        ]);
                    }
                }
            }
            /* lưu tag name */
            /* delete relation có sẵn */
            RelationTagInfoOrther::select('*')
                ->where('reference_type', 'free_wallpaper_info')
                ->where('reference_id', $idWallpaper)
                ->delete();
            if(!empty($request->get('tag'))){
                $tag    = json_decode($request->get('tag'), true);
                foreach($tag as $t){
                    $nameTag    = strtolower($t['value']);
                    /* kiểm tra xem tag name đã tồn tại chưa */
                    $infoTag    = Tag::select('*')
                                    ->where('name', $nameTag)
                                    ->first();
                    /* chưa tồn tại -> tạo và láy ra */
                    if(empty($infoTag)){
                        $enNameTag  = strtolower(Charactor::translateViToEn($nameTag));
                        $idTag      = Tag::insertItem([
                            'name'      => $nameTag,
                            'en_name'   => $enNameTag
                        ]);
                        $infoTag    = Tag::select('*')
                                        ->where('id', $idTag)
                                        ->first();
                    }
                    /* insert relation */
                    RelationTagInfoOrther::insertItem([
                        'tag_info_id'       => $infoTag->id,
                        'reference_type'    => 'free_wallpaper_info',
                        'reference_id'      => $idWallpaper
                    ]);
                }
            }
            DB::commit();
            return true;
        } catch (\Exception $exception){
            DB::rollBack();
            return false;
        }
    }

    public function deleteWallpaper(Request $request){
        $flag                       = false;
        if(!empty($request->get('id'))){
            $idWallpaper            = $request->get('id');
            $infoWallpaper          = FreeWallpaper::select('*')
                                        ->where('id', $idWallpaper)
                                        ->first();
            $flag                   = self::delete($infoWallpaper);
            /* xóa relation */
            if($flag==true){
                RelationFreewallpaperCategory::select('*')
                    ->where('free_wallpaper_info_id', $idWallpaper)
                    ->delete();
                RelationTagInfoOrther::select('*')
                    ->where('reference_id', $idWallpaper)
                    ->where('reference_type', 'free_wallpaper_info')
                    ->delete();
            }
            /* xóa trong cơ sở dữ liệu */
            $infoWallpaper->delete();
        }
        return $flag;
    }

    private static function delete($infoWallpaper){
        $flag   = false;
        if(!empty($infoWallpaper)){
            /* xóa wallpaper trong google_cloud_storage */
            Storage::disk('gcs')->delete($infoWallpaper->file_cloud);
            /* xóa wallpaper Small trong google_cloud_storage */
            Storage::disk('gcs')->delete(config('main.google_cloud_storage.freeWallpapers').$infoWallpaper->file_name.'-small.'.$infoWallpaper->extension);
            /* xóa wallpaper Mini trong google_cloud_storage */
            Storage::disk('gcs')->delete(config('main.google_cloud_storage.freeWallpapers').$infoWallpaper->file_name.'-mini.'.$infoWallpaper->extension);
            $flag = true;
        }
        return $flag;
    }

    public function loadModalUploadAndEdit(Request $request){
        $wallpaper      = null;
        if(!empty($request->get('wallpaper_id'))){
            $wallpaper  = FreeWallpaper::select('*')
                            ->where('id', $request->get('wallpaper_id'))
                            ->first();
        }
        $categories     = Category::all();
        /* tag name */
        $tags           = Tag::all();
        $arrayTag       = [];
        foreach($tags as $tag) $arrayTag[] = $tag->name;
        $result         = view('admin.freeWallpaper.formModalUploadAndEdit', compact('wallpaper', 'categories', 'arrayTag', 'tags'))->render();
        echo $result;
    }

    // public function searchWallpapers(Request $request){
    //     $response           = '';
    //     if(!empty($request->get('key_search'))&&!empty($request->get('product_price_id'))){
    //         $wallpapers     = FreeWallpaper::select('*')
    //                             ->where('name', 'like', '%'.$request->get('key_search').'%')
    //                             ->orderBy('price_uses_count', 'ASC')
    //                             ->orderBy('id', 'DESC')
    //                             ->withCount('priceUses') // Số lượng phần tử trong priceUses trả ra tên biến trong collection price_uses_count
    //                             ->get();
    //         $relations      = RelationProductPriceWallpaperInfo::select('*')
    //                             ->where('product_price_id', $request->get('product_price_id'))
    //                             ->get();
    //         foreach($wallpapers as $wallpaper){
    //             /* check có tồn tại chưa */
    //             $selected   = false;
    //             foreach($relations as $relation){
    //                 if($wallpaper->id==$relation->wallpaper_info_id){
    //                     $selected = true;
    //                     break;
    //                 }
    //             }   
    //             /* trả kết quả */
    //             $response   .= view('admin.product.oneRowSearchWallpaper', [
    //                 'wallpaper'         => $wallpaper,
    //                 'idProductPrice'    => $request->get('product_price_id'),
    //                 'selected'          => $selected
    //             ])->render();
    //         }
    //     }
    //     if(empty($response)) $response = '<div class="searchViewBefore_selectbox_item">Không có kết quả phù hợp!</div>';
    //     echo $response;
    // }

}
