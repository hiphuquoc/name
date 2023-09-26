<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RelationProductPriceWallpaperInfo;
use Illuminate\Http\Request;
use App\Services\BuildInsertUpdateModel;
use Illuminate\Support\Facades\Cookie;
use App\Models\Wallpaper;

use Intervention\Image\ImageManagerStatic;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;

class WallpaperController extends Controller {

    public function list(Request $request){
        $params             = [];
        /* Search theo tên */
        if(!empty($request->get('search_name'))) $params['search_name'] = $request->get('search_name');
        /* paginate */
        $viewPerPage        = Cookie::get('viewWallpaperInfo') ?? 50;
        $params['paginate'] = $viewPerPage;
        $list               = Wallpaper::getList($params);
        return view('admin.wallpaper.list', compact('list', 'params', 'viewPerPage'));
    }

    public function loadOneRow(Request $request){
        $response           = '';
        if(!empty($request->get('id'))){
            $item           = Wallpaper::select('*')
                                ->where('id', $request->get('id'))
                                ->first();
            $response       = view('admin.wallpaper.oneRow', compact('item'))->render();
        }
        echo $response;
    }

    public function loadFormUploadSourceAndWallpaper(Request $request){
        $xhtml = '';
        if(!empty($request->get('data_id'))){
            foreach($request->get('data_id') as $idBox){
                $xhtml .= view('admin.wallpaper.oneFormUploadSourceAndWallpaper', compact('idBox'))->render();
            }
        }
        echo $xhtml;
    }

    public function uploadWallpaperWithSource(Request $request){
        try {
            DB::beginTransaction();
            if (!empty($request->file('wallpapers')) && !empty($request->file('sources')) && count($request->file('wallpapers')) == count($request->file('sources'))){
                $wallpapers     = $request->file('wallpapers');
                $sources        = $request->file('sources');
                $arrayId        = [];
                $i              = 0;
                foreach ($wallpapers as $wallpaper) {
                    /* Lấy thông tin ảnh */
                    $imageInfo  = getimagesize($wallpaper);
                    $width      = $imageInfo[0];
                    $height     = $imageInfo[1];
                    $fileSize   = filesize($wallpaper);
                    $extensionWallpaper         = $wallpaper->getClientOriginalExtension();
                    $extensionDefault           = config('image.extension');
                    /* Lưu ảnh vào Google Cloud Storage */
                    $fileNameNonHaveExtension   = \App\Helpers\Charactor::convertStrToUrl($request->get('name')).'-'.time().'-'.$i;
                    $fileName   = $fileNameNonHaveExtension.'.'.$extensionWallpaper;
                    $fileUrlW   = config('main.google_cloud_storage.wallpapers').$fileName;
                    $fileUrlS   = config('main.google_cloud_storage.sources').$fileName;
                    /* wallpaper sẽ được upload vào storage và cả google_cloud_storage */
                    $flagW      = \App\Helpers\Upload::uploadWallpaper($wallpaper, $fileNameNonHaveExtension.'.'.$extensionDefault);
                    Storage::disk('gcs')->put($fileUrlW, file_get_contents($wallpaper));
                    /* source sẽ được tải vào google_cloud_storage */
                    $flagS      = Storage::disk('gcs')->put($fileUrlS, file_get_contents($sources[$i]));
                    if ($flagW==true&&$flagS==true) {
                        /* Lưu thông tin vào CSDL */
                        $idWallpaper = Wallpaper::insertItem([
                            'user_id'           => Auth::user()->id,
                            'name'              => $request->get('name'),
                            'description'       => $request->get('description') ?? null,
                            'file_name'         => $fileName,
                            'file_url_cloud'    => config('main.google_cloud_storage.wallpapers').$fileName,
                            'file_url_hosting'  => Storage::url(config('image.folder_upload').$fileNameNonHaveExtension.'.'.$extensionDefault),
                            'width'             => $width,
                            'height'            => $height,
                            'file_size'         => $fileSize,
                            'extension'         => $extensionWallpaper,
                            'mime_type'         => $imageInfo['mime']
                        ]);
                        $arrayId[]  = $idWallpaper;
                        ++$i;
                    }
                }
                DB::commit();
                $response       = [];
                $wallpapers     = Wallpaper::select('*')
                                    ->whereIn('id', $arrayId)
                                    ->get();
                $i              = 0;
                foreach($wallpapers as $item){
                    $response[$i]['id']         = $item->id;
                    $response[$i]['content']    = view('admin.wallpaper.oneRow', compact('item'))->render();
                    ++$i;
                }
                return json_encode($response);
            }
        } catch (\Exception $exception){
            DB::rollBack();
            return '';
        }
    }

