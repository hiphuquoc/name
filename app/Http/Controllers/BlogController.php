<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use App\Models\Page;
use App\Models\Category;
use App\Models\CategoryBlog;
use App\Models\Blog;
use App\Helpers\Url;

class BlogController extends Controller{

    public static function getBlogFeatured($language){
        $blogFeatured       = Blog::select('blog_info.*')
                                ->join('seo', 'seo.id', '=', 'blog_info.seo_id')
                                ->whereHas('seos.infoSeo', function ($query) use ($language) {
                                    $query->where('language', $language);
                                })
                                ->where('outstanding', 1)
                                ->where('status', 1)
                                ->with(['seos.infoSeo' => function($query) use ($language) {
                                    $query->where('language', $language);
                                }, 'seo', 'seos'])
                                ->orderBy('seo.ordering', 'DESC')
                                ->orderBy('id', 'DESC')
                                ->skip(0)
                                ->take(7)
                                ->get();
        return $blogFeatured;
    }
    
}
