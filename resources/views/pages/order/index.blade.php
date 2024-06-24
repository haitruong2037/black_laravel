@extends('layouts.app')
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Orders</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Orders</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    <!-- Main content -->
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
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
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
                            <form action="{{ route('admin.orders.index') }}" method="GET" class="form-inline">
                                <label for="status-filter" class="mr-2">Filter by Status:</label>
                                <select id="status-filter" class="form-control mr-2" name="status"
                                    onchange="this.form.submit()">
                                    <option value="">All</option>
                                    @foreach ($statuses as $value => $label)
                                        <option value="{{ $value }}"
                                            {{ request('status') == $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </form>
                        </div>
                        <div class="card-body">
                            <table id="example2" class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Created Date</th>
                                        <th>Orderer</th>
                                        <th>Discount</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                        <th>Note</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if (count($orders) > 0)
                                        @foreach ($orders as $key => $order)
                                            <tr>
                                                <td class="count-item">
                                                    {{ date_format($order->created_at, 'd-m-Y H:i') }}
                                                </td>
                                                <td class="count-item">
                                                    {{ $order->user->name }}
                                                </td>
                                                <td>{{ number_format($order->discount, 0, '.', ',') }} đ</td>
                                                <td>{{ number_format($order->total, 0, '.', ',') }} đ</td>
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
                                                <td style="max-width: 160px;">
                                                    <div class="text-truncate-container">
                                                        {{ $order->note }}
                                                    </div>
                                                </td>
                                                <td class="action-form">
                                                    <a href="{{ route('admin.orders.detail', ['id' => $order->id]) }}"
                                                        class="btn btn-info">
                                                        <i class="fas fa-info"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-danger delete-modal"
                                                        id="{{ $order->id }}" data-toggle="modal"
                                                        data_action_url="{{ route('admin.orders.destroy', ['id' => $order->id]) }}"
                                                        data-target="#modal_danger">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="7" class="text-center">Empty order</td>
                                        </tr>
                                    @endif
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th>Created Date</th>
                                        <th>Orderer</th>
                                        <th>Discount</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                        <th>Note</th>
                                    </tr>
                                </tfoot>
                            </table>
                            <div class="pagination-area">
                                {{ $orders->appends(request()->query())->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="modal_danger">
            <div class="modal-dialog">
                <div class="modal-content bg-danger">
                    <div class="modal-header">
                        <h4 class="modal-title">Delete Order</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure delete order ?</p>
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
    </section>
@endsection
