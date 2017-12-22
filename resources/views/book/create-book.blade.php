@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <pbl-book-create
                    :initial_book="{{ $book ?? \GuzzleHttp\json_encode([]) }}"
                    :author="{{ $author }}"
                    :auth_user="{{ Auth::user() }}"
                    :edit="{{ isset($book) ? 1 : 0 }}"
                    :kyc_passed=" {{ $author->kyc->verification_passed ?? 0 }}"
                ></pbl-book-create>
            </div>
        </div>
    </div>
@endsection
