@extends('layouts.app')

@section('content')
    @role('author')
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <pbl-author-dashboard
                            :author="{{ $user }}"
                            :books="{{ $books }}"
                    ></pbl-author-dashboard>
                </div>
            </div>
        </div>
    @endrole

    @role('reader')
        <pbl-reader-dashboard
                :last_viewed_book="{{ $lastViewedBook ?? \GuzzleHttp\json_encode([]) }}"
                :purchased_books="{{ $purchasedBooks ??  \GuzzleHttp\json_encode([]) }}"
        ></pbl-reader-dashboard>
    @endrole
@endsection
