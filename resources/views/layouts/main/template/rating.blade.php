@if(!empty($item->seo->rating_aggregate_star)&&!empty($item->seo->rating_aggregate_count))
    <div class="ratingBox">
        <div class="ratingBox_star">
            <div class="ratingBox_star_box">
                <span class="ratingBox_star_box_on"><i class="fas fa-star"></i></span>
                <span class="ratingBox_star_box_on"><i class="fas fa-star"></i></span>
                <span class="ratingBox_star_box_on"><i class="fas fa-star"></i></span>
                <span class="ratingBox_star_box_on"><i class="fas fa-star"></i></span>
                <span class="ratingBox_star_box_on"><i class="fas fa-star-half-alt"></i></span>
            </div>
            <div class="ratingBox_star_total">
                <span>{{ $item->seo->rating_aggregate_star }}</span> sao/<span>{{ $item->seo->rating_aggregate_count }}</span> đánh giá
            </div>
        </div>
    </div>
@endif