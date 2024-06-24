@extends('layouts.app')
@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Show Product</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Show Product</li>
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
                <!-- left column -->
                <div class="col-md-12">
                    <!-- jquery validation -->
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Product infomation</h3>
                        </div>
                        <!-- /.card-header -->
                        <!-- /.analytics -->
                        <div class="card-body product-analytics">
                            <div class="row mt-3 gap-4">
                               <div class="col-2">
                                    <div class="bg-light p-3 py-3">
                                        <p>Quantity in stock: </p>
                                        <p class="product-analytics__quantity">{{$product->quantity}}</p>
                                    </div>
                               </div>
                               <div class="col-2">
                                    <div class="bg-light p-3 py-3">
                                        <p>Quantity in order: </p>
                                        <p class="product-analytics__quantity">{{$dataAnalytics['orderCount']}}</p>
                                    </div>
                               </div>
                               <div class="col-2">
                                    <div class="bg-light p-3 py-3">
                                        <p>Quantity added to wishlist: </p>
                                        <p class="product-analytics__quantity">{{$dataAnalytics['wishlistCount']}}</p>
                                    </div>
                               </div>
                               <div class="col-2">
                                    <div class="bg-light p-3 py-3">
                                        <p>Quantity added to cart: </p>
                                        <p class="product-analytics__quantity">{{$dataAnalytics['cartQuantity']}}</p>
                                    </div>
                               </div>
                               <div class="col-2">
                                    <div class="bg-light p-3 py-3">
                                        <p>View: </p>
                                        <p class="product-analytics__quantity">{{$product->view}}</p>
                                    </div>
                               </div>
                               <div class="col-2">
                                    <div class="bg-light p-3 py-3">
                                        @if(isset($product->admin) && !empty($product->admin))
                                            <p>Created By: </p>
                                            <a href="{{ route('admin.manager_admin.show', ['id' => $product->admin->id]) }}" class="product-analytics__quantity">
                                                {{ $product->admin->name }}
                                            </a>
                                        @else
                                            <p>Created By: N/A</p>
                                        @endif
                                    </div>
                               </div>
                            </div>
                        </div>
                        <!-- /.end - analytics -->
                        <!-- form start -->
                        <form action="{{ route('admin.products.edit', ['id' => $product->id]) }}" method="POST"
                            id="update_product_form" enctype="multipart/form-data">
                            @csrf
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="name">Name</label>
                                    <input type="text" name="name" class="form-control"
                                        value="{{ old('name', $product->name) }}" id="name" placeholder="Product name">
                                    @error('name')
                                        <p class="error text-danger mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="row">
                                    <div class="form-group col-6">
                                        <label for="category_id">Category</label>
                                        <select class="form-control" name="category_id" value="{{ old('category_id') }}"
                                            id="category_id">
                                            <option disabled value="">Select Category</option>
                                            @if (count($categories) > 0)
                                                @foreach ($categories as $item)
                                                    @if ($item->id == $product->category_id)
                                                        <option selected value="{{ $item->id }}">{{ $item->name }}
                                                        </option>
                                                    @else
                                                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                                                    @endif
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
                                            value="{{ old('quantity', $product->quantity) }}" id="quantity"
                                            placeholder="Quantity">
                                        @error('quantity')
                                            <p class="error text-danger mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-6">
                                        <label for="price">Price (VND)</label>
                                        <input type="number" class="form-control" name="price" min="0"
                                            value="{{ old('price', $product->price) }}" id="price" placeholder="Price">
                                        @error('price')
                                            <p class="error text-danger mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div class="form-group col-6">
                                        <label for="discount">Discount (VND)</label>
                                        <input type="number" class="form-control" name="discount" min="0"
                                            value="{{ old('discount', $product->discount) }}" id="discount"
                                            placeholder="Discount">
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
                                                accept="image/*">
                                            <label class="custom-file-label" for="customFile">Choose file</label>
                                        </div>
                                        @error('image')
                                            <p class="error text-danger mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div class="form-group col-3">
                                        <label for="status">Status</label>
                                        <select class="form-control" name="status" id="status"
                                            value="{{ old('status') }}">
                                            <option @if ($product->status == 1) selected @endif value="1">Active
                                            </option>
                                            <option @if ($product->status == 0) selected @endif value="0">Disable
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
                                                value="1" id="hot"
                                                {{ old('hot') || $product->hot ? 'checked' : '' }}>
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
                                        {{ old('description', $product->description) }}
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
                        <!-- form end -->
                        <div class="card-body">
                            <div class="row">
                                <div class="form-group">
                                    <label>Products Images(Click to delete):</label>
                                </div>
                            </div>
                            <div class="d-flex flex-wrap mt-3">
                                @if (count($product->productImages) > 0)
                                    @foreach ($product->productImages as $item)
                                        <div class="col-sm-2 list-addtional-product-images">
                                            <div class="product-image-contant">
                                                <img src="{{ $item->url_image }}" class="img-fluid mb-2"
                                                    alt="{{ $item->file_name }}" />
                                            </div>
                                            <form
                                                action="{{ route('admin.products.destroyImage', ['id' => $product->id, 'imageId' => $item->id]) }}"
                                                method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-danger"><i
                                                        class="fas fa-times"></i></button>
                                            </form>
                                            @if ($item->default)
                                                <button class="btn btn-primary main-image">
                                                    Main Image
                                                </button>
                                            @else
                                                <form
                                                    action="{{ route('admin.products.setMainImage', ['id' => $product->id, 'imageId' => $item->id]) }}"
                                                    method="POST">
                                                    @csrf
                                                    <button class="btn btn-primary main-image">
                                                        Set Main Image
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    @endforeach
                                @else
                                    <p>No additional images</p>
                                @endif
                            </div>
                        </div>
                    </div>
                    <!-- /.card -->
                </div>
                <!--/.col (left) -->
            </div>
            <!-- /.row -->
        </div><!-- /.container-fluid -->
    </section>
@endsection
