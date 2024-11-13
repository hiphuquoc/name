<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Route;
use App\Models\Page;
use App\Models\Category;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\Admin\HelperController;
use App\Models\Tag;
use App\Models\Seo;
use App\Models\SeoContent;
use App\Models\Product;
use GeoIp2\Database\Reader;

// use App\Models\Prompt;
// use Intervention\Image\ImageManagerStatic;
// use Illuminate\Support\Facades\Http;
// use GuzzleHttp\Client;
// use AdityaDees\LaravelBard\LaravelBard;
// use App\Http\Controllers\Admin\TranslateController;
// use App\Jobs\AutoTranslateContent;
// use App\Models\FreeWallpaper;
// use App\Models\RelationSeoCategoryInfo;
use App\Models\RelationSeoProductInfo;
// use App\Models\RelationSeoTagInfo;
// use App\Models\RelationSeoPageInfo;
// use App\Models\Wallpaper;
// use Google\Client as Google_Client;
// use Illuminate\Support\Facades\DB;

// use Illuminate\Support\Facades\Mail;
// use App\Mail\SendProductMail;

// use DOMDocument;
// use PDO;
// use PhpParser\Node\Stmt\Switch_;

class HomeController extends Controller {
    public static function home(Request $request, $language = 'vi'){
        /* ngôn ngữ */
        SettingController::settingLanguage($language);
        /* cache HTML */
        $nameCache              = $language.'home.'.config('main_'.env('APP_NAME').'.cache.extension');
        $pathCache              = Storage::path(config('main_'.env('APP_NAME').'.cache.folderSave')).$nameCache;
        $cacheTime    	        = env('APP_CACHE_TIME') ?? 1800;
        if(file_exists($pathCache)&&$cacheTime>(time() - filectime($pathCache))){
            $xhtml              = file_get_contents($pathCache);
        }else {
            $item               = Page::select('*')
                ->whereHas('seos.infoSeo', function ($query) use ($language) {
                    $query->where('slug', $language);
                })
                ->with('seo', 'seos.infoSeo', 'type')
                ->first();
            /* lấy item seo theo ngôn ngữ được chọn */
            $itemSeo            = [];
            if (!empty($item->seos)) {
                foreach ($item->seos as $s) {
                    if ($s->infoSeo->language == $language) {
                        $itemSeo = $s->infoSeo;
                        break;
                    }
                }
            }
            $categories     = Category::select('*')
                                ->where('flag_show', 1)
                                ->get();
            $xhtml      = view('wallpaper.home.index', compact('item', 'itemSeo', 'language', 'categories'))->render();
            /* Ghi dữ liệu - Xuất kết quả */
            if(env('APP_CACHE_HTML')==true) Storage::put(config('main_'.env('APP_NAME').'.cache.folderSave').$nameCache, $xhtml);
        }
        echo $xhtml;
    }

