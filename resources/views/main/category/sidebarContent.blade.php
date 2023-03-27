<!-- TOC CONTENT -->
{{-- <div id="tocContentSidebar">
    <div class="tocContentSidebar">
        <div class="tocContentSidebar_title">
            <span class="tocContentSidebar_title_icon"></span>
            <span class="tocContentSidebar_title_text">Mục lục</span>
        </div>
        <div class="tocContentSidebar_list">
            @for($i=0;$i<10;++$i)
            <a href="#buildtocContentSidebar_0" class="tocContentSidebar_list_item">
                Top sữa tắm em bé trong nước được phụ huynh tin dùng nhất
            </a>
            @endfor
        </div>
    </div>
</div> --}}

@if(!empty($item->categoryBlogs)&&$item->categoryBlogs->isNotEmpty())
    
    @foreach($item->categoryBlogs as $categoryBlog)
        <div class="sidebarSectionBox" style="position:sticky;top:0;">
            <div class="sidebarSectionBox_title">
                <h2>{{ $categoryBlog->infoCategoryBlog->name }}</h2>
            </div>
            <div class="sidebarSectionBox_box" style="border:none;">
                <div class="categorySidebarBox">
                    @foreach($categoryBlog->infoCategoryBlog->blogs as $blog)
                        @php
                            if($loop->index>9) break;
                            $title  = $blog->infoBlog->name ?? $blog->infoBlog->seo->title ?? null;
                        @endphp
                        <a href="/{{ $blog->infoBlog->seo->slug_full ?? null }}" class="categorySidebarBox_item">
                            <div class="categorySidebarBox_item_image">
                                <img src="{{ Storage::url(config('image.loading_main_gif_small')) }}" data-src="{{ Storage::url($blog->infoBlog->seo->image_small) }}" alt="{{ $title }}" title="{{ $title }}" />
                            </div>
                            <div class="categorySidebarBox_item_title">
                                <h3>{{ $title }}</h3>
                            </div>
                        </a>
                    @endforeach
                    <div class="categorySidebarBox_item viewMore">
                        <a href="/{{ $categoryBlog->infoCategoryBlog->seo->slug_full ?? null }}">Xem tất cả<i class="fa-solid fa-angles-right"></i></a>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
@endif