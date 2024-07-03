<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\CategoryController as CategoryPublic;
use App\Http\Controllers\CategoryMoneyController as CategoryMoneyPublic;
use App\Http\Controllers\MomoController;
use App\Http\Controllers\ZalopayController;
use App\Http\Controllers\RoutingController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ConfirmController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\OrderController as OrderPublic;
use App\Http\Controllers\PageController as PagePublic;
use App\Http\Controllers\AccountController;
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
use App\Http\Controllers\Admin\TagController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\CategoryBlogController;
use App\Http\Controllers\Admin\BlogController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\CacheController;
use App\Http\Controllers\Admin\WallpaperController;
use App\Http\Controllers\Admin\FreeWallpaperController;
use App\Http\Controllers\Admin\SeoFreeWallpaperController;
use App\Http\Controllers\Admin\ProductPriceController;
use App\Http\Controllers\Admin\RedirectController;
use App\Http\Controllers\Admin\PromptController;
use App\Http\Controllers\Admin\ApiAIController;
use App\Http\Controllers\Admin\ChatGptController;
use App\Http\Controllers\Admin\HelperController;
use App\Http\Controllers\Admin\ToolCopyProductController;
use App\Http\Controllers\Admin\TranslateController;
use App\Http\Controllers\CheckOnpageController;

use App\Http\Controllers\Auth\ProviderController;
use App\Http\Controllers\GoogledriveController;
use App\Http\Controllers\PaypalController;

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

