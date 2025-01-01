<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\Models\Seo;
use App\Models\Category;
use App\Models\RelationSeoCategoryInfo;
use App\Models\Tag;
use App\Models\RelationSeoTagInfo;
use App\Models\Product;
use App\Models\RelationSeoProductInfo;
use App\Models\Page;
use App\Models\RelationSeoPageInfo;
use App\Models\SeoContent;
use App\Models\JobAutoTranslate;
use App\Http\Controllers\Admin\ChatGptController;
use App\Http\Controllers\Admin\HelperController;
use App\Models\JobAutoTranslateLinks;

class Tmp implements ShouldQueue {
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(){

    }

    public function handle(){
        try {
            $configLanguage  = config('language');
            $languages  = [];
            foreach($configLanguage as $key => $c) {
                $languages[] = $key;
            };

            $seoALl = Seo::all();

            foreach($seoALl as $seo){
                if(!in_array($seo->language, $languages)){
                    switch ($seo->type) {
                        case 'product_info':
                            RelationSeoProductInfo::select('*')
                                ->where('seo_id', $seo->id)
                                ->delete();
                            break;
                        case 'category_info' || 'style_info' || 'event_info':
                            RelationSeoCategoryInfo::select('*')
                                ->where('seo_id', $seo->id)
                                ->delete();
                            break;
                        case 'page_info':
                            RelationSeoPageInfo::select('*')
                                ->where('seo_id', $seo->id)
                                ->delete();
                            break;
                        case 'tag_info':
                            RelationSeoTagInfo::select('*')
                                ->where('seo_id', $seo->id)
                                ->delete();
                            break;
                        
                        default:
                            # code...
                            break;
                    }
                    Seo::select('*')
                        ->where('id', $seo->id)
                        ->delete();
                }
            }

        } catch (\Exception $e) {
            throw $e; // Đẩy lại lỗi để Laravel tự động thử lại
        }
    }

}
