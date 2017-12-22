@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <pbl-book-reader
                    :book="{{ $book }}"
                ></pbl-book-reader>
            </div>
        </div>
    </div>
@endsection
