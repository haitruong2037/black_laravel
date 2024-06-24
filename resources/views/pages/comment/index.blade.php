@extends('layouts.app')
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Comments</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Comments</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>

    <section class="content">
        <div class="container-fluid">
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible">
                    <h5><i class="icon fas fa-ban"></i> Error!</h5>
                    {{ session('error') }}
                </div>
            @elseif(session('success'))
                <div class="alert alert-success alert-dismissible">
                    <h5><i class="icon fas fa-check"></i> Success</h5>
                    {{ session('success') }}
                </div>
            @endif
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-12">
                                    <form action="{{ route('admin.comments.index') }}" method="GET">
                                        <div class="row justify-content-end">
                                            <livewire:comment-filter/>
                                            <div class="form-group col-2">
                                                <select class="form-control" name="rate" id="rate">
                                                    <option value="">Select Rate</option>
                                                    @for($rate = 1; $rate <= 5 ; $rate++)
                                                        <option value="{{$rate}}" @if (request()->input('rate') == $rate) selected @endif>
                                                            {{$rate}} Start
                                                        </option>
                                                    @endfor
                                                </select>
                                            </div>
                                            <div class="form-group col-2">
                                                <select class="form-control" name="hidden" id="hidden">
                                                    <option value="">Select Status</option>
                                                    <option value="0" @if (request()->input('hidden') == 0 && request()->input('hidden') != null) selected @endif>
                                                        Showing
                                                    </option>
                                                    <option value="1" @if (request()->input('hidden') == 1) selected @endif>
                                                        Hidden
                                                    </option>
                                                </select>
                                            </div>
                                            <div class="col-1">
                                                <button type="submit" class="btn btn-outline-primary">Search</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th class="count-item">Created At</th>
                                        <th>Product</th>
                                        <th>User</th>
                                        <th>Rate</th>
                                        <th>Content</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if (count($comments) > 0)
                                        @foreach ($comments as $key => $comment)
                                            <tr>
                                                <td style="width: 150px">
                                                    {{$comment->created_at}}
                                                </td>
                                                <td>
                                                    <img src="{{ $comment->product->url_image }}"
                                                        alt="{{ $comment->product->image }}"
                                                        class="table-custom-img"> 
                                                    <a href="{{route('admin.products.show', ['id' => $comment->product->id])}}" class="custom-link-redirect">{{ $comment->product->name }}</a> 
                                                </td>
                                                <td>
                                                    <a href="{{route('admin.users.show', ['id' => $comment->user->id])}}" class="custom-link-redirect"> {{ $comment->user->name }}</a> 
                                                </td>
                                                <td class="text-center font-weight-bold">{{ $comment->rate }} <i class="fas fa-star" style="color: #FFD43B;"></i></td>
                                                <td style="width: 450px">{{ $comment->content }}</td>
                                                <td class="order-option">
                                                    @if ($comment->hidden)
                                                        <button type="button" data-comment-hidden-id="{{$comment->id}}" class="btn btn-outline-danger comment-hidden-button">Hidden</button>
                                                    @else   
                                                        <button type="button" data-comment-hidden-id="{{$comment->id}}" class="btn btn-outline-success comment-hidden-button">Showing</button>
                                                    @endif
                                                </td>
                                                <td class="action-form">
                                                    <button class="btn btn-primary" data-livewire-action="view_comment_details" data-id="{{$comment->id}}" data-toggle="modal" data-target="#modal_comment_details1">
                                                        <i class="fas fa-eye"></i>
                                                    </button>

                                                    <button type="button" class="btn btn-danger delete-modal"
                                                        id="{{ $comment->id }}" data-toggle="modal"
                                                        data_action_url="{{ route('admin.comments.destroy', ['id' => $comment->id]) }}"
                                                        data-target="#modal_danger">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="7" class="text-center">Empty Product</td>
                                        </tr>
                                    @endif
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th class="count-item">Created At</th>
                                        <th>Product</th>
                                        <th>User</th>
                                        <th>Rate</th>
                                        <th>Content</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </tfoot>
                            </table>
                            @if (count($comments) > 0)
                                <div class="pagination-area">
                                    {{ $comments->appends(request()->query())->links() }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <livewire:comment.comment-details/>
        <div class="modal fade" id="modal_danger">
            <div class="modal-dialog">
                <div class="modal-content bg-danger">
                    <div class="modal-header">
                        <h4 class="modal-title">Delete Admin</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure delete this Admin ?</p>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-outline-light" data-dismiss="modal">Close</button>
                        <form action="" id="delete_form" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-light">Yes</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
