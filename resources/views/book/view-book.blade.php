@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <pbl-book-view
                    :initial_book="{{ $book }}"
                    :author="{{ $author }}"
                    :auth_user="{{ Auth::user() ?? \GuzzleHttp\json_encode([]) }}"
                ></pbl-book-view>
            </div>
        </div>
    </div>
@endsection
