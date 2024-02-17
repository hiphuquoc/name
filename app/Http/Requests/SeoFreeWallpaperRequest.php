<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;

class SeoFreeWallpaperRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name'                      => 'required',
            'seo_title'                 => 'required',
            'en_seo_title'              => 'required',
            'seo_description'           => 'required',
            // 'slug'                      => [
            //     'required',
            //     function($attribute, $value, $fail){
            //         $slug           = !empty(request('slug')) ? request('slug') : null;
            //         if(!empty($slug)){
            //             $flag       = false;
            //             $dataCheck  = DB::table('seo')
            //                             ->join('product_info', 'product_info.seo_id', '=', 'seo.id')
            //                             ->select('seo.slug', 'product_info.id')
            //                             ->where('slug', $slug)
            //                             ->first();
            //             if(!empty($dataCheck)){
            //                 if(empty(request('product_info_id'))){
            //                     $flag = true;
            //                 }else {
            //                     if(request('product_info_id')!=$dataCheck->id) $flag = true;
            //                 }
            //             }
            //             if($flag==true) $fail('Dường dẫn tĩnh đã trùng với một trang khác trên hệ thống!');
            //         }
            //     }
            // ]
        ];
    }

    public function messages()
    {
        return [
            'name.required'                     => 'Tiêu đề không được để trống!',
            'seo_title.required'                => 'Tiêu đề Seo không được để trống!',
            'en_seo_title.required'             => 'Tiêu đề Seo (EN) được để trống!',
            'seo_description.required'          => 'Mô tả Seo không được để trống!',
        ];
    }
}
