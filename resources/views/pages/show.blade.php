@extends('layouts.app')

@section('title', $page->title)

@section('content')
    @include('wiki::partials._header', ['title' => $page->title, 'breadcrumb' => $breadcrumb])

    <div class="row" id="wiki">
        <div class="col-md-3">
            @include('wiki::partials._sidebar')
        </div>

        <div class="col-md-9">
            <div class="card mb-3">
                <div class="card-body">
                    {!! $page->content !!}
                </div>
            </div>

            @if($previous !== null || $next !== null)
                <div class="row g-3 mb-3">
                    <div class="col-sm-6">
                        @if($previous !== null)
                            <a href="{{ route('wiki.pages.show', [$page->category, $previous]) }}" class="card h-100" rel="prev">
                                <div class="card-body">
                                    <div class="small text-body-secondary">
                                        @lang('pagination.previous')
                                    </div>
                                    <div class="fw-semibold">{{ $previous->title }}</div>
                                </div>
                            </a>
                        @endif
                    </div>

                    <div class="col-sm-6">
                        @if($next !== null)
                            <a href="{{ route('wiki.pages.show', [$page->category, $next]) }}" class="card h-100 text-end" rel="next">
                                <div class="card-body">
                                    <div class="small text-body-secondary">
                                        @lang('pagination.next')
                                    </div>
                                    <div class="fw-semibold">{{ $next->title }}</div>
                                </div>
                            </a>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
