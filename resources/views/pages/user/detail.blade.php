@extends('layouts.app')
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>User Details</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">User Details</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-7">
                    <div class="card">
                        <div class="card-body">
                            <h4>Total Order: {{ $totalOrders }}</h4>
                            @if ($totalOrders > 0)
                                <table id="example2" class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th>Created Date Order</th>
                                            <th>Total Discount</th>
                                            <th>Total Price</th>
                                            <th>Status</th>
                                            <th>View Detail</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($userDetail->order as $order)
                                            <tr>
                                                <td><a href=""
                                                        class="custom-link-redirect">{{ date_format($order->created_at, 'm-d-Y H:i') }}</a>
                                                </td>
                                                <td>
                                                    {{ number_format($order->discount, 0, '.', ',') }} đ
                                                </td>
                                                <td>
                                                    {{ number_format($order->total, 0, '.', ',') }} đ
                                                </td>
                                                @php
                                                    $statuses = [
                                                        'pending' => 'Pending',
                                                        'processing' => 'Processing',
                                                        'shipping' => 'Shipping',
                                                        'delivered' => 'Delivered',
                                                        'canceled' => 'Canceled',
                                                    ];
                                                    $statusClasses = [
                                                        'pending' => 'bg-secondary',
                                                        'processing' => 'bg-primary',
                                                        'shipping' => 'bg-info',
                                                        'delivered' => 'bg-success',
                                                        'canceled' => 'bg-danger',
                                                    ];
                                                @endphp
                                                <td>
                                                    <p class="{{ $statusClasses[$order->status] }} custom-status-user">
                                                        {{ $statuses[$order->status] }}
                                                    </p>
                                                </td>
                                                <td class="text-center">
                                                    <a href="{{ route('admin.orders.detail', ['id' => $order->id]) }}"
                                                        class="btn btn-info">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @else
                                <h4>This user has no orders yet</h4>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="card">
                        <div class="card-body">
                            <h4>Total Wishlist: {{ $totalWishlists }}</h4>
                            @if ($totalWishlists > 0)
                                <table id="example2" class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th>Product Name</th>
                                            <th>Image</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($userDetail->wishlist as $key => $wishlist)
                                            <tr>
                                                <td><a href="{{ route('admin.products.edit', ['id' => $wishlist->product->id]) }}"
                                                        class="custom-link-redirect">
                                                        {{ $wishlist->product->name }}
                                                    </a>
                                                </td>
                                                <td><img src="{{ $wishlist->product->url_image }}" alt=""
                                                        class="categories-img">
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @else
                                <h4>This user has no wishlist yet</h4>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
