@if(!empty($breadcrumb))
   <!-- === START:: Breadcrumb === -->
   @php
      $title      = config('data_language_1.'.$language.'.home');
      $iconArrow  = file_get_contents('storage/images/svg/icon_arrow_right.svg');
   @endphp
   <div class="breadcrumbBox">
      <div class="maxLine_1">
         <a href="/{{ $language }}" title="{{ $title }}" class="breadcrumbBox_home" aria-label="{{ $title }}">{{ $title }}</a>
         @for($i=0;$i<count($breadcrumb);++$i)
            {!! $iconArrow !!}
            @if($i!=(count($breadcrumb)-1))
               <a href="/{{ $breadcrumb[$i]->slug_full ?? null }}" title="{{ $breadcrumb[$i]->title }}">{{ $breadcrumb[$i]->title ?? null }}</a>
            @else
               <span>{{ $breadcrumb[$i]->title }}</span>
            @endif
         @endfor
      </div>
   </div>
   <!-- === END:: Breadcrumb === -->
@endif