    public static function test(Request $request){
        
        

        // $country_data = [
        //     ['name' => 'Afghanistan', 'price_factor' => 0.5],
        //     ['name' => 'Åland Islands', 'price_factor' => 1],
        //     ['name' => 'Albania', 'price_factor' => 1.5],
        //     ['name' => 'Algeria', 'price_factor' => 1.5],
        //     ['name' => 'American Samoa', 'price_factor' => 1],
        //     ['name' => 'Andorra', 'price_factor' => 1.5],
        //     ['name' => 'Angola', 'price_factor' => 1.5],
        //     ['name' => 'Anguilla', 'price_factor' => 1.5],
        //     ['name' => 'Antarctica', 'price_factor' => 0.5],
        //     ['name' => 'Antigua and Barbuda', 'price_factor' => 1.5],
        //     ['name' => 'Argentina', 'price_factor' => 2],
        //     ['name' => 'Armenia', 'price_factor' => 1.5],
        //     ['name' => 'Aruba', 'price_factor' => 1.5],
        //     ['name' => 'Australia', 'price_factor' => 2.5],
        //     ['name' => 'Austria', 'price_factor' => 2.5],
        //     ['name' => 'Azerbaijan', 'price_factor' => 1.5],
        //     ['name' => 'Bahamas', 'price_factor' => 1.5],
        //     ['name' => 'Bahrain', 'price_factor' => 2],
        //     ['name' => 'Bangladesh', 'price_factor' => 1.5],
        //     ['name' => 'Barbados', 'price_factor' => 1.5],
        //     ['name' => 'Belarus', 'price_factor' => 1.5],
        //     ['name' => 'Belgium', 'price_factor' => 2.5],
        //     ['name' => 'Belize', 'price_factor' => 1.5],
        //     ['name' => 'Benin', 'price_factor' => 1],
        //     ['name' => 'Bermuda', 'price_factor' => 2],
        //     ['name' => 'Bhutan', 'price_factor' => 1],
        //     ['name' => 'Bolivia', 'price_factor' => 1],
        //     ['name' => 'Bonaire, Sint Eustatius and Saba', 'price_factor' => 1],
        //     ['name' => 'Bosnia and Herzegovina', 'price_factor' => 1.5],
        //     ['name' => 'Botswana', 'price_factor' => 1.5],
        //     ['name' => 'Bouvet Island', 'price_factor' => 0.5],
        //     ['name' => 'Brazil', 'price_factor' => 2],
        //     ['name' => 'British Indian Ocean Territory', 'price_factor' => 1],
        //     ['name' => 'Brunei Darussalam', 'price_factor' => 2],
        //     ['name' => 'Bulgaria', 'price_factor' => 2],
        //     ['name' => 'Burkina Faso', 'price_factor' => 1],
        //     ['name' => 'Burundi', 'price_factor' => 1],
        //     ['name' => 'Cabo Verde', 'price_factor' => 1.5],
        //     ['name' => 'Cambodia', 'price_factor' => 1],
        //     ['name' => 'Cameroon', 'price_factor' => 1],
        //     ['name' => 'Canada', 'price_factor' => 2.5],
        //     ['name' => 'Cayman Islands', 'price_factor' => 1.5],
        //     ['name' => 'Central African Republic', 'price_factor' => 1],
        //     ['name' => 'Chad', 'price_factor' => 1],
        //     ['name' => 'Chile', 'price_factor' => 2],
        //     ['name' => 'China', 'price_factor' => 2.5],
        //     ['name' => 'Christmas Island', 'price_factor' => 0.5],
        //     ['name' => 'Cocos (Keeling) Islands', 'price_factor' => 0.5],
        //     ['name' => 'Colombia', 'price_factor' => 2],
        //     ['name' => 'Comoros', 'price_factor' => 1],
        //     ['name' => 'Congo', 'price_factor' => 1.5],
        //     ['name' => 'Congo, Democratic Republic of the', 'price_factor' => 1.5],
        //     ['name' => 'Cook Islands', 'price_factor' => 1],
        //     ['name' => 'Costa Rica', 'price_factor' => 2],
        //     ['name' => "Côte d'Ivoire", 'price_factor' => 1.5],
        //     ['name' => 'Croatia', 'price_factor' => 2],
        //     ['name' => 'Cuba', 'price_factor' => 1.5],
        //     ['name' => 'Curaçao', 'price_factor' => 1.5],
        //     ['name' => 'Cyprus', 'price_factor' => 2],
        //     ['name' => 'Czechia', 'price_factor' => 2],
        //     ['name' => 'Denmark', 'price_factor' => 2.5],
        //     ['name' => 'Djibouti', 'price_factor' => 1],
        //     ['name' => 'Dominica', 'price_factor' => 1.5],
        //     ['name' => 'Dominican Republic', 'price_factor' => 1.5],
        //     ['name' => 'Ecuador', 'price_factor' => 1.5],
        //     ['name' => 'Egypt', 'price_factor' => 2],
        //     ['name' => 'El Salvador', 'price_factor' => 1],
        //     ['name' => 'Equatorial Guinea', 'price_factor' => 1],
        //     ['name' => 'Eritrea', 'price_factor' => 1],
        //     ['name' => 'Estonia', 'price_factor' => 2],
        //     ['name' => 'Eswatini', 'price_factor' => 1],
        //     ['name' => 'Ethiopia', 'price_factor' => 1.5],
        //     // Add all remaining countries...
        //     ['name' => 'Falkland Islands (Malvinas)', 'price_factor' => 1],
        //     ['name' => 'Faroe Islands', 'price_factor' => 1.5],
        //     ['name' => 'Fiji', 'price_factor' => 1.5],
        //     ['name' => 'Finland', 'price_factor' => 2.5],
        //     ['name' => 'France', 'price_factor' => 2.5],
        //     ['name' => 'French Guiana', 'price_factor' => 2],
        //     ['name' => 'French Polynesia', 'price_factor' => 1.5],
        //     ['name' => 'French Southern Territories', 'price_factor' => 1.5],
        //     ['name' => 'Gabon', 'price_factor' => 1.5],
        //     ['name' => 'Gambia', 'price_factor' => 1],
        //     ['name' => 'Georgia', 'price_factor' => 1.5],
        //     ['name' => 'Germany', 'price_factor' => 2.5],
        //     ['name' => 'Ghana', 'price_factor' => 1.5],
        //     ['name' => 'Gibraltar', 'price_factor' => 2],
        //     ['name' => 'Greece', 'price_factor' => 2],
        //     ['name' => 'Greenland', 'price_factor' => 1.5],
        //     ['name' => 'Grenada', 'price_factor' => 1],
        //     ['name' => 'Guadeloupe', 'price_factor' => 1],
        //     ['name' => 'Guam', 'price_factor' => 1.5],
        //     ['name' => 'Guatemala', 'price_factor' => 1],
        //     ['name' => 'Guernsey', 'price_factor' => 1],
        //     ['name' => 'Guinea', 'price_factor' => 1],
        //     ['name' => 'Guinea-Bissau', 'price_factor' => 1],
        //     ['name' => 'Guyana', 'price_factor' => 1.5],
        //     ['name' => 'Haiti', 'price_factor' => 1],
        //     ['name' => 'Heard Island and McDonald Islands', 'price_factor' => 0.5],
        //     ['name' => 'Holy See', 'price_factor' => 1.5],
        //     ['name' => 'Honduras', 'price_factor' => 1],
        //     ['name' => 'Hong Kong', 'price_factor' => 2],
        //     ['name' => 'Hungary', 'price_factor' => 2],
        //     ['name' => 'Iceland', 'price_factor' => 2],
        //     ['name' => 'India', 'price_factor' => 2],
        //     ['name' => 'Indonesia', 'price_factor' => 2],
        //     ['name' => 'Iran, Islamic Republic of', 'price_factor' => 1.5],
        //     ['name' => 'Iraq', 'price_factor' => 1.5],
        //     ['name' => 'Ireland', 'price_factor' => 2.5],
        //     ['name' => 'Isle of Man', 'price_factor' => 1],
        //     ['name' => 'Israel', 'price_factor' => 2],
        //     ['name' => 'Italy', 'price_factor' => 2.5],
        //     ['name' => 'Jamaica', 'price_factor' => 1.5],
        //     ['name' => 'Japan', 'price_factor' => 2.5],
        //     ['name' => 'Jersey', 'price_factor' => 1],
        //     ['name' => 'Jordan', 'price_factor' => 1.5],
        //     ['name' => 'Kazakhstan', 'price_factor' => 2],
        //     ['name' => 'Kenya', 'price_factor' => 1.5],
        //     ['name' => 'Kiribati', 'price_factor' => 1],
        //     ['name' => 'Korea, Democratic People\'s Republic of', 'price_factor' => 1],
        //     ['name' => 'Korea, Republic of', 'price_factor' => 2],
        //     ['name' => 'Kuwait', 'price_factor' => 2],
        //     ['name' => 'Kyrgyzstan', 'price_factor' => 1.5],
        //     ['name' => 'Laos', 'price_factor' => 1],
        //     ['name' => 'Latvia', 'price_factor' => 2],
        //     ['name' => 'Lebanon', 'price_factor' => 1.5],
        //     ['name' => 'Lesotho', 'price_factor' => 1],
        //     ['name' => 'Liberia', 'price_factor' => 1],
        //     ['name' => 'Libya', 'price_factor' => 1.5],
        //     ['name' => 'Liechtenstein', 'price_factor' => 2],
        //     ['name' => 'Lithuania', 'price_factor' => 2],
        //     ['name' => 'Luxembourg', 'price_factor' => 2.5],
        //     ['name' => 'Macao', 'price_factor' => 1.5],
        //     ['name' => 'Madagascar', 'price_factor' => 1],
        //     ['name' => 'Malawi', 'price_factor' => 1],
        //     ['name' => 'Malaysia', 'price_factor' => 2],
        //     ['name' => 'Maldives', 'price_factor' => 1.5],
        //     ['name' => 'Mali', 'price_factor' => 1],
        //     ['name' => 'Malta', 'price_factor' => 2],
        //     ['name' => 'Marshall Islands', 'price_factor' => 1],
        //     ['name' => 'Martinique', 'price_factor' => 1],
        //     ['name' => 'Mauritania', 'price_factor' => 1.5],
        //     ['name' => 'Mauritius', 'price_factor' => 1.5],
        //     ['name' => 'Mayotte', 'price_factor' => 1],
        //     ['name' => 'Mexico', 'price_factor' => 2],
        //     ['name' => 'Micronesia, Federated States of', 'price_factor' => 1],
        //     ['name' => 'Moldova, Republic of', 'price_factor' => 1],
        //     ['name' => 'Monaco', 'price_factor' => 2.5],
        //     ['name' => 'Mongolia', 'price_factor' => 1.5],
        //     ['name' => 'Montenegro', 'price_factor' => 1.5],
        //     ['name' => 'Montserrat', 'price_factor' => 1],
        //     ['name' => 'Morocco', 'price_factor' => 2],
        //     ['name' => 'Mozambique', 'price_factor' => 1],
        //     ['name' => 'Myanmar', 'price_factor' => 1],
        //     ['name' => 'Namibia', 'price_factor' => 1.5],
        //     ['name' => 'Nauru', 'price_factor' => 1],
        //     ['name' => 'Nepal', 'price_factor' => 1],
        //     ['name' => 'Netherlands, Kingdom of the', 'price_factor' => 2.5],
        //     ['name' => 'New Caledonia', 'price_factor' => 1.5],
        //     ['name' => 'New Zealand', 'price_factor' => 2.5],
        //     ['name' => 'Nicaragua', 'price_factor' => 1.5],
        //     ['name' => 'Niger', 'price_factor' => 1],
        //     ['name' => 'Nigeria', 'price_factor' => 2],
        //     ['name' => 'Niue', 'price_factor' => 1],
        //     ['name' => 'Norfolk Island', 'price_factor' => 1],
        //     ['name' => 'North Macedonia', 'price_factor' => 1.5],
        //     ['name' => 'Northern Mariana Islands', 'price_factor' => 1],
        //     ['name' => 'Norway', 'price_factor' => 2.5],
        //     ['name' => 'Oman', 'price_factor' => 2],
        //     ['name' => 'Pakistan', 'price_factor' => 1.5],
        //     ['name' => 'Palau', 'price_factor' => 1.5],
        //     ['name' => 'Palestine, State of', 'price_factor' => 1],
        //     ['name' => 'Panama', 'price_factor' => 1.5],
        //     ['name' => 'Papua New Guinea', 'price_factor' => 1],
        //     ['name' => 'Paraguay', 'price_factor' => 1.5],
        //     ['name' => 'Peru', 'price_factor' => 1.5],
        //     ['name' => 'Philippines', 'price_factor' => 2],
        //     ['name' => 'Pitcairn', 'price_factor' => 1],
        //     ['name' => 'Poland', 'price_factor' => 2],
        //     ['name' => 'Portugal', 'price_factor' => 2],
        //     ['name' => 'Puerto Rico', 'price_factor' => 1.5],
        //     ['name' => 'Qatar', 'price_factor' => 2],
        //     ['name' => 'Réunion', 'price_factor' => 1.5],
        //     ['name' => 'Romania', 'price_factor' => 2],
        //     ['name' => 'Russian Federation', 'price_factor' => 2],
        //     ['name' => 'Rwanda', 'price_factor' => 1.5],
        //     ['name' => 'Saint Barthélemy', 'price_factor' => 1],
        //     ['name' => 'Saint Helena, Ascension and Tristan da Cunha', 'price_factor' => 1],
        //     ['name' => 'Saint Kitts and Nevis', 'price_factor' => 1.5],
        //     ['name' => 'Saint Lucia', 'price_factor' => 1.5],
        //     ['name' => 'Saint Martin (French part)', 'price_factor' => 1],
        //     ['name' => 'Saint Pierre and Miquelon', 'price_factor' => 1],
        //     ['name' => 'Saint Vincent and the Grenadines', 'price_factor' => 1],
        //     ['name' => 'Samoa', 'price_factor' => 1.5],
        //     ['name' => 'San Marino', 'price_factor' => 1.5],
        //     ['name' => 'Sao Tome and Principe', 'price_factor' => 1],
        //     ['name' => 'Saudi Arabia', 'price_factor' => 2.5],
        //     ['name' => 'Senegal', 'price_factor' => 1.5],
        //     ['name' => 'Serbia', 'price_factor' => 1.5],
        //     ['name' => 'Seychelles', 'price_factor' => 1],
        //     ['name' => 'Sierra Leone', 'price_factor' => 1],
        //     ['name' => 'Singapore', 'price_factor' => 2.5],
        //     ['name' => 'Sint Maarten (Dutch part)', 'price_factor' => 1.5],
        //     ['name' => 'Slovakia', 'price_factor' => 2],
        //     ['name' => 'Slovenia', 'price_factor' => 2],
        //     ['name' => 'Solomon Islands', 'price_factor' => 1],
        //     ['name' => 'Somalia', 'price_factor' => 1],
        //     ['name' => 'South Africa', 'price_factor' => 2],
        //     ['name' => 'South Georgia and the South Sandwich Islands', 'price_factor' => 0.5],
        //     ['name' => 'South Sudan', 'price_factor' => 1],
        //     ['name' => 'Spain', 'price_factor' => 2],
        //     ['name' => 'Sri Lanka', 'price_factor' => 1.5],
        //     ['name' => 'Sudan', 'price_factor' => 1],
        //     ['name' => 'Suriname', 'price_factor' => 1],
        //     ['name' => 'Svalbard and Jan Mayen', 'price_factor' => 0.5],
        //     ['name' => 'Sweden', 'price_factor' => 2.5],
        //     ['name' => 'Switzerland', 'price_factor' => 2.5],
        //     ['name' => 'Syrian Arab Republic', 'price_factor' => 1.5],
        //     ['name' => 'Taiwan, Province of China', 'price_factor' => 2],
        //     ['name' => 'Tajikistan', 'price_factor' => 1],
        //     ['name' => 'Tanzania, United Republic of', 'price_factor' => 1.5],
        //     ['name' => 'Thailand', 'price_factor' => 2],
        //     ['name' => 'Timor-Leste', 'price_factor' => 1],
        //     ['name' => 'Togo', 'price_factor' => 1],
        //     ['name' => 'Tokelau', 'price_factor' => 0.5],
        //     ['name' => 'Tonga', 'price_factor' => 1],
        //     ['name' => 'Trinidad and Tobago', 'price_factor' => 1.5],
        //     ['name' => 'Tunisia', 'price_factor' => 2],
        //     ['name' => 'Türkiye', 'price_factor' => 2],
        //     ['name' => 'Turkmenistan', 'price_factor' => 1.5],
        //     ['name' => 'Turks and Caicos Islands', 'price_factor' => 1],
        //     ['name' => 'Tuvalu', 'price_factor' => 1],
        //     ['name' => 'Uganda', 'price_factor' => 1.5],
        //     ['name' => 'Ukraine', 'price_factor' => 1.5],
        //     ['name' => 'United Arab Emirates', 'price_factor' => 3],
        //     ['name' => 'United Kingdom of Great Britain and Northern Ireland', 'price_factor' => 2.5],
        //     ['name' => 'United States of America', 'price_factor' => 3],
        //     ['name' => 'United States Minor Outlying Islands', 'price_factor' => 1],
        //     ['name' => 'Uruguay', 'price_factor' => 2],
        //     ['name' => 'Uzbekistan', 'price_factor' => 1.5],
        //     ['name' => 'Vanuatu', 'price_factor' => 1],
        //     ['name' => 'Venezuela, Bolivarian Republic of', 'price_factor' => 1],
        //     ['name' => 'Viet Nam', 'price_factor' => 1],
        //     ['name' => 'Virgin Islands (British)', 'price_factor' => 1],
        //     ['name' => 'Virgin Islands (U.S.)', 'price_factor' => 1.5],
        //     ['name' => 'Wallis and Futuna', 'price_factor' => 0.5],
        //     ['name' => 'Western Sahara', 'price_factor' => 1],
        //     ['name' => 'Yemen', 'price_factor' => 1],
        //     ['name' => 'Zambia', 'price_factor' => 1],
        //     ['name' => 'Zimbabwe', 'price_factor' => 1],
        // ];
        
    }