Route::middleware('auth', 'role:admin')->group(function (){
    Route::prefix('he-thong')->group(function(){
        /* ===== AI ===== */
        Route::get('/chatGpt', [ChatGptController::class, 'chatGpt'])->name('main.chatGpt');
        /* ===== REDIRECT ===== */
        Route::prefix('redirect')->group(function(){
            Route::get('/list', [RedirectController::class, 'list'])->name('admin.redirect.list');
            Route::get('/create', [RedirectController::class, 'create'])->name('admin.redirect.create');
            Route::get('/delete', [RedirectController::class, 'delete'])->name('admin.redirect.delete');
        });
        /* wallpaper */
        Route::prefix('wallpaper')->group(function(){
            Route::get('/list', [WallpaperController::class, 'list'])->name('admin.wallpaper.list');
            Route::post('/loadOneRow', [WallpaperController::class, 'loadOneRow'])->name('admin.wallpaper.loadOneRow');
            Route::post('/loadModalUploadAndEdit', [WallpaperController::class, 'loadModalUploadAndEdit'])->name('admin.wallpaper.loadModalUploadAndEdit');
            Route::post('/uploadWallpaperWithSource', [WallpaperController::class, 'uploadWallpaperWithSource'])->name('admin.wallpaper.uploadWallpaperWithSource');
            Route::post('/changeWallpaperWithSource', [WallpaperController::class, 'changeWallpaperWithSource'])->name('admin.wallpaper.changeWallpaperWithSource');
            Route::post('/deleteWallpaperAndSource', [WallpaperController::class, 'deleteWallpaperAndSource'])->name('admin.wallpaper.deleteWallpaperAndSource');
            Route::post('/loadFormUploadSourceAndWallpaper', [WallpaperController::class, 'loadFormUploadSourceAndWallpaper'])->name('admin.wallpaper.loadFormUploadSourceAndWallpaper');
            Route::post('/searchWallpapers', [WallpaperController::class, 'searchWallpapers'])->name('admin.wallpaper.searchWallpapers');
        });
        /* free wallpaper */
        Route::prefix('freeWallpaper')->group(function(){
            Route::get('/list', [FreeWallpaperController::class, 'list'])->name('admin.freeWallpaper.list');
            Route::post('/loadOneRow', [FreeWallpaperController::class, 'loadOneRow'])->name('admin.freeWallpaper.loadOneRow');
            Route::post('/loadModalUploadAndEdit', [FreeWallpaperController::class, 'loadModalUploadAndEdit'])->name('admin.freeWallpaper.loadModalUploadAndEdit');
            Route::post('/uploadWallpaper', [FreeWallpaperController::class, 'uploadWallpaper'])->name('admin.freeWallpaper.uploadWallpaper');
            Route::post('/updateWallpaper', [FreeWallpaperController::class, 'updateWallpaper'])->name('admin.freeWallpaper.updateWallpaper');
            Route::post('/deleteWallpaper', [FreeWallpaperController::class, 'deleteWallpaper'])->name('admin.freeWallpaper.deleteWallpaper');
            Route::post('/addFormUpload', [FreeWallpaperController::class, 'addFormUpload'])->name('admin.freeWallpaper.addFormUpload');
        });
        /* seo free wallpaper */
        Route::prefix('seoFreeWallpaper')->group(function(){
            Route::get('/list', [SeoFreeWallpaperController::class, 'list'])->name('admin.seoFreeWallpaper.list');
            Route::get('/view', [SeoFreeWallpaperController::class, 'view'])->name('admin.seoFreeWallpaper.view');
            Route::post('/createAndUpdate', [SeoFreeWallpaperController::class, 'createAndUpdate'])->name('admin.seoFreeWallpaper.createAndUpdate');
        });
        /* prompt */
        Route::prefix('prompt')->group(function(){
            Route::get('/list', [PromptController::class, 'list'])->name('admin.prompt.list');
            Route::get('/view', [PromptController::class, 'view'])->name('admin.prompt.view');
            Route::post('/createAndUpdate', [PromptController::class, 'createAndUpdate'])->name('admin.prompt.createAndUpdate');
            Route::get('/loadColumnTable', [PromptController::class, 'loadColumnTable'])->name('admin.prompt.loadColumnTable');
            Route::get('/delete', [PromptController::class, 'delete'])->name('admin.prompt.delete');
        });
        /* api ai */
        Route::prefix('apiai')->group(function(){
            Route::get('/list', [ApiAIController::class, 'list'])->name('admin.apiai.list');
            Route::get('/view', [ApiAIController::class, 'view'])->name('admin.apiai.view');
            Route::get('/changeApiActive', [ApiAIController::class, 'changeApiActive'])->name('admin.apiai.changeApiActive');
        });
        /* product */
        Route::prefix('product')->group(function(){
            Route::get('/list', [ProductController::class, 'list'])->name('admin.product.list');
            Route::get('/view', [ProductController::class, 'view'])->name('admin.product.view');
            Route::post('/createAndUpdate', [ProductController::class, 'createAndUpdate'])->name('admin.product.createAndUpdate');
            // Route::post('/create', [ProductController::class, 'create'])->name('admin.product.create');
            // Route::post('/update', [ProductController::class, 'update'])->name('admin.product.update');
            Route::get('/delete', [ProductController::class, 'delete'])->name('admin.product.delete');
        });
        /* product price */
        Route::prefix('productPrice')->group(function(){
            Route::post('/loadWallpaperByProductPrice', [ProductPriceController::class, 'loadWallpaperByProductPrice'])->name('admin.productPrice.loadWallpaperByProductPrice');
            Route::post('/addWallpaperToProductPrice', [ProductPriceController::class, 'addWallpaperToProductPrice'])->name('admin.productPrice.addWallpaperToProductPrice');
            Route::post('/deleteWallpaperToProductPrice', [ProductPriceController::class, 'deleteWallpaperToProductPrice'])->name('admin.productPrice.deleteWallpaperToProductPrice');
        });
        /* category */
        Route::prefix('category')->group(function(){
            Route::get('/list', [CategoryController::class, 'list'])->name('admin.category.list');
            Route::get('/view', [CategoryController::class, 'view'])->name('admin.category.view');
            Route::post('/createAndUpdate', [CategoryController::class, 'createAndUpdate'])->name('admin.category.createAndUpdate');
            Route::get('/delete', [CategoryController::class, 'delete'])->name('admin.category.delete');
        });
        /* tag */
        Route::prefix('tag')->group(function(){
            Route::get('/list', [TagController::class, 'list'])->name('admin.tag.list');
            Route::get('/view', [TagController::class, 'view'])->name('admin.tag.view');
            Route::post('/createAndUpdate', [TagController::class, 'createAndUpdate'])->name('admin.tag.createAndUpdate');
            Route::get('/delete', [TagController::class, 'delete'])->name('admin.tag.delete');
        });
        /* page */
        Route::prefix('page')->group(function(){
            Route::get('/list', [PageController::class, 'list'])->name('admin.page.list');
            Route::get('/view', [PageController::class, 'view'])->name('admin.page.view');
            Route::post('/createAndUpdate', [PageController::class, 'createAndUpdate'])->name('admin.page.createAndUpdate');
            Route::get('/delete', [PageController::class, 'delete'])->name('admin.page.delete');
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
        /* ===== CACHE ===== */
        Route::prefix('helper')->group(function(){
            Route::get('/convertStrToUrl', [HelperController::class, 'convertStrToUrl'])->name('admin.helper.convertStrToUrl');
        });
        /* ===== TOOL ===== */
        Route::prefix('toolCopyProduct')->group(function(){
            Route::get('/view', [ToolCopyProductController::class, 'view'])->name('admin.toolCopyProduct.view');
            Route::get('/create', [ToolCopyProductController::class, 'create'])->name('admin.toolCopyProduct.create');
        });
        /* ===== TRANSLATE ===== */
        Route::prefix('translate')->group(function(){
            Route::get('/list', [TranslateController::class, 'list'])->name('admin.translate.list');
            Route::get('/delete', [TranslateController::class, 'delete'])->name('admin.translate.delete');
            Route::post('/createJob', [TranslateController::class, 'createJob'])->name('admin.translate.createJob');
        });
    });
});
/* check onpage website */
Route::get('/buildListPostByUrl', [CheckOnpageController::class, 'buildListPostByUrl'])->name('main.checkOnpage.buildListPostByUrl');
Route::get('/crawler', [CheckOnpageController::class, 'crawler'])->name('main.checkOnpage.crawler');
/* login với google */
Route::get('/setCsrfFirstTime', [CookieController::class, 'setCsrfFirstTime'])->name('main.setCsrfFirstTime');
Route::post('/auth/google/callback', [ProviderController::class, 'googleCallback'])->name('main.google.callback');
/* login với facebook */
Route::get('/auth/facebook/redirect', [ProviderController::class, 'facebookRedirect'])->name('main.facebook.redirect');
Route::get('/auth/facebook/callback', [ProviderController::class, 'facebookCallback'])->name('main.facebook.callback');
/* tải hình ảnh khi hoàn tất thanh toán */
Route::get('/downloadSource', [GoogledriveController::class, 'downloadSource'])->name('main.downloadSource');
// Route::post('/downloadSourceAll', [ConfirmController::class, 'downloadSourceAll'])->name('main.downloadSourceAll');
/* thanh toán */
Route::prefix('payment')->group(function(){
    Route::get('/momoCreate', [MomoController::class, 'create'])->name('main.momo.create');
    Route::get('/zaloCreate', [ZalopayController::class, 'create'])->name('main.zalo.create');
});
/* trang chủ */
$validLanguages = ['']; // Ngôn ngữ mặc định
foreach (config('language') as $key => $value) {
    $validLanguages[] = $key;
}
Route::get('/{language?}', [HomeController::class, 'home'])
    ->where('language', implode('|', $validLanguages))
    ->name('main.home');
/* trang giỏ hàng */
$validCarts     = config('main.url_cart_page');
Route::get('/{slugCart}', [CartController::class, 'index'])
    ->where('slugCart', implode('|', $validCarts))
    ->name('main.cart');
/* trang xác nhận */
$validSlugs = config('main.url_confirm_page');
Route::get('/{slug}', [ConfirmController::class, 'confirm'])
    ->where('slug', implode('|', $validSlugs))
    ->name('main.confirm');
/* nháp */
Route::get('/test123', [HomeController::class, 'test'])->name('main.test');
Route::get('/chatgpt', [HomeController::class, 'chatGPT'])->name('main.chatGPT');
/* lỗi */
Route::get('/error', [\App\Http\Controllers\ErrorController::class, 'handle'])->name('error.handle');
Route::get('/addToCart', [CartController::class, 'addToCart'])->name('main.addToCart');
Route::get('/updateCart', [CartController::class, 'updateCart'])->name('main.updateCart');
Route::get('/removeProductCart', [CartController::class, 'removeProductCart'])->name('main.removeProductCart');
Route::get('/viewSortCart', [CartController::class, 'viewSortCart'])->name('main.viewSortCart');
Route::get('/loadTotalCart', [CartController::class, 'loadTotalCart'])->name('main.loadTotalCart');
Route::get('/paymentNow', [CheckoutController::class, 'paymentNow'])->name('main.paymentNow');
Route::post('/paymentCart', [CheckoutController::class, 'paymentCart'])->name('main.paymentCart');
Route::get('/handlePaymentMomo', [ConfirmController::class, 'handlePaymentMomo'])->name('main.handlePaymentMomo');
Route::get('/handlePaymentZalopay', [ConfirmController::class, 'handlePaymentZalopay'])->name('main.handlePaymentZalopay');
Route::get('/handlePaymentPaypal', [ConfirmController::class, 'handlePaymentPaypal'])->name('main.handlePaymentPaypal');
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
// Route::get('/registrySeller', [AjaxController::class, 'registrySeller'])->name('ajax.registrySeller');
Route::get('/setMessageModal', [AjaxController::class, 'setMessageModal'])->name('ajax.setMessageModal');
Route::get('/checkLoginAndSetShow', [AjaxController::class, 'checkLoginAndSetShow'])->name('ajax.checkLoginAndSetShow');
Route::get('/loadImageFromGoogleCloud', [AjaxController::class, 'loadImageFromGoogleCloud'])->name('ajax.loadImageFromGoogleCloud');
Route::get('/loadImageSource', [AjaxController::class, 'loadImageSource'])->name('ajax.loadImageSource');
Route::get('/downloadImageSource', [AjaxController::class, 'downloadImageSource'])->name('ajax.downloadImageSource');
Route::get('/setViewBy', [AjaxController::class, 'setViewBy'])->name('ajax.setViewBy');
Route::get('/showSortBoxFreeWallpaper', [AjaxController::class, 'showSortBoxFreeWallpaper'])->name('ajax.showSortBoxFreeWallpaper');
Route::get('/showSortBoxWallpaper', [AjaxController::class, 'showSortBoxWallpaper'])->name('ajax.showSortBoxWallpaper');
Route::get('/showSortBoxFreeWallpaperInTag', [AjaxController::class, 'showSortBoxFreeWallpaperInTag'])->name('ajax.showSortBoxFreeWallpaperInTag');
Route::get('/setSortBy', [AjaxController::class, 'setSortBy'])->name('ajax.setSortBy');
Route::get('/downloadImgFreeWallpaper', [AjaxController::class, 'downloadImgFreeWallpaper'])->name('ajax.downloadImgFreeWallpaper');
Route::get('/setFeelingFreeWallpaper', [AjaxController::class, 'setFeelingFreeWallpaper'])->name('ajax.setFeelingFreeWallpaper');
Route::get('/loadOneFreeWallpaper', [AjaxController::class, 'loadOneFreeWallpaper'])->name('ajax.loadOneFreeWallpaper');
Route::get('/loadMoreWallpaper', [CategoryMoneyPublic::class, 'loadMoreWallpaper'])->name('main.category.loadMoreWallpaper');
Route::get('/loadmoreFreeWallpapers', [CategoryPublic::class, 'loadmoreFreeWallpapers'])->name('main.category.loadmoreFreeWallpapers');
Route::get('/toogleHeartFeelingFreeWallpaper', [AjaxController::class, 'toogleHeartFeelingFreeWallpaper'])->name('ajax.toogleHeartFeelingFreeWallpaper');
/* login */
Route::get('/he-thong', [LoginController::class, 'loginForm'])->name('admin.loginForm');
Route::post('/loginAdmin', [LoginController::class, 'loginAdmin'])->name('admin.loginAdmin');
Route::post('/loginCustomer', [LoginController::class, 'loginCustomer'])->name('admin.loginCustomer');
Route::get('/logout', [LoginController::class, 'logout'])->name('admin.logout');
Route::get('/createUser', [LoginController::class, 'create'])->name('admin.createUser');
/* my account */
Route::middleware('auth')->group(function (){
    Route::prefix('tai-khoan')->group(function(){
        Route::get('/tai-xuong-cua-toi', [AccountController::class, 'orders'])->name('main.account.orders');

    });
});

/* ROUTING */
Route::middleware(['checkRedirect'])->group(function () {
    Route::get("/{slug}/{slug2?}/{slug3?}/{slug4?}/{slug5?}/{slug6?}/{slug7?}/{slug8?}/{slug9?}/{slug10?}", [RoutingController::class, 'routing'])->name('routing');
});