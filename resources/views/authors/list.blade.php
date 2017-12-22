@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            @foreach ($authors as $author)
                <div class="col-md-3">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <a href="{{ url('author', ['id' => $author->id])  }}" class="text-center">
                                <figure class="figure">
                                    <div class="row">
                                        <div class="col-md-6 col-md-offset-3">
                                            <img src="{{ $author->avatar }}" alt="" class="img-thumbnail img-circle">
                                        </div>
                                    </div>
                                    <figcaption>{{ $author->name }}</figcaption>
                                </figure>
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection
