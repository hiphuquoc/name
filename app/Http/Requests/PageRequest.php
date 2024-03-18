<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;

class PageRequest extends FormRequest
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
            'rating_aggregate_count'    => 'required',
            'rating_aggregate_star'     => 'required',
            'slug'                      => [
                'required',
                function($attribute, $value, $fail){
                    $slug           = !empty(request('slug')) ? request('slug') : null;
                    if(!empty($slug)){
                        $flag       = false;
                        if(request('type')!='edit'){
                            $dataCheck  = DB::table('seo')
                                            ->join('page_info', 'page_info.seo_id', '=', 'seo.id')
                                            ->select('seo.slug', 'page_info.id')
                                            ->where('slug', $slug)
                                            ->first();
                            if(!empty($dataCheck)) $flag = true;
                        }
                        if($flag==true) $fail('Dường dẫn tĩnh đã trùng với một Trang khác trên hệ thống!');
                    }
                }
            ],
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
