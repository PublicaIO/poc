@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <div class="panel {{ ($role == 'author') ? 'panel-primary' : 'panel-success' }}">
                    <div class="panel-heading">
                        @if($role == 'author')
                            <h3 class="panel-title">Author Registration</h3>
                        @else
                            <h3 class="panel-title">Reader Registration</h3>
                        @endif
                    </div>

                    <div class="panel-body">
                        <form class="form-horizontal" method="POST" action="{{ url("register/$role") }}">
                            {{ csrf_field() }}

                            <div class="form-wrap">
                                <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                                    <div class="form-field col-md-12 ">
                                        <div class="input">
                                            <label for="name">Name</label>
                                            <input id="name" type="text" name="name"
                                                   value="{{ old('name') }}" required autofocus>
                                        </div>

                                        @if ($errors->has('name'))
                                            <span class="help-block">
                                                    <strong>{{ $errors->first('name') }}</strong>
                                                </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="form-group{{ $errors->has('surname') ? ' has-error' : '' }}">
                                    <div class="form-field col-md-12 ">
                                        <div class="input">
                                            <label for="surname">Surname</label>
                                            <input id="surname" type="text" name="surname"
                                                   value="{{ old('surname') }}" required>
                                        </div>

                                        @if ($errors->has('surname'))
                                            <span class="help-block">
                                            <strong>{{ $errors->first('surname') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                                    <div class="form-field col-md-12 ">
                                        <div class="input">
                                            <label for="email">E-Mail Address</label>
                                            <input id="email" type="email" name="email"
                                                   value="{{ old('email') }}" required>
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
                                            <label for="password">Password</label>
                                            <input id="password" type="password"
                                                   name="password" required>
                                        </div>
                                        @if ($errors->has('password'))
                                            <span class="help-block">
                                            <strong>{{ $errors->first('password') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="form-field col-md-12 ">
                                        <div class="input">
                                            <label for="password-confirm">Confirm
                                                Password</label>


                                            <input id="password-confirm" type="password"
                                                   name="password_confirmation" required>

                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-md-12">
                                        <p>By creating an account you agree to <a href="https://publica.io/">Publica
                                                Terms of Use</a></p>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-md-12">
                                        <button type="submit" class="btn btn-primary">
                                            Register
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