    public function changeWallpaperWithSource(Request $request){
        // try {
        //     DB::beginTransaction();
            $extensionDefault   = config('image.extension');
            $fileNameNonHaveExtension   = \App\Helpers\Charactor::convertStrToUrl($request->get('name')).'-'.time().'-0';
            $wallpapers         = $request->file('wallpapers');
            $sources            = $request->file('sources');
            $idWallpaper        = $request->get('wallpaper_id');
            $infoWallpaper      = Wallpaper::select('*')
                                    ->where('id', $idWallpaper)
                                    ->first();
            /* trường hợp có thay đổi ảnh */
            if (!empty($wallpapers)||!empty($sources)){
                $dataUpdate                 = [];
                if(!empty($wallpapers)){ /* trường hợp thay wallpapers */
                    /* xóa wallpaper trong storage */
                    $filenameNotExtension   = pathinfo($infoWallpaper->file_name)['filename'];
                    $extension              = config('image.extension');
                    $wallpaperPathInStorage = Storage::path(config('image.folder_upload').$filenameNotExtension.'.'.$extension);
                    if(file_exists($wallpaperPathInStorage)) unlink($wallpaperPathInStorage);
                    /* xóa ảnh wallpaper mini trong storage */
                    $filenameNotExtension   = pathinfo($infoWallpaper->file_name)['filename'];
                    $wallpaperMiniPathInStorage = Storage::path(config('image.folder_upload').$filenameNotExtension.'-mini.'.$extension);
                    if(file_exists($wallpaperMiniPathInStorage)) unlink($wallpaperMiniPathInStorage);
                    /* xóa ảnh wallpaper small trong storage */
                    $wallpaperSmallPathInStorage = Storage::path(config('image.folder_upload').$filenameNotExtension.'-small.'.$extension);
                    if(file_exists($wallpaperSmallPathInStorage)) unlink($wallpaperSmallPathInStorage);
                    /* xóa wallpaper trong google_cloud_storage */
                    Storage::disk('gcs')->delete($infoWallpaper->file_url_cloud);
                    /* upload lại wallpaper mới */
                    $extensionWallpaper         = $wallpapers[0]->getClientOriginalExtension();
                    $fileName   = $fileNameNonHaveExtension.'.'.$extensionWallpaper;
                    $fileUrlW   = config('main.google_cloud_storage.wallpapers').$fileName;
                    \App\Helpers\Upload::uploadWallpaper($wallpapers[0], $fileNameNonHaveExtension.'.'.$extensionDefault);
                    Storage::disk('gcs')->put($fileUrlW, file_get_contents($wallpapers[0]));
                    /* đổi tên của sources (nếu có) */
                    $fileUrlS_old   = config('main.google_cloud_storage.sources').$infoWallpaper->file_name;
                    $fileUrlS_new   = config('main.google_cloud_storage.sources').$fileName;
                    Storage::disk('gcs')->move($fileUrlS_old, $fileUrlS_new);
                    // Storage::disk('gcs')->put($fileUrlS, file_get_contents($sources[$i]));
                    // /* cập nhật data update */
                    // $dataUpdate['name']         = $request->get('name');
                    // $dataUpdate['description']  = $request->get('description') ?? null;
                    // 'description'       => $request->get('description') ?? null,
                    // 'file_name'         => $fileName,
                    // 'file_url_cloud'    => config('main.google_cloud_storage.wallpapers').$fileName,
                    // 'file_url_hosting'  => Storage::url(config('image.folder_upload').$fileNameNonHaveExtension.'.'.$extensionDefault),
                    // 'width'             => $width,
                    // 'height'            => $height,
                    // 'file_size'         => $fileSize,
                    // 'extension'         => $extensionWallpaper,
                    // 'mime_type'         => $imageInfo['mime']
                }
                if(!empty($sources)){
                    $extensionSource    = $sources[0]->getClientOriginalExtension();
                    $fileNameS          = $fileNameNonHaveExtension.'.'.$extensionSource;
                    $fileUrlS           = config('main.google_cloud_storage.sources').$fileNameS;
                    Storage::disk('gcs')->put($fileUrlS, file_get_contents($sources[0]));
                }
                // /* cập nhật cơ sở dữ liệu */
                // $imageInfo  = getimagesize($wallpaper);
                // $width      = $imageInfo[0];
                // $height     = $imageInfo[1];
                // $fileSize   = filesize($wallpaper);
                // Wallpaper::updateItem($idWallpaper, [
                //     'name'              => $request->get('name'),
                //     'description'       => $request->get('description') ?? null,
                //     'file_name'         => $fileName,
                //     'file_url_cloud'    => config('main.google_cloud_storage.wallpapers').$fileName,
                //     'file_url_hosting'  => Storage::url(config('image.folder_upload').$fileNameNonHaveExtension.'.'.$extensionDefault),
                //     'width'             => $width,
                //     'height'            => $height,
                //     'file_size'         => $fileSize,
                //     'extension'         => $extensionWallpaper,
                //     'mime_type'         => $imageInfo['mime']
                // ]);
            }else { /* trường hợp không thay dổi ảnh */
                $fileUrlW_old   = config('main.google_cloud_storage.wallpapers').$infoWallpaper->file_name;
                $fileUrlS_old   = config('main.google_cloud_storage.sources').$infoWallpaper->file_name;
                $filePathW_hosting_old = public_path(ltrim($infoWallpaper->file_url_hosting, '/'));
                /* ảnh mini */
                $filePathW_mini_hosting_old     = public_path(ltrim(\App\Helpers\Image::getUrlImageMiniByUrlImage($infoWallpaper->file_url_hosting), '/'));
                $filePathW_small_hosting_old    = public_path(ltrim(\App\Helpers\Image::getUrlImageSmallByUrlImage($infoWallpaper->file_url_hosting), '/'));
                /* tên mới */
                $fileNameNonHaveExtension = \App\Helpers\Charactor::convertStrToUrl($request->get('name')).'-'.time().'-0';
                $fileName       = $fileNameNonHaveExtension.'.'.$infoWallpaper->extension;
                $fileUrlW_new   = config('main.google_cloud_storage.wallpapers').$fileName;
                $fileUrlS_new   = config('main.google_cloud_storage.sources').$fileName;
                $fileUrlW_hosting_new = Storage::url(config('image.folder_upload').$fileNameNonHaveExtension.'.'.$extensionDefault);
                $filePath_hosting_new = Storage::path(config('image.folder_upload').$fileNameNonHaveExtension.'.'.$extensionDefault);
                $filePath_mini_hosting_new = Storage::path(config('image.folder_upload').$fileNameNonHaveExtension.'-mini.'.$extensionDefault);
                $filePath_small_hosting_new = Storage::path(config('image.folder_upload').$fileNameNonHaveExtension.'-small.'.$extensionDefault);
                /* thay thế tên */
                Storage::disk('gcs')->move($fileUrlW_old, $fileUrlW_new);
                Storage::disk('gcs')->move($fileUrlS_old, $fileUrlS_new);
                @rename($filePathW_hosting_old, $filePath_hosting_new);
                @rename($filePathW_mini_hosting_old, $filePath_mini_hosting_new);
                @rename($filePathW_small_hosting_old, $filePath_small_hosting_new);
                /* cập nhật lại cơ sở dữ liệu */
                Wallpaper::updateItem($idWallpaper, [
                    'name'              => $request->get('name'),
                    'description'       => $request->get('description') ?? null,
                    'file_name'         => $fileName,
                    'file_url_cloud'    => $fileUrlW_new,
                    'file_url_hosting'  => $fileUrlW_hosting_new,
                ]);
            }  
        //     DB::commit();
        //     return true;
        // } catch (\Exception $exception){
        //     DB::rollBack();
        //     return false;
        // }
    }

