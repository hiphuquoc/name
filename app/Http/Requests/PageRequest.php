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
            'name'                      => 'required',
            'description'               => 'required',
            'en_name'                   => 'required',
            'en_description'            => 'required',
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
                        $dataCheck  = DB::table('seo')
                                        ->join('page_info', 'page_info.seo_id', '=', 'seo.id')
                                        ->select('seo.slug', 'page_info.id')
                                        ->where('slug', $slug)
                                        ->first();
                        if(!empty($dataCheck)){
                            if(empty(request('page_info_id'))){
                                $flag = true;
                            }else {
                                if(request('page_info_id')!=$dataCheck->id) $flag = true;
                            }
                        }
                        if($flag==true) $fail('Dường dẫn tĩnh đã trùng với một trang khác trên hệ thống!');
                    }
                }
            ],
            'content'                   => 'required',
            'en_content'                => 'required'
        ];
    }

    public function messages()
    {
        return [
            'name.required'                     => 'Tên trang không được để trống!',
            'description.min'                   => 'Mô tả trang không được để trống!',
            'en_name.required'                  => 'Tên trang không được để trống!',
            'en_description.required'           => 'Mô tả trang không được để trống!',
            'seo_title.required'                => 'Tiêu đề Seo không được để trống!',
            'seo_description.required'          => 'Mô tả Seo không được để trống!',
            // 'rating_aggregate_count.required'   => 'Mô tả trang không được để trống!',
            // 'rating_aggregate_star.required'    => '',
            'content.required'                  => 'Nội dung không được để trống!',
            'en_content.required'               => 'Nội dung (en) không được để trống!'
        ];
    }
}
