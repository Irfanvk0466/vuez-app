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
                    <div class="alert alert-danger">
                        <ul>@foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach</ul>
                    </div>
                @elseif (session('success'))
                    <div class="alert alert-success" role="alert">{{ session('success') }}</div>
                @elseif (session('error'))
                    <div class="alert alert-danger" role="alert">{{ session('error') }}</div>
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
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $product->name }}</td>
                            <td>{{ $product->description }}</td>
                            <td>{{ $product->starting_price }}</td>
                            <td>{{ $product->end_time }}</td>
                            <td>
                                @if($product->images && $product->images->first())
                                    <a href="{{ asset('storage/' . $product->images->first()->image_path) }}" target="_blank">
                                        <img src="{{ asset('storage/' . $product->images->first()->image_path) }}" width="60px" height="60px" style="object-fit: cover; border-radius: 4px; border: 1px solid #ccc;">
                                    </a>
                                @else
                                    <span class="text-muted">No Image</span>
                                @endif
                            </td>
                            <td>
                                <button class="btn btn-sm btn-warning edit-btn" data-id="{{ $product->id }}">Edit</button>
                                <form method="POST" action="{{ route('products.destroy', $product->id) }}" class="d-inline" onsubmit="return confirm('Are you sure to delete this product?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger">Delete</button>
                                </form>
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
            </div>
        </div>
    </div>
</div>

@include('admin.products.create')
@include('admin.products.edit')
@endsection

@section('script')
<script src="{{ asset('js/product-edit.js') }}"></script>
@endsection
