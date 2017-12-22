@extends('layouts.app')

@section('content')
    @if($author->hasRole('author'))
        <pbl-author-view
                :author="{{ $author }}"
                :info="{{ $author->info ?? \GuzzleHttp\json_encode([]) }}"
                :auth_user="{{Auth::user()}}"
                submit_url="{!! $submitUrl !!}"
                photo="{!! $photo !!}"
                :books="{{ $books }}"
        >
        </pbl-author-view>
    @endif

    @if($author->hasRole('reader'))
        <pbl-reader-view
                :user="{{ Auth::user() }}"
                avatar="{{ $author->avatar }}"
        >
        </pbl-reader-view>
    @endif

    @if(count($purchasedBooks))
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <h3>My Purchased Books</h3>
                </div>
            </div>

            @foreach($purchasedBooks->chunk(3) as $chunk)
                <div class="row">
                    @foreach($chunk as $book)
                        <div class="col-md-4">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    {{ $book->title }}
                                </div>
                                <div class="panel-body">
                                    <div class="text-center">
                                        <a href="{{ url('book/view', $book->id) }}">
                                            <img src="{{ $book->cover_art_url }}" alt="" class="img-thumbnail">
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>
    @endif
@endsection