    public function deleteWallpaperAndSource(Request $request){
        $flag                       = false;
        if(!empty($request->get('id'))){
            $idWallpaper            = $request->get('id');
            $infoWallpaper          = Wallpaper::select('*')
                                        ->where('id', $idWallpaper)
                                        ->first();
            $flag                   = self::delete($infoWallpaper);
            
            /* xóa hết tát cả relation của wallpaper này => tránh lỗi hệ thống ==== chỉ xóa khi xóa hẳn ảnh trong riêng function delete này */
            RelationProductPriceWallpaperInfo::select('*')
                ->where('wallpaper_info_id', $idWallpaper)
                ->delete();
            /* xóa trong cơ sở dữ liệu */
            $infoWallpaper->delete();
        }
        return $flag;
    }

    private static function delete($infoWallpaper){
        $flag   = false;
        if(!empty($infoWallpaper)){
            /* xóa wallpaper trong storage */
            $filenameNotExtension   = pathinfo($infoWallpaper->file_name)['filename'];
            $extension              = config('image.extension');
            $wallpaperPathInStorage = Storage::path(config('image.folder_upload').$filenameNotExtension.'.'.$extension);
            if(file_exists($wallpaperPathInStorage)) unlink($wallpaperPathInStorage);
            /* xóa ảnh wallpaper mini trong storage */
            $filenameNotExtension   = pathinfo($infoWallpaper->file_name)['filename'];
            $wallpaperMiniPathInStorage = Storage::path(config('image.folder_upload').$filenameNotExtension.'-mini.'.$extension);
            if(file_exists($wallpaperMiniPathInStorage)) unlink($wallpaperMiniPathInStorage);
            /* xóa ảnh wallpaper small trong storage */
            $wallpaperSmallPathInStorage = Storage::path(config('image.folder_upload').$filenameNotExtension.'-small.'.$extension);
            if(file_exists($wallpaperSmallPathInStorage)) unlink($wallpaperSmallPathInStorage);
            /* xóa wallpaper trong google_cloud_storage */
            $flag   = Storage::disk('gcs')->delete($infoWallpaper->file_url_cloud);
            /* xóa source trong google_cloud_storage */
            Storage::disk('gcs')->delete(config('main.google_cloud_storage.sources').$infoWallpaper->file_name);
        }
        return $flag;
    }

