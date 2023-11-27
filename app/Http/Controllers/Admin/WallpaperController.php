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

use App\Jobs\UploadSourceAndWallpaper;

class WallpaperController extends Controller {

    public function list(Request $request){
        $params             = [];
        /* paginate */
        $viewPerPage        = Cookie::get('viewWallpaperInfo') ?? 20;
        $params['paginate'] = $viewPerPage;
        /* Search theo tên */
        if(!empty($request->get('search_name'))) {
            $params['search_name'] = $request->get('search_name');
            $list           = Wallpaper::getList($params);
            $total          = $list->total();
        } else {
            $list           = new \Illuminate\Database\Eloquent\Collection;
            $total          = Wallpaper::count();
        }
        return view('admin.wallpaper.list', compact('list', 'total', 'params', 'viewPerPage'));
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
        set_time_limit(0);

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
            if (!empty($request->file('files.wallpaper')) && !empty($request->file('files.source'))){
                $wallpaper      = $request->file('files.wallpaper');
                $source         = $request->file('files.source');
                $i              = $request->get('count');
                /* Lấy thông tin ảnh */
                $imageInfo      = getimagesize($wallpaper);
                $width          = $imageInfo[0];
                $height         = $imageInfo[1];
                $fileSize       = filesize($wallpaper);
                $extensionWallpaper         = $wallpaper->getClientOriginalExtension();
                $extensionDefault           = config('image.extension');
                $fileNameNonHaveExtension   = \App\Helpers\Charactor::convertStrToUrl($request->get('name')).'-'.time().'-'.$i;
                $fileNameFull               = $fileNameNonHaveExtension.'.'.$extensionWallpaper;
                /* Lưu ảnh vào Google Cloud Storage */
                $fileUrlW                   = config('main.google_cloud_storage.wallpapers').$fileNameFull;
                $fileUrlS                   = config('main.google_cloud_storage.sources').$fileNameFull;
                /* wallpaper sẽ được upload vào storage và cả google_cloud_storage */
                \App\Helpers\Upload::uploadWallpaper($wallpaper, $fileNameNonHaveExtension.'.'.$extensionDefault);
                Storage::disk('gcs')->put($fileUrlW, file_get_contents($wallpaper));
                /* source sẽ được tải vào google_cloud_storage */
                Storage::disk('gcs')->put($fileUrlS, file_get_contents($source));
                /* Lưu thông tin vào CSDL */
                $idWallpaper = Wallpaper::insertItem([
                    'user_id'           => Auth::user()->id,
                    'name'              => $request->get('name'),
                    'description'       => $request->get('description') ?? null,
                    'file_name'         => $fileNameFull,
                    'file_url_cloud'    => config('main.google_cloud_storage.wallpapers').$fileNameFull,
                    'file_url_hosting'  => Storage::url(config('image.folder_upload').$fileNameNonHaveExtension.'.'.$extensionDefault),
                    'width'             => $width,
                    'height'            => $height,
                    'file_size'         => $fileSize,
                    'extension'         => $extensionWallpaper,
                    'mime_type'         => $imageInfo['mime']
                ]);
                DB::commit();
                if(!empty($idWallpaper)){
                    $response = [];
                    $infoWallpaper = Wallpaper::select('*')
                                        ->where('id', $idWallpaper)
                                        ->first();
                    $response['id'] = $idWallpaper;
                    $response['content'] = view('admin.wallpaper.oneRow', ['item' => $infoWallpaper])->render();
                    return json_encode($response);
                }
                return true;
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
            $wallpaper          = $request->file('files.wallpaper');
            $source             = $request->file('files.source');
            $idWallpaper        = $request->get('wallpaper_id');
            $infoWallpaper      = Wallpaper::select('*')
                                    ->where('id', $idWallpaper)
                                    ->first();
            $fileName           = pathinfo($infoWallpaper->file_name)['filename'];
            /* trường hợp có thay đổi wallpaper */
            if(!empty($wallpaper)){
                $extensionWallpaper     = $wallpaper->getClientOriginalExtension();
                $fileNameFull           = $fileName.'.'.$extensionWallpaper;
                /* xóa wallpaper trong storage */
                $extensionDefault       = config('image.extension');
                $wallpaperPathInStorage = Storage::path(config('image.folder_upload').$fileName.'.'.$extensionDefault);
                if(file_exists($wallpaperPathInStorage)) unlink($wallpaperPathInStorage);
                /* xóa ảnh wallpaper mini trong storage */
                $filenameNotExtension   = pathinfo($infoWallpaper->file_name)['filename'];
                $wallpaperMiniPathInStorage = Storage::path(config('image.folder_upload').$filenameNotExtension.'-mini.'.$extensionDefault);
                if(file_exists($wallpaperMiniPathInStorage)) unlink($wallpaperMiniPathInStorage);
                /* xóa ảnh wallpaper small trong storage */
                $wallpaperSmallPathInStorage = Storage::path(config('image.folder_upload').$filenameNotExtension.'-small.'.$extensionDefault);
                if(file_exists($wallpaperSmallPathInStorage)) unlink($wallpaperSmallPathInStorage);
                /* xóa wallpaper trong google_cloud_storage */
                $fileUrlW                   = config('main.google_cloud_storage.wallpapers').$fileNameFull;
                Storage::disk('gcs')->delete($fileUrlW);
                /* upload lại wallpaper mới */
                \App\Helpers\Upload::uploadWallpaper($wallpaper, $fileName.'.'.$extensionDefault);
                Storage::disk('gcs')->put($fileUrlW, file_get_contents($wallpaper));
            }
            /* trường hợp có thay đổi source */
            if(!empty($source)){
                $extensionSource    = $source->getClientOriginalExtension();
                $fileNameS          = $fileName.'.'.$extensionSource;
                /* xóa wallpaper trong google_cloud_storage */
                $fileUrlS           = config('main.google_cloud_storage.sources').$fileNameFull;
                Storage::disk('gcs')->delete($fileUrlS);
                /* upload lại source mới */
                Storage::disk('gcs')->put($fileUrlS, file_get_contents($source));
            }
            /* cập nhật cơ sở dữ liệu */
            $imageInfo  = getimagesize($wallpaper);
            $width      = $imageInfo[0];
            $height     = $imageInfo[1];
            $fileSize   = filesize($wallpaper);
            Wallpaper::updateItem($idWallpaper, [
                'name'              => $request->get('name'),
                'description'       => $request->get('description') ?? null,
                // 'file_name'         => $fileName,
                // 'file_url_cloud'    => config('main.google_cloud_storage.wallpapers').$fileNameFull,
                // 'file_url_hosting'  => Storage::url(config('image.folder_upload').$fileName.'.'.$extensionDefault),
                'width'             => $width,
                'height'            => $height,
                'file_size'         => $fileSize,
                'extension'         => $extensionWallpaper,
                'mime_type'         => $imageInfo['mime']
            ]);
        //     DB::commit();
            return true;
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
                                ->orderBy('price_uses_count', 'ASC')
                                ->orderBy('id', 'DESC')
                                ->withCount('priceUses') // Số lượng phần tử trong priceUses trả ra tên biến trong collection price_uses_count
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
