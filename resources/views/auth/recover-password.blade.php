@extends('auth.layout')
@section('content')
    <div class="card">
        <div class="card-body login-card-body">
            @if ($errors->any())
                @foreach ($errors->all() as $error)
                    <p class="login-box-msg text-danger">{{ $error }}</p>
                @endforeach
            @elseif (session('error'))
                <p class="login-box-msg text-danger">{{ session('error') }}</p>
            @elseif (session('success'))
                <p class="login-box-msg text-success">{{ session('success') }}</p>
            @else
                <p class="login-box-msg">You are only one step a way from your new password, recover your password now.</p>
            @endif

            <form action="{{ route('admin.password.update') }}" method="post">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                <input type="hidden" name="email" value="{{ $email }}">
                <div class="input-group mb-3">
                    <input type="password" class="form-control" name="password" placeholder="Password">
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-lock"></span>
                        </div>
                    </div>
                </div>
                <div class="input-group mb-3">
                    <input type="password" class="form-control" name="password_confirmation" placeholder="Confirm Password">
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-lock"></span>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary btn-block">Change password</button>
                    </div>
                    <!-- /.col -->
                </div>
            </form>

            <p class="mt-3 mb-1">
                <a href="{{ route('admin.login') }}">Login</a>
            </p>
        </div>
    </div>
@endsection
