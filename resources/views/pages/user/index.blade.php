@extends('layouts.app')
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>User</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">User</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <a href="{{ route('admin.users.create') }}" type="submit" class="btn btn-primary">Add User</a>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <table id="example2" class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Image</th>
                                        <th>Email</th>
                                        <th>Address</th>
                                        <th>Phone</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if ($users->count() > 0)
                                        @foreach ($users as $key => $user)
                                            <tr>
                                                <td>{{ ($users->currentPage() - 1) * $users->perPage() + $loop->iteration }}
                                                </td>
                                                <td>{{ $user->name }}</td>
                                                @if ($user->image == null)
                                                    <td>
                                                        <img src="{{ asset('images/users/defaultAvatar.png') }}"
                                                            alt="" class="custom-image">
                                                    </td>
                                                @else
                                                    <td>
                                                        <img src="{{ asset('storage/images/users/' . $user->image) }}"
                                                            alt="" class="custom-image">
                                                    </td>
                                                @endif
                                                <td>{{ $user->email }}</td>
                                                <td>{{ $user->address }}</td>
                                                <td>{{ $user->phone }}</td>
                                                <td class="action-form">
                                                    <a href="{{ route('admin.users.detail', ['id' => $user->id]) }}"
                                                        class="btn btn-info">
                                                        <i class="fas fa-info"></i>
                                                    </a>
                                                    <a href="{{ route('admin.users.edit', ['id' => $user->id]) }}"
                                                        class="btn btn-warning"><i class="fas fa-pen"></i></a>
                                                    <button type="button" class="btn btn-danger delete-modal"
                                                        id="{{ $user->id }}" data-toggle="modal"
                                                        data-target="#modal_danger"
                                                        data_action_url="{{ route('admin.users.destroy', ['id' => $user->id]) }}">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="7" class="text-center">Empty Users</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                            <div class="pagination-area">
                                {{ $users->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <div class="modal fade" id="modal_danger">
        <div class="modal-dialog">
            <div class="modal-content bg-danger">
                <div class="modal-header">
                    <h4 class="modal-title">Delete User</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Are you sure delete user ?</p>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-outline-light" data-dismiss="modal">Close</button>
                    <form action="" id="delete_form" method="POST">
                        @csrf
                        @method('DELETE')
                        <input type="hidden" name="category_id" id="category_id">
                        <button type="submit" class="btn btn-outline-light">Yes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
