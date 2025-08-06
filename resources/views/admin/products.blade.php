@extends('layouts.admin')

@section('content')

<div class="main-content-inner">
    <div class="main-content-wrap">
        <div class="flex items-center flex-wrap justify-between gap20 mb-27">
            <h3>All Products</h3>
            <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                <li>
                    <a href="{{ route('admin.index') }}">
                        <div class="text-tiny">Dashboard</div>
                    </a>
                </li>
                <li>
                    <i class="icon-chevron-right"></i>
                </li>
                <li>
                    <a href="{{ route('admin.products') }}">
                    <div class="text-tiny">All Products</div>
                    </a>
                </li>
            </ul>
        </div>
        @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif

        <div class="wg-box">
            <div class="flex items-center justify-between gap10 flex-wrap">
                <div class="wg-filter flex-grow">
                    <form class="form-search">
                        <fieldset class="name">
                            <input type="text" placeholder="Search here..." class="" name="name"
                                tabindex="2" value="" aria-required="true" required="">
                        </fieldset>
                        <div class="button-submit">
                            <button class="" type="submit"><i class="icon-search"></i></button>
                        </div>
                    </form>
                </div>
                <a class="tf-button style-1 w208" href="{{ route('admin.addProducts') }}"><i
                        class="icon-plus"></i>Add new</a>
            </div>
            
            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Price</th>
                            <th>SalePrice</th>
                            <th>SKU</th>
                            <th>Category</th>
                            <th>Brand</th>
                            <th>Featured</th>
                            <th>Stock</th>
                            <th>Quantity</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i=0 ?>
                        @foreach ($products as $product )
                            <?php $i++ ?>
                            <tr>
                                <td>{{ $i }}</td>
                                <td class="pname">
                                    <div class="image">
                                        <img src="{{ asset('uploads/products/thumbnails')}}/{{ $product->image }}" alt="{{ $product->name }}" class="image">
                                    </div>
                                    <div class="name">
                                        <a href="#" class="body-title-2">{{$product->name  }}</a>
                                        <div class="text-tiny mt-3">{{$product->short_description}}</div>
                                    </div>
                                </td>
                                <td>{{ $product->regular_price }}</td>
                                <td>{{ $product->sale_price }}</td>
                                <td>{{ $product->SKU }}</td>
                                <td>{{ $product->category->name }}</td>
                                <td>{{ $product->brand->name }}</td>
                                <td>{{ $product->featured ==0 ?'no':'yes' }}</td>
                                <td>{{ $product->stock_status }}</td>
                                <td>{{ $product->quantity }}</td>
                                <td>
                                    <div class="list-icon-function">
                                        <a href="#" target="_blank">
                                            <div class="item eye">
                                                <i class="icon-eye"></i>
                                            </div>
                                        </a>
                                        <a href="{{ route('admin.editProduct',$product->id) }}">
                                            <div class="item edit">
                                                <i class="icon-edit-3"></i>
                                            </div>
                                        </a>
                                        <form action="{{ route('admin.deleteProduct',$product->id) }}" method="POST">
                                            @csrf
                                            @method('delete')
                                            <div class="item text-danger delete">
                                                <i class="icon-trash-2"></i>
                                            </div>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="divider"></div>
            <div class="flex items-center justify-between flex-wrap gap10 wgp-pagination">

{{ $products->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>

@endsection



@push('scripts')
<script src="{{ asset('js/jquery.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>

  $(document).ready(function(){
    $('.delete').on('click', function(e){
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