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
        }

        $pages  = Tag::select('*')
                    ->get();
        foreach($pages as $page){
            foreach($page->seos as $seo){
                if(!empty($seo->infoSeo->language)&&!in_array($seo->infoSeo->language, $languages)){
                    if(!empty($seo->infoSeo->contents)) foreach($seo->infoSeo->contents as $c) $c->delete();
                    $seo->infoSeo()->delete();
                    $seo->delete();
                }
            }
        }

        $pages  = Page::select('*')
                    ->get();
        foreach($pages as $page){
            foreach($page->seos as $seo){
                if(!empty($seo->infoSeo->language)&&!in_array($seo->infoSeo->language, $languages)){
                    if(!empty($seo->infoSeo->contents)) foreach($seo->infoSeo->contents as $c) $c->delete();
                    $seo->infoSeo()->delete();
                    $seo->delete();
                }
            }
        }

        $pages  = Category::select('*')
                    ->get();
        foreach($pages as $page){
            foreach($page->seos as $seo){
                if(!empty($seo->infoSeo->language)&&!in_array($seo->infoSeo->language, $languages)){
                    if(!empty($seo->infoSeo->contents)) foreach($seo->infoSeo->contents as $c) $c->delete();
                    $seo->infoSeo()->delete();
                    $seo->delete();
                }
            }
        }

        } catch (\Exception $e) {
            throw $e; // Đẩy lại lỗi để Laravel tự động thử lại
        }
    }

}
