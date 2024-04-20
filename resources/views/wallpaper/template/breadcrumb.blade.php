@if(!empty($breadcrumb))
   <!-- === START:: Breadcrumb === -->
   @php
      $title      = config('language.'.$language.'.data.home');
   @endphp
   <div class="breadcrumbBox">
      <div class="maxLine_1">
         <a href="/{{ $language }}" title="{{ $title }}" class="breadcrumbBox_home" aria-label="{{ $title }}">{{ $title }}</a>
         @for($i=0;$i<count($breadcrumb);++$i)
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