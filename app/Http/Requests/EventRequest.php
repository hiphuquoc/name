<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;

class EventRequest extends FormRequest
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
            'seo_title'                 => 'required',
            'seo_description'           => 'required',
            'rating_aggregate_count'    => 'required',
            'rating_aggregate_star'     => 'required',
            'slug'                      => [
                'required',
                function($attribute, $value, $fail){
                    $slug = !empty(request('slug')) ? request('slug') : null;
                    if (!empty($slug)) {
                        $flag = false;
                
                        // Kiểm tra trong bảng event_info
                        $dataCheckEvent = DB::table('seo')
                            ->join('event_info', 'event_info.seo_id', '=', 'seo.id')
                            ->select('seo.slug', 'event_info.id')
                            ->where('slug', $slug)
                            ->first();
                
                        // Kiểm tra trong bảng category_info
                        $dataCheckCategory = DB::table('seo')
                            ->join('category_info', 'category_info.seo_id', '=', 'seo.id')
                            ->select('seo.slug', 'category_info.id')
                            ->where('slug', $slug)
                            ->first();
                
                        // Kiểm tra trong bảng style_info
                        $dataCheckStyle = DB::table('seo')
                            ->join('style_info', 'style_info.seo_id', '=', 'seo.id')
                            ->select('seo.slug', 'style_info.id')
                            ->where('slug', $slug)
                            ->first();
                
                        // Kiểm tra xem có trùng trong bảng event_info không
                        if (!empty($dataCheckEvent)&&!empty($dataCheckCategory)&&!empty($dataCheckStyle)) {
                            if (empty(request('event_info_id'))) {
                                $flag = true;
                            } else {
                                if (request('event_info_id') != $dataCheckEvent->id) $flag = true;
                            }
                        }
                
                        if ($flag == true) $fail('Dường dẫn tĩnh đã trùng với một trang khác trên hệ thống!');
                    }
                }
            ]
        ];
    }

    public function messages()
    {
        return [
            'tour_departure_id.min'             => 'Điểm khởi hành không được để trống!',
            'price_show.min'                    => 'Giá hiển thị không được nhỏ hơn 0!',
            'price_del.min'                     => 'Giá cũ không được nhỏ hơn 0!',
            'departure_schedule.min'            => 'Thời gian khởi hành không được để trống!',
            'days.min'                          => 'Số ngày không được nhỏ hơn 0!',
            'nights.min'                        => 'Số đêm không được nhỏ hơn 0!',
            'title.required'                    => 'Tiêu đề trang không được để trống!',
            'title.max'                         => 'Tiêu đề trang không được vượt quá 255 ký tự!',
            'description'                       => 'Mô tả trang không được để trống!',
            'ordering.min'                      => 'Giá trị không được nhỏ hơn 0!',
            'seo_title.required'                => 'Tiêu đề SEO không được để trống!',
            'seo_description.required'          => 'Mô tả SEO không được để trống!',
            'slug.required'                     => 'Đường dẫn tĩnh không được để trống!',
            'rating_aggregate_count.required'   => 'Số lượt đánh giá không được để trống!',
            'rating_aggregate_star.required'    => 'Điểm đánh giá không được để trống!',
            'special_content.required'          => 'Điểm nổi bật Tour (dạng giới thiệu) không được bỏ trống!',
            'special_list.required'             => 'Điểm nổi bật Tour (dạng danh sách) không được bỏ trống!',
            'include.required'                  => 'Tour bao gồm không được bỏ trống!',
            'not_include.required'              => 'Tour không bao gồm không được bỏ trống!',
            'policy_child.required'             => 'Chính sách Tour trẻ em không được bỏ trống!',
        ];
    }
}
