<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>

<body>
    <div id="app">
        <pbl-auth-user
            :init_user="{{ Auth::user() ?? \GuzzleHttp\json_encode([]) }}"
        ></pbl-auth-user>

        <header class="main-header">
            <div class="logotype">
                <a href="{{ url('/') }}">
                    <img src="{{ asset('img/publica.svg') }}" alt="Publica">
                </a>
            </div>

            <nav>

            </nav>

            @guest
            <div class="user-profile logged-out">
                <div class="user-register">
                    <ul>
                        <li><a href="{{ url('login/author') }}">Login</a></li>
                        <li><a href="{{ url('register/author') }}">Register</a></li>
                    </ul>
                </div>
            </div>
            @else
            <div class="user-profile logged-in">
                <div class="user-logged">
                    <div class="user-info">
                        <div class="user-name">
                            {{ Auth::user()->name }}
                        </div>

                        <div class="user-role">{{ (Auth::user()->hasRole('reader') ? 'Reader' : 'Author') }}</div>
                    </div>

                    <div class="user-pic">
                        <img src="{{ Auth::user()->avatar }}" alt="">
                    </div>

                    <nav class="user-nav">
                        <img src="{{ asset('img/arrow.svg') }}" alt="">
                    </nav>
                </div>

                <div class="user-menu">
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        {{ csrf_field() }}
                    </form>

                    <ul>
                        <li><a href="{{ url('author', ['id' => Auth::id()]) }}">My Profile</a></li>
                        <li><a href="{{ route('logout') }}" onclick="event.preventDefault();document.getElementById('logout-form').submit();">Logout</a></li>
                    </ul>
                </div>
            </div>
            <div class="user-wallet">
                <a href="#!">Wallet</a>
            </div>
            @endguest
        </header>

        @include('layouts.messages')
        @yield('content')
    </div>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}"></script>
</body>
</html>
