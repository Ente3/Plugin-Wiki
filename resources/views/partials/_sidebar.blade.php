<div class="card mb-3">
    <div class="card-body p-2">
        <nav aria-label="{{ trans('wiki::messages.title') }}">
            @foreach($tree as $category)
                @include('wiki::partials._sidebar-category', ['category' => $category])
            @endforeach
        </nav>
    </div>
</div>

@push('styles')
    <style>
        .collapsed .wiki-chevron {
            transform: rotate(-90deg);
        }
    </style>
@endpush
