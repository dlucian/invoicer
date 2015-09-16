@extends('layouts.master')

@section('title', 'Sign In')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col s6 offset-s3">
                <h1>Sign In</h1>
                @foreach($errors->all() as $error)
                    <div class="card-panel deep-orange darken-3 white-text">{!!$error!!}</div>
                @endforeach

                <form method="POST" action="/auth/login">
                    {!! csrf_field() !!}

                    <div class="row">
                        <div class="input-field col s12">
                            <input type="email" id="email" name="email" value="{{ old('email') }}" />
                            <label for="email">Email</label>
                        </div>
                    </div>


                    <div class="row">
                        <div class="input-field col s12">
                            <input type="password" name="password" id="password" />
                            <label for="password">Password</label>
                        </div>
                    </div>

                    <div class="row">
                        <div class="input-field col s12">
                            <input type="checkbox" name="remember" id="remember" />
                            <label for="remember">Remember Me</label>
                        </div>
                    </div>

                    <div class="row">
                        <div class="input-field col s12 center-align">
                            <button class="waves-effect waves-light btn" type="submit">Sign In</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
