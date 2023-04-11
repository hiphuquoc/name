<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\CategoryController as CategoryPublic;
use App\Http\Controllers\MomoController;
use App\Http\Controllers\ZalopayController;
use App\Http\Controllers\RoutingController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ConfirmController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\OrderController as OrderPublic;
use App\Http\Controllers\PageController as PagePublic;
use App\Http\Controllers\AjaxController;
use App\Http\Controllers\CookieController;
use App\Http\Controllers\SitemapController;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\GalleryController;
use App\Http\Controllers\Admin\SourceController;
use App\Http\Controllers\Admin\SliderController;
use App\Http\Controllers\Admin\ImageController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\ThemeController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\StyleController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\CategoryBlogController;
use App\Http\Controllers\Admin\BlogController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\CacheController;

use App\Http\Controllers\Auth\ProviderController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
/* login với google */
Route::get('/setCsrfFirstTime', [CookieController::class, 'setCsrfFirstTime'])->name('main.setCsrfFirstTime');
Route::post('/auth/google/callback', [ProviderController::class, 'googleCallback'])->name('main.google.callback');
/* login với facebook */
Route::get('/auth/facebook/redirect', [ProviderController::class, 'facebookRedirect'])->name('main.facebook.redirect');
Route::get('/auth/facebook/callback', [ProviderController::class, 'facebookCallback'])->name('main.facebook.callback');
/* tải hình ảnh khi hoàn tất thanh toán */
Route::post('/downloadSource', [ConfirmController::class, 'downloadSource'])->name('main.downloadSource');
Route::post('/downloadSourceAll', [ConfirmController::class, 'downloadSourceAll'])->name('main.downloadSourceAll');
/* thanh toán */
Route::prefix('payment')->group(function(){
    Route::get('/momoCreate', [MomoController::class, 'create'])->name('main.momo.create');
    Route::get('/zaloCreate', [ZalopayController::class, 'create'])->name('main.zalo.create');
});
/* trang chủ */
Route::get('/', [HomeController::class, 'home'])->name('main.home');
Route::get('/test', [HomeController::class, 'test'])->name('main.test');
/* trang category */
Route::prefix('category')->group(function(){
    Route::get('/loadMore', [CategoryPublic::class, 'loadMore'])->name('main.category.loadMore');
    Route::get('/loadMorePromotion', [CategoryPublic::class, 'loadMorePromotion'])->name('main.category.loadMorePromotion');
});
/* lỗi */
Route::get('/error', [\App\Http\Controllers\ErrorController::class, 'handle'])->name('error.handle');
/* page */
Route::get('/hinh-nen-dien-thoai-khuyen-mai', [PagePublic::class, 'saleOff'])->name('main.saleOff');
Route::get('/tim-kiem', [PagePublic::class, 'searchProduct'])->name('main.searchProduct');
/* cart */
Route::get('/gio-hang', [CartController::class, 'index'])->name('main.cart');
Route::get('/addToCart', [CartController::class, 'addToCart'])->name('main.addToCart');
Route::get('/updateCart', [CartController::class, 'updateCart'])->name('main.updateCart');
Route::get('/removeProductCart', [CartController::class, 'removeProductCart'])->name('main.removeProductCart');
Route::get('/viewSortCart', [CartController::class, 'viewSortCart'])->name('main.viewSortCart');
Route::post('/paymentNow', [CheckoutController::class, 'paymentNow'])->name('main.paymentNow');
Route::post('/paymentCart', [CheckoutController::class, 'paymentCart'])->name('main.paymentCart');
Route::get('/confirm', [ConfirmController::class, 'confirm'])->name('main.confirm');
/* check out */
Route::get('/thanh-toan', [CheckoutController::class, 'index'])->name('main.checkout');
/* order */
Route::post('/order', [OrderPublic::class, 'create'])->name('main.order');
Route::get('/viewConfirm', [OrderPublic::class, 'viewConfirm'])->name('main.viewConfirm');
/* sitemap */
Route::get('sitemap.xml', [SitemapController::class, 'main'])->name('sitemap.main');
Route::get('sitemap/{type}.xml', [SitemapController::class, 'child'])->name('sitemap.child');
/* AJAX */
Route::get('/buildTocContentMain', [AjaxController::class, 'buildTocContentMain'])->name('main.buildTocContentMain');
Route::get('/loadLoading', [AjaxController::class, 'loadLoading'])->name('ajax.loadLoading');
Route::get('/loadDistrictByIdProvince', [AjaxController::class, 'loadDistrictByIdProvince'])->name('ajax.loadDistrictByIdProvince');
Route::get('/searchProductAjax', [AjaxController::class, 'searchProductAjax'])->name('ajax.searchProductAjax');
Route::get('/registryEmail', [AjaxController::class, 'registryEmail'])->name('ajax.registryEmail');
Route::get('/registrySeller', [AjaxController::class, 'registrySeller'])->name('ajax.registrySeller');
Route::get('/setMessageModal', [AjaxController::class, 'setMessageModal'])->name('ajax.setMessageModal');
Route::get('/checkLoginAndSetShow', [AjaxController::class, 'checkLoginAndSetShow'])->name('ajax.checkLoginAndSetShow');
/* login */
Route::get('/admin', [LoginController::class, 'loginForm'])->name('admin.loginForm');
Route::post('/loginAdmin', [LoginController::class, 'loginAdmin'])->name('admin.loginAdmin');
Route::get('/createUser', [LoginController::class, 'create'])->name('admin.createUser');

