@extends('auth.layout')
@section('content')
    <!-- /.login-logo -->
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
                <p class="login-box-msg">You forgot your password? Here you can easily retrieve a new password.</p>
            @endif
            <form action="{{route('admin.password.email')}}" method="post">
                @csrf
                <div class="input-group mb-3">
                <input type="email" class="form-control" required name="email" placeholder="Email">
                <div class="input-group-append">
                    <div class="input-group-text">
                    <span class="fas fa-envelope"></span>
                    </div>
                </div>
                </div>
                <div class="row">
                <div class="col-12">
                    <button type="submit" class="btn btn-primary btn-block">Request new password</button>
                </div>
                </div>
            </form>

            <p class="mt-3 mb-1">
                <a href="{{route('admin.login')}}">Login</a>
            </p>
        </div>
        <!-- /.login-card-body -->
    </div>
@endsection