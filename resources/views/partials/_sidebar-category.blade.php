@php($expanded = $breadcrumb->contains('id', $category->id))

<button type="button" class="btn w-100 text-start d-flex gap-2 px-3 @if(! $expanded) collapsed @endif"
        data-bs-toggle="collapse" data-bs-target="#wiki-category-{{ $category->id }}"
        aria-expanded="{{ $expanded ? 'true' : 'false' }}" aria-controls="wiki-category-{{ $category->id }}">
    <i class="{{ $category->icon ?? 'bi bi-book' }}"></i>
    <span class="flex-grow-1 fw-semibold">{{ $category->name }}</span>
    <i class="bi bi-chevron-down small wiki-chevron"></i>
</button>

<div class="collapse @if($expanded) show @endif" id="wiki-category-{{ $category->id }}">
    <ul class="nav nav-pills flex-column ms-3 mb-1">
        @foreach($category->pages as $catPage)
            <li class="nav-item">
                <a href="{{ route('wiki.pages.show', [$category, $catPage]) }}"
                   class="nav-link @if($page->is($catPage)) active @endif"
                   @if($page->is($catPage)) aria-current="page" @endif>
                    {{ $catPage->title }}
                </a>
            </li>
        @endforeach

        @foreach($category->categories as $subCategory)
            <li class="nav-item">
                @include('wiki::partials._sidebar-category', ['category' => $subCategory])
            </li>
        @endforeach
    </ul>
</div>
