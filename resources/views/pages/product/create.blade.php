@extends('layouts.app')
@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Create New Product</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Add New Product</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    <section class="content">
        <div class="container-fluid">
            @if ($errors->has('error'))
                <div class="alert alert-danger alert-dismissible">
                    <h5><i class="icon fas fa-ban"></i> Error!</h5>
                    {{ $errors->first('error') }}
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
                <!-- left column -->
                <div class="col-md-12">
                    <!-- jquery validation -->
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">New product infomation</h3>
                        </div>
                        <!-- /.card-header -->
                        <!-- form start -->
                        <form action="{{ route('admin.products.store') }}" method="POST" id="create_product_form"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="name">Name</label>
                                    <input type="text" name="name" class="form-control" value="{{ old('name') }}"
                                        id="name" placeholder="Product name">
                                    @error('name')
                                        <p class="error text-danger mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="row">
                                    <div class="form-group col-6">
                                        <label for="category_id">Category</label>
                                        <select class="form-control" name="category_id" id="category_id">
                                            <option disabled value="">Select Category</option>
                                            @if (count($categories) > 0)
                                                @foreach ($categories as $item)
                                                    <option value="{{ $item->id }}"
                                                        @if (old('category_id') == $item->id) selected @endif>
                                                        {{ $item->name }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                        @error('category_id')
                                            <p class="error text-danger mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div class="form-group col-6">
                                        <label for="quantity">Quantity</label>
                                        <input type="number" class="form-control" name="quantity" min="0"
                                            value="{{ old('quantity') }}" id="quantity" placeholder="Quantity">
                                        @error('quantity')
                                            <p class="error text-danger mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-6">
                                        <label for="price">Price (VND)</label>
                                        <input type="number" class="form-control" name="price" min="0"
                                            value="{{ old('price') }}" id="price" placeholder="Price">
                                        @error('price')
                                            <p class="error text-danger mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div class="form-group col-6">
                                        <label for="discount">Discount (VND)</label>
                                        <input type="number" class="form-control" name="discount" min="0"
                                            value="{{ old('discount') }}" id="discount" placeholder="Discount">
                                        @error('discount')
                                            <p class="error text-danger mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-6">
                                        <label for="customFile">Main Image</label>
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input" name="image" id="customFile"
                                                value="{{ old('image') }}" accept="image/*">
                                            <label class="custom-file-label" for="customFile">Choose file</label>
                                        </div>
                                        @error('image')
                                            <p class="error text-danger mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div class="form-group col-3">
                                        <label for="status">Status</label>
                                        <select class="form-control" name="status" id="status">
                                            <option @if (old('status') == 1) selected @endif value="1">Active
                                            </option>
                                            <option @if (old('status') == 0 && old('status') != null) selected @endif value="0">Disable
                                            </option>
                                        </select>
                                        @error('status')
                                            <p class="error text-danger mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div class="form-group col-3">
                                        <label for="hot">Hot Product</label>
                                        <div class="form-check">
                                            <input type="checkbox" name="hot" class="form-check-input"
                                                value="1" id="hot" {{ old('hot') ? 'checked' : '' }}>
                                            <label class="form-check-label">Hot Product</label>
                                        </div>
                                        @error('hot')
                                            <p class="error text-danger mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="summernote">Description</label>
                                    <textarea id="summernote" name="description">
                                        {{ old('description') }}
                                    </textarea>
                                    @error('description')
                                        <p class="error text-danger mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="additional_images">Additional Images</label>
                                    <div class="custom-file">
                                        <input type="file" name="additional_images[]" class="custom-file-input"
                                            id="customFile" multiple accept="image/*">
                                        <label class="custom-file-label" for="customFile">Choose files</label>
                                        @error('additional_images')
                                            <p class="error text-danger mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <!-- /.card-body -->
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </div>
                        </form>
                    </div>
                    <!-- /.card -->
                </div>
                <!--/.col (left) -->
            </div>
            <!-- /.row -->
        </div><!-- /.container-fluid -->
    </section>
@endsection
