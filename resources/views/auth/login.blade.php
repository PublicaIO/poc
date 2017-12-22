@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <div class="panel {{ ($role == 'author') ? 'panel-primary' : 'panel-success' }}">
                    <div class="panel-heading">
                        @if($role == 'author')
                            <h3 class="panel-title">Author Sign In</h3>
                        @else
                            <h3 class="panel-title">Reader Sign In</h3>
                        @endif
                    </div>

                    <div class="panel-body">
                        <form class="form-horizontal" method="POST" action="{{ route('login', ['book' => $book]) }}">
                            {{ csrf_field() }}

                            <div class="form-wrap">
                                <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                                    <div class="form-field col-md-12 ">
                                        <div class="input">
                                            <label for="email" class="">E-Mail Address</label>
                                            <input id="email" type="email" class="" name="email"
                                                   value="{{ old('email') }}" required autofocus>
                                        </div>

                                        @if ($errors->has('email'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('email') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                                    <div class="form-field col-md-12 ">
                                        <div class="input">
                                            <label for="password" class="">Password</label>
                                            <input id="password" type="password" class="" name="password" required>
                                        </div>

                                        @if ($errors->has('password'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('password') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-md-12">
                                        <div class="checkbox">
                                            <label>
                                                <input type="checkbox"
                                                       name="remember" {{ old('remember') ? 'checked' : '' }}> Remember
                                                Me
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-md-12">
                                        <button type="submit" class="btn btn-primary">Login</button>
                                        <a class="btn btn-link" href="{{ route('password.request') }}">Forgot Your
                                            Password?</a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="panel {{ ($role == 'author') ? 'panel-primary' : 'panel-success' }}">
                    <div class="panel-heading">
                        @if($role == 'author')
                            <h3 class="panel-title">New Author?</h3>
                        @else
                            <h3 class="panel-title">New Reader?</h3>
                        @endif
                    </div>

                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-12 text-center">
                                <a href="{{ url("/register/$role") }}" class="btn btn-primary">Create Your Publica
                                    Account</a>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
