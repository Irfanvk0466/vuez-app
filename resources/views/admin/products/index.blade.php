@extends('layouts.master')
@section('title') Products @endsection

@section('css')
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="p-3">
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @elseif (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @elseif (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
            </div>
            <div class="card-header d-flex align-items-center">
                <h4 class="card-title mb-0 flex-grow-1">Products</h4>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addProductModal">Add Product</button>
            </div>

            <div class="card-body">
                <table class="table table-bordered align-middle">
                    <thead>
                        <tr>
                            <th>SN</th>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Starting Price</th>
                            <th>Auction End Time</th>
                            <th>Image</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($products as $index => $product)
                        <tr>
                            <td>{{ $products->firstItem() + $index }}</td>
                            <td>{{ $product->name }}</td>
                            <td>{{ $product->description }}</td>
                            <td>{{ $product->starting_price }}</td>
                            <td>{{ $product->end_time }}</td>
                            <td>
                                @if($product->images && $product->images->count())
                                    <div class="d-flex flex-wrap gap-2">
                                        @foreach($product->images as $img)
                                            <a href="{{ asset('storage/' . $img->image_path) }}" target="_blank">
                                                <img src="{{ asset('storage/' . $img->image_path) }}" width="60px" height="60px"
                                                    style="object-fit: cover; border-radius: 4px; border: 1px solid #ccc;">
                                            </a>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-muted">No Image</span>
                                @endif
                            </td>
                            <td>
                                <button class="btn btn-sm btn-warning edit-btn" data-id="{{ $product->id }}">Edit</button>
                                <button class="btn btn-sm btn-danger delete-product-btn"
                                        data-id="{{ $product->id }}"
                                        data-action="{{ route('products.destroy', $product->id) }}"
                                        data-bs-toggle="modal"
                                        data-bs-target="#deleteConfirmationModal">
                                    Delete
                                </button>
                            </td>
                        </tr>
                        @endforeach

                        @if($products->isEmpty())
                            <tr>
                                <td colspan="7" class="text-center text-muted">No products found.</td>
                            </tr>
                        @endif
                    </tbody>
                </table>

                <div class="mt-3 d-flex justify-content-center">
                    {{ $products->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

@include('admin.products.create')
@include('admin.products.edit')
@include('components.delete-modal')
@endsection

@section('script')
<script src="{{ asset('js/product-edit.js') }}"></script>
<script>
    $(document).on('click', '.delete-product-btn', function () {
        const actionUrl = $(this).data('action');
        $('#delete-form').attr('action', actionUrl);
    });
</script>
@endsection