    // public static function copyProductBySource($urlSource, $urlSearch){
    //     $response  = [];
    //     $productSource  = Product::select('*')
    //         ->whereHas('seo', function ($query) use($urlSource){
    //             $query->where('slug', $urlSource);
    //         })
    //         ->with('seo', 'seos.infoSeo.contents')
    //         ->first();

    //     $tmp            = Product::select('*')
    //         ->whereHas('seo', function ($query) use($urlSearch){
    //             $query->where('slug', 'LIKE', $urlSearch.'%');
    //         })
    //         ->where('id', '!=', $productSource->id)
    //         ->with('seo', 'seos.infoSeo.contents')
    //         ->get();
    //     $k      = 1;
    //     foreach ($tmp as $t) {
    //         /* xóa relation seos -> infoSeo -> contents (nếu có) */
    //         foreach ($t->seos as $seo) {
    //             foreach ($seo->infoSeo->contents as $content) {
    //                 SeoContent::select('*')
    //                     ->where('id', $content->id)
    //                     ->delete();
    //             }
    //             \App\Models\RelationSeoProductInfo::select('*')
    //                 ->where('seo_id', $seo->seo_id)
    //                 ->delete();
    //             Seo::select('*')
    //                 ->where('id', $seo->seo_id)
    //                 ->delete();
    //         }
    //         /* tạo dữ liệu mới */
    //         $i = 0;
    //         foreach ($productSource->seos as $seoS) {
    //             /* tạo seo */
    //             $tmp2   = $seoS->infoSeo->toArray();
    //             $insert = [];
    //             foreach ($tmp2 as $key => $value) {
    //                 if ($key != 'contents' && $key != 'id') $insert[$key] = $value;
    //             }
    //             $insert['link_canonical']   = $tmp2['id'];
    //             $insert['slug']             = $tmp2['slug'] . '-' . $k;
    //             $insert['slug_full']        = $tmp2['slug_full'] . '-' . $k;
    //             $idSeo = Seo::insertItem($insert);
    //             /* cập nhật lại seo_id của product */
    //             if ($insert['language'] == 'vi') {
    //                 Product::updateItem($t->id, [
    //                     'seo_id' => $idSeo,
    //                 ]);
    //             }
    //             $response[] = $idSeo;
    //             /* tạo relation_seo_product_info */
    //             RelationSeoProductInfo::insertItem([
    //                 'seo_id'    => $idSeo,
    //                 'product_info_id' => $t->id,
    //             ]);
    //             /* tạo content */
    //             foreach ($seoS->infoSeo->contents as $content) {
    //                 $contentInsert = $content->content;
    //                 $contentInsert = str_replace($seoS->infoSeo->slug_full, $insert['slug_full'], $contentInsert);
    //                 SeoContent::insertItem([
    //                     'seo_id'    => $idSeo,
    //                     'content'   => $contentInsert,
    //                     'ordering'  => $content->ordering,
    //                 ]);
    //             }
    //             ++$i;
    //         }
    //         /* copy relation product và category */
    //         \App\Models\RelationCategoryProduct::select('*')
    //             ->where('product_info_id', $t->id)
    //             ->delete();
    //         foreach($productSource->categories as $category){
    //             \App\Models\RelationCategoryProduct::insertItem([
    //                 'category_info_id'       => $category->category_info_id,
    //                 'product_info_id'      => $t->id
    //             ]);
    //         }
    //         /* copy relation product và tag */
    //         \App\Models\RelationTagInfoOrther::select('*')
    //             ->where('reference_type', 'product_info')
    //             ->where('reference_id', $t->id)
    //             ->delete();
    //         foreach($productSource->tags as $tag){
    //             \App\Models\RelationTagInfoOrther::insertItem([
    //                 'tag_info_id'       => $tag->tag_info_id,
    //                 'reference_type'    => 'product_info',
    //                 'reference_id'      => $t->id
    //             ]);
    //         }
    //         ++$k;
    //     }
    //     return $response;
    // }

