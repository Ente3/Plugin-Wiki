@extends('layouts.app')

@section('title', trans('wiki::messages.title'))

@section('content')
    @include('wiki::partials._header', ['title' => trans('wiki::messages.title')])

    <div class="row" id="wiki">
        @foreach($categories as $category)
            <div class="col-md-3 gy-3">
                <a href="{{ route('wiki.show', $category) }}">
                    <div class="card">
                        <div class="card-body text-center">
                            @if($category->usesImage())
                                <div class="d-flex justify-content-center align-items-center mb-3" style="height: 3.75rem;">
                                    <img src="{{ $category->image_url }}" alt="" class="mw-100 mh-100" style="object-fit: contain;">
                                </div>
                            @else
                                <i class="{{ $category->icon ?? 'bi bi-book' }} fs-1 mb-3 text-primary"></i>
                            @endif

                            <h2>{{ $category->name }}</h2>
                        </div>
                    </div>
                </a>
            </div>
        @endforeach
    </div>
@endsection