Route::middleware('auth', 'role:admin')->group(function () {
        /* product */
        Route::prefix('product')->group(function(){
            Route::get('/list', [ProductController::class, 'list'])->name('admin.product.list');
            Route::get('/view', [ProductController::class, 'view'])->name('admin.product.view');
            Route::post('/create', [ProductController::class, 'create'])->name('admin.product.create');
            Route::post('/update', [ProductController::class, 'update'])->name('admin.product.update');
            Route::get('/delete', [ProductController::class, 'delete'])->name('admin.product.delete');
            Route::post('/uploadImageProductPriceAjaxToFile', [ProductController::class, 'uploadImageProductPriceAjaxToFile'])->name('admin.product.uploadImageProductPriceAjaxToFile');
            Route::post('/uploadImageProductPriceAjaxToSource', [ProductController::class, 'uploadImageProductPriceAjaxToSource'])->name('admin.product.uploadImageProductPriceAjaxToSource');
        });
        /* category */
        Route::prefix('category')->group(function(){
            Route::get('/list', [CategoryController::class, 'list'])->name('admin.category.list');
            Route::get('/view', [CategoryController::class, 'view'])->name('admin.category.view');
            Route::post('/create', [CategoryController::class, 'create'])->name('admin.category.create');
            Route::post('/update', [CategoryController::class, 'update'])->name('admin.category.update');
        });
        /* Style */
        Route::prefix('style')->group(function(){
            Route::get('/list', [StyleController::class, 'list'])->name('admin.style.list');
            Route::get('/view', [StyleController::class, 'view'])->name('admin.style.view');
            Route::post('/create', [StyleController::class, 'create'])->name('admin.style.create');
            Route::post('/update', [StyleController::class, 'update'])->name('admin.style.update');
        });
        /* brand */
        Route::prefix('brand')->group(function(){
            Route::get('/list', [BrandController::class, 'list'])->name('admin.brand.list');
            Route::get('/view', [BrandController::class, 'view'])->name('admin.brand.view');
            Route::post('/create', [BrandController::class, 'create'])->name('admin.brand.create');
            Route::post('/update', [BrandController::class, 'update'])->name('admin.brand.update');
        });
        /* page */
        Route::prefix('page')->group(function(){
            Route::get('/list', [PageController::class, 'list'])->name('admin.page.list');
            Route::get('/view', [PageController::class, 'view'])->name('admin.page.view');
            Route::post('/create', [PageController::class, 'create'])->name('admin.page.create');
            Route::post('/update', [PageController::class, 'update'])->name('admin.page.update');
            Route::get('/deleteItem', [PageController::class, 'deleteItem'])->name('admin.page.deleteItem');
        });
        /* ===== Category Blog ===== */
        Route::prefix('categoryBlog')->group(function(){
            Route::get('/', [CategoryBlogController::class, 'list'])->name('admin.categoryBlog.list');
            Route::post('/create', [CategoryBlogController::class, 'create'])->name('admin.categoryBlog.create');
            Route::get('/view', [CategoryBlogController::class, 'view'])->name('admin.categoryBlog.view');
            Route::post('/update', [CategoryBlogController::class, 'update'])->name('admin.categoryBlog.update');
        });
        /* ===== Blog ===== */
        Route::prefix('blog')->group(function(){
            Route::get('/', [BlogController::class, 'list'])->name('admin.blog.list');
            Route::post('/create', [BlogController::class, 'create'])->name('admin.blog.create');
            Route::get('/view', [BlogController::class, 'view'])->name('admin.blog.view');
            Route::post('/update', [BlogController::class, 'update'])->name('admin.blog.update');
            /* Delete AJAX */
            Route::get('/delete', [BlogController::class, 'delete'])->name('admin.blog.delete');
        });
        /* ===== Order ===== */
        Route::prefix('order')->group(function(){
            Route::get('/', [OrderController::class, 'list'])->name('admin.order.list');
            Route::get('/view', [OrderController::class, 'view'])->name('admin.order.view');
            Route::get('/viewExport', [OrderController::class, 'viewExport'])->name('admin.order.viewExport');
            // Route::post('/create', [OrderController::class, 'create'])->name('admin.order.create');
            /* Delete AJAX */
            // Route::get('/delete', [BlogController::class, 'delete'])->name('admin.blog.delete');
        });
        /* setting */
        Route::prefix('setting')->group(function(){
            Route::get('/view', [SettingController::class, 'view'])->name('admin.setting.view');
            Route::get('/slider', [SettingController::class, 'slider'])->name('admin.setting.slider');
        });
        /* theme */
        Route::prefix('theme')->group(function(){
            Route::get('/view', [ThemeController::class, 'view'])->name('admin.theme.view');
            Route::post('/create', [ThemeController::class, 'create'])->name('admin.theme.create');
            Route::post('/update', [ThemeController::class, 'update'])->name('admin.theme.update');
            Route::get('/list', [ThemeController::class, 'list'])->name('admin.theme.list');
            Route::get('/{id}/setColor', [ThemeController::class, 'setColor'])->name('admin.theme.setColor');
        });
        /* slider */
        Route::prefix('slider')->group(function(){
            Route::post('/remove', [SliderController::class, 'remove'])->name('admin.slider.remove');
        });
        /* gallery */
        Route::prefix('gallery')->group(function(){
            Route::post('/remove', [GalleryController::class, 'remove'])->name('admin.gallery.remove');
        });
        /* gallery */
        Route::prefix('source')->group(function(){
            Route::post('/remove', [SourceController::class, 'remove'])->name('admin.source.remove');
        });
        /* image */
        Route::prefix('image')->group(function(){
            Route::get('/', [ImageController::class, 'list'])->name('admin.image.list');
            Route::post('/uploadImages', [ImageController::class, 'uploadImages'])->name('admin.image.uploadImages');
            Route::post('/loadImage', [ImageController::class, 'loadImage'])->name('admin.image.loadImage');
            Route::post('/loadModal', [ImageController::class, 'loadModal'])->name('admin.image.loadModal');
            Route::post('/changeName', [ImageController::class, 'changeName'])->name('admin.image.changeName');
            Route::post('/changeImage', [ImageController::class, 'changeImage'])->name('admin.image.changeImage');
            Route::post('/removeImage', [ImageController::class, 'removeImage'])->name('admin.image.removeImage');

            // Route::get('/toolRename', [ImageController::class, 'toolRename'])->name('admin.image.toolRename');
        });
        /* ===== CACHE ===== */
        Route::prefix('cache')->group(function(){
            Route::get('/clearCacheHtml', [CacheController::class, 'clear'])->name('admin.cache.clearCache');
        });
});
/* ROUTING */
Route::get("/{slug}/{slug2?}/{slug3?}/{slug4?}/{slug5?}/{slug6?}/{slug7?}/{slug8?}/{slug9?}/{slug10?}", [RoutingController::class, 'routing'])->name('routing');