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
            'title'                     => 'required',
            'seo_title'                 => 'required',
            'seo_description'           => 'required',
            // 'slug'                      => [
            //     'required',
            //     function($attribute, $value, $fail){
            //         $slug           = !empty(request('slug')) ? request('slug') : null;
            //         if(!empty($slug)){
            //             $flag       = false;
            //             $dataCheck  = DB::table('seo')
            //                             ->join('category_info', 'category_info.seo_id', '=', 'seo.id')
            //                             ->select('seo.slug', 'category_info.id')
            //                             ->where('slug', $slug)
            //                             ->first();
            //             if(!empty($dataCheck)){
            //                 if(empty(request('category_info_id'))){
            //                     $flag = true;
            //                 }else {
            //                     if(request('category_info_id')!=$dataCheck->id) $flag = true;
            //                 }
            //             }
            //             if($flag==true) $fail('Dường dẫn tĩnh đã trùng với một Tag khác trên hệ thống!');
            //         }
            //     }
            // ],
        ];
    }

    public function messages()
    {
        return [
            'title.required'                     => 'Tiêu đề không được để trống!',
            'description.required'              => 'Mô tả không được để trống!',
            'seo_title.required'                => 'Tiêu đề Seo không được để trống!',
            'seo_description.required'          => 'Mô tả Seo không được để trống!',
            'en_name.required'                  => 'Tiêu đề (bản tiếng anh) không được để trống!',
            'en_description.required'           => 'Mô tả (bản tiếng anh) không được để trống!',
            'en_seo_title.required'             => 'Tiêu đề Seo (bản tiếng anh) không được để trống!',
            'en_seo_description.required'       => 'Mô tả Seo (bản tiếng anh) không được để trống!',
            'rating_aggregate_count.required'   => 'Số lượt đánh giá không được để trống!',
            'rating_aggregate_star.required'    => 'Số sao không được để trống!',
            'slug.required'                     => 'Đường dẫn tĩnh không được để trống!',
            'en_slug.required'                  => 'Đường dẫn tĩnh (bản tiếng anh) không được để trống!'
        ];
    }
}
