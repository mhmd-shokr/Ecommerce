@extends('layouts.app')



@section('content')
    <style>
        .filled-close {
            color: red;
        }
    </style>

    <main class="pt-90">
        <div class="mb-4 pb-4"></div>
        <section class="shop-checkout container">
            <h2 class="page-title">Wishlist</h2>
            @if(Cart::instance('wishlist')->content()->count()>0)
                
            <div class="checkout-steps">
                <a href="shop_cart.html" class="checkout-steps__item active">
                    <span class="checkout-steps__item-number">01</span>
                    <span class="checkout-steps__item-title">
                        <span>Shopping Bag</span>
                        <em>Manage Your Items List</em>
                    </span>
                </a>
                <a href="shop_checkout.html" class="checkout-steps__item">
                    <span class="checkout-steps__item-number">02</span>
                    <span class="checkout-steps__item-title">
                        <span>Shipping and Checkout</span>
                        <em>Checkout Your Items List</em>
                    </span>
                </a>
                <a href="shop_order_complete.html" class="checkout-steps__item">
                    <span class="checkout-steps__item-number">03</span>
                    <span class="checkout-steps__item-title">
                        <span>Confirmation</span>
                        <em>Review And Submit Your Order</em>
                    </span>
                </a>
            </div>
            <div class="shopping-cart">
                <div class="cart-table__wrapper">
                    <table class="cart-table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th></th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Subtotal</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($items as $item)
                                <tr>
                                    <td>
                                        <div class="shopping-cart__product-item">
                                            <img loading="lazy"
                                                src="{{asset('uploads/products/thumbnails/' . $item->model->image) }}" width="120"
                                                height="120" alt="{{ $item->name }}" />
                                        </div>
                                    </td>
                                    <td>
                                        <div class="shopping-cart__product-item__detail">
                                            <h4>{{ $item->name }}</h4>
                                            {{-- <ul class="shopping-cart__product-item__options">
                                                <li>Color: Yellow</li>
                                                <li>Size: L</li>
                                            </ul> --}}
                                        </div>
                                    </td>
                                    <td>
                                        <span class="shopping-cart__product-price">${{ $item->price}}</span>
                                    </td>
                                    <td>
                                        {{$item->qty}} piece
                                    </td>
                                    <td>
                                        <span class="shopping-cart__subtotal">${{ $item->subtotal() }}</span>
                                    </td>
                                    <td>
                                        <div class="row">
                                            <div class="col-6">
                                                <form action="{{ route('wishlist.moveToCart',$item->rowId)}}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-warning">Move To Cart</button>
                                                </form>
                                            </div>
                                    
                                            <div class="col-6">
                                                <form action="{{ route('wishlist.removeItem', $item->rowId)}}" method="POST"
                                                    id="removeItem-{{ $item->rowId }}">
                                                    @csrf
                                                    @method('delete')
                                                    <a href="javascript:void(0)" class="remove-cart filled-close" title="remove"
                                                        onclick="document.getElementById('removeItem-{{ $item->rowId }}').submit()">
                                                        <svg width="10" height="10" viewBox="0 0 10 10" fill="#767676"
                                                            xmlns="http://www.w3.org/2000/svg">
                                                            <path d="M0.259435 8.85506L9.11449 0L10 0.885506L1.14494 9.74056L0.259435 8.85506Z" />
                                                            <path d="M0.885506 0.0889838L9.74057 8.94404L8.85506 9.82955L0 0.97449L0.885506 0.0889838Z" />
                                                        </svg>
                                                    </a>
                                                </form>
                                            </div>
                                        </div>
                                    </td>
                                    
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="cart-table-footer">
                        <form action="{{ route('wishlist.clear') }}" method="Post">
                            @csrf
                            @method('delete')
                            <button type="submit" class="btn btn-light delete">Clear WishList</button>
                        </form>
                    </div>
                </div>      
                @else     
                <div class="row">
                    <div class="col md-12">
                        <p>No Item Found In Your WishList</p>
                        <a href="{{ route('shop.index')}}" class="btn btn-info">Wishlist Now</a>
                    </div>
                </div>
             @endif

            </div>
        </section>
    </main>

@endsection
@push('scripts')
    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>

        $(document).ready(function () {
            $('.delete').on('click', function (e) {
                e.preventDefault();
                var form = $(this).closest('form');

                Swal.fire({
                    title: 'Are you sure?',
                    text: "You want to delete this record!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes',
                    cancelButtonText: 'No'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    </script>
@endpush