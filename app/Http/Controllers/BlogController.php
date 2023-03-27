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

    // public static function index(Request $request){
    //     $flagMatch          = true;
    //     $item               = Page::select('*')
    //                             ->whereHas('seo', function($q){
    //                                 $q->where('slug', 'tin-tuc');
    //                             })
    //                             ->with('seo')
    //                             ->first();
    //     /* breadcrumb */
    //     $breadcrumb         = Url::buildBreadcrumb('tin-tuc');
    //     /* lấy id category và id category con (đệ quy) */
    //     $allCategory        = CategoryBlog::all();
    //     $arrayCategory      = [];
    //     foreach($allCategory as $category) $arrayCategory[] = $category->id;
    //     $blogs              = Blog::select('*')
    //                             ->whereHas('categories.infoCategory', function($query) use($arrayCategory){
    //                                 $query->whereIn('id', $arrayCategory);
    //                             })
    //                             ->with('seo')
    //                             ->get();
    //     /* lấy tất cả category hiển thị theo cây */
    //     $categoriesBlog     = CategoryBlog::getTreeCategory();
    //     /* lấy danh mục sản phẩm */
    //     $categories         = Category::getTreeCategory();
    //     /* lấy danh sách từng blog theo category child */
    //     $infoCategoryChilds = CategoryBlog::select('*')
    //                             ->whereHas('seo', function($q){
    //                                 $q->where('level', 1);
    //                             })
    //                             ->with('seo')
    //                             ->get();
    //     foreach($infoCategoryChilds as $infoCategoryChild){
    //         $infoCategoryChild->childs  = Blog::select('*')
    //                                         ->whereHas('categories.infoCategory', function($query) use($infoCategoryChild){
    //                                             $query->where('id', $infoCategoryChild->id);
    //                                         })
    //                                         ->with('seo')
    //                                         ->get();
    //     }
    //     return view('main.categoryBlog.indexParent', compact('item', 'breadcrumb', 'categoriesBlog', 'categories', 'infoCategoryChilds', 'blogs'));
    // }
}
