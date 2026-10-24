<div class="row">
    <div class="col-md-9">
        @isset($breadcrumb)
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item">
                        <a href="{{ route('wiki.index') }}">{{ trans('wiki::messages.title') }}</a>
                    </li>

                    @foreach($breadcrumb as $item)
                        <li class="breadcrumb-item">
                            <a href="{{ route('wiki.show', $item) }}">{{ $item->name }}</a>
                        </li>
                    @endforeach
                </ol>
            </nav>
        @endisset

        <h1>{{ $title }}</h1>
    </div>

    <div class="col-md-3">
        <form class="mb-3" action="{{ route('wiki.search') }}" method="GET" role="search">
            <label class="visually-hidden" for="searchInput">
                {{ trans('messages.actions.search') }}
            </label>

            <div class="input-group">
                <input type="search" class="form-control" id="searchInput" name="q" value="{{ $search ?? '' }}" placeholder="{{ trans('messages.actions.search') }}" required>

                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-search"></i>
                </button>
            </div>
        </form>
    </div>
</div>
