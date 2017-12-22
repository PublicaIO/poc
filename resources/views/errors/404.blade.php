@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">Page Not Found</div>

                <div class="panel-body">
                    <a href="{{ url()->previous() }}" class="btn btn-primary">BACK</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
