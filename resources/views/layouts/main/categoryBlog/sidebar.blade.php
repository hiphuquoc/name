@if(!empty($categories)&&$categories->isNotEmpty())
   <div class="sidebarSectionBox">
      <div class="sidebarSectionBox_title">
         <h2>Danh mục sản phẩm</h2>
      </div>
      <div class="sidebarSectionBox_box">
         @foreach($categories as $category)
            <a href="/{{ $category->seo->slug_full ?? null }}" title="{{ $category->name ?? $category->seo->title ?? null }}" class="sidebarSectionBox_box_item">
               <i class="fa-solid fa-angle-right"></i><h3>{{ $category->name ?? $category->seo->title ?? null }}</h3>
            </a>
            {{-- @if(!empty($category->childs)&&$category->childs->isNotEmpty())
               @foreach($category->childs as $child)
               <a href="/{{ $child->seo->slug_full ?? null }}" title="{{ $child->name ?? $child->seo->title ?? null }}" class="sidebarSectionBox_box_item children">
                  <i class="fa-solid fa-circle-notch"></i><h3>{{ $child->name ?? $child->seo->title ?? null }}</h3>
               </a>
               @endforeach
            @endif --}}
         @endforeach
      </div>
   </div>
@endif

@if(!empty($categoriesBlog)&&$categoriesBlog->isNotEmpty())
   <div class="sidebarSectionBox">
      <div class="sidebarSectionBox_title">
         <h2>Chuyên mục blog</h2>
      </div>
      <div class="sidebarSectionBox_box">
         @foreach($categoriesBlog as $category)
            <a href="/{{ $category->seo->slug_full ?? null }}" title="{{ $category->name ?? $category->seo->title ?? null }}" class="sidebarSectionBox_box_item">
               <i class="fa-regular fa-folder"></i><h3>{{ $category->name ?? $category->seo->title ?? null }}</h3>
            </a>
            @if(!empty($category->childs)&&$category->childs->isNotEmpty())
               @foreach($category->childs as $child)
               <a href="/{{ $child->seo->slug_full ?? null }}" title="{{ $child->name ?? $child->seo->title ?? null }}" class="sidebarSectionBox_box_item children">
                  <i class="fa-solid fa-circle-notch"></i><h3>{{ $child->name ?? $child->seo->title ?? null }}</h3>
               </a>
               @endforeach
            @endif
         @endforeach
      </div>
   </div>
@endif