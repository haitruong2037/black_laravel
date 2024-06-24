@extends('auth.layout')
@section('content')
    <div class="card">
        <!-- /.login-card-body -->
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
                <p class="login-box-msg">Sign in to start your session</p>
            @endif
            <form action="{{route('admin.login')}}" method="POST">
                @csrf
                <div class="input-group mb-3">
                    <input type="email" required class="form-control" name="email" placeholder="Email">
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-envelope"></span>
                        </div>
                    </div>
                </div>
                <div class="input-group mb-3">
                    <input type="password" class="form-control" name="password" placeholder="Password">
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-lock"></span>
                        </div>
                    </div>
                </div>
                <div>
                    <div>
                        <button type="submit" class="btn btn-primary btn-block">Sign In</button>
                    </div>
                </form>
                <p class="mb-0">
                    <a href="{{route('admin.password.request')}}" class="text-center">Forgot password?</a>
                </p>
            </div>
            <!-- /.login-card-body -->
        </div>
        <!-- /.login-card-body -->
    </div>
@endsection
        