@extends('layouts.app')

@section('content')
    <div class="container">
        @foreach($books->chunk(3) as $chunk)
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
                                <ul class="list-group">
                                    <li class="list-group-item">
                                        <span class="badge">{{ $book->price_for_crowdsale }} PBL</span>
                                        Price During Crowdsale
                                    </li>
                                    <li class="list-group-item">
                                        <span class="badge">{{ $book->price_after_crowdsale }} PBL</span>
                                        Price After Crowdsale
                                    </li>
                                    <li class="list-group-item">
                                        <span class="badge">{{ $book->crowdsale_end_date }}</span>
                                        Crowdsale End Date
                                    </li>
                                    <li class="list-group-item">
                                        <span class="badge">{{ $book->presale_keys_amount }}</span>
                                        Crowdsale Available Keys
                                    </li>
                                    <li class="list-group-item">
                                        <span class="badge">{{ $book->total_keys_amount }}</span>
                                        Total Amount of Keys Available
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endforeach
    </div>
@endsection
