@extends('layouts.app')

@section('content')
<style>
    .pt-90 {
        padding-top: 90px !important;
    }
    .page-title {
        font-size: 1.5rem;
        font-weight: 700;
        text-transform: uppercase;
        margin-bottom: 40px;
        border-bottom: 2px solid #ddd;
        padding-bottom: 13px;
    }
    .table th {
        background-color: #6a6e51 !important;
        color: #fff;
        text-align: center;
    }
    .table td {
        vertical-align: middle;
    }
    .image img {
        max-width: 50px;
        border-radius: 8px;
    }
    .badge {
        font-size: 0.85rem;
        padding: 0.5em 0.8em;
    }
    .card {
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.05);
    }
    .card h5 {
        font-weight: bold;
    }
    .table-transaction>tbody>tr:nth-of-type(odd) {
            --bs-table-accent-bg: #fff !important;
        }
</style>

<main class="pt-90" style="padding-top: 0px;">
    <section class="my-account container">
        <h2 class="page-title">Orders Details</h2>
        <div class="row">
            <div class="col-lg-2">
                @include('users.account-nav')
            </div>

            <div class="col-lg-10">
                <div class="card p-4 mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5>Ordered Items</h5>
                        <a class="btn btn-outline-secondary btn-sm" href="orders.html">
                            <i class="bi bi-arrow-left"></i> Back
                        </a>
                    </div>

                    <!-- Order Info -->
                    <div class="table-responsive mb-4">
                        <table class="table table-bordered table-trnsaction">
                            <tr>
                                <th>Order No</th>
                                <td>{{ $order->id }}</td>
                                <th>Mobile</th>
                                <td>{{ $order->phone }}</td>
                                <th>Zip Code</th>
                                <td>{{ $order->zip }}</td>
                            </tr>
                            <tr>
                                <th>Order Date</th>
                                <td>{{ $order->created_at }}</td>
                                <th>Delivered Date</th>
                                <td>{{ $order->delivered_date }}</td>
                                <th>Canceled Date</th>
                                <td>{{ $order->canceled_date }}</td>
                            </tr>
                            <tr>
                                <th>Order Status</th>
                                <td colspan="5">
                                    @if($order->status == 'delivered')
                                        <span class="badge bg-success">Delivered</span>
                                    @elseif ($order->status == 'canceled')
                                        <span class="badge bg-danger">Canceled</span>
                                    @else
                                        <span class="badge bg-warning text-dark">Ordered</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>

                    <!-- Ordered Items -->
                    <div class="table-responsive mb-4">
                        <table class="table table-hover table-bordered text-center">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Price</th>
                                    <th>Quantity</th>
                                    <th>SKU</th>
                                    <th>Category</th>
                                    <th>Brand</th>
                                    <th>Options</th>
                                    <th>Return Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($orderItems as $item)
                                    <tr>
                                        <td class="pname text-start">
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="image">
                                                    <img src="{{ asset('uploads/products/thumbnails/' . $item->product->image) }}"
                                                        alt="{{ $item->product->name }}">
                                                </div>
                                                <div>
                                                    <a href="{{ route('shop.product.details', $item->product->slug) }}" target="_blank" class="fw-bold">
                                                        {{ $item->product->name }}
                                                    </a>
                                                </div>
                                            </div>
                                        </td>
                                        <td>${{ number_format($item->price, 2) }}</td>
                                        <td>{{ $item->quantity }}</td>
                                        <td>{{ $item->product->SKU }}</td>
                                        <td>{{ $item->product->category->name ?? '-' }}</td>
                                        <td>{{ $item->product->brand->name ?? '-' }}</td>
                                        <td>{{ $item->options ?? '-' }}</td>
                                        <td>{{ $item->return_status == 0 ? "No" : "Yes" }}</td>
                                        <td>
                                            <a href="#" class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        {{ $orderItems->links('pagination::bootstrap-5') }}
                    </div>

                    <!-- Shipping Address -->
                    <div class="card p-3 mb-4">
                        <h5>Shipping Address</h5>
                        <p>{{ $order->name }}</p>
                        <p>{{ $order->address }}</p>
                        <p>{{ $order->locality }}</p>
                        <p>{{ $order->city }} , {{ $order->country }}</p>
                        <p>{{ $order->landmark }}</p>
                        <p>{{ $order->zip }}</p>
                        <p class="fw-bold">Mobile: {{ $order->phone }}</p>
                    </div>

                    <!-- Transactions -->
                    <div class="card p-3">
                        <h5>Transactions</h5>
                        <table class="table table-bordered text-center">
                            <tr>
                                <th>Subtotal</th>
                                <td>${{ $order->subtotal }}</td>
                                <th>Tax</th>
                                <td>${{ $order->tax }}</td>
                                <th>Discount</th>
                                <td>${{ $order->discount }}</td>
                            </tr>
                            <tr>
                                <th>Total</th>
                                <td>${{ $order->total }}</td>
                                <th>Payment Mode</th>
                                <td>{{ $order->transaction->mode }}</td>
                                <th>Status</th>
                                <td>
                                    @if($transaction->status == 'approved')
                                        <span class="badge bg-success">Approved</span>
                                    @elseif ($transaction->status == 'declined')
                                        <span class="badge bg-danger">Declined</span>
                                    @elseif($transaction->status == 'refundex')
                                        <span class="badge bg-secondary">Refunded</span>
                                    @else
                                        <span class="badge bg-warning text-dark">Pending</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </section>
</main>
@endsection
