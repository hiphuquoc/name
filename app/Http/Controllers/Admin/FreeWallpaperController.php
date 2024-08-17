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
use App\Models\RelationFreeWallpaperUser;
use App\Models\Tag;
use App\Models\Seo;
use App\Models\EnSeo;
use App\Models\RelationCategoryThumnail;
use App\Models\RelationSeoEnSeo;
use App\Models\RelationSeoTagInfo;

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
            foreach($tags as $tag) if(!empty($tag->seo->title)) $arrayTag[] = $tag->seo->title;
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
                // $extensionW                     = config('image.extension');
                $extensionW                     = $wallpaper->getClientOriginalExtension();
                $fileNameNonHaveExtensionW      = Charactor::convertStrToUrl($request->get('name')).'-'.time().'-'.$i;
                $folderW                        = config('main_'.env('APP_NAME').'.google_cloud_storage.freeWallpapers');
                $fileUrlW                       = $folderW.$fileNameNonHaveExtensionW.'.'.$extensionW;
                /* upload wallpaper lên google_cloud_storage với 3 bản Full Small Mini (thông qua function Upload) */
                $fileUpload = \App\Helpers\Upload::uploadWallpaper($wallpaper, $fileNameNonHaveExtensionW.'.'.$extensionW, $folderW);
                /* Lưu thông tin vào CSDL */
                $idWallpaper = FreeWallpaper::insertItem([
                    'user_id'       => Auth::user()->id,
                    'file_name'     => $fileNameNonHaveExtensionW,
                    'extension'     => $extensionW,
                    'file_cloud'    => $fileUrlW,
                    'width'         => $widthW,
                    'height'        => $heightW,
                    'file_size'     => $fileSizeW,
                    'mine_type'     => $miniTypeW
                ]);
                /* lưu categories */
                self::saveCategories($idWallpaper, $request->all());
                /* lưu tag name */
                if(!empty($request->get('tags'))) self::createOrGetTagName($idWallpaper, 'free_wallpaper_info', $request->get('tags'));
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
            $flagShow           = !empty($request->get('flag_thumnail_category'))&&$request->get('flag_thumnail_category')=='on' ? 1 : 0;
            FreeWallpaper::updateItem($idWallpaper, [
                'flag_thumnail_category'    => $flagShow,
            ]);
            /* lưu categories */
            self::saveCategories($idWallpaper, $request->all());
            /* lưu tag name */
            if(!empty($request->get('tags'))) self::createOrGetTagName($idWallpaper, 'free_wallpaper_info', $request->get('tags'));
            DB::commit();
            return true;
        } catch (\Exception $exception){
            DB::rollBack();
            return false;
        }
    }
    
    public static function saveCategories($idWallpaper, $requestAll = []){
        if(!empty($idWallpaper)&&!empty($requestAll)){
            RelationFreewallpaperCategory::select('*')
                ->where('free_wallpaper_info_id', $idWallpaper)
                ->delete();
            foreach(config('main_'.env('APP_NAME').'.category_type') as $type){
                if(!empty($requestAll[$type['key']])){
                    /* vừa dùng ajax vùa dùng controller nên có thể là chuỗi hoặc array -> kiểm tra trước khi đưa vào xử lý */
                    $arrayCategory = is_string($requestAll[$type['key']]) ? explode(',', $requestAll[$type['key']]) : $requestAll[$type['key']];
                    foreach($arrayCategory as $idCategory){
                        RelationFreewallpaperCategory::insertItem([
                            'free_wallpaper_info_id'    => $idWallpaper,
                            'category_info_id'          => $idCategory
                        ]);
                    }
                }
            }
        }
    }

    public static function createOrGetTagName($idWallpaper, $table, $jsonTagName = null){
        if(!empty($idWallpaper)&&!empty($jsonTagName)){
            RelationTagInfoOrther::select('*')
                ->where('reference_type', $table)
                ->where('reference_id', $idWallpaper)
                ->delete();
            $tag    = json_decode($jsonTagName, true);
            foreach($tag as $t){
                $nameTag    = strtolower($t['value']);
                /* kiểm tra xem tag name đã tồn tại chưa */
                $infoTag = Tag::select('*')
                    ->whereHas('seo', function ($query) use ($nameTag) {
                        $query->whereRaw('LOWER(title) = ?', [$nameTag]);
                    })
                    ->with('seo')
                    ->first();
                $idTag      = $infoTag->id ?? 0;
                /* chưa tồn tại -> tạo và láy ra */
                if(empty($idTag)) $idTag  = self::createSeoTmp($nameTag);
                /* insert relation */
                RelationTagInfoOrther::insertItem([
                    'tag_info_id'       => $idTag,
                    'reference_type'    => $table,
                    'reference_id'      => $idWallpaper
                ]);
            }
        }
    }

    private static function createSeoTmp($nameTag){
        /* tạo bảng seo tạm */
        $slug       = config('main_'.env('APP_NAME').'.auto_fill.slug.vi').'-'.Charactor::convertStrToUrl($nameTag);
        $idSeo      = Seo::insertItem([
            'title'                     => $nameTag,
            'seo_title'                 => $nameTag,
            'level'                     => 1,
            'type'                      => 'tag_info',
            'slug'                      => $slug,
            'slug_full'                 => $slug,
            'rating_author_name'        => 1,
            'rating_author_star'        => 5,
            'rating_aggregate_count'    => rand(100,5000),
            'rating_aggregate_star'     => '4.'.rand(5, 9),
            'created_by'                => Auth::user()->id,
            'language'                  => 'vi'
        ]);
        /* tạo bảng tag */
        $idTag      = Tag::insertItem(['seo_id' => $idSeo]);
        /* tạo Relation */
        RelationSeoTagInfo::insertItem([
            'seo_id'        => $idSeo,
            'tag_info_id'   => $idTag
        ]);
        return $idTag;
    }

    public function deleteWallpaper(Request $request){
        $flag                       = false;
        if(!empty($request->get('id'))){
            $idWallpaper            = $request->get('id');
            $infoWallpaper          = FreeWallpaper::select('*')
                                        ->where('id', $idWallpaper)
                                        ->with('seo')
                                        ->first();
            $flag                   = self::delete($infoWallpaper);
        }
        return $flag;
    }

    private static function delete($infoWallpaper){
        $flag   = false;
        if(!empty($infoWallpaper)){
            /* xóa wallpaper trong google_cloud_storage */
            Storage::disk('gcs')->delete($infoWallpaper->file_cloud);
            /* xóa wallpaper large trong google_cloud_storage */
            Storage::disk('gcs')->delete(config('main_'.env('APP_NAME').'.google_cloud_storage.freeWallpapers').$infoWallpaper->file_name.'-large.'.$infoWallpaper->extension);
            /* xóa wallpaper Small trong google_cloud_storage */
            Storage::disk('gcs')->delete(config('main_'.env('APP_NAME').'.google_cloud_storage.freeWallpapers').$infoWallpaper->file_name.'-small.'.$infoWallpaper->extension);
            /* xóa wallpaper Mini trong google_cloud_storage */
            Storage::disk('gcs')->delete(config('main_'.env('APP_NAME').'.google_cloud_storage.freeWallpapers').$infoWallpaper->file_name.'-mini.'.$infoWallpaper->extension);
            /* xóa relation */
            /* categories */
            RelationFreeWallpaperUser::select('*')
                ->where('free_wallpaper_info_id', $infoWallpaper->id)
                ->delete();
            /* categories */
            RelationFreewallpaperCategory::select('*')
                ->where('free_wallpaper_info_id', $infoWallpaper->id)
                ->delete();
            /* thumnails của category */
            RelationCategoryThumnail::select('*')
                ->where('free_wallpaper_info_id', $infoWallpaper->id)
                ->delete();
            /* tags */
            RelationTagInfoOrther::select('*')
                ->where('reference_id', $infoWallpaper->id)
                ->where('reference_type', 'free_wallpaper_info')
                ->delete();
            /* delete các trang seos ngôn ngữ */
            foreach($infoWallpaper->seos as $s){
                $imageSmallPath     = Storage::path(config('admin.images.folderUpload').basename($s->infoSeo->image_small));
                if(file_exists($imageSmallPath)) @unlink($imageSmallPath);
                $imagePath          = Storage::path(config('admin.images.folderUpload').basename($s->infoSeo->image));
                if(file_exists($imagePath)) @unlink($imagePath);
                foreach($s->infoSeo->contents as $c) $c->delete();
                $s->infoSeo()->delete();
                $s->delete();
            }
            $infoWallpaper->delete();
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
        foreach($tags as $tag) if(!empty($tag->seo->title)) $arrayTag[] = $tag->seo->title;
        $result         = view('admin.freeWallpaper.formModalUploadAndEdit', compact('wallpaper', 'categories', 'arrayTag', 'tags'))->render();
        echo $result;
    }

}
