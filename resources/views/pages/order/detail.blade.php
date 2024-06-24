@extends('layouts.app')
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Orders Detail</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Order Detail</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <div>
                                <div class="font-weight-bold d-flex justify-content-between">
                                    <div>
                                        <p>Orderer: {{ $order->user->name }}</p>
                                        <p>Receiver: {{ $order->orderAddress->name }}</p>
                                        <p>Address: {{ $order->orderAddress->address }}</p>
                                    </div>
                                    <div>
                                        <p>Phone: {{ $order->orderAddress->phone }}</p>
                                        <p>Order create date: {{ date_format($order->created_at, 'd-m-Y') }}</p>
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
                                        <td class="order-option">
                                            <form action="{{ route('admin.orders.edit', ['id' => $order->id]) }}"
                                                method="POST">
                                                @csrf
                                                <select class="form-control {{ $statusClasses[$order->status] }}"
                                                    name="status" onchange="this.form.submit()">
                                                    @foreach ($statuses as $value => $label)
                                                        <option value="{{ $value }}"
                                                            {{ $order->status == $value ? 'selected' : '' }}>
                                                            {{ $label }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </form>
                                        </td>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <div class="font-weight-bold">
                                <p>Total Discount: {{ number_format($order->discount, 0, '.', ',') }} đ</p>
                                <p>Total Price: {{ number_format($order->total, 0, '.', ',') }} đ</p>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <div>
                                <h4 class="font-weight-bold">Note</h4>
                                <p>{{ $order->note }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-body">
                            <table id="example2" class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Product Name</th>
                                        <th>Image</th>
                                        <th>Price</th>
                                        <th>Discount</th>
                                        <th>Quantity</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if ($orderDetails->count() > 0)
                                        @foreach ($orderDetails as $key => $orderDetail)
                                            <tr>
                                                <td><a href="{{ route('admin.products.edit', ['id' => $orderDetail->product->id]) }}"
                                                        class="custom-link-redirect">{{ $orderDetail->product->name }}</a>
                                                </td>
                                                <td><img src="{{ $orderDetail->product->url_image }}" alt=""
                                                        class="categories-img">
                                                </td>
                                                <td>{{ number_format($orderDetail->price, 0, '.', ',') }} đ</td>
                                                <td>{{ number_format($orderDetail->discount, 0, '.', ',') }} đ</td>
                                                <td>{{ $orderDetail->quantity }}</td>
                                            </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                            <div class="pagination-area">
                                {{ $orderDetails->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
