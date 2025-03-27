@extends('layouts.auth-master')

@section('content')
    <div class="login-container">
        <div class="header">
            <img class="w-50" src="{!! asset('/icons/tms_logo1.png')!!}" alt="Logo">
            <div class="title text-white fw-bolder">CFS</div>
        </div>
        <div class="mt-3">
            <h2 class="fs-2 fw-bolder" style="color:rgb(15, 34, 56);">Login</h2>
        </div>
        <form class="p-4" action="/login" method="POST">
            @csrf
            @if ($errors->has('auth.failed'))
                            <div class="alert alert-danger">
                                {{ $errors->first('auth.failed') }}
                            </div>
            @endif
            @error('username')
                <h6 class="alert alert-danger">{{  $message  }}</h6>
            @enderror
            <div class="form-floating mb-3">
                <input type="text" name="username" class="form-control" id="floatingUsername" placeholder="" value="{{ old('username') }}">
                <label for="floatingUsername">Username</label>
            </div>
            @error('password')
                <h6 class="alert alert-danger">{{  $message  }}</h6>
            @enderror
            <div class="form-floating mb-3">
                <input type="password" name="password" class="form-control" id="floatingPassword" placeholder="" value="{{ old('password') }}">
                <label for="floatingPassword">Password</label>
            </div>
            <button type="submit" class="w-100 btn btn-style text-white border border-0">Login</button>
        </form>
    </div>
@endsection