    // public static function getCategories($params){
    //     $language       = session()->get('language');
    //     $sortBy         = $params['sort_by'] ?? null;
    //     $loaded         = $params['loaded'] ?? 0;
    //     $requestLoad    = $params['request_load'] ?? 10;
    //     $type           = $params['type'] ?? 'category_info'; /* category_info, style_info, event_info */
    //     $response       = [];
    //     $items          = Category::select('*')
    //                         ->whereHas('seo', function($query) use($type){
    //                             $query->where('level', 2)
    //                                 ->where('type', $type);
    //                         })
    //                         ->whereHas('seos.infoSeo', function($query) use($language){
    //                             $query->where('language', $language);
    //                         })
    //                         ->where('flag_show', 1)
    //                         ->when(empty($sortBy), function($query){
    //                             $query->orderBy('id', 'ASC');
    //                         })
    //                         ->when($sortBy=='newest'||$sortBy=='propose', function($query){
    //                             $query->orderBy('id', 'DESC');
    //                         })
    //                         ->when($sortBy=='favourite', function($query){
    //                             $query->orderBy('heart', 'DESC')
    //                                     ->orderBy('id', 'DESC');
    //                         })
    //                         ->when($sortBy=='oldest', function($query){
    //                             $query->orderBy('id', 'ASC');
    //                         })
    //                         // ->with(['seo', 'seos.infoSeo' => function($query) use($language) {
    //                         //     $query->where('language', $language);
    //                         // }])
    //                         ->skip($loaded)
    //                         ->take($requestLoad)
    //                         ->get();
    //     $total          = Category::select('*')
    //                         ->whereHas('seo', function($query) use($type){
    //                             $query->where('level', 2)
    //                                 ->where('type', $type);
    //                         })
    //                         ->whereHas('seos.infoSeo', function($query) use($language){
    //                             $query->where('language', $language);
    //                         })
    //                         ->where('flag_show', 1)
    //                         ->count();
    //     $response['items']      = $items;
    //     $response['total']      = $total;
    //     $response['loaded']     = $loaded + $requestLoad;
    //     return $response;
    // }
}