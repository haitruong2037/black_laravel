@extends('layouts.app')
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Products</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Products</li>
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
                                <div class="col-2">
                                    <a href="{{ route('admin.products.create') }}" class="btn btn-primary">Create
                                        Product</a>
                                </div>
                                <div class="col-10">
                                    <form action="{{ route('admin.products.index') }}" method="GET">
                                        <div class="row justify-content-end">
                                            <div class="form-group col-3">
                                                <select class="form-control" name="category" id="category">
                                                    <option value="">Select Category</option>
                                                    @if (count($categories) > 0)
                                                        @foreach ($categories as $item)
                                                            <option value="{{ $item->id }}"
                                                                @if (request()->input('category') == $item->id) selected @endif>
                                                                {{ $item->name }}
                                                            </option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                            <div class="form-group col-3">
                                                <select class="form-control" name="status" id="status">
                                                    <option value="">Select Status</option>
                                                    <option value="1"
                                                        @if (request()->input('status') == 1) selected @endif>Active</option>
                                                    <option value="0"
                                                        @if (request()->input('status') == 0 && request()->input('status') != null) selected @endif>
                                                        Disable
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
                                        <th class="count-item">#</th>
                                        <th>Name</th>
                                        <th>Category</th>
                                        <th>Image</th>
                                        <th>Price</th>
                                        <th>Quantity</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if (count($products) > 0)
                                        @foreach ($products as $key => $product)
                                            <tr>
                                                <td class="count-item">
                                                    {{ ($products->currentPage() - 1) * $products->perPage() + $loop->iteration }}
                                                </td>
                                                <td>{{ $product->name }}</td>
                                                <td>{{ $product->category->name }}</td>
                                                <td>
                                                    <img src="{{ $product->url_image }}"
                                                        alt="{{ $product->image }}"
                                                        class="table-custom-img">
                                                </td>
                                                <td>{{ number_format($product->price, 0, '.', ',') }} đ</td>
                                                <td>{{ $product->quantity }}</td>
                                                <td class="action-form">
                                                    <a href="{{ route('admin.products.show', ['id' => $product->id]) }}"
                                                        class="btn btn-primary">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-danger delete-modal"
                                                        id="{{ $product->id }}" data-toggle="modal"
                                                        data_action_url="{{ route('admin.products.destroy', ['id' => $product->id]) }}"
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
                                        <th class="count-item">#</th>
                                        <th>Name</th>
                                        <th>Category</th>
                                        <th>Image</th>
                                        <th>Price</th>
                                        <th>Quantity</th>
                                        <th>Action</th>
                                    </tr>
                                </tfoot>
                            </table>
                            @if (count($products) > 0)
                                <div class="pagination-area">
                                    {{ $products->appends(request()->query())->links() }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
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