    public function loadModalUploadAndEdit(Request $request){
        $wallpaper      = null;
        if(!empty($request->get('wallpaper_id'))){
            $wallpaper  = Wallpaper::select('*')
                            ->where('id', $request->get('wallpaper_id'))
                            ->first();
        }
        $result         = view('admin.wallpaper.formModalUploadAndEdit', compact('wallpaper'))->render();
        echo $result;
    }

    public function searchWallpapers(Request $request){
        $response           = '';
        if(!empty($request->get('key_search'))&&!empty($request->get('product_price_id'))){
            $wallpapers     = Wallpaper::select('*')
                                ->where('name', 'like', '%'.$request->get('key_search').'%')
                                ->get();
            $relations      = RelationProductPriceWallpaperInfo::select('*')
                                ->where('product_price_id', $request->get('product_price_id'))
                                ->get();
            foreach($wallpapers as $wallpaper){
                /* check có tồn tại chưa */
                $selected   = false;
                foreach($relations as $relation){
                    if($wallpaper->id==$relation->wallpaper_info_id){
                        $selected = true;
                        break;
                    }
                }   
                /* trả kết quả */
                $response   .= view('admin.product.oneRowSearchWallpaper', [
                    'wallpaper'         => $wallpaper,
                    'idProductPrice'    => $request->get('product_price_id'),
                    'selected'          => $selected
                ])->render();
            }
        }
        if(empty($response)) $response = '<div class="searchViewBefore_selectbox_item">Không có kết quả phù hợp!</div>';
        echo $response;
    }
}
